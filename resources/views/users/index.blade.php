<x-app-layout>
    <div class="space-y-8">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.25em] text-blue-700 mb-4">
                USER MANAGEMENT
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Manage system users</h1>
            <p class="text-gray-500 mt-2">View, manage and organize all system users and their access.</p>
        </div>

        <div class="flex items-center justify-between gap-4 flex-wrap">
            <a href="{{ route('users.create') }}" class="btn-premium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add User
            </a>

            <div class="flex items-center gap-3 flex-1 max-w-md">
                <div class="relative flex-1">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search users..." class="search-input">
                </div>
                <button class="btn-premium-glass p-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="glass-panel border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 rounded-2xl">
                {{ session('status') }}
            </div>
        @endif

        <div class="glass-panel overflow-hidden rounded-3xl border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                        <tr>
                            <th class="table-header">
                                <div class="flex items-center gap-2">
                                    NAME
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="table-header">EMAIL</th>
                            <th class="table-header">
                                <div class="flex items-center gap-2">
                                    ROLE
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="table-header">JOB TITLE</th>
                            <th class="table-header">OFFICE</th>
                            <th class="table-header text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="table-cell">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($user->role === 'admin')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                @endif
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <span class="text-sm text-gray-600">{{ $user->email }}</span>
                                </td>
                                <td class="table-cell">
                                    @if($user->role === 'admin')
                                        <span class="role-badge-admin">{{ ucfirst($user->role) }}</span>
                                    @else
                                        <span class="role-badge-user">{{ ucfirst($user->role) }}</span>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    <span class="text-sm text-gray-600">{{ $user->job_title ?? '-' }}</span>
                                </td>
                                <td class="table-cell">
                                    <span class="text-sm text-gray-600">{{ $user->office ?? '-' }}</span>
                                </td>
                                <td class="table-cell text-right">
                                    <div x-data="{ menuOpen: false }" class="relative">
                                        <button @click="menuOpen = !menuOpen" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="6" r="2"></circle>
                                                <circle cx="12" cy="12" r="2"></circle>
                                                <circle cx="12" cy="18" r="2"></circle>
                                            </svg>
                                        </button>
                                        <div x-show="menuOpen" @click.outside="menuOpen = false" x-transition class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-200 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] z-50">
                                            <a href="{{ route('users.edit', $user) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 first:rounded-t-2xl">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit User
                                            </a>
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="border-t border-gray-100">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 last:rounded-b-2xl" onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete User
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-6 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
