<div id="chart-data-container" data-chart="{{ json_encode($chartData) }}" class="hidden"></div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-5 mb-6">
        <x-stat-card title="Total Pendapatan" :value="'Rp ' . number_format($todayRevenue, 0, ',', '.')" icon="wallet" color="brand" url="{{ route('laporan') }}" />
        <x-stat-card title="Total Transaksi" :value="(string) $todayTransactions" icon="shopping-cart" color="emerald" url="{{ route('transaksi.history') }}" />
        <x-stat-card title="Produk Terjual" :value="(string) $todayItemsSold" icon="package" color="amber" url="{{ route('laporan') }}" />
        <x-stat-card title="Rata-rata Transaksi" :value="'Rp ' . number_format($todayAvgTransaction, 0, ',', '.')" icon="bar-chart-3" color="purple" url="{{ route('laporan') }}" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">
        <div class="xl:col-span-2">
            <x-card title="Grafik Pendapatan" subtitle="7 hari terakhir" icon="trending-up">
                <x-slot name="action">
                    <div class="flex items-center gap-4 text-sm bg-dark-800/50 p-2 rounded-xl border border-dark-600/30">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-500 font-medium">Cash</span>
                            <span class="text-emerald-400 font-bold">Rp {{ number_format($todayCashRevenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-px h-8 bg-dark-600/50"></div>
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-500 font-medium">Non-Cash</span>
                            <span class="text-purple-400 font-bold">Rp {{ number_format($todayNonCashRevenue, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </x-slot>
                <div id="revenue-chart"></div>
            </x-card>
        </div>

        {{-- Top Products --}}
        <div>
            <x-card title="Produk Terlaris" subtitle="{{ $filter === 'today' ? 'Hari Ini' : ($filter === '7days' ? '7 Hari Terakhir' : '30 Hari Terakhir') }}" icon="crown">
                @if($topProducts->isEmpty())
                    <div class="flex flex-col items-center py-8 text-slate-500">
                        <i data-lucide="package-open" class="w-10 h-10 mb-2 opacity-30"></i>
                        <p class="text-sm">Belum ada data penjualan</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($topProducts as $i => $item)
                            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-dark-600/50 transition-colors group cursor-pointer" onclick="window.location.href='{{ route('produk.index') }}'">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold shrink-0
                                    {{ $i === 0 ? 'bg-amber-500/10 text-amber-400' : ($i === 1 ? 'bg-slate-400/10 text-slate-400' : ($i === 2 ? 'bg-orange-500/10 text-orange-400' : 'bg-dark-600 text-slate-500')) }}">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors truncate">
                                        {{ $item->product->nama ?? 'Produk Dihapus' }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ number_format($item->total_qty) }} terjual</p>
                                </div>
                                <p class="text-xs font-medium text-slate-400">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <x-card title="Transaksi Terbaru" subtitle="5 transaksi terakhir" icon="receipt" :noPadding="true">
        <x-slot name="action">
            <x-button variant="ghost" size="sm" icon="arrow-right" href="{{ route('transaksi.history') }}">Lihat Semua</x-button>
        </x-slot>

        <x-table :headers="['ID', 'Tanggal', 'Item', 'Total', 'Bayar', 'Kembalian', 'Kasir']">
            @forelse($recentTransactions as $trx)
                <tr class="hover:bg-dark-600/30 transition-colors cursor-pointer" onclick="window.location.href='{{ route('transaksi.history', ['search' => $trx->id]) }}'">
                    <td class="px-5 py-3.5 text-sm font-mono font-medium text-brand-400">#TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3.5 text-sm text-slate-400">{{ $trx->tanggal->format('d M Y') }}</td>
                    <td class="px-5 py-3.5 text-sm text-slate-400">{{ $trx->items->sum('qty') }} item</td>
                    <td class="px-5 py-3.5 text-sm font-medium text-white">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-sm text-slate-300">Rp {{ number_format($trx->bayar, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-sm text-emerald-400">Rp {{ number_format($trx->kembalian, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-sm text-slate-400">{{ $trx->user->name ?? 'Admin' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center text-slate-500">
                            <i data-lucide="receipt" class="w-12 h-12 mb-3 opacity-30"></i>
                            <p class="font-medium">Belum ada transaksi</p>
                            <p class="text-xs mt-1">Mulai transaksi pertama di POS</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>
