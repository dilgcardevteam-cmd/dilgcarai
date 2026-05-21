<x-app-layout>
    <div class="space-y-8 py-1">
        <div class="max-w-4xl">
            <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-50 to-violet-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.32em] text-violet-700 ring-1 ring-violet-100">
                FEATURED NOTEBOOKS
            </div>
            <h1 class="dashboard-heading mt-5 text-[30px] font-extrabold leading-[1.05] tracking-[-0.04em] text-slate-900 sm:text-[36px] lg:text-[40px]">
                Manage Featured Notebooks
            </h1>
            <p class="mt-3 max-w-3xl text-[17px] leading-7 text-slate-500">
                Control which notebooks appear in the Featured Notebooks section on the user dashboard.
            </p>
        </div>

        @if (session('status'))
            <div class="glass-panel rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-8">
            <section class="glass-panel overflow-hidden rounded-[30px] border border-white/70">
                <div class="border-b border-slate-100/80 p-6 sm:p-7">
                    <h2 class="text-[22px] font-extrabold tracking-[-0.03em] text-slate-900">Add Notebook to Featured</h2>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <form method="GET" action="{{ route('featured-notebooks.index') }}" class="w-full max-w-md">
                            <label class="relative block">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Search notebooks..."
                                    value="{{ $search }}"
                                    class="search-input h-[50px] pl-12 text-[14px]"
                                >
                            </label>
                        </form>
                    </div>
                </div>

                <div class="p-6 sm:p-7">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @forelse ($allNotebooks as $notebook)
                            <div class="group flex items-center gap-4 rounded-[22px] border border-slate-100 bg-white p-4 shadow-[0_12px_28px_rgba(15,23,42,0.05)] transition-all duration-300 hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_18px_42px_rgba(15,23,42,0.08)]">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-white shadow-[0_14px_24px_rgba(79,70,229,0.18)]" style="background: linear-gradient(135deg, {{ $notebook->cover_color ?? '#6d5ef9' }} 0%, #4f46e5 100%);">
                                    <span class="text-lg">📘</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-[15px] font-extrabold tracking-[-0.02em] text-slate-900">
                                        {{ $notebook->title }}
                                    </div>
                                    <div class="mt-1 text-[12px] font-medium text-slate-500">
                                        {{ $notebook->sources_count }} sources
                                    </div>
                                </div>
                                <div x-data="{ menu{{ $notebook->id }}Open: false }" class="relative">
                                    <button
                                        @click="menu{{ $notebook->id }}Open = !menu{{ $notebook->id }}Open"
                                        class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                        type="button"
                                    >
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="6" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="18" r="2"></circle>
                                        </svg>
                                    </button>
                                    <div
                                        x-show="menu{{ $notebook->id }}Open"
                                        @click.outside="menu{{ $notebook->id }}Open = false"
                                        x-transition
                                        class="absolute right-0 top-full z-50 mt-2 w-48 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.12)]"
                                    >
                                        <form method="POST" action="{{ route('featured-notebooks.add', $notebook) }}">
                                            @csrf
                                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:bg-blue-50">
                                                Add to Featured
                                            </button>
                                        </form>
                                        <a href="{{ route('notebooks.edit', $notebook) }}" class="flex items-center gap-3 border-t border-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            Edit Notebook
                                        </a>
                                        <form method="POST" action="{{ route('notebooks.destroy', $notebook) }}" class="border-t border-slate-100" onsubmit="return confirm('Are you sure you want to delete this notebook?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-[24px] border border-dashed border-slate-200 bg-white/70 px-8 py-14 text-center text-slate-400">
                                No admin notebooks available to feature.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="glass-panel overflow-hidden rounded-[30px] border border-white/70">
                <div class="border-b border-slate-100/80 p-6 sm:p-7">
                    <h2 class="text-[22px] font-extrabold tracking-[-0.03em] text-slate-900">Featured Notebook List</h2>
                    <p class="mt-2 text-sm text-slate-500">Drag and drop to reorder featured notebooks</p>
                </div>

                <div class="p-6 sm:p-7">
                    <div id="featured-list" class="space-y-3" x-data="{ order: [] }">
                        @forelse ($featuredNotebooks as $notebook)
                            <div
                                class="group flex items-center gap-4 rounded-[22px] border border-slate-100 bg-white p-4 shadow-[0_12px_28px_rgba(15,23,42,0.05)] transition-all duration-300 hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_18px_42px_rgba(15,23,42,0.08)]"
                                data-id="{{ $notebook->id }}"
                            >
                                <svg class="h-5 w-5 cursor-grab text-slate-400 active:cursor-grabbing" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-2xl text-white shadow-[0_14px_24px_rgba(79,70,229,0.18)]" style="background: linear-gradient(135deg, {{ $notebook->cover_color ?? '#6d5ef9' }} 0%, #4f46e5 100%);">
                                    <span>📘</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-[17px] font-extrabold tracking-[-0.02em] text-slate-900">
                                        {{ $notebook->title }}
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-3 text-[13px] text-slate-500">
                                        <span>{{ $notebook->created_at?->format('d M Y') }}</span>
                                        <span>• {{ $notebook->sources_count }} sources</span>
                                    </div>
                                </div>
                                <div x-data="{ featuredMenu{{ $notebook->id }}Open: false }" class="relative">
                                    <button
                                        @click="featuredMenu{{ $notebook->id }}Open = !featuredMenu{{ $notebook->id }}Open"
                                        class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                        type="button"
                                    >
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="6" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="18" r="2"></circle>
                                        </svg>
                                    </button>
                                    <div
                                        x-show="featuredMenu{{ $notebook->id }}Open"
                                        @click.outside="featuredMenu{{ $notebook->id }}Open = false"
                                        x-transition
                                        class="absolute right-0 top-full z-50 mt-2 w-48 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.12)]"
                                    >
                                        <a href="{{ route('notebooks.edit', $notebook) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                            Edit Notebook
                                        </a>
                                        <form method="POST" action="{{ route('featured-notebooks.remove', $notebook) }}" class="border-t border-slate-100">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50" onclick="return confirm('Are you sure you want to remove this notebook from featured list?')">
                                                Remove from Featured
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[24px] border border-dashed border-slate-200 bg-white/70 px-8 py-14 text-center text-slate-400">
                                No featured notebooks yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($featuredNotebooks->isNotEmpty())
                    <div class="border-t border-slate-100/80 p-6 sm:p-7">
                        <form id="reorder-form" method="POST" action="{{ route('featured-notebooks.reorder') }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="order" id="reorder-input">
                            <button type="submit" class="btn-premium px-6 py-3 text-sm">
                                Save Order
                            </button>
                        </form>
                    </div>
                @endif
            </section>

            <section class="glass-panel overflow-hidden rounded-[30px] border border-white/70">
                <div class="border-b border-slate-100/80 p-6 sm:p-7">
                    <h2 class="text-[22px] font-extrabold tracking-[-0.03em] text-slate-900">User Preview</h2>
                    <p class="mt-2 text-sm text-slate-500">How featured notebooks appear to users</p>
                </div>

                <div class="bg-gradient-to-br from-[#0c1635] via-[#111c4a] to-[#1b2f78] p-6 sm:p-7">
                    <div class="flex gap-4 overflow-x-auto pb-2">
                        @forelse ($featuredNotebooks as $notebook)
                            <div class="group relative min-w-[320px] overflow-hidden rounded-[24px] shadow-[0_26px_48px_rgba(2,8,23,0.25)] transition-transform duration-300 hover:-translate-y-1" style="height: 260px;">
                                <div class="absolute inset-0" style="background: linear-gradient(135deg, {{ $notebook->cover_color ?? '#6d5ef9' }} 0%, #0f172a 100%); filter: brightness(0.7);"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-[#061126]/95 via-[#061126]/45 to-transparent"></div>
                                <div class="absolute inset-0 opacity-60" style="background-image: radial-gradient(circle at top right, rgba(255,255,255,0.18) 0, transparent 24%), radial-gradient(circle at bottom left, rgba(255,255,255,0.1) 0, transparent 20%);"></div>

                                <div class="absolute bottom-0 left-0 right-0 p-6">
                                    <div class="mb-3 flex items-center gap-2 text-[13px] font-semibold text-white/70">
                                        <div class="flex h-5 w-5 items-center justify-center rounded bg-white text-[11px] font-extrabold text-slate-900">
                                            {{ strtoupper(substr($notebook->owner->name ?? 'N', 0, 1)) }}
                                        </div>
                                        {{ $notebook->owner->name ?? 'NoteGov' }}
                                    </div>
                                    <div class="text-[22px] font-extrabold leading-tight tracking-[-0.03em] text-white">
                                        {{ $notebook->title }}
                                    </div>
                                    <div class="mt-3 flex items-center justify-between gap-3 text-[13px] text-white/65">
                                        <div>
                                            {{ $notebook->created_at->format('d M Y') }} • {{ $notebook->sources_count }} sources
                                        </div>
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-900 shadow-[0_10px_24px_rgba(255,255,255,0.14)]">
                                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="w-full rounded-[24px] border border-white/10 bg-white/5 px-8 py-14 text-center text-white/55">
                                No featured notebooks to preview.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const featuredList = document.getElementById('featured-list');
            if (featuredList && featuredList.children.length > 0) {
                new Sortable(featuredList, {
                    animation: 150,
                    handle: 'svg',
                    onEnd: function() {
                        const items = Array.from(featuredList.children).filter(el => el.dataset.id);
                        const order = items.map(el => parseInt(el.dataset.id));
                        document.getElementById('reorder-input').value = JSON.stringify(order);
                    }
                });
            }

            document.getElementById('reorder-form')?.addEventListener('submit', function() {
                const items = Array.from(document.getElementById('featured-list').children).filter(el => el.dataset.id);
                const order = items.map(el => parseInt(el.dataset.id));
                document.getElementById('reorder-input').value = JSON.stringify(order);
            });
        });
    </script>
</x-app-layout>
