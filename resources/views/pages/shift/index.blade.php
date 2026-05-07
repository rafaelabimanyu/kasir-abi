@extends('layouts.master')

@section('title', 'Shift Saya')

@section('page-header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Shift Saya</h1>
        <p class="text-slate-500 mt-1">Riwayat jam kerja dan ringkasan pendapatan shift Anda.</p>
    </div>
</div>
@endsection

@section('content')
    <x-card title="Riwayat Shift" icon="clock" :noPadding="true">
        <x-table :headers="['Waktu Mulai', 'Waktu Selesai', 'Durasi', 'Total TRX', 'Total Pendapatan', 'Status']">
            @forelse($shifts as $shift)
                <tr class="hover:bg-dark-600/30 transition-colors">
                    <td class="px-5 py-3.5 text-sm text-slate-300">
                        {{ $shift->started_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-5 py-3.5 text-sm text-slate-400">
                        {{ $shift->ended_at ? $shift->ended_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-sm text-slate-400">
                        {{ $shift->ended_at ? $shift->started_at->diffForHumans($shift->ended_at, true) : $shift->started_at->diffForHumans(now(), true) }}
                    </td>
                    <td class="px-5 py-3.5 text-sm font-medium text-white">
                        {{ $shift->transactions()->count() }}
                    </td>
                    <td class="px-5 py-3.5 text-sm font-medium text-emerald-400">
                        Rp {{ number_format($shift->transactions()->where('status', 'success')->sum('total'), 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5">
                        @if(!$shift->ended_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-500/10 text-brand-400 border border-brand-500/20">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-dark-600/50 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="clock" class="w-8 h-8 text-slate-400 opacity-50"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-300">Belum ada riwayat shift</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
        
        @if($shifts->hasPages())
            <div class="px-5 py-4 border-t border-dark-600/40">
                {{ $shifts->links('vendor.pagination.tailwind-dark') }}
            </div>
        @endif
    </x-card>
@endsection
