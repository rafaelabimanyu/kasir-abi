{{-- AREA PESAN --}}
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
                </template                {{-- Bubble kiri (lawan) --}}
                <template x-if="msg.sender_id !== {{ auth()->id() }}">
                    <div class="flex items-end gap-2 mb-1 justify-start group">
                        <template x-if="shouldShowAvatar(index)">
                            <div class="w-8 h-8 rounded-full bg-dark-600 border border-dark-500/50 flex items-center justify-center shrink-0 mb-0.5">
                                <span class="text-[11px] font-bold text-slate-300" x-text="selectedUserName.charAt(0).toUpperCase()"></span>
                            </div>
                        </template>
                        <template x-if="!shouldShowAvatar(index)"><div class="w-8 shrink-0"></div></template>
                        <div class="max-w-[70%] relative">
                            <div class="px-3.5 py-2 rounded-2xl rounded-bl-sm bg-dark-600 border border-dark-500/50 text-slate-200 shadow-sm"
                                :class="msg.is_deleted_for_all ? 'opacity-50 italic text-slate-400' : ''">
                                {{-- Attachment Preview (Left) --}}
                                <template x-if="msg.attachment_path && !msg.is_deleted_for_all">
                                    <div class="mb-2 overflow-hidden rounded-xl bg-dark-700/50 border border-dark-500/30">
                                        <template x-if="msg.attachment_type === 'image'">
                                            <img :src="'/' + msg.attachment_path" class="max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity min-w-[120px] max-h-[200px] object-cover" @click="window.open('/' + msg.attachment_path)">
                                        </template>
                                        <template x-if="msg.attachment_type === 'video'">
                                            <video :src="'/' + msg.attachment_path" controls class="max-w-full h-auto min-w-[180px] max-h-[200px]"></video>
                                        </template>
                                        <template x-if="msg.attachment_type === 'file'">
                                            <a :href="'/' + msg.attachment_path" target="_blank" class="flex items-center gap-3 p-2.5 hover:bg-dark-500 transition-colors">
                                                <div class="w-9 h-9 rounded-lg bg-dark-800 flex items-center justify-center">
                                                    <i data-lucide="file-text" class="w-4 h-4 text-brand-400"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[11px] font-medium text-white truncate" x-text="msg.attachment_path.split('/').pop()"></p>
                                                    <p class="text-[9px] text-slate-500">Dokumen</p>
                                                </div>
                                                <i data-lucide="download" class="w-3.5 h-3.5 text-slate-600"></i>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                                <p x-show="msg.message" class="text-[13px] whitespace-pre-wrap break-words leading-snug font-jakarta" 
                                   :class="msg.is_deleted_for_all ? 'flex items-center gap-1.5' : ''">
                                    <i x-show="msg.is_deleted_for_all" data-lucide="ban" class="w-3.5 h-3.5 text-slate-500"></i>
                                    <span x-text="msg.message"></span>
                                </p>
                            </div>
                            <p class="text-[9px] text-slate-600 mt-0.5 ml-1" x-text="formatTime(msg.created_at)"></p>
                            
                            {{-- Action Buttons (Left) --}}
                            <div x-show="!msg.is_deleted_for_all" class="absolute -right-8 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity flex items-center">
                                <button @click="deleteMessage(msg.id, 'me')" class="p-1 rounded-lg bg-dark-700 border border-dark-600 text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Hapus untuk saya">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Bubble kanan (saya) --}}
                <template x-if="msg.sender_id === {{ auth()->id() }}">
                    <div class="flex items-end gap-2 mb-1 justify-end group">
                        <div class="max-w-[70%] relative">
                            <div class="px-3.5 py-2 rounded-2xl rounded-br-sm text-white shadow-sm"
                                :class="msg.is_deleted_for_all ? 'bg-dark-700 border border-dark-600 opacity-50 italic text-slate-400 shadow-none' : 'bg-brand-600 border border-brand-500/50 shadow-brand-500/10'">
                                {{-- Attachment Preview (Right) --}}
                                <template x-if="msg.attachment_path && !msg.is_deleted_for_all">
                                    <div class="mb-2 overflow-hidden rounded-xl bg-black/10 border border-white/10">
                                        <template x-if="msg.attachment_type === 'image'">
                                            <img :src="'/' + msg.attachment_path" class="max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity min-w-[120px] max-h-[200px] object-cover" @click="window.open('/' + msg.attachment_path)">
                                        </template>
                                        <template x-if="msg.attachment_type === 'video'">
                                            <video :src="'/' + msg.attachment_path" controls class="max-w-full h-auto min-w-[180px] max-h-[200px]"></video>
                                        </template>
                                        <template x-if="msg.attachment_type === 'file'">
                                            <a :href="'/' + msg.attachment_path" target="_blank" class="flex items-center gap-3 p-2.5 hover:bg-white/10 transition-colors">
                                                <div class="w-9 h-9 rounded-lg bg-black/20 flex items-center justify-center">
                                                    <i data-lucide="file-text" class="w-4 h-4 text-white"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[11px] font-medium text-white truncate" x-text="msg.attachment_path.split('/').pop()"></p>
                                                    <p class="text-[9px] text-brand-200">Dokumen</p>
                                                </div>
                                                <i data-lucide="download" class="w-3.5 h-3.5 text-brand-200"></i>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                                <p x-show="msg.message" class="text-[13px] whitespace-pre-wrap break-words leading-snug font-jakarta"
                                   :class="msg.is_deleted_for_all ? 'flex items-center gap-1.5' : ''">
                                    <i x-show="msg.is_deleted_for_all" data-lucide="ban" class="w-3.5 h-3.5 text-slate-500"></i>
                                    <span x-text="msg.message"></span>
                                </p>
                            </div>
                            <div class="flex items-center justify-end gap-1 mt-0.5 mr-1">
                                <p class="text-[9px] text-slate-600" x-text="formatTime(msg.created_at)"></p>
                                {{-- Status pesan --}}
                                <template x-if="!msg.is_deleted_for_all">
                                    <div class="flex items-center">
                                        <span x-show="msg.read_at" class="text-brand-400">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <path d="M20 6L9 17l-5-5"/><path d="M16 6l-11 11-5-5" class="opacity-50"/>
                                            </svg>
                                        </span>
                                        <span x-show="!msg.read_at" class="text-slate-600">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 6L9 17l-5-5"/>
                                            </svg>
                                        </span>
                                    </div>
                                </template>
                            </div>
                            
                            {{-- Action Buttons (Right) --}}
                            <div x-show="!msg.is_deleted_for_all" class="absolute -left-14 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                                <button @click="deleteMessage(msg.id, 'all')" class="p-1 rounded-lg bg-dark-700 border border-dark-600 text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Hapus untuk semua">
                                    <i data-lucide="trash" class="w-3 h-3"></i>
                                </button>
                                <button @click="deleteMessage(msg.id, 'me')" class="p-1 rounded-lg bg-dark-700 border border-dark-600 text-slate-500 hover:text-slate-200 hover:bg-dark-600 transition-all" title="Hapus untuk saya">
                                    <i data-lucide="user-x" class="w-3 h-3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>
