{{-- FORM INPUT CHAT --}}
<div x-show="selectedUserId" style="display:none;" class="p-3 bg-dark-800 border-t border-dark-600/50 shrink-0">
    {{-- Preview File Terpilih --}}
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

    <form @submit.prevent="sendMessage($event)" class="flex items-end gap-2" enctype="multipart/form-data">
        <div class="flex items-center gap-1 shrink-0">
            {{-- Tombol Lampiran --}}
            <button @click="$refs.attachmentInput.click()" type="button" class="w-10 h-10 flex items-center justify-center rounded-xl bg-dark-700 border border-dark-600 text-slate-400 hover:text-white hover:bg-dark-600 transition-all" title="Lampiran">
                <i data-lucide="plus" class="w-5 h-5"></i>
            </button>
            <input type="file" name="attachment" x-ref="attachmentInput" @change="handleFileSelect" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx">
            
            {{-- Tombol Emoji --}}
            <div class="relative">
                <button id="emoji-picker-button" type="button" class="w-10 h-10 flex items-center justify-center rounded-xl bg-dark-700 border border-dark-600 text-slate-400 hover:text-white hover:bg-dark-600 transition-all" title="Emoji">
                    <i data-lucide="smile" class="w-5 h-5"></i>
                </button>
                <div id="emoji-picker-container" class="absolute bottom-full left-0 mb-2 z-[1000]" style="display:none;"></div>
            </div>
        </div>

        <textarea id="chat-message-input" name="message" x-model="newMessage" x-ref="messageInput"
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
