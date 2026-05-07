<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Kasir Abi — Sistem Kasir Modern untuk Bisnis Anda">

    <title>@yield('title', 'Dashboard') — Kasir Abi</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="antialiased min-h-screen overflow-x-hidden" x-data="globalShortcuts()">
    <div x-data="{ sidebarOpen: false }" 
         x-init="$watch('sidebarOpen', val => { if(window.innerWidth < 1024) document.body.style.overflow = val ? 'hidden' : '' })" 
         @resize.window="if(window.innerWidth >= 1024) { sidebarOpen = false; document.body.style.overflow = '' }"
         class="flex min-h-screen relative w-full">
         
        {{-- Sidebar Overlay (Mobile) --}}
        <div x-show="sidebarOpen" 
             x-transition.opacity.duration.300ms
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm"
             style="display: none;"></div>

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
            {{-- Navbar --}}
            @include('layouts.partials.navbar')

            {{-- Page Content --}}
            <main class="flex-1 p-3 sm:p-4 lg:p-6 w-full max-w-full overflow-x-hidden">
                {{-- Page Header --}}
                @hasSection('page-header')
                    <div class="mb-6 md:mb-8 animate-fade-in">
                        @yield('page-header')
                    </div>
                @endif



                {{-- Main Content Area --}}
                <div class="animate-fade-in">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            <footer class="px-6 py-4 border-t border-dark-700/50">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-slate-500">
                    <div class="flex items-center flex-wrap gap-2">
                        <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('store_name', 'Kasir Abi') }}. All rights reserved.</p>
                        <span class="hidden sm:inline-block mx-1">|</span>
                        <button @click="showShortcutModal = true" class="hover:text-white transition-colors flex items-center gap-1.5 focus:outline-none cursor-pointer">
                            <i data-lucide="keyboard" class="w-3.5 h-3.5"></i> Daftar Shortcut
                        </button>
                    </div>
                    <p class="flex items-center gap-1.5">
                        Made with <span class="text-red-400">♥</span> for your business
                    </p>
                </div>
            </footer>
        </div>
    </div>

    {{-- Global Shortcut Modal --}}
    <div x-show="showShortcutModal" x-transition.opacity.duration.300ms class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showShortcutModal = false" x-show="showShortcutModal" x-transition.scale.origin.bottom.duration.300ms class="bg-dark-800 border border-dark-600 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col max-h-full">
            <div class="flex items-center justify-between p-5 border-b border-dark-600/50">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="keyboard" class="w-5 h-5 text-brand-400"></i> Keyboard Shortcuts
                </h3>
                <button @click="showShortcutModal = false" class="text-slate-400 hover:text-white transition-colors cursor-pointer focus:outline-none">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-6">
                <div>
                    <h4 class="text-sm font-semibold text-slate-300 mb-3 uppercase tracking-wider">Global (Semua Halaman)</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Buka / Tutup Daftar Shortcut</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + /</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Fokus Global Search</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + K</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Ke Halaman Dashboard</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + H</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Ke Halaman POS</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + P</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Ke Halaman Laporan</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + L</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Ke User Management</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + U</kbd></div>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-300 mb-3 uppercase tracking-wider">Halaman POS</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Fokus Pencarian Produk</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">F2</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Proses Pembayaran</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Enter</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Reset / Void Keranjang</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">ESC</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Simpan Transaksi (Paksa)</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + Shift + S</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Hold Transaksi</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + Shift + D</kbd></div>
                        <div class="flex justify-between items-center text-sm"><span class="text-slate-400">Kosongkan Keranjang</span><kbd class="px-2 py-1 bg-dark-600 text-slate-300 rounded border border-dark-500 font-mono text-xs shadow-sm">Ctrl + Backspace</kbd></div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-dark-600/50 bg-dark-800/80 text-center">
                <a href="{{ url('shortcuts') }}" class="text-brand-400 hover:text-brand-300 text-sm font-medium transition-colors">Lihat Panduan Lengkap &rarr;</a>
            </div>
        </div>
    </div>

    {{-- Initialize Lucide Icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>

    {{-- Global Toast Notification --}}
    <div x-data="globalToast()" 
         @notify.window="showToast($event.detail.message, $event.detail.type)"
         class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none"
         style="min-width: 320px;">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible"
                 x-transition:enter="toast-enter"
                 x-transition:leave="toast-leave"
                 class="pointer-events-auto flex items-start gap-3 px-4 py-3.5 rounded-xl shadow-card backdrop-blur-md border relative overflow-hidden"
                 :class="{
                     'bg-emerald-500/15 border-emerald-500/30 text-emerald-400': toast.type === 'success',
                     'bg-red-500/15 border-red-500/30 text-red-400': toast.type === 'error',
                     'bg-amber-500/15 border-amber-500/30 text-amber-400': toast.type === 'warning',
                 }">
                
                {{-- Icon --}}
                <div class="shrink-0 mt-0.5">
                    <i x-show="toast.type === 'success'" data-lucide="check-circle" class="w-5 h-5"></i>
                    <i x-show="toast.type === 'error'" data-lucide="alert-circle" class="w-5 h-5"></i>
                    <i x-show="toast.type === 'warning'" data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white" x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'error' ? 'Kesalahan' : 'Peringatan')"></p>
                    <p class="text-xs opacity-90 mt-0.5 leading-relaxed" x-text="toast.message"></p>
                </div>

                {{-- Close Button --}}
                <button @click="removeToast(toast.id)" class="shrink-0 opacity-50 hover:opacity-100 transition-opacity">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>

                {{-- Progress Bar --}}
                <div class="absolute bottom-0 left-0 h-0.5 bg-current opacity-30" 
                     :style="`width: ${toast.progress}%; transition: width 100ms linear;`"></div>
            </div>
        </template>
    </div>

    {{-- Initialize Alpine Global Toast --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('globalToast', () => ({
                toasts: [],
                showToast(message, type = 'success') {
                    const id = Date.now();
                    const toast = { id, message, type, visible: true, progress: 100 };
                    this.toasts.push(toast);
                    
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });

                    let interval = setInterval(() => {
                        const t = this.toasts.find(t => t.id === id);
                        if(t) {
                            t.progress -= (100 / 30); // 3 seconds = 30 * 100ms
                            if(t.progress <= 0) {
                                clearInterval(interval);
                                this.removeToast(id);
                            }
                        } else {
                            clearInterval(interval);
                        }
                    }, 100);
                },
                removeToast(id) {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) {
                        toast.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300); // match leave transition duration
                    }
                },
                init() {
                    @if(session('success'))
                        setTimeout(() => this.showToast("{!! addslashes(session('success')) !!}", 'success'), 100);
                    @endif
                    @if(session('error'))
                        setTimeout(() => this.showToast("{!! addslashes(session('error')) !!}", 'error'), 100);
                    @endif
                    @if(session('warning'))
                        setTimeout(() => this.showToast("{!! addslashes(session('warning')) !!}", 'warning'), 100);
                    @endif
                }
            }));
        });

        function globalSearch() {
            return {
                query: '',
                isOpen: false,
                loading: false,
                results: [],
                selectedIndex: -1,
                abortController: null,
                
                async fetchResults() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.isOpen = false;
                        return;
                    }
                    
                    this.loading = true;
                    this.isOpen = true;
                    
                    if (this.abortController) {
                        this.abortController.abort();
                    }
                    this.abortController = new AbortController();
                    
                    try {
                        const response = await fetch(`/search/global?q=${encodeURIComponent(this.query)}`, {
                            signal: this.abortController.signal,
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        this.results = data;
                        this.selectedIndex = -1;
                        this.$nextTick(() => { lucide.createIcons(); });
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error('Search error:', error);
                        }
                    } finally {
                        this.loading = false;
                    }
                },
                
                clear() {
                    this.query = '';
                    this.results = [];
                    this.isOpen = false;
                    this.selectedIndex = -1;
                },
                
                moveDown() {
                    if (!this.isOpen || this.results.length === 0) return;
                    this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
                },
                
                moveUp() {
                    if (!this.isOpen || this.results.length === 0) return;
                    this.selectedIndex = this.selectedIndex <= 0 ? this.results.length - 1 : this.selectedIndex - 1;
                },
                
                selectCurrent() {
                    if (this.selectedIndex >= 0 && this.selectedIndex < this.results.length) {
                        window.location.href = this.results[this.selectedIndex].url;
                    }
                }
            }
        }
    </script>

    {{-- Initialize Alpine Global Shortcuts --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('globalShortcuts', () => ({
                showShortcutModal: false,
                init() {
                    window.addEventListener('keydown', (e) => {
                        const activeEl = document.activeElement;
                        const isInput = activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA' || activeEl.tagName === 'SELECT' || activeEl.isContentEditable;
                        
                        // Ctrl + /
                        if (e.ctrlKey && e.key === '/') {
                            e.preventDefault();
                            this.showShortcutModal = !this.showShortcutModal;
                            return;
                        }

                        // Ignore global shortcuts if typing in input
                        if (isInput && e.key !== 'Escape') return;

                        // ESC (close modal)
                        if (e.key === 'Escape' && this.showShortcutModal) {
                            e.preventDefault();
                            this.showShortcutModal = false;
                            return;
                        }

                        // Global Navigations
                        if (e.ctrlKey && !e.shiftKey) {
                            switch(e.key.toLowerCase()) {
                                case 'k':
                                    e.preventDefault();
                                    const searchInput = document.querySelector('[x-data="globalSearch()"] input');
                                    if (searchInput) {
                                        searchInput.focus();
                                    }
                                    break;
                                case 'h':
                                    e.preventDefault();
                                    window.location.href = '{{ route("dashboard") }}';
                                    break;
                                case 'p':
                                    e.preventDefault();
                                    window.location.href = '{{ route("pos") }}';
                                    break;
                                case 'l':
                                    e.preventDefault();
                                    window.location.href = '{{ route("laporan") }}';
                                    break;
                                case 'u':
                                    e.preventDefault();
                                    window.location.href = '{{ url("users") }}';
                                    break;
                            }
                        }
                    });
                }
            }));
        });
    </script>

    {{-- Navbar Chat Badge --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('navChatBadge', () => ({
                totalUnread: 0,
                interval: null,
                init() {
                    @if(!request()->routeIs('chat*'))
                    this.fetchUnread();
                    this.interval = setInterval(() => this.fetchUnread(), 10000);
                    this.$cleanup(() => clearInterval(this.interval));
                    @endif
                },
                async fetchUnread() {
                    try {
                        const res = await fetch('/chat/status', { headers: { 'Accept': 'application/json' } });
                        if (res.ok) {
                            const data = await res.json();
                            this.totalUnread = data.total_unread || 0;
                        }
                    } catch(e) {}
                }
            }));
        });
    </script>

    @stack('scripts')

</body>
</html>
