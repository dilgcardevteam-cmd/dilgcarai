<x-app-layout>
    <div class="space-y-10">
        <div class="text-left">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.25em] text-blue-700 mb-5">
                USER MANAGEMENT
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Edit: {{ $user->name }}</h1>
            <p class="text-gray-600 text-base">Update user details and manage access permissions.</p>
        </div>

        <div class="glass-panel max-w-4xl rounded-3xl border border-gray-200 shadow-[0_10px_50px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="p-8 border-b border-gray-100 bg-gradient-to-r from-white to-gray-50">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center shadow-sm">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($user->role === 'admin')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            @endif
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <div class="inline-flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[11px] font-semibold text-blue-700">
                                {{ strtoupper($user->role) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('users.update', $user) }}" class="p-10">
                @csrf
                @method('PATCH')

                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="Full Name" class="!text-gray-700 !text-sm !font-semibold" />
                            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full input-shell !text-gray-900" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email Address" class="!text-gray-700 !text-sm !font-semibold" />
                            <x-text-input id="email" name="email" type="email" class="mt-2 block w-full input-shell !text-gray-900" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="role" value="System Role" class="!text-gray-700 !text-sm !font-semibold" />
                            <select id="role" name="role" class="mt-2 block w-full input-shell !text-gray-900">
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="job_title" value="Job Title" class="!text-gray-700 !text-sm !font-semibold" />
                            <x-text-input id="job_title" name="job_title" type="text" class="mt-2 block w-full input-shell !text-gray-900" :value="old('job_title', $user->job_title)" />
                            <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="office" value="Office / Department" class="!text-gray-700 !text-sm !font-semibold" />
                        <x-text-input id="office" name="office" type="text" class="mt-2 block w-full input-shell !text-gray-900" :value="old('office', $user->office)" />
                        <x-input-error :messages="$errors->get('office')" class="mt-2" />
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <x-input-label for="password" value="Password" class="!text-gray-700 !text-sm !font-semibold" />
                        <p class="text-sm text-gray-500 mt-1 mb-2">Leave blank to keep current password</p>
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full input-shell !text-gray-900" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 pt-6">
                        <button type="submit" class="btn-premium px-8 py-3 text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn-premium-glass px-6 py-3 text-base">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
