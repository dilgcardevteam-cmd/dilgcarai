<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Notebook;
use App\Models\Source;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class AdminAnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function __invoke(): View
    {
        $days = 14;
        $startDate = Carbon::now()->subDays($days);

        // Fetch counts for charts
        $userActivity = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('aggregate', 'date');

        $workspaceUsage = Notebook::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('aggregate', 'date');

        $aiRequests = Message::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->where('role', 'user')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('aggregate', 'date');

        // Fill in missing dates with zero
        $chartData = [];
        for ($i = 0; $i <= $days; $i++) {
            $date = Carbon::now()->subDays($days - $i)->format('Y-m-d');
            $chartData['labels'][] = Carbon::parse($date)->format('M d');
            $chartData['userActivity'][] = $userActivity[$date] ?? 0;
            $chartData['workspaceUsage'][] = $workspaceUsage[$date] ?? 0;
            $chartData['aiRequests'][] = $aiRequests[$date] ?? 0;
        }

        return view('analytics.index', [
            'totals' => [
                'users' => User::count(),
                'notebooks' => Notebook::count(),
                'active_sessions' => DB::table('sessions')->count(),
                'ai_requests' => Message::where('role', 'user')->count(),
                'sources' => Source::count(),
                'indexed_sources' => Source::where('status', 'indexed')->count(),
            ],
            'chartData' => $chartData,
            'latestActivity' => ActivityLog::with(['user', 'notebook'])->latest('created_at')->limit(8)->get(),
            'sourceStatuses' => Source::query()
                ->selectRaw('status, count(*) as aggregate')
                ->groupBy('status')
                ->orderBy('status')
                ->get(),
        ]);
    }
}
