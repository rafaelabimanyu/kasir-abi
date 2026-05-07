@extends('layouts.master')

@section('title', 'Riwayat Transaksi')

@section('page-header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Riwayat Transaksi</h1>
        <p class="text-slate-500 mt-1">Kelola dan pantau seluruh transaksi toko.</p>
    </div>
</div>
@endsection

@section('content')
<div x-data="transactionManager()">
    {{-- Filter Card --}}
    <div class="mb-6 relative">
        <div class="absolute right-6 top-6 z-10" x-show="loading" style="display: none;">
            <i data-lucide="loader-2" class="w-5 h-5 text-brand-500 animate-spin"></i>
        </div>
        <x-card>
            <div class="flex flex-col md:flex-row items-end gap-4">
                <div class="flex-1 w-full">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Pencarian ID</label>
                    <input type="text" x-model="search" @input.debounce.300ms="fetchData" placeholder="Cari ID TRX..."
                        class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                <div class="w-full md:w-auto">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Mulai</label>
                    <input type="date" x-model="start_date" @change="fetchData"
                        class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                <div class="w-full md:w-auto">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Akhir</label>
                    <input type="date" x-model="end_date" @change="fetchData"
                        class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                @if(auth()->user()->isAdmin())
                <div class="w-full md:w-auto">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Kasir</label>
                    <select x-model="kasir_id" @change="fetchData" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                        <option value="">Semua Kasir</option>
                        @foreach($kasirs as $k)
                            <option value="{{ $k->id }}" {{ request('kasir_id') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="w-full md:w-auto">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Status</label>
                    <select x-model="status" @change="fetchData" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                        <option value="">Semua Status</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="void" {{ request('status') === 'void' ? 'selected' : '' }}>Void</option>
                    </select>
                </div>
                <div class="w-full md:w-auto">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Metode Bayar</label>
                    <select x-model="payment_method" @change="fetchData" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                        <option value="">Semua Metode</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="e-wallet" {{ request('payment_method') === 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>
                <button type="button" @click="resetFilters" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 bg-dark-600 hover:bg-dark-500 text-slate-300 rounded-xl text-sm font-medium transition-colors">
                    Reset
                </button>
            </div>
        </x-card>
    </div>

    {{-- Transactions Table --}}
    <x-card title="Daftar Transaksi" icon="receipt" :noPadding="true">
        <div id="data-container" :class="{'opacity-50 pointer-events-none': loading}" class="transition-opacity duration-200">
            @include('pages.transaksi._table')
        </div>
    </x-card>

    {{-- Detail Modal --}}
    <div x-show="detailModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" style="display: none;">
        <div x-show="detailModal" x-transition.opacity @click="detailModal = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="detailModal" x-transition.scale.origin.bottom class="relative bg-dark-800 rounded-2xl shadow-2xl w-full max-w-lg border border-dark-600/50 flex flex-col max-h-full">
            <div class="flex items-center justify-between px-6 py-4 border-b border-dark-600/50">
                <h3 class="text-lg font-bold text-white">Detail Transaksi <span x-text="'#TRX-'+String(detailData?.id).padStart(4,'0')"></span></h3>
                <button @click="detailModal = false" class="text-slate-400 hover:text-white cursor-pointer"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1">
                <template x-if="loadingDetail">
                    <div class="flex justify-center py-12"><i data-lucide="loader-2" class="w-8 h-8 text-brand-500 animate-spin"></i></div>
                </template>
                
                <template x-if="!loadingDetail && detailData">
                    <div class="space-y-4 text-sm text-slate-300">
                        <div class="flex justify-between pb-3 border-b border-dark-600/30">
                            <span>Kasir: <strong x-text="detailUser" class="text-white"></strong></span>
                            <span x-text="detailData.tanggal"></span>
                        </div>
                        
                        <div class="space-y-2">
                            <template x-for="item in detailItems" :key="item.id">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-white" x-text="item.product ? item.product.nama : 'Produk Dihapus'"></p>
                                        <p class="text-xs text-slate-500" x-text="item.qty + ' x Rp ' + new Intl.NumberFormat('id-ID').format(item.harga)"></p>
                                    </div>
                                    <span class="font-medium" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.qty * item.harga)"></span>
                                </div>
                            </template>
                        </div>
                        
                        <div class="pt-4 border-t border-dark-600/30 space-y-1">
                            <div class="flex justify-between"><span>Metode Bayar</span><span class="uppercase font-medium" x-text="detailData.payment_method"></span></div>
                            <div class="flex justify-between"><span>Total</span><strong class="text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(detailData.total)"></strong></div>
                            <div class="flex justify-between"><span>Bayar</span><span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(detailData.bayar)"></span></div>
                            <div class="flex justify-between text-emerald-400"><span>Kembalian</span><span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(detailData.kembalian)"></span></div>
                        </div>

                        <template x-if="detailData.status === 'void'">
                            <div class="mt-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs">
                                <p class="font-bold mb-1">Transaksi di-void</p>
                                <p>Oleh: <span x-text="detailData.void_by?.name || 'Admin'"></span> pada <span x-text="detailData.void_at"></span></p>
                                <p>Alasan: <span x-text="detailData.void_reason"></span></p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            
            <div class="p-4 border-t border-dark-600/50 bg-dark-800/50 rounded-b-2xl flex justify-end">
                <template x-if="detailData">
                    <x-button variant="primary" icon="printer" @click="window.open(`/transaksi/${detailData.id}/print`, '_blank')">Cetak Struk</x-button>
                </template>
            </div>
        </div>
    </div>

    {{-- Void Modal --}}
    @if(auth()->user()->isAdmin())
    <div x-show="voidModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" style="display: none;">
        <div x-show="voidModal" x-transition.opacity @click="voidModal = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="voidModal" x-transition.scale.origin.bottom class="relative bg-dark-800 rounded-2xl shadow-2xl w-full max-w-md border border-dark-600/50">
            <form :action="`/transaksi/${voidId}/void`" method="POST" class="p-6">
                @csrf
                <div class="flex items-center gap-4 mb-4 text-red-400">
                    <div class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Void Transaksi</h3>
                        <p class="text-sm text-slate-400">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                
                <p class="text-sm text-slate-300 mb-4">
                    Stok produk akan dikembalikan dan transaksi akan ditandai sebagai VOID.
                </p>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Alasan Void <span class="text-red-400">*</span></label>
                    <textarea name="void_reason" required rows="3" placeholder="Contoh: Kesalahan input produk..."
                              class="w-full bg-dark-900 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-red-500/50 transition-all"></textarea>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" @click="voidModal = false" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-dark-600 transition-colors cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium bg-red-600 hover:bg-red-500 text-white shadow-glow cursor-pointer">Konfirmasi Void</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function transactionManager() {
    return {
        search: '{{ request("search") }}',
        start_date: '{{ request("start_date") }}',
        end_date: '{{ request("end_date") }}',
        status: '{{ request("status") }}',
        kasir_id: '{{ request("kasir_id") }}',
        payment_method: '{{ request("payment_method") }}',
        loading: false,
        abortController: null,
        
        detailModal: false,
        voidModal: false,
        voidId: null,
        loadingDetail: false,
        detailData: null,
        detailItems: [],
        detailUser: '',
        
        init() {
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
                    if (this.start_date) params.append('start_date', this.start_date);
                    if (this.end_date) params.append('end_date', this.end_date);
                    if (this.status) params.append('status', this.status);
                    if (this.kasir_id) params.append('kasir_id', this.kasir_id);
                    if (this.payment_method) params.append('payment_method', this.payment_method);
                    fetchUrl = `{{ route('transaksi.history') }}?${params.toString()}`;
                    window.history.pushState({}, '', fetchUrl);
                }
                
                const response = await fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.abortController.signal
                });
                
                const html = await response.text();
                document.getElementById('data-container').innerHTML = html;
                
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
        },
        
        resetFilters() {
            this.search = '';
            this.start_date = '';
            this.end_date = '';
            this.status = '';
            this.kasir_id = '';
            this.payment_method = '';
            this.fetchData();
        },
        
        showDetail(id) {
            this.detailModal = true;
            this.loadingDetail = true;
            this.detailData = null;
            
            fetch(`/transaksi/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.detailData = data.transaction;
                        this.detailItems = data.items;
                        this.detailUser = data.user;
                    }
                    this.loadingDetail = false;
                })
                .catch(() => {
                    this.loadingDetail = false;
                    alert('Gagal mengambil data.');
                });
        },
        
        openVoidModal(id) {
            this.voidId = id;
            this.voidModal = true;
        }
    }
}
</script>
@endpush
