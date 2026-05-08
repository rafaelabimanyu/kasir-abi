@extends('layouts.master')

@section('title', 'Buku Panduan')

@section('page-header')
<div class="flex items-center gap-3">
    <div class="w-10 h-10 bg-brand-500/10 border border-brand-500/20 rounded-xl flex items-center justify-center">
        <i data-lucide="book-open" class="w-6 h-6 text-brand-400"></i>
    </div>
    <div>
        <h1 class="text-2xl font-bold text-white">Buku Panduan Pengguna</h1>
        <p class="text-sm text-slate-400 mt-0.5">Panduan resmi penggunaan sistem Kasir Abi.</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pb-12">
    {{-- Sidebar Navigasi Panduan --}}
    <div class="lg:col-span-3 space-y-4">
        <div class="sticky top-24 space-y-2">
            <a href="#admin" class="flex items-center gap-3 px-4 py-3 bg-dark-800 hover:bg-dark-700 border border-dark-600/50 rounded-xl text-sm font-medium text-slate-300 transition-all group">
                <i data-lucide="user-cog" class="w-4 h-4 text-brand-400"></i>
                <span>Panduan Admin</span>
            </a>
            <a href="#kasir" class="flex items-center gap-3 px-4 py-3 bg-dark-800 hover:bg-dark-700 border border-dark-600/50 rounded-xl text-sm font-medium text-slate-300 transition-all group">
                <i data-lucide="shopping-cart" class="w-4 h-4 text-emerald-400"></i>
                <span>Panduan Kasir</span>
            </a>
            <a href="#tips" class="flex items-center gap-3 px-4 py-3 bg-dark-800 hover:bg-dark-700 border border-dark-600/50 rounded-xl text-sm font-medium text-slate-300 transition-all group">
                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-400"></i>
                <span>Tips & Trik</span>
            </a>
        </div>
    </div>

    {{-- Konten Utama --}}
    <div class="lg:col-span-9 space-y-8">
        {{-- Intro --}}
        <div class="p-8 bg-gradient-to-br from-dark-800 to-dark-900 border border-dark-600/50 rounded-3xl relative overflow-hidden shadow-card">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-brand-500/10 blur-[100px] rounded-full"></div>
            <div class="relative">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    Selamat Datang <span class="animate-bounce">🚀</span>
                </h2>
                <p class="text-slate-400 leading-relaxed max-w-2xl">
                    Sistem ini dirancang untuk memudahkan manajemen toko dan transaksi harian Anda dengan antarmuka yang modern dan responsif. Silakan pelajari panduan di bawah ini sesuai dengan peran Anda dalam sistem.
                </p>
            </div>
        </div>

        {{-- Section Admin --}}
        <section id="admin" class="space-y-4">
            <div class="flex items-center gap-3 px-4 py-2 bg-brand-500/10 rounded-xl border border-brand-500/20 w-fit">
                <i data-lucide="user-cog" class="w-5 h-5 text-brand-400"></i>
                <h3 class="text-lg font-bold text-brand-300 uppercase tracking-wider">Role: Admin</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-card title="Produk & Kategori" icon="package">
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">1</span>
                            <span><b>Tambah Produk:</b> Menu Produk > Tambah. Isi nama, kategori, harga beli/jual, dan stok.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">2</span>
                            <span><b>Manajemen Stok:</b> Perbarui stok manual jika ada barang masuk atau retur.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">3</span>
                            <span><b>Kategori:</b> Kelompokkan produk untuk memudahkan pencarian saat transaksi.</span>
                        </li>
                    </ul>
                </x-card>

                <x-card title="Laporan Pendapatan" icon="bar-chart-3">
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">1</span>
                            <span><b>Melihat Laporan:</b> Menu Laporan untuk ringkasan harian, mingguan, atau bulanan.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">2</span>
                            <span><b>Filter Data:</b> Gunakan filter tanggal untuk performa periode tertentu.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">3</span>
                            <span><b>Export:</b> Unduh laporan dalam format <b>PDF</b> atau <b>Excel</b>.</span>
                        </li>
                    </ul>
                </x-card>

                <x-card title="Manajemen User" icon="users">
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">1</span>
                            <span><b>Buat Akun Kasir:</b> Melalui menu User Management dengan role Kasir.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">2</span>
                            <span><b>Monitor Aktivitas:</b> Cek status online dan aktivitas staf di menu Chat.</span>
                        </li>
                    </ul>
                </x-card>

                <x-card title="Pesan Internal" icon="message-square">
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">1</span>
                            <span><b>Komunikasi:</b> Berkoordinasi dengan kasir tanpa aplikasi pihak ketiga.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-brand-400 shrink-0 mt-0.5">2</span>
                            <span><b>Lampiran:</b> Kirim gambar/dokumen dan hapus pesan (me/all).</span>
                        </li>
                    </ul>
                </x-card>
            </div>
        </section>

        {{-- Section Kasir --}}
        <section id="kasir" class="space-y-4 pt-4">
            <div class="flex items-center gap-3 px-4 py-2 bg-emerald-500/10 rounded-xl border border-emerald-500/20 w-fit">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-400"></i>
                <h3 class="text-lg font-bold text-emerald-300 uppercase tracking-wider">Role: Kasir</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-card title="Transaksi POS" icon="monitor">
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-emerald-400 shrink-0 mt-0.5">1</span>
                            <span><b>Pilih Produk:</b> Klik gambar atau cari produk untuk masuk keranjang.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-emerald-400 shrink-0 mt-0.5">2</span>
                            <span><b>Pembayaran:</b> Masukkan nominal uang, sistem hitung kembalian otomatis.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-emerald-400 shrink-0 mt-0.5">3</span>
                            <span><b>Cetak Struk:</b> Klik Cetak Struk setelah transaksi berhasil.</span>
                        </li>
                    </ul>
                </x-card>

                <x-card title="Manajemen Shift" icon="clock">
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-emerald-400 shrink-0 mt-0.5">1</span>
                            <span><b>Buka Shift:</b> Awal kerja untuk mencatat modal awal.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-emerald-400 shrink-0 mt-0.5">2</span>
                            <span><b>Pengeluaran:</b> Catat pengeluaran toko menggunakan uang kas.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-full bg-dark-700 flex items-center justify-center text-[10px] font-bold text-emerald-400 shrink-0 mt-0.5">3</span>
                            <span><b>Tutup Shift:</b> Akhir kerja untuk mencocokkan uang fisik.</span>
                        </li>
                    </ul>
                </x-card>
            </div>
        </section>

        {{-- Section Tips --}}
        <section id="tips" class="space-y-4 pt-4">
            <div class="flex items-center gap-3 px-4 py-2 bg-amber-500/10 rounded-xl border border-amber-500/20 w-fit">
                <i data-lucide="lightbulb" class="w-5 h-5 text-amber-400"></i>
                <h3 class="text-lg font-bold text-amber-300 uppercase tracking-wider">Tips & Trik Umum</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-5 bg-dark-800 border border-dark-600/50 rounded-2xl">
                    <i data-lucide="keyboard" class="w-6 h-6 text-brand-400 mb-3"></i>
                    <h4 class="text-sm font-bold text-white mb-2">Shortcuts</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Gunakan tombol pintasan untuk mempercepat input produk di POS.</p>
                </div>
                <div class="p-5 bg-dark-800 border border-dark-600/50 rounded-2xl">
                    <i data-lucide="moon" class="w-6 h-6 text-indigo-400 mb-3"></i>
                    <h4 class="text-sm font-bold text-white mb-2">Dark Mode</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Aktifkan mode gelap untuk kenyamanan mata saat bekerja malam hari.</p>
                </div>
                <div class="p-5 bg-dark-800 border border-dark-600/50 rounded-2xl">
                    <i data-lucide="trash-2" class="w-6 h-6 text-red-400 mb-3"></i>
                    <h4 class="text-sm font-bold text-white mb-2">Hapus Pesan</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Klik icon sampah pada balon chat untuk menarik pesan yang salah.</p>
                </div>
            </div>
        </section>

        {{-- Footer Support --}}
        <div class="p-6 bg-dark-800/50 border border-dark-600/30 rounded-2xl text-center">
            <p class="text-sm text-slate-500 italic">Terima kasih telah menggunakan Kasir Abi. Jika ada kendala teknis lebih lanjut, hubungi tim support sistem.</p>
        </div>
    </div>
</div>
@endsection
