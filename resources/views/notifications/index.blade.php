<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="chip mb-2">Notifications</div>
            <div class="text-2xl font-bold text-white sm:text-3xl">System alerts, sharing updates, and source indexing status.</div>
        </div>
    </x-slot>

    <div class="panel p-6">
        <div class="space-y-4">
            @forelse ($notifications as $notification)
                <div class="panel-muted px-5 py-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-lg font-semibold text-white">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="mt-2 text-sm leading-7 text-slate-300">{{ $notification->data['message'] ?? 'No details available.' }}</p>
                            <p class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-500">{{ $notification->created_at?->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-3">
                            @if (! $notification->read_at)
                                <form method="POST" action="{{ route('notifications.update', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-secondary">Mark as read</button>
                                </form>
                            @endif
                            @if (! empty($notification->data['route']))
                                <a href="{{ $notification->data['route'] }}" class="btn-primary">Open</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="panel-muted px-6 py-12 text-center">
                    <p class="text-xl font-semibold text-white">No notifications yet</p>
                    <p class="mt-2 text-sm text-slate-300">Share a notebook, upload a source, or process content to see system updates here.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
