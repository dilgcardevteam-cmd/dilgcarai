<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NoteGov AI DILG') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|space-grotesk:400,500,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .sidebar-bg {
                background-color: #0f172a !important;
            }
            .sidebar-link {
                color: rgba(255, 255, 255, 0.7) !important;
                transition: all 0.2s ease;
            }
            .sidebar-link:hover {
                color: white !important;
                background-color: rgba(255, 255, 255, 0.08) !important;
            }
            .sidebar-link.active {
                color: white !important;
                background-color: rgba(255, 255, 255, 0.12) !important;
            }
            .sidebar-text-muted {
                color: rgba(255, 255, 255, 0.45) !important;
            }
            .sidebar-white {
                color: white !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div x-data="{ navOpen: false }" class="relative min-h-screen">
            <div class="relative flex min-h-screen">
                <!-- Desktop Sidebar -->
                <aside class="hidden lg:flex lg:flex-col lg:fixed lg:h-screen lg:inset-y-0 lg:left-0 sidebar-bg z-20" style="width: 280px;">
                    <div class="px-6 py-8 flex flex-col h-full">
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

                        <div class="mt-6">
                            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center sidebar-white font-bold text-sm">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold sidebar-white">{{ Auth::user()->name ?? 'User' }}</p>
                                        <p class="text-xs sidebar-text-muted">{{ ucfirst(Auth::user()->role ?? 'User') }}</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition text-sm font-medium sidebar-white">
                                        Log out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="flex min-w-0 flex-1 flex-col" style="margin-left: 0;">
                    <!-- Mobile Header -->
                    <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between gap-4 px-4 py-4 lg:px-6 lg:py-5">
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
                                        <button @click="appUserMenuOpen = !appUserMenuOpen" class="flex items-center gap-3 px-3 py-2 rounded-lg border border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 transition">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'User' }}</span>
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
                    <aside x-show="navOpen" x-transition class="fixed inset-y-0 left-0 z-50 w-80 sidebar-bg lg:hidden">
                        <div class="px-5 py-6 flex flex-col h-full">
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

                            <div class="mt-6">
                                <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center sidebar-white font-bold text-sm">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold sidebar-white">{{ Auth::user()->name ?? 'User' }}</p>
                                            <p class="text-xs sidebar-text-muted">{{ ucfirst(Auth::user()->role ?? 'User') }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block w-full text-left px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition text-sm font-medium sidebar-white mb-2">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg bg-red-500/20 hover:bg-red-500/30 transition text-sm font-medium sidebar-white">
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Main Content Area with Fixed Sidebar Margin for Desktop -->
                    <main class="relative flex-1 px-4 pb-10 pt-6 lg:px-8 lg:pt-8" style="margin-left: 0; padding-right: 1.5rem;">
                        <div class="lg:ml-[280px] lg:mr-4">
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
