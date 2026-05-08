{{-- Sidebar Navigation --}}
<aside id="sidebar"
    :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
    class="fixed top-0 left-0 z-50 w-64 h-screen bg-dark-800 border-r border-dark-600/50 transform transition-transform duration-300 ease-in-out flex flex-col">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-dark-600/50 shrink-0">
        <div class="w-9 h-9 bg-gradient-to-br from-brand-500 to-brand-700 rounded-xl flex items-center justify-center shadow-glow">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
        </div>

        <div>
            <h1 class="text-lg font-bold text-white tracking-tight">
                {{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}
            </h1>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider">Point of Sale</p>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        {{-- MENU UTAMA --}}
        <p class="px-3 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">
            Menu Utama
        </p>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('dashboard') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Dashboard</span>
        </a>
        @endif

        <a href="{{ route('pos') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('pos') ? 'active' : '' }}">
            <i data-lucide="monitor" class="w-5 h-5"></i>
            <span>POS Kasir</span>

            <span class="ml-auto px-2 py-0.5 text-[10px] font-bold bg-brand-500/20 text-brand-400 rounded-full">
                LIVE
            </span>
        </a>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('produk.index') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('produk.*') ? 'active' : '' }}">
            <i data-lucide="package" class="w-5 h-5"></i>
            <span>Produk</span>
        </a>
        @endif


        {{-- LAPORAN --}}
        <p class="px-3 mt-6 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">
            Laporan & Data
        </p>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('transaksi.history') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
            <i data-lucide="receipt" class="w-5 h-5"></i>
            <span>Riwayat Transaksi</span>
        </a>

        <a href="{{ route('laporan') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('laporan') ? 'active' : '' }}">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            <span>Laporan</span>
        </a>
        @endif

        @if(auth()->user()->isKasir())
        <a href="{{ route('shift.saya') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('shift.saya') ? 'active' : '' }}">
            <i data-lucide="clock" class="w-5 h-5"></i>
            <span>Shift Saya</span>
        </a>
        @endif


        {{-- BANTUAN --}}
        <p class="px-3 mt-6 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">
            Bantuan
        </p>

        <a href="{{ route('panduan') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('panduan') ? 'active' : '' }}">
            <i data-lucide="book-open" class="w-5 h-5"></i>
            <span>Buku Panduan</span>
        </a>


        {{-- SISTEM --}}
        <p class="px-3 mt-6 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">
            Sistem
        </p>

        <a href="{{ route('chat') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('chat*') ? 'active' : '' }}">
            <i data-lucide="message-square" class="w-5 h-5"></i>
            <span>Pesan Internal</span>
        </a>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.index') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i data-lucide="users" class="w-5 h-5"></i>
            <span>User Management</span>
        </a>

        <a href="{{ route('pengaturan') }}"
            class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <i data-lucide="settings" class="w-5 h-5"></i>
            <span>Pengaturan</span>
        </a>
        @endif

    </nav>


    {{-- Footer --}}
    <div class="p-3 border-t border-dark-600/50 shrink-0">
        <div class="p-3 bg-gradient-to-br from-brand-600/10 to-brand-500/5 rounded-xl border border-brand-500/10">

            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                <span class="text-xs text-emerald-400 font-medium">Online</span>
            </div>

            <p class="text-xs text-white font-medium">
                {{ auth()->user()->name }}
            </p>

            <p class="text-[10px] text-slate-500 capitalize">
                {{ auth()->user()->role }}
            </p>

        </div>
    </div>

</aside>