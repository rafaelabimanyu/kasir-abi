<x-table :headers="['Produk', 'Kategori', 'Harga', 'Stok', 'Status', 'Aksi']">
    @forelse($products as $product)
        <tr class="hover:bg-dark-600/30 transition-colors">
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                    @if($product->gambar)
                        <img src="{{ asset('storage/' . $product->gambar) }}" alt="{{ $product->nama }}" class="w-10 h-10 rounded-xl object-cover border border-dark-600/50">
                    @else
                        <div class="w-10 h-10 bg-dark-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="package" class="w-5 h-5 text-slate-500"></i>
                        </div>
                    @endif
                    <span class="text-sm font-medium text-white highlight-target">{{ $product->nama }}</span>
                </div>
            </td>
            <td class="px-5 py-3.5">
                <x-badge color="brand">{{ $product->category->nama }}</x-badge>
            </td>
            <td class="px-5 py-3.5 text-sm font-medium text-white">
                Rp {{ number_format($product->harga, 0, ',', '.') }}
            </td>
            <td class="px-5 py-3.5 text-sm {{ $product->stok < 5 ? 'text-red-400 font-semibold' : 'text-slate-300' }}">
                {{ $product->stok }}
            </td>
            <td class="px-5 py-3.5">
                @if($product->stok === 0)
                    <x-badge color="red">Habis</x-badge>
                @elseif($product->stok < 5)
                    <x-badge color="amber">Low Stock</x-badge>
                @else
                    <x-badge color="emerald">Tersedia</x-badge>
                @endif
            </td>
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-1">
                    <x-button variant="ghost" size="sm" icon="pencil" href="{{ route('produk.edit', $product) }}"></x-button>
                    <form action="{{ route('produk.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <x-button variant="ghost" size="sm" icon="trash-2" type="submit"></x-button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-5 py-12 text-center">
                <div class="flex flex-col items-center text-slate-500">
                    <i data-lucide="package-open" class="w-12 h-12 mb-3 opacity-30"></i>
                    <p class="font-medium">Belum ada produk</p>
                    <p class="text-xs mt-1">Tambahkan produk pertama Anda</p>
                </div>
            </td>
        </tr>
    @endforelse
</x-table>

{{-- Pagination --}}
@if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
    <div class="px-5 py-4 border-t border-dark-600/40 ajax-pagination">
        {{ $products->links('vendor.pagination.tailwind-dark') }}
    </div>
@endif
