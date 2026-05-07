{{--
    Stat Card Component

    @props:
    - $title - Stat label
    - $value - Stat value
    - $icon - Lucide icon name
    - $trend (optional) - 'up' or 'down'
    - $trendValue (optional) - e.g. '+12%'
    - $color (optional) - 'brand', 'emerald', 'amber', 'red' — default: 'brand'
--}}

@props([
    'title',
    'value',
    'icon',
    'trend' => null,
    'trendValue' => null,
    'color' => 'brand',
    'url' => null,
])

@php
    $colors = [
        'brand'   => ['bg' => 'bg-brand-500/10', 'text' => 'text-brand-400', 'ring' => 'ring-brand-500/20'],
        'emerald' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'ring' => 'ring-emerald-500/20'],
        'amber'   => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'ring' => 'ring-amber-500/20'],
        'red'     => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'ring' => 'ring-red-500/20'],
        'purple'  => ['bg' => 'bg-purple-500/10', 'text' => 'text-purple-400', 'ring' => 'ring-purple-500/20'],
    ];
    $c = $colors[$color] ?? $colors['brand'];
@endphp

@if($url)
<a href="{{ $url }}" class="block bg-dark-700 border border-dark-600/50 rounded-2xl p-5 shadow-soft hover:shadow-card transition-shadow duration-300 group cursor-pointer">
@else
<div class="bg-dark-700 border border-dark-600/50 rounded-2xl p-5 shadow-soft hover:shadow-card transition-shadow duration-300 group">
@endif
    <div class="flex items-start justify-between mb-4">
        <div class="w-11 h-11 {{ $c['bg'] }} rounded-xl flex items-center justify-center ring-1 {{ $c['ring'] }} group-hover:scale-110 transition-transform duration-300">
            <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $c['text'] }}"></i>
        </div>
        @if($trend)
            <div class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium {{ $trend === 'up' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                <i data-lucide="{{ $trend === 'up' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                {{ $trendValue }}
            </div>
        @endif
    </div>
    <p class="text-2xl font-bold text-white tracking-tight">{{ $value }}</p>
    <p class="text-sm text-slate-500 mt-1">{{ $title }}</p>
@if($url)
</a>
@else
</div>
@endif
