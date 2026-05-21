<x-app-layout>
    <div class="space-y-8">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.25em] text-blue-700 mb-4">
                USER MANAGEMENT
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Add new user</h1>
            <p class="text-gray-500 mt-2">Create a new system user and set their access permissions.</p>
        </div>

        <div class="glass-panel max-w-3xl rounded-3xl border border-gray-100 p-8">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="space-y-7">
                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-2 block w-full input-shell" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-2 block w-full input-shell" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" name="password" type="password" class="mt-2 block w-full input-shell" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" class="mt-2 block w-full input-shell">
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="job_title" value="Job Title" />
                        <x-text-input id="job_title" name="job_title" type="text" class="mt-2 block w-full input-shell" :value="old('job_title')" />
                        <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="office" value="Office" />
                        <x-text-input id="office" name="office" type="text" class="mt-2 block w-full input-shell" :value="old('office')" />
                        <x-input-error :messages="$errors->get('office')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
                        <button type="submit" class="btn-premium">Add User</button>
                        <a href="{{ route('users.index') }}" class="btn-premium-glass">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
