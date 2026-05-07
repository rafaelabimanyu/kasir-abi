@extends('layouts.master')

@section('title', 'Profil Saya')

@section('page-header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Profil Saya</h1>
        <p class="text-slate-500 mt-1">Kelola informasi pribadi dan kata sandi Anda.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl animate-fade-in">
    <div class="bg-dark-700 border border-dark-600/50 rounded-2xl shadow-soft overflow-hidden">
        {{-- Header Card with Avatar --}}
        <div class="p-6 md:p-8 border-b border-dark-600/40 bg-dark-800/30 flex items-center gap-5">
            <div class="w-20 h-20 bg-gradient-to-br {{ $user->isAdmin() ? 'from-brand-500 to-purple-600' : 'from-emerald-500 to-teal-600' }} rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-glow shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-slate-400 text-sm mt-0.5">{{ $user->email }}</p>
                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-dark-600 border border-dark-500 text-slate-300 capitalize">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    {{ $user->role }}
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" x-data="{ loading: false, showPassword: false }" @submit="loading = true" class="p-6 md:p-8">
            @csrf
            
            <div class="space-y-6">
                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-dark-800 border {{ $errors->has('name') ? 'border-red-500/50 focus:border-red-500' : 'border-dark-600/50 focus:border-brand-500/50' }} rounded-xl px-4 py-2.5 text-sm text-white outline-none transition-all">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Alamat Email <span class="text-red-400">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full bg-dark-800 border {{ $errors->has('email') ? 'border-red-500/50 focus:border-red-500' : 'border-dark-600/50 focus:border-brand-500/50' }} rounded-xl px-4 py-2.5 text-sm text-white outline-none transition-all">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role (Readonly) --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-300 mb-1.5">Hak Akses</label>
                    <input type="text" id="role" value="{{ ucfirst($user->role) }}" readonly disabled
                           class="w-full bg-dark-800/50 border border-dark-600/30 rounded-xl px-4 py-2.5 text-sm text-slate-500 outline-none cursor-not-allowed">
                    <p class="mt-1.5 text-xs text-slate-500">Peran akun Anda tidak dapat diubah sendiri.</p>
                </div>

                <div class="h-px bg-dark-600/50 my-6"></div>

                {{-- Password Baru --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Kata Sandi Baru (Opsional)</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah"
                               class="w-full bg-dark-800 border {{ $errors->has('password') ? 'border-red-500/50 focus:border-red-500' : 'border-dark-600/50 focus:border-brand-500/50' }} rounded-xl px-4 py-2.5 pr-10 text-sm text-white outline-none transition-all">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-300 transition-colors cursor-pointer">
                            <i data-lucide="eye" x-show="!showPassword" class="w-4 h-4"></i>
                            <i data-lucide="eye-off" x-show="showPassword" class="w-4 h-4" x-cloak></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @else
                        <p class="mt-1.5 text-xs text-slate-500">Minimal 6 karakter. Diperlukan untuk memperbarui kata sandi saat ini.</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-dark-600/40">
                <p class="text-xs text-slate-500">
                    <i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1 -mt-0.5"></i>
                    Terakhir diperbarui: {{ $user->updated_at->diffForHumans() }}
                </p>
                
                <x-button variant="primary" type="submit" x-bind:disabled="loading" class="w-full sm:w-auto min-w-[160px]">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="loading" x-cloak></i>
                    <i data-lucide="save" class="w-4 h-4" x-show="!loading"></i>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
