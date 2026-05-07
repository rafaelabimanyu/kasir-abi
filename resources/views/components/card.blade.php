{{--
    Card Component
    
    @props:
    - $title (optional) - Card title
    - $subtitle (optional) - Card subtitle
    - $icon (optional) - Lucide icon name
    - $padding (optional) - Custom padding class, default: 'p-5 md:p-6'
    - $noPadding (optional) - Remove body padding
--}}

@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'padding' => 'p-5 md:p-6',
    'noPadding' => false,
])

<div {{ $attributes->merge(['class' => 'bg-dark-700 border border-dark-600/50 rounded-2xl shadow-soft overflow-hidden']) }}>
    @if($title)
        <div class="flex items-center justify-between px-5 md:px-6 py-4 border-b border-dark-600/40">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="w-9 h-9 bg-brand-500/10 rounded-xl flex items-center justify-center">
                        <i data-lucide="{{ $icon }}" class="w-4.5 h-4.5 text-brand-400"></i>
                    </div>
                @endif
                <div>
                    <h3 class="text-base font-semibold text-white">{{ $title }}</h3>
                    @if($subtitle)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @isset($action)
                <div>{{ $action }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $noPadding ? '' : $padding }}">
        {{ $slot }}
    </div>
</div>
