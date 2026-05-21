<x-app-layout>
    <div class="space-y-8">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-purple-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.25em] text-purple-700 mb-4">
                FEATURED NOTEBOOKS
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Manage Featured Notebooks</h1>
            <p class="text-gray-500 mt-2">Control which notebooks appear in the Featured Notebooks section on the user dashboard.</p>
        </div>

        @if (session('status'))
            <div class="glass-panel border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 rounded-2xl">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-8">
            <section class="glass-panel overflow-hidden rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Add Notebook to Featured</h2>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="relative flex-1 max-w-md">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <form method="GET" action="{{ route('featured-notebooks.index') }}" class="w-full">
                                <input type="text" name="search" placeholder="Search notebooks..." class="search-input" value="{{ $search }}" style="width: 100%;">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse ($allNotebooks as $notebook)
                            <div class="border border-gray-100 bg-white rounded-2xl p-4 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: {{ $notebook->cover_color ?? '#6366f1' }};">
                                    📓
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $notebook->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $notebook->sources_count }} sources</div>
                                </div>
                                <div x-data="{ menu{{ $notebook->id }}Open: false }" class="relative">
                                    <button @click="menu{{ $notebook->id }}Open = !menu{{ $notebook->id }}Open" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="6" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="18" r="2"></circle>
                                        </svg>
                                    </button>
                                    <div x-show="menu{{ $notebook->id }}Open" @click.outside="menu{{ $notebook->id }}Open = false" x-transition class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-200 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] z-50">
                                        <form method="POST" action="{{ route('featured-notebooks.add', $notebook) }}">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 first:rounded-t-2xl">
                                                Add to Featured
                                            </button>
                                        </form>
                                        <a href="{{ route('notebooks.edit', $notebook) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 border-t border-gray-100">
                                            Edit Notebook
                                        </a>
                                        <form method="POST" action="{{ route('notebooks.destroy', $notebook) }}" class="border-t border-gray-100" onsubmit="return confirm('Are you sure you want to delete this notebook?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 last:rounded-b-2xl">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <div class="text-gray-400">No admin notebooks available to feature.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="glass-panel overflow-hidden rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Featured Notebook List</h2>
                    <p class="text-sm text-gray-500">Drag and drop to reorder featured notebooks</p>
                </div>
                <div class="p-6">
                    <div id="featured-list" class="space-y-3" x-data="{ order: []">
                        @forelse ($featuredNotebooks as $notebook)
                            <div class="border border-gray-100 bg-white rounded-2xl p-5 flex items-center gap-4" data-id="{{ $notebook->id }}">
                                <svg class="w-5 h-5 text-gray-400 cursor-grab active:cursor-grabbing" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl" style="background: {{ $notebook->cover_color ?? '#6366f1' }};">
                                    📓
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-lg font-bold text-gray-900">{{ $notebook->title }}</div>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-sm text-gray-500">{{ $notebook->created_at?->format('d M Y') }}</span>
                                        <span class="text-sm text-gray-500">• {{ $notebook->sources_count }} sources</span>
                                    </div>
                                </div>
                                <div x-data="{ featuredMenu{{ $notebook->id }}Open: false }" class="relative">
                                    <button @click="featuredMenu{{ $notebook->id }}Open = !featuredMenu{{ $notebook->id }}Open" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="6" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="18" r="2"></circle>
                                        </svg>
                                    </button>
                                    <div x-show="featuredMenu{{ $notebook->id }}Open" @click.outside="featuredMenu{{ $notebook->id }}Open = false" x-transition class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-200 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] z-50">
                                        <a href="{{ route('notebooks.edit', $notebook) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 first:rounded-t-2xl">
                                            Edit Notebook
                                        </a>
                                        <form method="POST" action="{{ route('featured-notebooks.remove', $notebook) }}" class="border-t border-gray-100">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 last:rounded-b-2xl" onclick="return confirm('Are you sure you want to remove this notebook from featured list?')">
                                                Remove from Featured
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="text-gray-400">No featured notebooks yet.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if ($featuredNotebooks->isNotEmpty())
                    <div class="p-6 border-t border-gray-100">
                        <form id="reorder-form" method="POST" action="{{ route('featured-notebooks.reorder') }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="order" id="reorder-input">
                            <button type="submit" class="btn-premium">
                                Save Order
                            </button>
                        </form>
                    </div>
                @endif
            </section>

            <section class="glass-panel overflow-hidden rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">User Preview</h2>
                    <p class="text-sm text-gray-500">How featured notebooks appear to users</p>
                </div>
                <div class="p-6" style="background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);">
                    <div class="flex gap-4 overflow-x-auto pb-4">
                        @forelse ($featuredNotebooks as $notebook)
                            <div style="min-width: 320px; position: relative; border-radius: 20px; overflow: hidden; cursor: pointer; transition: all 0.3s ease; height: 260px;">
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, {{ $notebook->cover_color ?? '#6366f1' }} 0%, #0f0f23 100%); filter: brightness(0.5); background-size: cover; background-position: center;"></div>
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(15,15,35,0.95) 0%, rgba(15,15,35,0.4) 50%, transparent 100%);"></div>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.7);">
                                        <div style="width: 20px; height: 20px; border-radius: 4px; background: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #1a1a2e;">
                                            {{ strtoupper(substr($notebook->owner->name ?? 'N', 0, 1)) }}
                                        </div>
                                        {{ $notebook->owner->name ?? 'NoteGov' }}
                                    </div>
                                    <div style="font-family: 'Space Grotesk', sans-serif; font-size: 20px; font-weight: 700; margin-bottom: 12px; color: white;">
                                        {{ $notebook->title }}
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="font-size: 13px; color: rgba(255,255,255,0.6);">
                                            {{ $notebook->created_at->format('d M Y') }} • {{ $notebook->sources_count }} sources
                                        </div>
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; color: #1a1a2e;">
                                            <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="w-full text-center py-12">
                                <div class="text-white/50">No featured notebooks to preview.</div>
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

            document.getElementById('reorder-form')?.addEventListener('submit', function(e) {
                const items = Array.from(document.getElementById('featured-list').children).filter(el => el.dataset.id);
                const order = items.map(el => parseInt(el.dataset.id));
                document.getElementById('reorder-input').value = JSON.stringify(order);
            });
        });
    </script>
</x-app-layout>
