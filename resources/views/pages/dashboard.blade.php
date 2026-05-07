@extends('layouts.master')

@section('title', 'Dashboard')

@section('page-header')
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Dashboard</h1>
        <div class="mt-2 text-slate-400">
            <p class="font-semibold text-white">{{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}</p>
            <p class="text-sm mt-0.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 inline-block mr-1"></i>{{ \App\Models\Setting::get('store_address', 'Jl. Contoh No. 123, Jakarta') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <x-button variant="primary" icon="plus" size="sm" href="{{ route('pos') }}">Transaksi Baru</x-button>
    </div>
</div>
@endsection

@section('content')
<div x-data="dashboardManager()">
    <div class="flex justify-between items-center mb-5">
        <h2 class="text-lg font-semibold text-white">Ringkasan</h2>
        <div class="inline-flex bg-dark-700 p-1 rounded-xl border border-dark-600/50">
            <button @click="setFilter('today')" :class="filter === 'today' ? 'bg-brand-600 text-white shadow-glow' : 'text-slate-400 hover:text-slate-300'" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors cursor-pointer">Hari Ini</button>
            <button @click="setFilter('7days')" :class="filter === '7days' ? 'bg-brand-600 text-white shadow-glow' : 'text-slate-400 hover:text-slate-300'" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors cursor-pointer">7 Hari</button>
            <button @click="setFilter('30days')" :class="filter === '30days' ? 'bg-brand-600 text-white shadow-glow' : 'text-slate-400 hover:text-slate-300'" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors cursor-pointer">30 Hari</button>
        </div>
    </div>
    
    <div id="dashboard-content" class="relative min-h-[400px]">
        <div x-show="loading" class="absolute inset-0 bg-dark-800/50 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl transition-opacity" style="display: none;">
            <i data-lucide="loader-2" class="w-8 h-8 text-brand-500 animate-spin"></i>
        </div>
        @include('pages.dashboard._content')
    </div>
</div>
@endsection

@push('scripts')
{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
function dashboardManager() {
    return {
        filter: '{{ request('filter', 'today') }}',
        loading: false,
        chart: null,
        
        init() {
            this.initChart();
            @if(isset($activeShift) && $activeShift)
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Shift masih berjalan. Jangan lupa untuk menutup shift jika sudah selesai.', type: 'warning' } }));
                }, 500);
            @endif
        },
        
        initChart() {
            if (this.chart) {
                this.chart.destroy();
            }
            
            let data = [];
            const container = document.getElementById('chart-data-container');
            if (container) {
                data = JSON.parse(container.getAttribute('data-chart'));
            } else {
                data = @json($chartData);
            }
            
            const options = {
                series: [{
                    name: 'Pendapatan',
                    data: data.map(d => d.revenue)
                }, {
                    name: 'Transaksi',
                    data: data.map(d => d.count)
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    background: 'transparent',
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                colors: ['#6366f1', '#34d399'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                stroke: { curve: 'smooth', width: 2.5 },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: data.map(d => d.date),
                    labels: { style: { colors: '#64748b', fontSize: '12px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: [{
                    labels: {
                        style: { colors: '#64748b', fontSize: '12px' },
                        formatter: v => v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v >= 1000 ? (v/1000)+'rb' : v
                    }
                }, {
                    opposite: true,
                    labels: { style: { colors: '#64748b', fontSize: '12px' } }
                }],
                grid: {
                    borderColor: '#242433',
                    strokeDashArray: 4,
                    padding: { left: 8, right: 8 }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: '#94a3b8' },
                    fontSize: '12px',
                    markers: { size: 5, shape: 'circle' },
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val, { seriesIndex }) {
                            return seriesIndex === 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(val) : val + ' transaksi';
                        }
                    }
                },
                responsive: [{
                    breakpoint: 640,
                    options: { chart: { height: 220 } }
                }]
            };

            this.chart = new ApexCharts(document.querySelector('#revenue-chart'), options);
            this.chart.render();
        },
        
        async setFilter(val) {
            this.filter = val;
            this.loading = true;
            try {
                const res = await fetch(`{{ route('dashboard') }}?filter=${val}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await res.text();
                document.getElementById('dashboard-content').innerHTML = html;
                
                // Ensure icons are re-rendered
                if (window.lucide) {
                    lucide.createIcons();
                }
                
                // Re-init chart
                this.initChart();
            } catch (e) {
                console.error(e);
            }
            this.loading = false;
        }
    };
}
</script>
@endpush
