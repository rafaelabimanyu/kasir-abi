{{-- Sidebar Navigation --}}
<aside id="sidebar" 
       :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
       class="fixed top-0 left-0 z-50 w-64 h-screen bg-dark-800 border-r border-dark-600/50 transform transition-transform duration-300 ease-in-out flex flex-col">

    {{-- Logo Section --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-dark-600/50 shrink-0">
        <div class="w-9 h-9 bg-gradient-to-br from-brand-500 to-brand-700 rounded-xl flex items-center justify-center shadow-glow">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-white tracking-tight">{{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}</h1>
            <p class="text-[10px] text-slate-500 -mt-0.5 font-medium uppercase tracking-wider">Point of Sale</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        {{-- Main Menu --}}
        <p class="px-3 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">Menu Utama</p>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('dashboard') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Dashboard</span>
        </a>
        @endif

        <a href="{{ route('pos') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('pos') ? 'active' : '' }}">
            <i data-lucide="monitor" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">POS Kasir</span>
            <span class="ml-auto px-2 py-0.5 text-[10px] font-bold bg-brand-500/20 text-brand-400 rounded-full">LIVE</span>
        </a>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('produk.index') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('produk.*') ? 'active' : '' }}">
            <i data-lucide="package" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Produk</span>
        </a>

        {{-- Reports --}}
        <p class="px-3 mt-6 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">Laporan & Data</p>

        <a href="{{ route('transaksi.history') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
            <i data-lucide="receipt" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Riwayat Transaksi</span>
        </a>

        @if(auth()->user()->isKasir())
        <a href="{{ route('shift.saya') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('shift.saya') ? 'active' : '' }}">
            <i data-lucide="clock" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Shift Saya</span>
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <a href="{{ route('laporan') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('laporan') ? 'active' : '' }}">
            <i data-lucide="bar-chart-3" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Laporan</span>
        </a>
        @endif

        {{-- Help & Guide --}}
        <p class="px-3 mt-6 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">Bantuan</p>
        
        <a href="{{ route('panduan') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('panduan') ? 'active' : '' }}">
            <i data-lucide="book-open" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Buku Panduan</span>
        </a>

        {{-- Systems --}}
        <p class="px-3 mt-6 mb-2 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">Sistem</p>

        <div x-data="navChatBadge()" class="relative">
            <a href="{{ route('chat') }}"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('chat*') ? 'active' : '' }}">
                <i data-lucide="message-square" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
                <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Pesan Internal</span>
                <span x-show="totalUnread > 0"
                      class="ml-auto min-w-[18px] h-[18px] flex items-center justify-center bg-brand-500 text-white text-[9px] font-bold rounded-full px-1"
                      x-text="totalUnread > 9 ? '9+' : totalUnread"></span>
            </a>
        </div>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.index') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i data-lucide="users" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">User Management</span>
        </a>

        <a href="{{ route('pengaturan') }}"
           class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 hover:bg-surface-hover {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <i data-lucide="settings" class="sidebar-icon w-5 h-5 text-slate-400 group-hover:text-brand-400 transition-colors"></i>
            <span class="sidebar-text text-slate-300 group-hover:text-white transition-colors">Pengaturan</span>
        </a>
        @endif
    </nav>

    {{-- Sidebar Footer --}}
    <div class="p-3 border-t border-dark-600/50 shrink-0">
        <div class="p-3 bg-gradient-to-br from-brand-600/10 to-brand-500/5 rounded-xl border border-brand-500/10">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 bg-emerald-400 rounded-full pulse-dot"></div>
                <span class="text-xs font-medium text-emerald-400">Online</span>
            </div>
            <p class="text-xs text-white font-medium">{{ auth()->user()->name }}</p>
            <p class="text-[10px] text-slate-500 capitalize">{{ auth()->user()->role }}</p>
        </div>
    </div>
</aside>
