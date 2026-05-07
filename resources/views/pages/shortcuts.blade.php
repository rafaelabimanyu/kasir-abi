@extends('layouts.master')

@section('title', 'Panduan Shortcut')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i data-lucide="keyboard" class="w-6 h-6 text-brand-400"></i>
                Panduan Keyboard Shortcut
            </h1>
            <p class="text-sm text-slate-400 mt-1">Daftar kombinasi tombol untuk mempercepat navigasi dan operasional.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Global Shortcuts --}}
    <x-card title="Global (Semua Halaman)" icon="globe">
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Buka / Tutup Daftar Shortcut</p>
                    <p class="text-xs text-slate-500">Menampilkan modal panduan shortcut ini secara cepat.</p>
                </div>
                <kbd class="px-3 py-1.5 bg-dark-600 text-brand-400 rounded-lg border border-brand-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + /</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Fokus Global Search</p>
                    <p class="text-xs text-slate-500">Memindahkan kursor langsung ke kolom pencarian atas.</p>
                </div>
                <kbd class="px-3 py-1.5 bg-dark-600 text-brand-400 rounded-lg border border-brand-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + K</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Ke Halaman Dashboard</p>
                </div>
                <kbd class="px-3 py-1.5 bg-dark-600 text-brand-400 rounded-lg border border-brand-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + H</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Ke Halaman POS</p>
                </div>
                <kbd class="px-3 py-1.5 bg-dark-600 text-brand-400 rounded-lg border border-brand-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + P</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Ke Halaman Laporan</p>
                </div>
                <kbd class="px-3 py-1.5 bg-dark-600 text-brand-400 rounded-lg border border-brand-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + L</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Ke User Management</p>
                </div>
                <kbd class="px-3 py-1.5 bg-dark-600 text-brand-400 rounded-lg border border-brand-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + U</kbd>
            </div>
        </div>
    </x-card>

    {{-- POS Shortcuts --}}
    <x-card title="Halaman POS" icon="shopping-cart" color="emerald">
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Fokus Pencarian Produk</p>
                    <p class="text-xs text-slate-500">Langsung mengetik nama produk di halaman POS.</p>
                </div>
                <kbd class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-lg border border-emerald-500/30 font-mono text-sm shadow-sm font-bold">F2</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Proses Pembayaran</p>
                    <p class="text-xs text-slate-500">Sama dengan menekan tombol Bayar (jika valid).</p>
                </div>
                <kbd class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-lg border border-emerald-500/30 font-mono text-sm shadow-sm font-bold">Enter</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Reset / Tutup Modal</p>
                    <p class="text-xs text-slate-500">Membatalkan seluruh pesanan saat ini atau menutup notifikasi.</p>
                </div>
                <kbd class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-lg border border-emerald-500/30 font-mono text-sm shadow-sm font-bold">ESC</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Simpan Transaksi</p>
                    <p class="text-xs text-slate-500">Cara paksa memproses pembayaran dari mana saja di halaman POS.</p>
                </div>
                <kbd class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-lg border border-emerald-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + Shift + S</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Hold Transaksi</p>
                    <p class="text-xs text-slate-500">Menyimpan sementara transaksi (Hold).</p>
                </div>
                <kbd class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-lg border border-emerald-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + Shift + D</kbd>
            </div>
            <div class="flex justify-between items-center p-3 bg-dark-700/50 rounded-xl border border-dark-600/50">
                <div>
                    <p class="text-sm font-medium text-white">Kosongkan Keranjang</p>
                    <p class="text-xs text-slate-500">Sama dengan Void secara instan tanpa dialog.</p>
                </div>
                <kbd class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 rounded-lg border border-emerald-500/30 font-mono text-sm shadow-sm font-bold">Ctrl + Backspace</kbd>
            </div>
        </div>
    </x-card>
</div>

<div class="mt-6">
    <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center shrink-0">
            <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
        </div>
        <div>
            <h4 class="text-base font-semibold text-blue-400">Tips Penggunaan</h4>
            <p class="text-sm text-slate-300 mt-1 leading-relaxed">
                Shortcut ini dirancang untuk bekerja ketika Anda <strong>tidak</strong> sedang mengetik di dalam kotak form. Jika Anda sedang mengetik di input, fitur navigasi (seperti <code>Ctrl + H</code>) dinonaktifkan secara sementara untuk mencegah tabrakan. Namun, kombinasi spesifik seperti <code>Ctrl + Shift + S</code> akan tetap diprioritaskan di dalam POS.
            </p>
        </div>
    </div>
</div>
@endsection
