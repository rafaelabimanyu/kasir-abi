{{-- CHAT HEADER --}}
<div x-show="selectedUserId" style="display:none;" class="px-4 py-3 border-b border-dark-600/50 flex items-center gap-3 bg-dark-800 shrink-0">
    <div class="relative shrink-0">
        <div class="w-9 h-9 rounded-full bg-brand-500/20 border border-brand-500/30 flex items-center justify-center">
            <span class="text-sm font-bold text-brand-300" x-text="selectedUserName ? selectedUserName.charAt(0).toUpperCase() : ''"></span>
        </div>
        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-dark-700"
              :class="onlineStatus[selectedUserId] ? 'bg-emerald-400' : 'bg-slate-600'"></span>
    </div>
    <div class="flex-1">
        <h3 class="text-sm font-semibold text-white" x-text="selectedUserName"></h3>
        <div class="flex items-center gap-2">
            <p class="text-xs" :class="onlineStatus[selectedUserId] ? 'text-emerald-400' : 'text-slate-500'"
               x-text="onlineStatus[selectedUserId] ? 'Online' : 'Offline'"></p>
            <template x-if="partnerTyping">
                <span class="text-[10px] text-brand-400 animate-pulse">sedang mengetik...</span>
            </template>
        </div>
    </div>
    <div x-show="loading && messages.length > 0">
        <i data-lucide="loader-2" class="w-4 h-4 text-slate-600 animate-spin"></i>
    </div>
</div>
