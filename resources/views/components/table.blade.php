{{--
    Table Component

    Usage:
    <x-table :headers="['Nama', 'Harga', 'Stok', 'Aksi']">
        <tr>
            <td class="px-5 py-3.5 text-sm text-slate-300">...</td>
        </tr>
    </x-table>
--}}

@props([
    'headers' => [],
])

<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-dark-600/50">
                @foreach($headers as $header)
                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-dark-600/30">
            {{ $slot }}
        </tbody>
    </table>
</div>
