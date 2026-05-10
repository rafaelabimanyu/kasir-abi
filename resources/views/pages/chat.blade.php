@extends('layouts.master')
@push('scripts')
    <script src="https://unpkg.com/picmo@latest/dist/umd/index.js"></script>
@endpush
@section('title', 'Pesan Internal')

@section('page-header')
    <div class="flex items-center gap-2 mb-1">
        <h1 class="text-2xl font-bold text-white flex items-center gap-2">
            <i data-lucide="message-square" class="w-6 h-6 text-brand-400"></i>
            Pesan Internal
        </h1>
        <span
            class="ml-2 px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20 rounded-full">Live</span>
    </div>
    <p class="text-sm text-slate-400">Chat langsung antar pengguna sistem.</p>
@endsection

@section('content')
    <div x-data="chatApp()" class="flex flex-row h-full bg-dark-800 border border-dark-600/50 rounded-2xl overflow-hidden shadow-card" style="height:calc(100vh - 205px);min-height:520px;">

        {{-- 1. Sidebar Kontak --}}
        <div class="w-[300px] shrink-0 h-full border-r border-dark-600/50">
            @include('pages.chat.partials.contact-list')
        </div>

        {{-- 2. Area Chat Utama --}}
        <div class="flex-1 flex flex-col h-full relative overflow-hidden">

            {{-- Empty state --}}
            <div x-show="!selectedUserId" class="absolute inset-0 flex flex-col items-center justify-center bg-dark-800 z-10">
                <div class="w-16 h-16 rounded-2xl bg-dark-700 border border-dark-600/50 flex items-center justify-center mb-3">
                    <i data-lucide="message-circle-dashed" class="w-8 h-8 text-slate-600"></i>
                </div>
                <h3 class="text-base font-semibold text-white mb-1">Mulai Percakapan</h3>
                <p class="text-sm text-slate-500">Pilih kontak dari daftar kiri.</p>
            </div>

            {{-- Header Chat --}}
            @include('pages.chat.partials.chat-header')

            {{-- Log Pesan --}}
            @include('pages.chat.partials.chat-messages')

            {{-- Input Pesan --}}
            @include('pages.chat.partials.chat-input')
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

    .emoji-picker-popup {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        border-radius: 12px;
        overflow: hidden;
        border: 1px border rgba(255,255,255,0.1);
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
            pickerInitialized: false,

            init() {
                this.filteredUsers = this.allUsers;
                this.allUsers.forEach(u => {
                    this.onlineStatus[u.id] = u.last_activity_at
                        ? (new Date() - new Date(u.last_activity_at)) < 120000
                        : false;
                });
                this.fetchStatus();
                this.bgInterval = setInterval(() => this.fetchStatus(), 10000);

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
                this.$nextTick(() => {
                    const trigger = document.querySelector('#emoji-picker-button');
                    const container = document.querySelector('#emoji-picker-container');

                    if (trigger && container && !this.pickerInitialized) {
                        try {
                            const picker = picmo.createPicker({
                                theme: 'dark',
                                showSearch: false,
                                showVariants: false,
                                showPreview: false,
                                width: '280px',
                                height: '320px'
                            });

                            container.appendChild(picker.domElement);
                            this.pickerInitialized = true;

                            picker.addEventListener('emoji:select', event => {
                                this.newMessage += event.emoji;
                                this.$nextTick(() => {
                                    if (this.$refs.messageInput) {
                                        this.autoResize(this.$refs.messageInput);
                                        this.$refs.messageInput.focus();
                                    }
                                });
                            });

                            trigger.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                const isHidden = container.style.display === 'none';
                                container.style.display = isHidden ? 'block' : 'none';
                            });

                            document.addEventListener('click', (e) => {
                                if (!container.contains(e.target) && !trigger.contains(e.target)) {
                                    container.style.display = 'none';
                                }
                            });

                            console.log('[Chat] Emoji Picker Ready');
                        } catch(e) {
                            console.error('[Chat] Failed to init Picmo:', e);
                        }
                    }
                });
            },

            handleFileSelect(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (file.size > 20 * 1024 * 1024) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'File terlalu besar (max 20MB).', type: 'error' } }));
                    e.target.value = '';
                    return;
                }

                const type = file.type.startsWith('image/') ? 'image' : (file.type.startsWith('video/') ? 'video' : 'file');

                if (type === 'image') {
                    const reader = new FileReader();
                    reader.onload = (e) => { 
                        this.selectedFilePreview = { type: 'image', url: e.target.result, name: file.name }; 
                        this.$nextTick(() => lucide.createIcons());
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.selectedFilePreview = { type: type, name: file.name };
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

                // Re-init picker when user is selected to ensure element visibility
                this.initEmojiPicker();

                // Initial fetch
                this.doPoll(true).then(() => {
                    this.pollInterval = setInterval(() => this.doPoll(false), 3000);
                });

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
                    const prevMsgCount = this.messages.length;
                    const lastMsgId = prevMsgCount > 0 ? this.messages[prevMsgCount - 1].id : null;
                    const newLastMsgId = data.messages.length > 0 ? data.messages[data.messages.length - 1].id : null;

                    this.messages = data.messages;
                    this.partnerTyping = data.partner_typing;
                    this.onlineStatus = Object.assign({}, this.onlineStatus, data.online_status);

                    const counts = Object.assign({}, data.unread_counts);
                    counts[this.selectedUserId] = 0;
                    this.unreadCounts = counts;

                    if (initial || (newLastMsgId !== lastMsgId)) {
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

            async sendMessage(event) {
                if (!this.selectedUserId || this.sending) return;

                const text = this.newMessage.trim();
                const attachment = this.$refs.attachmentInput.files[0];

                if (!text && !attachment) return;

                this.sending = true;

                const formData = new FormData();
                formData.append('receiver_id', this.selectedUserId);
                if (text) formData.append('message', text);
                if (attachment) formData.append('attachment', attachment);

                // Simpan state lama untuk rollback jika gagal
                const oldMessage = this.newMessage;
                const oldFilePreview = this.selectedFilePreview;

                // Clear input segera (Optimistic UI)
                this.newMessage = '';
                this.selectedFilePreview = null;
                this.$refs.attachmentInput.value = '';
                if (this.$refs.messageInput) this.$refs.messageInput.style.height = 'auto';

                try {
                    const res = await fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (res.ok) {
                        this.messages.push(data);
                        this.scrollToBottom();
                        this.$nextTick(() => {
                            lucide.createIcons();
                            // Reset scroll height again to be sure
                            if (this.$refs.messageInput) this.$refs.messageInput.style.height = 'auto';
                        });
                    } else {
                        // Rollback
                        this.newMessage = oldMessage;
                        this.selectedFilePreview = oldFilePreview;
                        window.dispatchEvent(new CustomEvent('notify', { 
                            detail: { message: data.message || 'Gagal mengirim pesan.', type: 'error' } 
                        }));
                    }
                } catch(e) {
                    this.newMessage = oldMessage;
                    this.selectedFilePreview = oldFilePreview;
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Kesalahan jaringan atau server.', type: 'error' } 
                    }));
                } finally {
                    this.sending = false;
                    this.$nextTick(() => { if (this.$refs.messageInput) this.$refs.messageInput.focus(); });
                }
            },

            async deleteMessage(msgId, type) {
                const confirmMsg = type === 'all' 
                    ? 'Hapus pesan ini untuk semua orang?' 
                    : 'Hapus pesan ini untuk Anda saja?';

                if (!confirm(confirmMsg)) return;

                try {
                    const res = await fetch(`/chat/delete/${msgId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ type })
                    });

                    if (res.ok) {
                        if (type === 'all') {
                            const msg = this.messages.find(m => m.id === msgId);
                            if (msg) {
                                msg.is_deleted_for_all = true;
                                msg.message = 'Pesan ini telah dihapus';
                                msg.attachment_path = null;
                                msg.attachment_type = null;
                            }
                        } else {
                            this.messages = this.messages.filter(m => m.id !== msgId);
                        }
                        this.$nextTick(() => lucide.createIcons());
                    } else {
                        const err = await res.json();
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: err.message || 'Gagal menghapus pesan.', type: 'error' } }));
                    }
                } catch(e) {
                    console.error('[Delete]', e);
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Kesalahan jaringan.', type: 'error' } }));
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
                    if (el) {
                        el.scrollTo({
                            top: el.scrollHeight,
                            behavior: 'smooth'
                        });
                    }
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
