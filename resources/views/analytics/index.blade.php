<x-app-layout>
    <div class="space-y-8 py-2">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="btn-premium-outline flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Analytics</h1>
                    <p class="text-gray-500 mt-1 text-sm">Here's what's happening with your platform today.</p>
                </div>
            </div>
        </div>

        <!-- Top Stats Cards -->
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-sky-50 rounded-2xl text-sky-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">Total Users</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($totals['users']) }}</h3>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        12%
                    </span>
                </div>
                <div class="h-8">
                    <svg viewBox="0 0 200 40" class="w-full h-full">
                        <path d="M0,30 Q20,25 40,28 T80,22 T120,26 T160,18 T200,22" fill="none" stroke="#38BDF8" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">vs last 14 days</p>
            </div>

            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-violet-50 rounded-2xl text-violet-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">Total Workspaces</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($totals['notebooks']) }}</h3>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        8%
                    </span>
                </div>
                <div class="h-8">
                    <svg viewBox="0 0 200 40" class="w-full h-full">
                        <path d="M0,32 Q20,28 40,30 T80,24 T120,28 T160,20 T200,24" fill="none" stroke="#8B5CF6" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">vs last 14 days</p>
            </div>

            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-amber-50 rounded-2xl text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">Active Sessions</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($totals['active_sessions']) }}</h3>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">Live</span>
                </div>
                <div class="h-8">
                    <svg viewBox="0 0 200 40" class="w-full h-full">
                        <path d="M0,30 Q20,26 40,28 T80,25 T120,27 T160,24 T200,26" fill="none" stroke="#F59E0B" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">vs last 14 days</p>
            </div>

            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-rose-50 rounded-2xl text-rose-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500">AI Requests</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($totals['ai_requests']) }}</h3>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        24%
                    </span>
                </div>
                <div class="h-8">
                    <svg viewBox="0 0 200 40" class="w-full h-full">
                        <path d="M0,30 Q20,28 40,25 T80,28 T120,22 T160,25 T200,20" fill="none" stroke="#F43F5E" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">vs last 14 days</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid gap-5 lg:grid-cols-2">
            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">User Activity & Workspace Growth</h3>
                        <p class="text-xs text-gray-500 mt-1">Track user and workspace growth over time.</p>
                    </div>
                    <select class="text-xs border border-gray-200 bg-white rounded-xl px-3 py-2 focus:ring-0">
                        <option>Last 14 days</option>
                        <option>Last 30 days</option>
                    </select>
                </div>
                <div class="flex items-center gap-6 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <span class="text-xs font-semibold text-gray-600">Users</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                        <span class="text-xs font-semibold text-gray-600">Workspaces</span>
                    </div>
                </div>
                <div class="h-[300px] w-full">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">AI Analytics (Requests)</h3>
                        <p class="text-xs text-gray-500 mt-1">Daily breakdown of AI requests.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1 text-xs text-gray-600"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Requests</span>
                    </div>
                </div>
                <div class="h-[300px] w-full">
                    <canvas id="aiChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Lower Section: Activity & Mix -->
        <div class="grid gap-5 lg:grid-cols-2">
            <!-- Latest Activity -->
            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Latest Platform Activity</h3>
                        <p class="text-xs text-gray-500 mt-1">Recent actions across the platform.</p>
                    </div>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All</a>
                </div>
                <div class="space-y-4">
                    @foreach ($latestActivity as $activity)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-50 to-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if(str_contains($activity->description, 'user') || str_contains($activity->description, 'registered'))
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    @elseif(str_contains($activity->description, 'notebook') || str_contains($activity->description, 'workspace'))
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    @endif
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $activity->description }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $activity->user->name ?? 'System' }} • {{ $activity->notebook->title ?? 'Global' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">{{ $activity->created_at?->format('M d, Y') }}</p>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 mt-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Success
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Source Status -->
            <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                <div class="mb-6">
                    <h3 class="text-base font-bold text-gray-900">Source Status Mix</h3>
                    <p class="text-xs text-gray-500 mt-1">Distribution of source statuses.</p>
                </div>
                <div class="flex items-center gap-8">
                    <div class="relative w-40 h-40">
                        <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#F1F5F9" stroke-width="12"/>
                            @php
                                $totalSources = $totals['sources'] > 0 ? $totals['sources'] : 1;
                                $currentOffset = 0;
                                $statusColors = [
                                    'indexed' => '#10B981',
                                    'pending' => '#F59E0B',
                                    'error' => '#F43F5E',
                                    'processing' => '#3B82F6',
                                ];
                            @endphp
                            @foreach ($sourceStatuses as $row)
                                @php
                                    $percentage = ($row->aggregate / $totalSources) * 100;
                                    $circumference = 2 * M_PI * 40;
                                    $dashArray = ($percentage / 100) * $circumference;
                                @endphp
                                <circle 
                                    cx="50" 
                                    cy="50" 
                                    r="40" 
                                    fill="none" 
                                    stroke="{{ $statusColors[$row->status] ?? '#94A3B8' }}" 
                                    stroke-width="12"
                                    stroke-dasharray="{{ $dashArray }} {{ $circumference }}"
                                    stroke-dashoffset="{{ -$currentOffset }}"
                                    stroke-linecap="round"
                                />
                                @php
                                    $currentOffset += $dashArray;
                                @endphp
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($totals['sources']) }}</span>
                            <span class="text-xs text-gray-500">Total</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-3">
                        @foreach ($sourceStatuses as $row)
                            @php
                                $colors = [
                                    'indexed' => ['dot' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'label' => 'Active'],
                                    'pending' => ['dot' => 'bg-amber-500', 'text' => 'text-amber-700', 'label' => 'Pending'],
                                    'error' => ['dot' => 'bg-rose-500', 'text' => 'text-rose-700', 'label' => 'Error'],
                                    'processing' => ['dot' => 'bg-blue-500', 'text' => 'text-blue-700', 'label' => 'Processing'],
                                ];
                                $c = $colors[$row->status] ?? ['dot' => 'bg-gray-500', 'text' => 'text-gray-700', 'label' => 'Archived'];
                                $percentage = ($totals['sources'] > 0) ? round(($row->aggregate / $totals['sources']) * 100) : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $c['dot'] }}"></span>
                                    <span class="text-sm font-medium text-gray-700">{{ $c['label'] }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-600">{{ $percentage }}% ({{ $row->aggregate }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctxActivity = document.getElementById('activityChart').getContext('2d');
            new Chart(ctxActivity, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Users',
                            data: @json($chartData['userActivity']),
                            borderColor: '#38BDF8',
                            backgroundColor: 'rgba(56, 189, 248, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Workspaces',
                            data: @json($chartData['workspaceUsage']),
                            borderColor: '#8B5CF6',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            padding: 12,
                            backgroundColor: '#1e293b',
                            titleFont: { size: 13 },
                            bodyFont: { size: 13 },
                            cornerRadius: 10,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: true, color: '#F1F5F9', drawBorder: false },
                            ticks: { font: { size: 11 }, color: '#94A3B8', padding: 10 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#94A3B8', padding: 10 }
                        }
                    }
                }
            });

            const ctxAi = document.getElementById('aiChart').getContext('2d');
            new Chart(ctxAi, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Requests',
                        data: @json($chartData['aiRequests']),
                        backgroundColor: '#F43F5E',
                        borderRadius: 8,
                        barThickness: 14,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 12,
                            backgroundColor: '#1e293b',
                            cornerRadius: 10,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: true, color: '#F1F5F9', drawBorder: false },
                            ticks: { font: { size: 11 }, color: '#94A3B8', padding: 10 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#94A3B8', padding: 10 }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
