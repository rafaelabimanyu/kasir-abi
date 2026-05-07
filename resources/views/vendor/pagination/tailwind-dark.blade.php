@if ($paginator->hasPages())
    <nav class="flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }} produk
        </p>
        <div class="flex gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm rounded-lg bg-dark-600 text-slate-600 cursor-not-allowed">←</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm rounded-lg bg-dark-600 text-slate-400 hover:text-white hover:bg-dark-500 transition-colors">←</a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-sm rounded-lg bg-dark-600 text-slate-500">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 text-sm rounded-lg bg-brand-600 text-white font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-sm rounded-lg bg-dark-600 text-slate-400 hover:text-white hover:bg-dark-500 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm rounded-lg bg-dark-600 text-slate-400 hover:text-white hover:bg-dark-500 transition-colors">→</a>
            @else
                <span class="px-3 py-1.5 text-sm rounded-lg bg-dark-600 text-slate-600 cursor-not-allowed">→</span>
            @endif
        </div>
    </nav>
@endif
