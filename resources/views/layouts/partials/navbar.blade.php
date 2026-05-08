{{-- Top Navbar --}}
<header class="sticky top-0 z-30 h-16 bg-dark-800/80 backdrop-blur-xl border-b border-dark-600/50 shrink-0">
    <div class="flex items-center justify-between h-full px-4 md:px-6">
        {{-- Left: Mobile Toggle + Search --}}
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-xl text-slate-400 hover:text-white hover:bg-dark-600 transition-colors cursor-pointer active:scale-95">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <div class="relative" x-data="globalSearch()">
                <div class="hidden md:flex items-center gap-2 bg-dark-700 rounded-xl px-3 py-2 border border-dark-600/50 focus-within:border-brand-500/50 focus-within:shadow-glow transition-all w-72 lg:w-96">
                    <i data-lucide="search" class="w-4 h-4 text-slate-500" x-show="!loading"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 text-brand-500 animate-spin" x-show="loading" style="display: none;"></i>
                    <input type="text" x-model="query" @input.debounce.300ms="fetchResults" @keydown.down.prevent="moveDown" @keydown.up.prevent="moveUp" @keydown.enter.prevent="selectCurrent" @keydown.escape="isOpen = false" @focus="if(query.length >= 2) isOpen = true" @click.outside="isOpen = false" placeholder="Cari produk, transaksi, user..." class="bg-transparent text-sm text-slate-300 placeholder-slate-500 outline-none w-full">
                    <kbd class="hidden lg:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-medium text-slate-500 bg-dark-600 rounded-md border border-dark-500" x-show="!query">⌘K</kbd>
                    <button @click="clear()" x-show="query" class="text-slate-500 hover:text-slate-300" style="display: none;"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
                
                {{-- Dropdown Results --}}
                <div x-show="isOpen" x-transition.opacity.duration.200ms style="display: none;" class="absolute top-full left-0 mt-2 w-full bg-dark-800 border border-dark-600/50 rounded-2xl shadow-2xl overflow-hidden z-50 max-h-96 overflow-y-auto">
                    <template x-if="results.length > 0">
                        <ul class="py-2">
                            <template x-for="(item, index) in results" :key="index">
                                <li>
                                    <a :href="item.url" class="flex items-center gap-3 px-4 py-2 hover:bg-dark-700/50 transition-colors" :class="{'bg-dark-700': selectedIndex === index}">
                                        <div class="w-8 h-8 rounded-lg bg-dark-600 flex items-center justify-center shrink-0">
                                            <i :data-lucide="item.icon" class="w-4 h-4 text-brand-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white truncate" x-text="item.title"></p>
                                            <p class="text-xs text-slate-400 truncate" x-text="item.subtitle"></p>
                                        </div>
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded bg-dark-600 text-slate-400 uppercase" x-text="item.type"></span>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="results.length === 0 && query.length >= 2 && !loading">
                        <div class="py-8 text-center">
                            <i data-lucide="search-x" class="w-8 h-8 text-slate-500 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-400">Tidak ada hasil untuk "<span x-text="query" class="text-white"></span>"</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-2">
            {{-- Real-time Clock --}}
            <div x-data="realTimeClock()" class="hidden xl:flex items-center gap-3 px-4 py-2 bg-dark-700/50 border border-dark-600/50 rounded-xl mr-2 shadow-sm">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-brand-500/10 text-brand-400 border border-brand-500/20 shadow-glow-sm">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <span class="text-[13px] font-semibold text-slate-200 font-jakarta tracking-wide whitespace-nowrap" x-text="currentTime"></span>
            </div>

            {{-- Quick Chat --}}
            <div x-data="navChatBadge()" class="relative">
                <a href="{{ route('chat') }}" class="flex items-center justify-center w-10 h-10 rounded-xl text-slate-400 hover:text-brand-400 hover:bg-dark-600 transition-colors" title="Pesan Internal">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </a>
                <span x-show="totalUnread > 0"
                      class="absolute -top-1 -right-1 min-w-[17px] h-[17px] flex items-center justify-center bg-brand-500 text-white text-[9px] font-bold rounded-full px-1 pointer-events-none"
                      x-text="totalUnread > 9 ? '9+' : totalUnread"></span>
            </div>

            {{-- Quick POS --}}
            <a href="{{ route('pos') }}" class="hidden sm:flex items-center gap-2 px-3 py-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-medium rounded-xl transition-all hover:shadow-glow">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Transaksi Baru</span>
            </a>

            <div class="w-px h-6 bg-dark-600 mx-1"></div>

            {{-- User Menu --}}
            <div class="relative">
                <button id="user-menu-btn" class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-dark-600 transition-colors">
                    <div class="w-8 h-8 bg-gradient-to-br {{ auth()->user()->isAdmin() ? 'from-brand-500 to-purple-600' : 'from-emerald-500 to-teal-600' }} rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-glow">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-white leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-slate-500 leading-tight capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <i data-lucide="chevron-down" class="hidden md:block w-4 h-4 text-slate-500"></i>
                </button>

                {{-- User Dropdown --}}
                <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-dark-700 border border-dark-600/50 rounded-2xl shadow-card overflow-hidden dropdown-enter">
                    <div class="px-4 py-3 border-b border-dark-600/50">
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="py-1.5">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-dark-600/50 transition-colors">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span>Profil Saya</span>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('pengaturan') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-dark-600/50 transition-colors">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            <span>Pengaturan</span>
                        </a>
                        @endif
                    </div>
                    <div class="border-t border-dark-600/50 py-1.5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/5 transition-colors cursor-pointer">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
