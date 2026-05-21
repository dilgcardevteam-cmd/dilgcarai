<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="chip mb-2">Edit notebook</div>
            <div class="text-2xl font-bold text-gray-900 sm:text-3xl">Refine notebook settings, scope, and presentation.</div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('notebooks.update', $notebook) }}">
        @csrf
        @method('PATCH')
        @include('notebooks.partials.form')
    </form>
</x-app-layout>
