<x-table :headers="['ID Transaksi', 'Tanggal', 'Kasir', 'Total', 'Metode', 'Status', 'Aksi']">
    @forelse($transactions as $trx)
        <tr class="hover:bg-dark-600/30 transition-colors">
            <td class="px-5 py-3.5 text-sm font-mono font-medium text-brand-400 highlight-target">
                #TRX-{{ str_pad($trx->id, 4, '0', STR_PAD_LEFT) }}
            </td>
            <td class="px-5 py-3.5 text-sm text-slate-400">
                {{ $trx->tanggal->format('d M Y') }}
                <span class="block text-xs text-slate-500">{{ $trx->created_at->format('H:i') }}</span>
            </td>
            <td class="px-5 py-3.5 text-sm text-slate-300">
                {{ $trx->user->name ?? 'Unknown' }}
            </td>
            <td class="px-5 py-3.5 text-sm font-medium text-white">
                Rp {{ number_format($trx->total, 0, ',', '.') }}
            </td>
            <td class="px-5 py-3.5">
                @if($trx->payment_method === 'cash')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Cash</span>
                @elseif($trx->payment_method === 'qris')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">QRIS</span>
                @elseif($trx->payment_method === 'transfer')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">Transfer</span>
                @elseif($trx->payment_method === 'e-wallet')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20">E-Wallet</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">{{ ucfirst($trx->payment_method) }}</span>
                @endif
            </td>
            <td class="px-5 py-3.5">
                @if($trx->status === 'success')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Success</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">Void</span>
                @endif
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-2">
                    <button @click="showDetail({{ $trx->id }})" class="p-1.5 text-slate-400 hover:text-brand-400 hover:bg-brand-500/10 rounded-lg transition-colors cursor-pointer" title="Lihat Detail">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                    
                    @if(auth()->user()->isAdmin() && $trx->status === 'success')
                    <button @click="openVoidModal({{ $trx->id }})" class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors cursor-pointer" title="Void Transaksi">
                        <i data-lucide="ban" class="w-4 h-4"></i>
                    </button>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="px-5 py-12 text-center text-slate-500">Tidak ada transaksi ditemukan.</td>
        </tr>
    @endforelse
</x-table>

@if($transactions->hasPages())
    <div class="px-5 py-4 border-t border-dark-600/40 ajax-pagination">
        {{ $transactions->links('vendor.pagination.tailwind-dark') }}
    </div>
@endif
