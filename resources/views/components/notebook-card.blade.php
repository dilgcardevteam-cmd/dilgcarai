@props(['notebook'])

<a href="{{ route('notebooks.show', $notebook) }}" class="group block overflow-hidden transition-all duration-300 hover:-translate-y-1">
    <div class="rounded-[22px] p-6 text-white relative overflow-hidden" style="background: linear-gradient(135deg, {{ $notebook->cover_color ?? '#1e40af' }} 0%, {{ $notebook->cover_color ?? '#0f172a' }} 100%);">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-400/30 to-transparent rounded-full blur-3xl transform translate-x-20 -translate-y-20"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-blue-300/20 to-transparent rounded-full blur-3xl transform -translate-x-20 translate-y-20"></div>
            <svg class="absolute bottom-0 right-0 w-32 h-32 text-white/10" viewBox="0 0 100 100" fill="none">
                <circle cx="80" cy="80" r="30" stroke="currentColor" stroke-width="1"/>
                <circle cx="50" cy="50" r="20" stroke="currentColor" stroke-width="1"/>
            </svg>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-white/60 font-semibold">{{ $notebook->category?->name ?: 'General notebook' }}</p>
                    <h3 class="mt-3 text-xl font-bold leading-tight">{{ $notebook->title }}</h3>
                </div>
                <span class="inline-flex items-center rounded-xl bg-white/10 backdrop-blur-sm border border-white/15 px-3 py-1.5 text-[11px] font-semibold text-white/90">{{ str($notebook->visibility)->headline() }}</span>
            </div>
            <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-white/80">{{ $notebook->summary ?: \Illuminate\Support\Str::limit(strip_tags($notebook->description ?: 'AI-ready governance notebook for policies, projects, and local government operations.'), 160) }}</p>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm">
        <div class="flex items-center gap-4 text-gray-600">
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-xs font-medium">{{ $notebook->sources_count ?? $notebook->sources()->count() }} sources</span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span class="text-xs font-medium">{{ $notebook->chats_count ?? $notebook->chats()->count() }} chats</span>
            </div>
        </div>
        <div class="flex items-center gap-1.5 text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-xs">Updated {{ optional($notebook->last_activity_at ?? $notebook->updated_at)->diffForHumans() }}</span>
        </div>
    </div>
</a>
