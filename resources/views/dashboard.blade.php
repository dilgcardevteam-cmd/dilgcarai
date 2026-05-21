<x-app-layout>
    <div class="dashboard-page space-y-7 py-1">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-3xl">
                <p class="mb-3 text-[11px] font-extrabold uppercase tracking-[0.38em] text-blue-600">
                    DILG KNOWLEDGE ASSISTANT
                </p>
                <h1 class="dashboard-heading text-[30px] font-extrabold leading-[1.05] text-slate-900 sm:text-[36px] lg:text-[40px]">
                    Welcome back, {{ $user->name }} 👋
                </h1>
                <p class="mt-3 max-w-2xl text-[17px] leading-7 text-slate-500">
                    Here's what's happening with your platform today.
                </p>
            </div>

            <div class="shrink-0">
                <a href="{{ route('analytics') }}" class="btn-premium-glass px-5 py-4 text-[15px] font-semibold text-slate-700">
                    <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Show System Analytics
                </a>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="dashboard-stat-card p-6">
                <div class="dashboard-stat-icon bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="mt-4 text-[13px] font-semibold text-slate-500">Total Notebooks</p>
                <h3 class="mt-2 text-[38px] font-extrabold tracking-[-0.05em] text-slate-900">{{ number_format($notebooks->count()) }}</h3>
                <p class="mt-2 text-[12px] text-slate-400">All notebooks in workspace</p>
            </div>

            <div class="dashboard-stat-card p-6">
                <div class="dashboard-stat-icon bg-gradient-to-br from-violet-100 to-violet-50 text-violet-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <p class="mt-4 text-[13px] font-semibold text-slate-500">Categories</p>
                <h3 class="mt-2 text-[38px] font-extrabold tracking-[-0.05em] text-slate-900">{{ number_format($categories->count()) }}</h3>
                <p class="mt-2 text-[12px] text-slate-400">Notebook categories</p>
            </div>

            <div class="dashboard-stat-card p-6">
                <div class="dashboard-stat-icon bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <p class="mt-4 text-[13px] font-semibold text-slate-500">Your Role</p>
                <h3 class="mt-2 text-[38px] font-extrabold tracking-[-0.05em] text-slate-900">{{ ucfirst($user->role ?? 'User') }}</h3>
                <div class="mt-3">
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-100">Full Access</span>
                </div>
            </div>

            <div class="dashboard-stat-card p-6">
                <div class="dashboard-stat-icon bg-gradient-to-br from-amber-100 to-amber-50 text-amber-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="mt-4 text-[13px] font-semibold text-slate-500">Last Active</p>
                <h3 class="mt-2 text-[28px] font-extrabold tracking-[-0.04em] text-slate-900 xl:text-[34px]">
                    {{ $user->last_active_at?->format('M d, Y') ?? now()->format('M d, Y') }}
                </h3>
                <p class="mt-2 text-[12px] text-slate-400">Your last active date</p>
            </div>
        </div>

        <div>
            <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-[18px] font-extrabold tracking-[-0.03em] text-slate-900">Recent Notebooks</h2>
                <div class="flex items-center gap-4">
                    <a href="{{ route('notebooks.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                        View all
                    </a>
                    <form method="POST" action="{{ route('notebooks.create.quick') }}">
                        @csrf
                        <button type="submit" class="btn-premium px-5 py-3 text-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create notebook
                        </button>
                    </form>
                </div>
            </div>

            @if($notebooks->count() > 0)
                <div class="max-w-[640px]">
                    <x-notebook-card :notebook="$notebooks->first()" />
                </div>
            @else
                <div class="glass-panel col-span-full rounded-[28px] border border-white/70 px-10 py-16 text-center">
                    <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.831 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <p class="text-xl font-semibold text-slate-900">No notebooks yet.</p>
                    <form method="POST" action="{{ route('notebooks.create.quick') }}" class="mt-8 inline-block">
                        @csrf
                        <button type="submit" class="btn-premium">Create Notebook</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="mt-8 glass-panel rounded-[28px] border border-white/70 p-5">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Secured. Reliable. Government-Grade.</h3>
                        <p class="mt-1 text-xs text-slate-500">Your data is encrypted and protected with enterprise-grade security and compliance standards.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 rounded-full bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    NoteGov AI DILG Platform
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
