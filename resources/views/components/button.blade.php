{{--
    Button Component

    @props:
    - $variant (optional) - 'primary', 'secondary', 'danger', 'ghost', 'success' — default: 'primary'
    - $size (optional) - 'sm', 'md', 'lg' — default: 'md'
    - $icon (optional) - Lucide icon name
    - $href (optional) - If set, renders as <a> tag
    - $type (optional) - Button type, default: 'button'
--}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'href' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-800 cursor-pointer active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed disabled:active:scale-100 min-h-[44px]';

    $variants = [
        'primary'   => 'bg-brand-600 hover:bg-brand-500 text-white focus:ring-brand-500 hover:shadow-glow',
        'secondary' => 'bg-dark-600 hover:bg-dark-500 text-slate-300 hover:text-white border border-dark-500 focus:ring-dark-400',
        'danger'    => 'bg-red-600/10 hover:bg-red-600/20 text-red-400 border border-red-500/20 focus:ring-red-500',
        'ghost'     => 'hover:bg-dark-600 text-slate-400 hover:text-white focus:ring-dark-400',
        'success'   => 'bg-emerald-600 hover:bg-emerald-500 text-white focus:ring-emerald-500',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        @endif
        {{ $slot }}
    </button>
@endif
