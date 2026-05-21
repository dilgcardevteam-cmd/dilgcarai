<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="chip mb-2">Account profile</div>
            <div class="text-2xl font-bold text-white sm:text-3xl">Manage your account details, credentials, and access settings.</div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="panel p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="panel p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="panel p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
