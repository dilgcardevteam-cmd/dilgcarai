<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="chip mb-2">Create notebook</div>
            <div class="text-2xl font-bold text-gray-900 sm:text-3xl">Create a new governance notebook for policy intelligence.</div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('notebooks.store') }}">
        @csrf
        @include('notebooks.partials.form')
    </form>
</x-app-layout>
