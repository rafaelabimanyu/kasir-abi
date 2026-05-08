@extends('layouts.master')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.4/dist/index.min.js"></script>
@endpush
@section('title', 'Pesan Internal')

@section('page-header')
<div class="flex items-center gap-2 mb-1">
    <h1 class="text-2xl font-bold text-white flex items-center gap-2">
        <i data-lucide="message-square" class="w-6 h-6 text-brand-400"></i>
        Pesan Internal
    </h1>
    <span class="ml-2 px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20 rounded-full">Live</span>
</div>
<p class="text-sm text-slate-400">Chat langsung antar pengguna sistem.</p>
@endsection

@section('content')
<div x-data="chatApp()" class="flex bg-dark-800 border border-dark-600/50 rounded-2xl overflow-hidden shadow-card" style="height:calc(100vh - 205px);min-height:520px;">

    {{-- SIDEBAR --}}
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

    {{-- CHAT AREA --}}
    <div class="flex-1 flex flex-col relative overflow-hidden">

        {{-- Empty state --}}
        <div x-show="!selectedUserId" class="absolute inset-0 flex flex-col items-center justify-center bg-dark-800 z-10">
            <div class="w-16 h-16 rounded-2xl bg-dark-700 border border-dark-600/50 flex items-center justify-center mb-3">
                <i data-lucide="message-circle-dashed" class="w-8 h-8 text-slate-600"></i>
            </div>
            <h3 class="text-base font-semibold text-white mb-1">Mulai Percakapan</h3>
            <p class="text-sm text-slate-500">Pilih kontak dari daftar kiri.</p>
        </div>

        {{-- Chat Header --}}
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
                <p class="text-xs" :class="onlineStatus[selectedUserId] ? 'text-emerald-400' : 'text-slate-500'"
                   x-text="onlineStatus[selectedUserId] ? 'Online' : 'Offline'"></p>
            </div>
            <div x-show="loading && messages.length > 0">
                <i data-lucide="loader-2" class="w-4 h-4 text-slate-600 animate-spin"></i>
            </div>
        </div>

        {{-- Typing indicator --}}
        <div x-show="partnerTyping && selectedUserId" style="display:none;"
             class="px-5 py-1.5 bg-dark-800 border-b border-dark-600/30">
            <p class="text-xs text-slate-500 flex items-center gap-1.5">
                <span class="flex gap-0.5">
                    <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </span>
                <span x-text="selectedUserName + ' sedang mengetik...'"></span>
            </p>
        </div>

        {{-- Messages --}}
        <div id="messages-container" x-show="selectedUserId" style="display:none;" class="flex-1 overflow-y-auto p-4 custom-scrollbar">
            <template x-if="loading && messages.length === 0">
                <div class="flex justify-center py-8">
                    <i data-lucide="loader-2" class="w-6 h-6 text-brand-500 animate-spin"></i>
                </div>
            </template>
            <template x-if="!loading && messages.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-slate-600">
                    <i data-lucide="messages-square" class="w-10 h-10 mb-3"></i>
                    <p class="text-sm">Belum ada pesan. Sapa sekarang! 👋</p>
                </div>
            </template>

            <div class="space-y-1">
                <template x-for="(msg, index) in messages" :key="msg.id">
                    <div>
                        <template x-if="shouldShowDate(index)">
                            <div class="flex items-center gap-3 my-4">
                                <div class="flex-1 h-px bg-dark-600/40"></div>
                                <span class="text-[11px] text-slate-600 px-3 py-1 bg-dark-700 rounded-full border border-dark-600/50" x-text="formatDate(msg.created_at)"></span>
                                <div class="flex-1 h-px bg-dark-600/40"></div>
                            </div>
                        </template>

                        {{-- Bubble kiri (lawan) --}}
                        <template x-if="msg.sender_id !== {{ auth()->id() }}">
                            <div class="flex items-end gap-2 mb-1 justify-start">
                                <template x-if="shouldShowAvatar(index)">
                                    <div class="w-7 h-7 rounded-full bg-dark-600 border border-dark-500/50 flex items-center justify-center shrink-0 mb-0.5">
                                        <span class="text-[11px] font-bold text-slate-300" x-text="selectedUserName.charAt(0).toUpperCase()"></span>
                                    </div>
                                </template>
                                <template x-if="!shouldShowAvatar(index)"><div class="w-7 shrink-0"></div></template>
                                <div class="max-w-[75%]">
                                    <div class="px-3 py-2 rounded-2xl rounded-bl-sm bg-dark-600 border border-dark-500/50 text-slate-200 shadow-sm">
                                        {{-- Attachment Preview (Left) --}}
                                        <template x-if="msg.attachment_path">
                                            <div class="mb-2 overflow-hidden rounded-xl bg-dark-700/50 border border-dark-500/30">
                                                <template x-if="msg.attachment_type === 'image'">
                                                    <img :src="'/storage/' + msg.attachment_path" class="max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity min-w-[150px]" @click="window.open('/storage/' + msg.attachment_path)">
                                                </template>
                                                <template x-if="msg.attachment_type === 'video'">
                                                    <video :src="'/storage/' + msg.attachment_path" controls class="max-w-full h-auto min-w-[200px]"></video>
                                                </template>
                                                <template x-if="msg.attachment_type === 'file'">
                                                    <a :href="'/storage/' + msg.attachment_path" target="_blank" class="flex items-center gap-3 p-3 hover:bg-dark-500 transition-colors">
                                                        <div class="w-10 h-10 rounded-lg bg-dark-800 flex items-center justify-center">
                                                            <i data-lucide="file-text" class="w-5 h-5 text-brand-400"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-medium text-white truncate" x-text="msg.attachment_path.split('/').pop()"></p>
                                                            <p class="text-[10px] text-slate-500">Dokumen</p>
                                                        </div>
                                                        <i data-lucide="download" class="w-4 h-4 text-slate-600"></i>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                        <p x-show="msg.message" class="text-[13.5px] whitespace-pre-wrap break-words leading-relaxed font-jakarta" x-text="msg.message"></p>
                                    </div>
                                    <p class="text-[10px] text-slate-600 mt-1 ml-1" x-text="formatTime(msg.created_at)"></p>
                                </div>
                            </div>
                        </template>

                        {{-- Bubble kanan (saya) --}}
                        <template x-if="msg.sender_id === {{ auth()->id() }}">
                            <div class="flex items-end gap-2 mb-1 justify-end">
                                <div class="max-w-[75%]">
                                    <div class="px-3 py-2 rounded-2xl rounded-br-sm bg-brand-600 border border-brand-500/50 text-white shadow-sm shadow-brand-500/10">
                                        {{-- Attachment Preview (Right) --}}
                                        <template x-if="msg.attachment_path">
                                            <div class="mb-2 overflow-hidden rounded-xl bg-black/10 border border-white/10">
                                                <template x-if="msg.attachment_type === 'image'">
                                                    <img :src="'/storage/' + msg.attachment_path" class="max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity min-w-[150px]" @click="window.open('/storage/' + msg.attachment_path)">
                                                </template>
                                                <template x-if="msg.attachment_type === 'video'">
                                                    <video :src="'/storage/' + msg.attachment_path" controls class="max-w-full h-auto min-w-[200px]"></video>
                                                </template>
                                                <template x-if="msg.attachment_type === 'file'">
                                                    <a :href="'/storage/' + msg.attachment_path" target="_blank" class="flex items-center gap-3 p-3 hover:bg-white/10 transition-colors">
                                                        <div class="w-10 h-10 rounded-lg bg-black/20 flex items-center justify-center">
                                                            <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-medium text-white truncate" x-text="msg.attachment_path.split('/').pop()"></p>
                                                            <p class="text-[10px] text-brand-200">Dokumen</p>
                                                        </div>
                                                        <i data-lucide="download" class="w-4 h-4 text-brand-200"></i>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                        <p x-show="msg.message" class="text-[13.5px] whitespace-pre-wrap break-words leading-relaxed font-jakarta" x-text="msg.message"></p>
                                    </div>
                                    <div class="flex items-center justify-end gap-1 mt-1 mr-1">
                                        <p class="text-[10px] text-slate-600" x-text="formatTime(msg.created_at)"></p>
                                        {{-- Read receipt status --}}
                                        <span x-show="msg.read_at" class="text-brand-400">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <path d="M20 6L9 17l-5-5"/><path d="M16 6l-11 11-5-5" class="opacity-50"/>
                                            </svg>
                                        </span>
                                        <span x-show="!msg.read_at" class="text-slate-600">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 6L9 17l-5-5"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- Input --}}
        <div x-show="selectedUserId" style="display:none;" class="p-3 bg-dark-800 border-t border-dark-600/50 shrink-0">
            {{-- Selected File Preview --}}
            <template x-if="selectedFilePreview">
                <div class="mb-3 p-2.5 bg-dark-700 border border-brand-500/30 rounded-2xl flex items-center gap-3 animate-fade-in">
                    <div class="w-12 h-12 rounded-xl bg-dark-600 overflow-hidden flex items-center justify-center shrink-0 border border-dark-500/50">
                        <template x-if="selectedFilePreview.type === 'image'">
                            <img :src="selectedFilePreview.url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="selectedFilePreview.type !== 'image'">
                            <i :data-lucide="selectedFilePreview.type === 'video' ? 'video' : 'file-text'" class="w-6 h-6 text-brand-400"></i>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-white truncate" x-text="selectedFilePreview.name"></p>
                        <p class="text-[10px] text-slate-500 uppercase" x-text="selectedFilePreview.type"></p>
                    </div>
                    <button @click="selectedFilePreview = null; $refs.attachmentInput.value = ''" class="w-8 h-8 flex items-center justify-center rounded-full bg-dark-600 text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </template>

            <form @submit.prevent="sendMessage()" class="flex items-end gap-2">
                <div class="flex items-center gap-1 shrink-0">
                    {{-- Attachment Button --}}
                    <button @click="$refs.attachmentInput.click()" type="button" class="w-10 h-10 flex items-center justify-center rounded-xl bg-dark-700 border border-dark-600 text-slate-400 hover:text-white hover:bg-dark-600 transition-all" title="Lampiran">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </button>
                    <input type="file" x-ref="attachmentInput" @change="handleFileSelect" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx">
                    
                    {{-- Emoji Button --}}
                    <button id="emoji-trigger" type="button" class="w-10 h-10 flex items-center justify-center rounded-xl bg-dark-700 border border-dark-600 text-slate-400 hover:text-white hover:bg-dark-600 transition-all" title="Emoji">
                        <i data-lucide="smile" class="w-5 h-5"></i>
                    </button>
                </div>

                <textarea id="chat-message-input" x-model="newMessage" x-ref="messageInput"
                    @keydown="handleKeydown($event)"
                    @input="autoResize($event.target); notifyTyping()"
                    placeholder="Ketik pesan..."
                    maxlength="1000" rows="1"
                    class="flex-1 bg-dark-700 border border-dark-600 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all resize-none overflow-y-auto font-jakarta"
                    style="max-height:120px;min-height:42px;"></textarea>

                <button type="submit" :disabled="(!newMessage.trim() && !selectedFilePreview) || sending"
                    class="w-10 h-10 flex items-center justify-center rounded-xl border transition-all shrink-0 focus:outline-none disabled:cursor-not-allowed"
                    :class="(newMessage.trim() || selectedFilePreview) && !sending ? 'bg-brand-600 hover:bg-brand-500 border-brand-500 text-white shadow-glow-sm' : 'bg-dark-700 border-dark-600 text-slate-600'">
                    <i x-show="!sending" data-lucide="send" class="w-4 h-4 ml-0.5"></i>
                    <i x-show="sending" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                </button>
            </form>
            <div class="flex justify-between mt-1 px-0.5">
                <p class="text-[10px] text-slate-700 font-medium">Shift+Enter = baris baru</p>
                <p class="text-[10px] tabular-nums" :class="newMessage.length > 900 ? 'text-amber-500' : 'text-slate-700'" x-text="newMessage.length + '/1000'"></p>
            </div>
        </div>
    </div>
</div>

{{-- Chat Toast Notifications --}}
<div id="chat-toasts" class="fixed bottom-6 right-6 z-[200] flex flex-col gap-2 pointer-events-none" style="max-width:320px;">
    <template x-for="toast in chatToasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openChatFromToast(toast.senderId)"
             class="pointer-events-auto flex items-start gap-3 bg-dark-700 border border-dark-500/60 rounded-2xl p-3.5 shadow-2xl cursor-pointer hover:border-brand-500/40 transition-colors">
            <div class="w-9 h-9 rounded-full bg-brand-500/20 border border-brand-500/30 flex items-center justify-center shrink-0">
                <span class="text-sm font-bold text-brand-300" x-text="toast.senderName.charAt(0).toUpperCase()"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white" x-text="toast.senderName"></p>
                <p class="text-xs text-slate-400 truncate mt-0.5" x-text="toast.preview"></p>
            </div>
            <i data-lucide="message-square" class="w-4 h-4 text-brand-400 shrink-0 mt-0.5"></i>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<style>
.custom-scrollbar::-webkit-scrollbar{width:4px}
.custom-scrollbar::-webkit-scrollbar-track{background:transparent}
.custom-scrollbar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.07);border-radius:10px}
.custom-scrollbar::-webkit-scrollbar-thumb:hover{background:rgba(255,255,255,.14)}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

.shadow-glow-sm {
    box-shadow: 0 0 15px -3px rgba(79, 70, 229, 0.4);
}
</style>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatApp', () => ({
        allUsers: @json($users),
        filteredUsers: [],
        searchQuery: '',
        selectedUserId: null,
        selectedUserName: '',
        messages: [],
        newMessage: '',
        selectedFilePreview: null,
        loading: false,
        sending: false,
        partnerTyping: false,
        unreadCounts: {},
        onlineStatus: {},
        chatToasts: [],
        lastMsgIds: {},
        pollInterval: null,
        bgInterval: null,
        typingTimeout: null,

        init() {
            this.filteredUsers = this.allUsers;
            this.allUsers.forEach(u => {
                this.onlineStatus[u.id] = u.last_activity_at
                    ? (new Date() - new Date(u.last_activity_at)) < 120000
                    : false;
            });
            this.fetchStatus();
            this.bgInterval = setInterval(() => this.fetchStatus(), 10000);
            
            // Initialize Emoji Picker
            this.$nextTick(() => {
                this.initEmojiPicker();
                lucide.createIcons();
            });

            this.$cleanup(() => { 
                this.stopPolling(); 
                clearInterval(this.bgInterval); 
            });
        },

        initEmojiPicker() {
            const picker = new EmojiButton({
                theme: 'dark',
                autoHide: false,
                position: 'top-start'
            });
            const trigger = document.querySelector('#emoji-trigger');
            if (trigger) {
                picker.on('emoji', selection => {
                    this.newMessage += selection.emoji;
                    this.$nextTick(() => {
                        this.autoResize(this.$refs.messageInput);
                        this.$refs.messageInput.focus();
                    });
                });
                trigger.addEventListener('click', () => picker.togglePicker(trigger));
            }
        },

        handleFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            if (file.size > 20 * 1024 * 1024) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'File terlalu besar (max 20MB).', type: 'error' } }));
                e.target.value = '';
                return;
            }

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => { 
                    this.selectedFilePreview = { type: 'image', url: e.target.result, name: file.name }; 
                    this.$nextTick(() => lucide.createIcons());
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                this.selectedFilePreview = { type: 'video', name: file.name };
                this.$nextTick(() => lucide.createIcons());
            } else {
                this.selectedFilePreview = { type: 'file', name: file.name };
                this.$nextTick(() => lucide.createIcons());
            }
        },

        filterUsers() {
            const q = this.searchQuery.toLowerCase().trim();
            this.filteredUsers = q
                ? this.allUsers.filter(u => u.name.toLowerCase().includes(q) || (u.email && u.email.toLowerCase().includes(q)))
                : this.allUsers;
            this.$nextTick(() => lucide.createIcons());
        },

        selectUser(user) {
            if (this.selectedUserId === user.id) return;
            this.selectedUserId = user.id;
            this.selectedUserName = user.name;
            this.messages = [];
            this.loading = true;
            this.partnerTyping = false;
            this.selectedFilePreview = null;
            this.stopPolling();
            this.doPoll(true);
            this.pollInterval = setInterval(() => this.doPoll(false), 3000);
            this.$nextTick(() => { if (this.$refs.messageInput) this.$refs.messageInput.focus(); });
        },

        async doPoll(initial = false) {
            if (!this.selectedUserId) return;
            try {
                const res = await fetch(`/chat/poll/${this.selectedUserId}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const data = await res.json();
                const prevLen = this.messages.length;
                this.messages = data.messages;
                this.partnerTyping = data.partner_typing;
                this.onlineStatus = Object.assign({}, this.onlineStatus, data.online_status);
                const counts = Object.assign({}, data.unread_counts);
                counts[this.selectedUserId] = 0;
                this.unreadCounts = counts;
                if (initial || data.messages.length > prevLen) {
                    this.scrollToBottom();
                    this.$nextTick(() => lucide.createIcons());
                }
            } catch(e) { console.error('[Poll]', e); }
            finally { if (initial) this.loading = false; }
        },

        async fetchStatus() {
            try {
                const res = await fetch('/chat/status', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                this.onlineStatus = Object.assign({}, this.onlineStatus, data.online_status);
                const newCounts = data.unread_counts;
                Object.entries(newCounts).forEach(([senderId, count]) => {
                    const id = parseInt(senderId);
                    const prev = this.unreadCounts[id] || 0;
                    if (count > prev && id !== this.selectedUserId) {
                        const user = this.allUsers.find(u => u.id === id);
                        if (user) this.showChatToast(id, user.name, 'Pesan baru dari ' + user.name);
                    }
                });
                if (!this.selectedUserId) {
                    this.unreadCounts = newCounts;
                } else {
                    const merged = Object.assign({}, newCounts);
                    merged[this.selectedUserId] = 0;
                    this.unreadCounts = merged;
                }
            } catch(e) { console.error('[Status]', e); }
        },

        async sendMessage() {
            const text = this.newMessage.trim();
            const file = this.$refs.attachmentInput.files[0];
            
            if (!text && !file || !this.selectedUserId || this.sending) return;
            
            const formData = new FormData();
            formData.append('receiver_id', this.selectedUserId);
            if (text) formData.append('message', text);
            if (file) formData.append('attachment', file);

            this.newMessage = '';
            this.$refs.attachmentInput.value = '';
            this.selectedFilePreview = null;
            this.sending = true;
            
            this.$nextTick(() => { if (this.$refs.messageInput) this.$refs.messageInput.style.height = 'auto'; });
            
            try {
                const res = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                if (res.ok) {
                    const msg = await res.json();
                    this.messages.push(msg);
                    this.scrollToBottom();
                    this.$nextTick(() => lucide.createIcons());
                } else {
                    this.newMessage = text;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Gagal mengirim pesan.', type: 'error' } }));
                }
            } catch(e) {
                this.newMessage = text;
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Kesalahan jaringan.', type: 'error' } }));
            } finally {
                this.sending = false;
                this.$nextTick(() => { if (this.$refs.messageInput) this.$refs.messageInput.focus(); });
            }
        },

        notifyTyping() {
            if (!this.selectedUserId) return;
            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                fetch('/chat/typing', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ receiver_id: this.selectedUserId })
                }).catch(() => {});
            }, 500);
        },

        handleKeydown(e) {
            if (e.key === 'Enter') {
                if (e.shiftKey) {
                    this.$nextTick(() => this.autoResize(this.$refs.messageInput));
                } else {
                    e.preventDefault();
                    this.sendMessage();
                }
            }
        },

        autoResize(el) {
            if (!el) return;
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        },

        stopPolling() {
            if (this.pollInterval) { clearInterval(this.pollInterval); this.pollInterval = null; }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('messages-container');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        showChatToast(senderId, senderName, preview) {
            const id = Date.now();
            this.chatToasts.push({ id, senderId, senderName, preview, visible: true });
            this.$nextTick(() => lucide.createIcons());
            setTimeout(() => {
                const t = this.chatToasts.find(x => x.id === id);
                if (t) t.visible = false;
                setTimeout(() => { this.chatToasts = this.chatToasts.filter(x => x.id !== id); }, 300);
            }, 5000);
        },

        openChatFromToast(senderId) {
            const user = this.allUsers.find(u => u.id === senderId);
            if (user) this.selectUser(user);
            this.chatToasts = [];
        },

        shouldShowDate(index) {
            if (index === 0) return true;
            const prev = this.messages[index - 1], curr = this.messages[index];
            return new Date(prev.created_at).toDateString() !== new Date(curr.created_at).toDateString();
        },

        shouldShowAvatar(index) {
            if (index === 0) return true;
            const prev = this.messages[index - 1], curr = this.messages[index];
            return prev.sender_id !== curr.sender_id;
        },

        formatDate(s) {
            if (!s) return '';
            const d = new Date(s), today = new Date(), yest = new Date();
            yest.setDate(yest.getDate() - 1);
            if (d.toDateString() === today.toDateString()) return 'Hari ini';
            if (d.toDateString() === yest.toDateString()) return 'Kemarin';
            return d.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
        },

        formatTime(s) {
            if (!s) return '';
            return new Date(s).toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
        }
    }));
});
</script>
@endpush
