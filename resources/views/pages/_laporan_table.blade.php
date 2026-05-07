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
                    <p class="text-xs mt-1.5 text-center text-slate-500 max-w-[250px]">Transaksi yang dilakukan pada periode ini akan muncul di sini. Coba ubah filter tanggal.</p>
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
