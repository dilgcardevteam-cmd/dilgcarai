<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NoteGov AI DILG') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|space-grotesk:400,500,700&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <style>
            :root {
                --notegov-blue: #002c76;
                --notegov-blue-deep: #031b4e;
                --notegov-surface: #f4f7fb;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(37, 99, 235, 0.06), transparent 32%),
                    linear-gradient(180deg, #ffffff 0%, #f6f8fc 56%, #eef3fb 100%);
                color: #0f172a;
            }

            .sidebar-bg {
                background:
                    radial-gradient(circle at top right, rgba(59, 130, 246, 0.22), transparent 22%),
                    radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.24), transparent 28%),
                    linear-gradient(180deg, #07122e 0%, #081e4f 52%, #002c76 100%) !important;
            }

            .dashboard-sidebar {
                position: relative;
                border-right: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow:
                    0 18px 60px rgba(2, 8, 23, 0.36),
                    inset 0 1px 0 rgba(255, 255, 255, 0.06);
                overflow: hidden;
            }

            .dashboard-sidebar::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at 100% 0%, rgba(125, 211, 252, 0.16), transparent 24%),
                    linear-gradient(135deg, rgba(255, 255, 255, 0.04), transparent 36%);
                pointer-events: none;
            }

            .dashboard-sidebar a {
                text-decoration: none;
            }

            .dashboard-sidebar > div {
                position: relative;
            }

            .dashboard-sidebar .space-y-1.flex-1 {
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .dashboard-sidebar .space-y-1.flex-1 > a {
                display: flex;
                align-items: center;
                gap: 0.8rem;
                min-height: 52px;
                padding: 0.85rem 1rem;
                border-radius: 18px;
                border: 1px solid transparent;
                font-size: 0.98rem;
                line-height: 1.1;
            }

            .dashboard-sidebar .space-y-1.flex-1 > a span {
                letter-spacing: -0.01em;
            }

            .dashboard-sidebar .space-y-1.flex-1 > a:hover {
                transform: translateX(1px);
            }

            .dashboard-sidebar .space-y-1.flex-1 > a.active {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.38), rgba(37, 99, 235, 0.18)) !important;
                border-color: rgba(147, 197, 253, 0.22);
                box-shadow:
                    0 16px 30px rgba(3, 27, 78, 0.24),
                    inset 0 1px 0 rgba(255, 255, 255, 0.08);
            }

            .dashboard-sidebar .space-y-1.flex-1 > a:not(.active):hover {
                background: rgba(255, 255, 255, 0.07) !important;
                border-color: rgba(255, 255, 255, 0.08);
            }

            .dashboard-sidebar .space-y-1.flex-1 > a svg {
                width: 20px;
                height: 20px;
                flex-shrink: 0;
            }

            .dashboard-sidebar .space-y-1.flex-1 > a.active svg,
            .dashboard-sidebar .space-y-1.flex-1 > a:hover svg {
                filter: drop-shadow(0 4px 8px rgba(15, 23, 42, 0.12));
            }

            .dashboard-sidebar .space-y-1.flex-1 > .pt-6 {
                padding-top: 1.2rem;
            }

            .dashboard-sidebar .space-y-1.flex-1 > .pt-6 .sidebar-text-muted {
                padding-left: 1rem;
                font-size: 0.7rem;
                letter-spacing: 0.22em;
            }

            .dashboard-sidebar .space-y-1.flex-1 > a.active span,
            .dashboard-sidebar .space-y-1.flex-1 > a:hover span {
                color: #fff;
            }

            .dashboard-sidebar .px-6.py-8 > a:first-child {
                margin-bottom: 2rem;
                padding: 0.2rem 0.1rem 0.5rem;
            }

            .dashboard-sidebar .px-6.py-8 > a:first-child > div:first-child,
            .dashboard-sidebar .px-5.py-6 > .flex.items-center.justify-between.mb-8 > .flex.items-center.gap-3 > div:first-child {
                box-shadow:
                    0 14px 28px rgba(37, 99, 235, 0.25),
                    inset 0 1px 0 rgba(255, 255, 255, 0.08);
            }

            .dashboard-sidebar .px-6.py-8 > a:first-child p:last-child {
                font-family: 'Space Grotesk', sans-serif;
                font-size: 1.08rem;
                letter-spacing: -0.02em;
            }

            .dashboard-sidebar .px-6.py-8 > a:first-child p:first-child {
                color: rgba(96, 165, 250, 0.95);
            }

            .dashboard-sidebar .sidebar-link:hover,
            .dashboard-sidebar .sidebar-link.active {
                border-radius: 18px;
            }

            .sidebar-link {
                color: rgba(255, 255, 255, 0.72) !important;
                transition: all 0.22s ease;
                border: 1px solid transparent;
                position: relative;
                z-index: 1;
            }

            .sidebar-link:hover {
                color: white !important;
                background: rgba(255, 255, 255, 0.07) !important;
                border-color: rgba(255, 255, 255, 0.08);
            }

            .sidebar-link.active {
                color: white !important;
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.42), rgba(37, 99, 235, 0.24)) !important;
                border-color: rgba(147, 197, 253, 0.22);
                box-shadow: 0 18px 32px rgba(3, 27, 78, 0.28);
            }

            .sidebar-link svg {
                filter: drop-shadow(0 2px 6px rgba(15, 23, 42, 0.08));
            }

            .sidebar-text-muted {
                color: rgba(255, 255, 255, 0.5) !important;
            }

            .sidebar-white {
                color: white !important;
            }

            .dashboard-sidebar .space-y-1.flex-1 > a.active {
                position: relative;
                overflow: hidden;
            }

            .dashboard-sidebar .space-y-1.flex-1 > a.active::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), transparent 38%);
                pointer-events: none;
            }

            .app-topbar {
                background: rgba(255, 255, 255, 0.92);
                backdrop-filter: blur(16px);
                border-bottom: 1px solid rgba(148, 163, 184, 0.22);
                box-shadow: 0 8px 30px rgba(15, 23, 42, 0.04);
            }

            .app-topbar-inner {
                min-height: 80px;
            }

            .app-profile-chip {
                border-radius: 9999px;
                border: 1px solid rgba(226, 232, 240, 1);
                background: rgba(255, 255, 255, 0.96);
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
                transition: all 0.2s ease;
            }

            .app-profile-chip:hover {
                border-color: rgba(191, 219, 254, 1);
                box-shadow: 0 14px 30px rgba(37, 99, 235, 0.08);
            }

            .btn-premium {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                border-radius: 9999px;
                background: linear-gradient(135deg, #2359e8 0%, #0f4ccf 55%, #002c76 100%);
                color: #fff;
                padding: 0.9rem 1.4rem;
                font-size: 0.875rem;
                font-weight: 700;
                box-shadow: 0 18px 38px rgba(2, 44, 118, 0.24);
                transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            }

            .btn-premium:hover {
                transform: translateY(-1px);
                box-shadow: 0 22px 42px rgba(2, 44, 118, 0.28);
                filter: brightness(1.02);
            }

            .btn-premium-glass {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                border-radius: 9999px;
                border: 1px solid rgba(226, 232, 240, 1);
                background: rgba(255, 255, 255, 0.92);
                color: #0f172a;
                padding: 0.85rem 1.15rem;
                font-size: 0.875rem;
                font-weight: 700;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
                transition: all 0.2s ease;
            }

            .btn-premium-glass:hover {
                border-color: rgba(191, 219, 254, 1);
                background: #fff;
                box-shadow: 0 16px 30px rgba(37, 99, 235, 0.08);
                transform: translateY(-1px);
            }

            .btn-premium-outline {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                border-radius: 9999px;
                border: 1px solid rgba(191, 219, 254, 1);
                background: rgba(255, 255, 255, 0.92);
                color: #0f4ccf;
                padding: 0.85rem 1.2rem;
                font-size: 0.875rem;
                font-weight: 700;
                box-shadow: 0 10px 24px rgba(37, 99, 235, 0.05);
                transition: all 0.2s ease;
            }

            .btn-premium-outline:hover {
                background: #eff6ff;
                border-color: rgba(96, 165, 250, 1);
                box-shadow: 0 16px 30px rgba(37, 99, 235, 0.08);
                transform: translateY(-1px);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.82);
                backdrop-filter: blur(18px);
                border: 1px solid rgba(255, 255, 255, 0.72);
                box-shadow: 0 12px 36px rgba(15, 23, 42, 0.06);
            }

            .dashboard-page .dashboard-heading {
                font-family: 'Space Grotesk', 'Manrope', ui-sans-serif, system-ui, sans-serif;
                letter-spacing: -0.04em;
            }

            .dashboard-stat-card {
                position: relative;
                overflow: hidden;
                border-radius: 28px;
                border: 1px solid rgba(226, 232, 240, 0.9);
                background: rgba(255, 255, 255, 0.92);
                box-shadow: 0 18px 42px rgba(15, 23, 42, 0.07);
                transition: transform 0.25s ease, box-shadow 0.25s ease;
            }

            .dashboard-stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 24px 54px rgba(15, 23, 42, 0.09);
            }

            .dashboard-stat-card::after {
                content: '';
                position: absolute;
                inset: auto 0 0 0;
                height: 52px;
                background: linear-gradient(180deg, transparent 0%, rgba(59, 130, 246, 0.05) 100%);
                pointer-events: none;
            }

            .dashboard-stat-icon {
                width: 58px;
                height: 58px;
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.95);
            }

            .dashboard-notebook-card {
                position: relative;
                overflow: hidden;
                border-radius: 30px;
                color: #fff;
                background: linear-gradient(135deg, #6d5ef9 0%, #4f46e5 44%, #2447e4 100%);
                box-shadow: 0 26px 54px rgba(72, 69, 193, 0.28);
            }

            .dashboard-notebook-card::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 30%),
                    radial-gradient(circle at bottom left, rgba(255, 255, 255, 0.12), transparent 26%);
                pointer-events: none;
            }

            .dashboard-notebook-card::after {
                content: '';
                position: absolute;
                right: 18px;
                bottom: 10px;
                width: 170px;
                height: 170px;
                border-radius: 9999px;
                background:
                    radial-gradient(circle at center, rgba(255, 255, 255, 0.15) 1px, transparent 1.4px) 0 0 / 10px 10px;
                opacity: 0.4;
                mask-image: radial-gradient(circle at center, #000 52%, transparent 100%);
                pointer-events: none;
            }

            @media (min-width: 1024px) {
                .dashboard-shell {
                    overflow: hidden;
                }

            .dashboard-shell .app-main {
                    height: calc(100vh - 81px);
                    overflow: hidden;
                }

                .dashboard-shell .app-content-inner {
                    height: 100%;
                    overflow: hidden;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100 {{ request()->routeIs('dashboard') ? 'dashboard-shell' : '' }}">
        <div x-data="{ navOpen: false }" class="relative min-h-screen">
            <div class="relative flex min-h-screen">
                <!-- Desktop Sidebar -->
                <aside class="hidden lg:flex lg:flex-col lg:fixed lg:h-screen lg:inset-y-0 lg:left-0 sidebar-bg dashboard-sidebar z-20" style="width: 280px;">
                    <div class="relative z-10 px-6 py-8 flex flex-col h-full">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-4 mb-10">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <x-application-logo class="h-7 w-7 shrink-0 sidebar-white" />
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.28em] text-blue-400">DILG</p>
                                <p class="text-xl font-bold sidebar-white">NoteGov AI</p>
                            </div>
                        </a>

                        <div class="space-y-1 flex-1">
                            <a href="{{ route('notebooks.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('notebooks.*') && !request()->routeIs('notebooks.show') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-semibold">My Workspace</span>
                            </a>

                            @if(auth()->user()->role === 'admin')
                                <div class="pt-6 pb-2">
                                    <p class="px-4 text-xs font-bold uppercase tracking-wider sidebar-text-muted">Admin Tools</p>
                                </div>
                                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span class="font-semibold">Admin Dashboard</span>
                                </a>
                                <a href="{{ route('users.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span class="font-semibold">User Management</span>
                                </a>
                                <a href="{{ route('featured-notebooks.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('featured-notebooks.*') ? 'active' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.95 1.71l-1.52 4.674c-.3.921-1.604.921-1.902 0l-5.449-1.675a1 1 0 00-.95.69h-4.915c-.969 0-1.371-1.24-.95-1.71l1.52-4.674a1 1 0 00-.95-.69H5.183c-.969 0-1.371 1.24-.95 1.71l1.519 4.674c.3.921 1.603.921 1.902 0l5.45 1.675c.3.921 1.604-.921 1.902 0z"></path>
                                    </svg>
                                    <span class="font-semibold">Featured Notebooks</span>
                                </a>
                                <a href="{{ route('settings.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-1.066 2.572c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c.94 1.543-.826 3.31-2.37 2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="font-semibold">System Settings</span>
                                </a>
                            @endif
                        </div>

                    </div>
                </aside>

                <!-- Main Content -->
                <div class="flex min-w-0 flex-1 flex-col" style="margin-left: 0;">
                    <!-- Mobile Header -->
                    <header class="sticky top-0 z-30 app-topbar">
                        <div class="app-topbar-inner flex items-center justify-between gap-4 px-4 py-4 lg:px-6 lg:py-5">
                            <div class="flex items-center gap-3">
                                <button type="button" class="lg:hidden px-4 py-2 rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700" @click="navOpen = true">Menu</button>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-blue-600">DILG KNOWLEDGE ASSISTANT</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                @auth
                                    <!-- Desktop User Menu -->
                                    <div x-data="{ appUserMenuOpen: false }" class="relative" style="margin-right: 16px;">
                                        <button @click="appUserMenuOpen = !appUserMenuOpen" class="app-profile-chip flex items-center gap-3 px-3.5 py-2.5 hover:bg-white">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-[0_10px_18px_rgba(37,99,235,0.18)]">
                                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-semibold text-slate-700">{{ Auth::user()->name ?? 'User' }}</span>
                                        </button>
                                        <div x-show="appUserMenuOpen" @click.outside="appUserMenuOpen = false" class="absolute top-12 bg-white border border-gray-200 rounded-xl shadow-xl min-w-[220px] z-50" style="right: 0;">
                                            <div class="px-5 py-4 border-b border-gray-100">
                                                <p class="font-bold text-gray-900 text-sm">{{ Auth::user()->name ?? 'User' }}</p>
                                                <p class="text-sm text-gray-500 mt-1">{{ Auth::user()->email ?? '' }}</p>
                                            </div>
                                            <a href="{{ route('profile.edit') }}" class="block px-5 py-3 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">Profile</a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-5 py-3 text-red-600 font-semibold text-sm hover:bg-gray-50 transition">
                                                    Log out
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </header>

                    <!-- Mobile Sidebar Overlay -->
                    <div x-show="navOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" @click="navOpen = false"></div>
                    
                    <!-- Mobile Sidebar -->
                    <aside x-show="navOpen" x-transition class="fixed inset-y-0 left-0 z-50 w-80 sidebar-bg dashboard-sidebar lg:hidden">
                        <div class="relative z-10 px-5 py-6 flex flex-col h-full">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                        <x-application-logo class="h-6 w-6 shrink-0 sidebar-white" />
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.24em] sidebar-text-muted">NoteGov AI DILG</p>
                                        <p class="font-semibold sidebar-white">Navigation</p>
                                    </div>
                                </div>
                                <button type="button" class="px-4 py-2 rounded-lg border border-white/20 bg-white/10 text-sm font-semibold sidebar-white hover:bg-white/20 transition" @click="navOpen = false">Close</button>
                            </div>
                            
                            <div class="space-y-1 flex-1">
                                <a href="{{ route('notebooks.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('notebooks.*') && !request()->routeIs('notebooks.show') ? 'active' : '' }}" @click="navOpen = false">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="font-semibold">My Workspace</span>
                                </a>

                                @if(auth()->user()->role === 'admin')
                                    <div class="pt-6 pb-2">
                                        <p class="px-4 text-xs font-bold uppercase tracking-wider sidebar-text-muted">Admin Tools</p>
                                    </div>
                                    <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'active' : '' }}" @click="navOpen = false">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <span class="font-semibold">Admin Dashboard</span>
                                    </a>
                                    <a href="{{ route('users.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('users.*') ? 'active' : '' }}" @click="navOpen = false">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <span class="font-semibold">User Management</span>
                                    </a>
                                    <a href="{{ route('featured-notebooks.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('featured-notebooks.*') ? 'active' : '' }}" @click="navOpen = false">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.95 1.71l-1.52 4.674c-.3.921-1.604.921-1.902 0l-5.449-1.675a1 1 0 00-.95.69h-4.915c-.969 0-1.371-1.24-.95-1.71l1.52-4.674a1 1 0 00-.95-.69H5.183c-.969 0-1.371 1.24-.95 1.71l1.519 4.674c.3.921 1.603.921 1.902 0l5.45 1.675c.3.921 1.604-.921 1.902 0z"></path>
                                        </svg>
                                        <span class="font-semibold">Featured Notebooks</span>
                                    </a>
                                    <a href="{{ route('settings.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('settings.*') ? 'active' : '' }}" @click="navOpen = false">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-1.066 2.572c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c.94 1.543-.826 3.31-2.37 2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-semibold">System Settings</span>
                                    </a>
                                @endif
                            </div>

                        </div>
                    </aside>

                    <!-- Main Content Area with Fixed Sidebar Margin for Desktop -->
                    <main class="app-main relative flex-1 px-4 pb-6 pt-6 lg:px-8 lg:pt-8" style="margin-left: 0; padding-right: 1.5rem;">
                        <div class="app-content-inner lg:ml-[280px] lg:mr-4">
                            @if (session('status'))
                                <div class="mb-6 rounded-xl border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-800">
                                    {{ session('status') }}
                                </div>
                            @endif

                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
