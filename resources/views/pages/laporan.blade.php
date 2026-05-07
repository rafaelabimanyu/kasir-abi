@extends('layouts.master')

@section('title', 'Laporan')

@section('page-header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Laporan Penjualan</h1>
        <p class="text-slate-500 mt-1">Analisis penjualan dan performa bisnis Anda.</p>
    </div>
    <div class="flex items-center gap-2">
        <x-button variant="secondary" icon="file-text" size="sm" href="{{ route('laporan.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}">Export PDF</x-button>
        <x-button variant="secondary" icon="table" size="sm" href="{{ route('laporan.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}">Export Excel</x-button>
    </div>
</div>
@endsection

@section('content')
<div x-data="laporanManager()">
    {{-- Date Filter --}}
    <div class="mb-6 relative">
        <div class="absolute right-6 top-6 z-10" x-show="loading" style="display: none;">
            <i data-lucide="loader-2" class="w-5 h-5 text-brand-500 animate-spin"></i>
        </div>
        <x-card>
            <div class="flex flex-col sm:flex-row items-end gap-4">
                <div class="flex-1 w-full">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Mulai</label>
                    <input type="date" x-model="start_date" @change="fetchData"
                        class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Tanggal Akhir</label>
                    <input type="date" x-model="end_date" @change="fetchData"
                        class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a :href="`{{ route('laporan.export.pdf') }}?start_date=${start_date}&end_date=${end_date}`" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center min-h-[44px] px-4 py-2 bg-dark-600 hover:bg-dark-500 text-white rounded-xl text-sm font-medium transition-colors" title="Export PDF">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </a>
                    <a :href="`{{ route('laporan.export.excel') }}?start_date=${start_date}&end_date=${end_date}`" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center min-h-[44px] px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl shadow-glow text-sm font-medium transition-colors" title="Export Excel">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    <div id="data-container" :class="{'opacity-50 pointer-events-none': loading}" class="transition-opacity duration-200">
        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6" id="stats-container">
            <x-stat-card title="Total Pendapatan" :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')" icon="wallet" color="brand" />
            <x-stat-card title="Total Transaksi" :value="(string) $totalTransactions" icon="receipt" color="emerald" />
            <x-stat-card title="Produk Terjual" :value="(string) $totalItemsSold" icon="package" color="amber" />
            <x-stat-card title="Rata-rata / Transaksi" :value="'Rp ' . number_format($avgTransaction, 0, ',', '.')" icon="calculator" color="purple" />
        </div>

        {{-- Daily Revenue Chart --}}
        <div class="mb-6" id="chart-wrapper">
            <x-card title="Pendapatan Harian" subtitle="Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}" icon="trending-up">
                @if($dailyChart->isEmpty())
                    <div class="flex flex-col items-center py-12 text-slate-500 animate-fade-in">
                        <div class="w-16 h-16 bg-dark-600/50 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="bar-chart-3" class="w-8 h-8 text-slate-400 opacity-50"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-300">Grafik tidak tersedia</p>
                        <p class="text-xs mt-1.5 text-center text-slate-500">Belum ada data pendapatan pada periode yang dipilih.</p>
                    </div>
                @else
                    <div id="laporan-chart" data-chart='@json($dailyChart)'></div>
                @endif
            </x-card>
        </div>

        {{-- Transactions Table --}}
        <x-card title="Riwayat Transaksi" icon="list" :noPadding="true">
            <div id="table-wrapper">
                <x-table :headers="['ID Transaksi', 'Tanggal', 'Detail Produk', 'Qty', 'Total', 'Bayar', 'Kembalian']">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-dark-600/30 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-mono font-medium text-brand-400">
                                #TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-400">
                                {{ $trx->tanggal->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="space-y-0.5">
                                    @foreach($trx->items as $item)
                                        <p class="text-xs text-slate-400">
                                            <span class="text-slate-300">{{ $item->product->nama ?? '—' }}</span>
                                            <span class="text-slate-500">× {{ $item->qty }}</span>
                                        </p>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-400">
                                {{ $trx->items->sum('qty') }} item
                            </td>
                            <td class="px-5 py-3.5 text-sm font-medium text-white">
                                Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-300">
                                Rp {{ number_format($trx->bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-emerald-400">
                                Rp {{ number_format($trx->kembalian, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center animate-fade-in">
                                <div class="flex flex-col items-center text-slate-500">
                                    <div class="w-16 h-16 bg-dark-600/50 rounded-full flex items-center justify-center mb-4">
                                        <i data-lucide="receipt" class="w-8 h-8 text-slate-400 opacity-50"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-300">Tidak ada riwayat transaksi</p>
                                    <p class="text-xs mt-1.5 text-center text-slate-500 max-w-[250px]">Transaksi yang dilakukan pada periode ini akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-table>

                @if($transactions->hasPages())
                    <div class="px-5 py-4 border-t border-dark-600/40 ajax-pagination">
                        {{ $transactions->links('vendor.pagination.tailwind-dark') }}
                    </div>
                @endif
            </div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
function laporanManager() {
    return {
        start_date: '{{ $startDate }}',
        end_date: '{{ $endDate }}',
        loading: false,
        abortController: null,
        
        init() {
            document.getElementById('data-container').addEventListener('click', (e) => {
                let link = e.target.closest('.ajax-pagination a');
                if (link) {
                    e.preventDefault();
                    this.fetchData(link.href);
                }
            });
            this.initChart();
        },
        
        async fetchData(url = null) {
            this.loading = true;
            if (this.abortController) this.abortController.abort();
            this.abortController = new AbortController();
            
            try {
                let fetchUrl = url instanceof Event ? null : url;
                if (!fetchUrl) {
                    const params = new URLSearchParams();
                    if (this.start_date) params.append('start_date', this.start_date);
                    if (this.end_date) params.append('end_date', this.end_date);
                    fetchUrl = `{{ route('laporan') }}?${params.toString()}`;
                    window.history.pushState({}, '', fetchUrl);
                }
                
                const response = await fetch(fetchUrl, {
                    signal: this.abortController.signal
                });
                
                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                
                document.getElementById('stats-container').innerHTML = doc.getElementById('stats-container').innerHTML;
                document.getElementById('chart-wrapper').innerHTML = doc.getElementById('chart-wrapper').innerHTML;
                document.getElementById('table-wrapper').innerHTML = doc.getElementById('table-wrapper').innerHTML;
                
                this.initChart();
                this.$nextTick(() => { lucide.createIcons(); });
            } catch (error) {
                if (error.name !== 'AbortError') console.error(error);
            } finally {
                this.loading = false;
            }
        },
        
        initChart() {
            const chartEl = document.querySelector('#laporan-chart');
            if (!chartEl) return;
            
            let dailyData = [];
            try {
                dailyData = JSON.parse(chartEl.dataset.chart);
            } catch(e) {}
            
            if (window.laporanChartInstance) {
                window.laporanChartInstance.destroy();
            }

            window.laporanChartInstance = new ApexCharts(chartEl, {
                series: [{
                    name: 'Pendapatan',
                    type: 'area',
                    data: dailyData.map(d => d.revenue)
                }, {
                    name: 'Transaksi',
                    type: 'column',
                    data: dailyData.map(d => d.count)
                }],
                chart: {
                    height: 320,
                    background: 'transparent',
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                colors: ['#6366f1', '#34d399'],
                fill: {
                    type: ['gradient', 'solid'],
                    gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] },
                    opacity: [1, 0.85],
                },
                stroke: { curve: 'smooth', width: [2.5, 0] },
                plotOptions: {
                    bar: { borderRadius: 4, columnWidth: '40%' }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: dailyData.map(d => {
                        const dt = new Date(d.date);
                        return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
                    }),
                    labels: { style: { colors: '#64748b', fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: [{
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' },
                        formatter: v => v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v >= 1000 ? Math.round(v/1000)+'rb' : v
                    }
                }, {
                    opposite: true,
                    labels: { style: { colors: '#64748b', fontSize: '11px' } }
                }],
                grid: { borderColor: '#242433', strokeDashArray: 4 },
                legend: {
                    position: 'top', horizontalAlign: 'right',
                    labels: { colors: '#94a3b8' }, fontSize: '12px',
                    markers: { size: 5 },
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: (val, { seriesIndex }) =>
                            seriesIndex === 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(val) : val + ' trx'
                    }
                },
            });
            window.laporanChartInstance.render();
        }
    }
}
</script>
@endpush
