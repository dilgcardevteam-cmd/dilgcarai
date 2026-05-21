@props(['notebook'])

<a href="{{ route('notebooks.show', $notebook) }}" class="group block overflow-hidden transition-all duration-300 hover:-translate-y-1">
    <div class="dashboard-notebook-card p-6 sm:p-7">
        <div class="absolute inset-0 opacity-100">
            <div class="absolute top-5 right-6 h-24 w-24 rounded-full bg-white/12 blur-2xl"></div>
            <div class="absolute bottom-5 left-5 h-28 w-28 rounded-full bg-white/10 blur-2xl"></div>
        </div>

        <div class="relative z-10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.34em] text-white/70">
                        {{ $notebook->category?->name ?: 'General notebook' }}
                    </p>
                    <h3 class="mt-3 text-[28px] font-extrabold leading-[1.06] tracking-[-0.04em] text-white">
                        {{ $notebook->title }}
                    </h3>
                </div>
                <span class="inline-flex items-center rounded-full border border-white/20 bg-white/12 px-3.5 py-1.5 text-[11px] font-semibold text-white/90 backdrop-blur-sm">
                    {{ str($notebook->visibility)->headline() }}
                </span>
            </div>

            <p class="mt-4 max-w-[92%] text-sm leading-7 text-white/84">
                {{ $notebook->summary ?: \Illuminate\Support\Str::limit(strip_tags($notebook->description ?: 'AI-ready governance notebook for policies, projects, and local government operations.'), 160) }}
            </p>

            <div class="mt-8 border-t border-white/12 pt-4">
                <div class="flex items-center justify-between gap-4 text-xs text-white/82">
                    <div class="flex items-center gap-5">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-medium">{{ $notebook->sources_count ?? $notebook->sources()->count() }} sources</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="font-medium">{{ $notebook->chats_count ?? $notebook->chats()->count() }} chats</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-white/74">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="whitespace-nowrap text-[11px] font-medium">
                            Updated {{ optional($notebook->last_activity_at ?? $notebook->updated_at)->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
