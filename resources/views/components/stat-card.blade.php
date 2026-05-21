@props([
    'label',
    'value',
    'hint' => null,
    'accent' => 'sky',
])

@php
    $accentMap = [
        'sky' => 'from-sky-400/25 to-cyan-300/10 text-sky-100',
        'amber' => 'from-amber-300/25 to-orange-300/10 text-amber-100',
        'emerald' => 'from-emerald-300/25 to-teal-300/10 text-emerald-100',
        'rose' => 'from-rose-300/20 to-fuchsia-300/10 text-rose-100',
    ];
@endphp

<div class="panel overflow-hidden p-5">
    <div class="rounded-[22px] bg-gradient-to-br {{ $accentMap[$accent] ?? $accentMap['sky'] }} p-5">
        <p class="text-xs uppercase tracking-[0.24em] text-white/65">{{ $label }}</p>
        <p class="mt-4 text-3xl font-bold text-white">{{ $value }}</p>
        @if ($hint)
            <p class="mt-2 text-sm text-white/70">{{ $hint }}</p>
        @endif
    </div>
</div>
