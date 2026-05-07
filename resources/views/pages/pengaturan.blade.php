@extends('layouts.master')

@section('title', 'Pengaturan')

@section('page-header')
<div>
    <h1 class="text-2xl md:text-3xl font-bold text-white">Pengaturan</h1>
    <p class="text-slate-500 mt-1">Konfigurasi sistem dan preferensi aplikasi.</p>
</div>
@endsection

@section('content')
<div x-data="{ activeTab: 'toko' }" class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    {{-- Settings Navigation --}}
    <div class="xl:col-span-1">
        <x-card>
            <nav class="space-y-1">
                @php
                    $settingsMenu = [
                        ['id'=>'toko', 'icon'=>'store','label'=>'Informasi Toko'],
                        ['id'=>'struk', 'icon'=>'receipt','label'=>'Struk & Invoice'],
                        ['id'=>'pajak', 'icon'=>'percent','label'=>'Pajak & Diskon'],
                        ['id'=>'printer', 'icon'=>'printer','label'=>'Printer'],
                        ['id'=>'notif', 'icon'=>'bell','label'=>'Notifikasi'],
                        ['id'=>'keamanan', 'icon'=>'shield','label'=>'Keamanan'],
                        ['id'=>'backup', 'icon'=>'database','label'=>'Backup Data'],
                    ];
                @endphp
                @foreach($settingsMenu as $item)
                    <button @click="activeTab = '{{ $item['id'] }}'" 
                        :class="activeTab === '{{ $item['id'] }}' ? 'bg-brand-500/10 text-brand-400 border border-brand-500/20' : 'text-slate-400 hover:text-white hover:bg-dark-600 border-transparent'"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all cursor-pointer border text-left">
                        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
                        <span>{{ $item['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </x-card>
    </div>

    {{-- Settings Content --}}
    <div class="xl:col-span-2">
        
        {{-- Toko --}}
        <div x-show="activeTab === 'toko'">
            <x-card title="Informasi Toko" subtitle="Detail dan identitas toko Anda" icon="store">
                <form action="{{ route('pengaturan.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Toko</label>
                            <input type="text" name="settings[store_name]" value="{{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}" required class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 focus:shadow-glow transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Telepon</label>
                            <input type="text" name="settings[store_phone]" value="{{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}" required class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Alamat</label>
                        <textarea name="settings[store_address]" rows="3" required class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all resize-none">{{ \App\Models\Setting::get('store_address', 'Jl. Contoh No. 123, Jakarta') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Mata Uang</label>
                            <select name="settings[currency]" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none appearance-none">
                                <option value="IDR" {{ \App\Models\Setting::get('currency') == 'IDR' ? 'selected' : '' }}>IDR - Rupiah</option>
                                <option value="USD" {{ \App\Models\Setting::get('currency') == 'USD' ? 'selected' : '' }}>USD - Dollar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Zona Waktu</label>
                            <select name="settings[timezone]" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none appearance-none">
                                <option value="Asia/Jakarta" {{ \App\Models\Setting::get('timezone') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar" {{ \App\Models\Setting::get('timezone') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura" {{ \App\Models\Setting::get('timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <x-button variant="primary" icon="save" type="submit">Simpan Perubahan</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Struk --}}
        <div x-show="activeTab === 'struk'" style="display: none;">
            <x-card title="Struk & Invoice" subtitle="Pengaturan tampilan cetak struk" icon="receipt">
                <form action="{{ route('pengaturan.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Footer Struk (Teks Bawah)</label>
                        <textarea name="settings[receipt_footer]" rows="3" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all resize-none">{{ \App\Models\Setting::get('receipt_footer', "Terima Kasih Atas Kunjungan Anda!\nBarang yang sudah dibeli tidak dapat ditukar.") }}</textarea>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-dark-800 rounded-xl border border-dark-600/50">
                        <div>
                            <p class="text-sm font-medium text-white">Tampilkan Logo Toko</p>
                            <p class="text-xs text-slate-500 mt-0.5">Cetak logo hitam putih di atas struk</p>
                        </div>
                        <input type="hidden" name="settings[receipt_show_logo]" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[receipt_show_logo]" value="1" {{ \App\Models\Setting::get('receipt_show_logo', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-dark-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <x-button variant="primary" icon="save" type="submit">Simpan Perubahan</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Pajak --}}
        <div x-show="activeTab === 'pajak'" style="display: none;">
            <x-card title="Pajak & Diskon" subtitle="Atur PPN dan diskon global" icon="percent">
                <form action="{{ route('pengaturan.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Persentase Pajak (PPN)</label>
                        <div class="flex items-center gap-2 bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 focus-within:border-brand-500/50 transition-all">
                            <input type="number" name="settings[tax_percentage]" value="{{ \App\Models\Setting::get('tax_percentage', '11') }}" step="0.1" class="bg-transparent text-sm text-white outline-none w-full">
                            <span class="text-slate-500 font-medium">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Tipe Pajak</label>
                        <select name="settings[tax_type]" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none appearance-none">
                            <option value="exclusive" {{ \App\Models\Setting::get('tax_type', 'exclusive') == 'exclusive' ? 'selected' : '' }}>Exclusive (Ditambahkan di luar harga)</option>
                            <option value="inclusive" {{ \App\Models\Setting::get('tax_type') == 'inclusive' ? 'selected' : '' }}>Inclusive (Termasuk dalam harga)</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-dark-800 rounded-xl border border-dark-600/50">
                        <div>
                            <p class="text-sm font-medium text-white">Aktifkan Pajak Otomatis</p>
                            <p class="text-xs text-slate-500 mt-0.5">Pajak akan otomatis ditambahkan di POS</p>
                        </div>
                        <input type="hidden" name="settings[tax_enabled]" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[tax_enabled]" value="1" {{ \App\Models\Setting::get('tax_enabled', '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-dark-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Diskon Global</label>
                        <div class="flex items-center gap-2 bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 focus-within:border-brand-500/50 transition-all">
                            <input type="number" name="settings[discount_global]" value="{{ \App\Models\Setting::get('discount_global', '0') }}" class="bg-transparent text-sm text-white outline-none w-full">
                            <span class="text-slate-500 font-medium">%</span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <x-button variant="primary" icon="save" type="submit">Simpan Perubahan</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Printer --}}
        <div x-show="activeTab === 'printer'" style="display: none;">
            <x-card title="Printer" subtitle="Koneksi perangkat cetak struk" icon="printer">
                <form action="{{ route('pengaturan.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Tipe Printer</label>
                        <select name="settings[printer_type]" class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none appearance-none">
                            <option value="thermal_58" {{ \App\Models\Setting::get('printer_type') == 'thermal_58' ? 'selected' : '' }}>Thermal Bluetooth (58mm)</option>
                            <option value="thermal_80" {{ \App\Models\Setting::get('printer_type') == 'thermal_80' ? 'selected' : '' }}>Thermal USB (80mm)</option>
                            <option value="browser" {{ \App\Models\Setting::get('printer_type', 'browser') == 'browser' ? 'selected' : '' }}>Sistem Browser Default</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <x-button variant="primary" icon="save" type="submit">Simpan Perubahan</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Notifikasi --}}
        <div x-show="activeTab === 'notif'" style="display: none;">
            <x-card title="Notifikasi" subtitle="Pemberitahuan sistem" icon="bell">
                <form action="{{ route('pengaturan.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex items-center justify-between p-4 bg-dark-800 rounded-xl border border-dark-600/50">
                        <div>
                            <p class="text-sm font-medium text-white">Notifikasi Sistem</p>
                            <p class="text-xs text-slate-500 mt-0.5">Aktifkan atau nonaktifkan notifikasi sistem</p>
                        </div>
                        <input type="hidden" name="settings[notification_enabled]" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[notification_enabled]" value="1" {{ \App\Models\Setting::get('notification_enabled', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-dark-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>
                    <div class="flex justify-end pt-2">
                        <x-button variant="primary" icon="save" type="submit">Simpan Perubahan</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Keamanan --}}
        <div x-show="activeTab === 'keamanan'" style="display: none;">
            <x-card title="Keamanan" subtitle="Ganti password dan pengaturan otentikasi" icon="shield">
                <form action="{{ route('pengaturan.password') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Password Lama</label>
                        <input type="password" name="current_password" required class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                        @error('current_password') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Password Baru</label>
                        <input type="password" name="new_password" required class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                        @error('new_password') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" required class="w-full bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-brand-500/50 transition-all">
                    </div>
                    <div class="flex justify-end pt-2">
                        <x-button variant="primary" icon="key" type="submit">Update Password</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Backup Data --}}
        <div x-show="activeTab === 'backup'" style="display: none;">
            <x-card title="Backup Data" subtitle="Amankan data transaksi dan produk" icon="database">
                <div class="p-6 border border-dark-600/50 rounded-xl bg-dark-800/50 text-center">
                    <div class="w-16 h-16 bg-brand-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="download-cloud" class="w-8 h-8 text-brand-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Backup Database</h3>
                    <p class="text-sm text-slate-400 mb-6 max-w-sm mx-auto">Unduh seluruh data produk, transaksi, pengguna, dan pengaturan ke perangkat Anda dalam format .json.</p>
                    <a href="{{ route('pengaturan.backup') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium transition-all rounded-xl shadow-glow bg-brand-600 hover:bg-brand-500 text-white cursor-pointer">
                        <i data-lucide="download" class="w-4 h-4"></i> Download Backup (.json)
                    </a>
                </div>
            </x-card>
        </div>

    </div>
</div>
@endsection
