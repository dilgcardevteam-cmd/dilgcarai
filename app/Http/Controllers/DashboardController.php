<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Notebook;
use App\Services\NotebookInsightsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, NotebookInsightsService $insights): View
    {
        $user = $request->user();
        
        $notebooks = Notebook::query()
            ->with(['owner', 'category'])
            ->withCount(['sources', 'chats', 'members'])
            ->accessibleBy($user)
            ->orderByDesc('last_activity_at')
            ->take(6)
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('dashboard', [
            'notebooks' => $notebooks,
            'categories' => $categories,
            'user' => $user,
        ]);
    }
}
