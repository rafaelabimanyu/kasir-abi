<x-table :headers="['User', 'Email', 'Role', 'Status', 'Ditambahkan Pada', 'Aksi']">
    @forelse($users as $u)
        <tr class="hover:bg-dark-600/30 transition-colors">
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        @php
                            $initial = strtoupper(substr($u->name, 0, 1));
                            $colors = ['from-brand-500 to-purple-600', 'from-emerald-500 to-teal-600', 'from-amber-500 to-orange-600', 'from-pink-500 to-rose-600'];
                            $color = $colors[$u->id % count($colors)];
                            $isOnline = $u->id === auth()->id();
                        @endphp
                        <div class="w-10 h-10 bg-gradient-to-br {{ $color }} rounded-xl flex items-center justify-center text-white text-sm font-bold">{{ $initial }}</div>
                        @if($isOnline)
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-dark-700"></div>
                        @endif
                    </div>
                    <span class="text-sm font-medium text-white highlight-target">{{ $u->name }}</span>
                </div>
            </td>
            <td class="px-5 py-3.5 text-sm text-slate-400 highlight-target">{{ $u->email }}</td>
            <td class="px-5 py-3.5 highlight-target">
                <x-badge :color="$u->role==='admin'?'brand':($u->role==='manager'?'purple':'emerald')">{{ ucfirst($u->role) }}</x-badge>
            </td>
            <td class="px-5 py-3.5"><x-badge :color="$isOnline?'emerald':'slate'">{{ $isOnline ? 'Online' : 'Offline' }}</x-badge></td>
            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $u->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center gap-1">
                    <button @click="editUser({{ $u->toJson() }})" class="p-2 text-slate-400 hover:text-brand-400 hover:bg-brand-500/10 rounded-lg transition-colors" title="Edit">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </button>
                    @if($u->id !== auth()->id())
                    <button @click="confirmDelete({{ $u->id }})" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-5 py-12 text-center text-slate-500">Tidak ada data.</td>
        </tr>
    @endforelse
</x-table>

@if($users->hasPages())
    <div class="px-5 py-4 border-t border-dark-600/40 ajax-pagination">
        {{ $users->links('vendor.pagination.tailwind-dark') }}
    </div>
@endif
