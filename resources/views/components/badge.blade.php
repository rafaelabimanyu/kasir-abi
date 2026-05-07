{{--
    Badge Component

    @props:
    - $color (optional) - 'brand', 'emerald', 'amber', 'red', 'slate' — default: 'brand'
--}}

@props([
    'color' => 'brand',
])

@php
    $colors = [
        'brand'   => 'bg-brand-500/10 text-brand-400 ring-brand-500/20',
        'emerald' => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
        'amber'   => 'bg-amber-500/10 text-amber-400 ring-amber-500/20',
        'red'     => 'bg-red-500/10 text-red-400 ring-red-500/20',
        'slate'   => 'bg-slate-500/10 text-slate-400 ring-slate-500/20',
        'purple'  => 'bg-purple-500/10 text-purple-400 ring-purple-500/20',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-lg ring-1 ' . ($colors[$color] ?? $colors['brand'])]) }}>
    {{ $slot }}
</span>
