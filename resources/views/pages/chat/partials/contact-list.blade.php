{{-- SIDEBAR KONTAK --}}
<div class="flex flex-col border-r border-dark-600/50 bg-dark-700/40 shrink-0" style="width:280px;min-width:220px;">
    <div class="p-3 border-b border-dark-600/50 space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Kontak</h2>
            <span class="text-xs text-slate-600" x-text="filteredUsers.length + ' user'"></span>
        </div>
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500 pointer-events-none"></i>
            <input type="text" x-model="searchQuery" @input="filterUsers()" placeholder="Cari nama / email..."
                class="w-full bg-dark-600 border border-dark-500 rounded-xl pl-9 pr-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 transition-all">
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-2 space-y-0.5 custom-scrollbar">
        <template x-if="filteredUsers.length === 0">
            <div class="flex flex-col items-center py-10 text-slate-600">
                <i data-lucide="search-x" class="w-7 h-7 mb-2"></i>
                <p class="text-xs">Tidak ditemukan</p>
            </div>
        </template>
        <template x-for="user in filteredUsers" :key="user.id">
            <button @click="selectUser(user)"
                :class="selectedUserId === user.id ? 'bg-brand-500/10 border-brand-500/40 text-white' : 'border-transparent text-slate-400 hover:bg-dark-600/60 hover:text-white'"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border transition-all text-left group focus:outline-none cursor-pointer">
                <div class="relative shrink-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow-inner"
                         :class="selectedUserId === user.id ? 'bg-brand-500/25 border border-brand-500/40 text-brand-300' : 'bg-dark-600 border border-dark-500/50'">
                        <span x-text="user.name.charAt(0).toUpperCase()"></span>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-dark-700"
                          :class="onlineStatus[user.id] ? 'bg-emerald-400' : 'bg-slate-600'"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium truncate" x-text="user.name"></p>
                        <span x-show="unreadCounts[user.id]"
                              class="ml-1 min-w-[18px] h-[18px] flex items-center justify-center bg-brand-500 text-white text-[10px] font-bold rounded-full px-1 shrink-0"
                              x-text="unreadCounts[user.id] > 9 ? '9+' : unreadCounts[user.id]"></span>
                    </div>
                    <p class="text-[11px] truncate" :class="onlineStatus[user.id] ? 'text-emerald-400' : 'text-slate-600'"
                       x-text="onlineStatus[user.id] ? 'Online' : 'Offline'"></p>
                </div>
            </button>
        </template>
    </div>

    <div class="p-3 border-t border-dark-600/50">
        <div class="flex items-center gap-2 px-2">
            <div class="w-8 h-8 rounded-full bg-brand-500/20 border border-brand-500/30 flex items-center justify-center shrink-0">
                <span class="text-xs font-bold text-brand-300">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-emerald-400">● Online</p>
            </div>
        </div>
    </div>
</div>
