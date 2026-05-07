@extends('layouts.master')

@section('title', 'Produk')

@section('page-header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Produk</h1>
        <p class="text-slate-500 mt-1">Kelola semua produk dan inventaris toko Anda.</p>
    </div>
    <x-button variant="primary" icon="plus" size="sm" href="{{ route('produk.create') }}">Tambah Produk</x-button>
</div>
@endsection

@section('content')
<div x-data="liveSearch('{{ route('produk.index') }}')">
    <x-card :noPadding="true">
        {{-- Filters --}}
        <div class="px-5 py-4 border-b border-dark-600/40 flex flex-col sm:flex-row gap-3 relative">
            <div class="absolute right-6 top-6" x-show="loading" style="display: none;">
                <i data-lucide="loader-2" class="w-5 h-5 text-brand-500 animate-spin"></i>
            </div>
            <div class="flex-1 flex items-center gap-2 bg-dark-800 rounded-xl px-3 py-2 border border-dark-600/50 focus-within:border-brand-500/50 transition-all">
                <i data-lucide="search" class="w-4 h-4 text-slate-500"></i>
                <input type="text" x-model="search" @input.debounce.300ms="fetchData" placeholder="Cari produk..." class="bg-transparent text-sm text-slate-300 placeholder-slate-500 outline-none w-full">
            </div>
            <select x-model="kategori" @change="fetchData" class="bg-dark-800 border border-dark-600/50 rounded-xl px-3 py-2 text-sm text-slate-300 outline-none">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('kategori') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                @endforeach
            </select>
            <select x-model="status" @change="fetchData" class="bg-dark-800 border border-dark-600/50 rounded-xl px-3 py-2 text-sm text-slate-300 outline-none">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Rendah</option>
                <option value="empty" {{ request('status') === 'empty' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>

        <div id="data-container" :class="{'opacity-50 pointer-events-none': loading}" class="transition-opacity duration-200">
            @include('pages.produk._table')
        </div>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
function liveSearch(baseUrl) {
    return {
        search: '{{ request("search") }}',
        kategori: '{{ request("kategori") }}',
        status: '{{ request("status") }}',
        loading: false,
        abortController: null,
        
        init() {
            // Handle pagination clicks via AJAX
            document.getElementById('data-container').addEventListener('click', (e) => {
                let link = e.target.closest('.ajax-pagination a');
                if (link) {
                    e.preventDefault();
                    this.fetchData(link.href);
                }
            });
        },
        
        async fetchData(url = null) {
            this.loading = true;
            
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            
            try {
                let fetchUrl = url instanceof Event ? null : url;
                if (!fetchUrl) {
                    const params = new URLSearchParams();
                    if (this.search) params.append('search', this.search);
                    if (this.kategori) params.append('kategori', this.kategori);
                    if (this.status) params.append('status', this.status);
                    fetchUrl = `${baseUrl}?${params.toString()}`;
                    window.history.pushState({}, '', fetchUrl);
                }
                
                const response = await fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.abortController.signal
                });
                
                const html = await response.text();
                document.getElementById('data-container').innerHTML = html;
                
                // Highlight keyword
                if (this.search && this.search.length >= 2) {
                    this.highlightText(this.search);
                }
                
                this.$nextTick(() => { lucide.createIcons(); });
            } catch (error) {
                if (error.name !== 'AbortError') console.error(error);
            } finally {
                this.loading = false;
            }
        },
        
        highlightText(keyword) {
            const regex = new RegExp(`(${keyword})`, 'gi');
            document.querySelectorAll('#data-container .highlight-target').forEach(el => {
                const originalText = el.textContent;
                el.innerHTML = originalText.replace(regex, '<mark class="bg-brand-500/30 text-brand-400 rounded px-1">$1</mark>');
            });
        }
    }
}
</script>
@endpush
