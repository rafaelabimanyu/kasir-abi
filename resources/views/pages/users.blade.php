@extends('layouts.master')

@section('title', 'User Management')

@section('page-header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">User Management</h1>
        <p class="text-slate-500 mt-1">Kelola akun pengguna dan hak akses sistem.</p>
    </div>
    <button x-data @click="$dispatch('open-modal', 'add-user')" class="flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold rounded-xl shadow-glow transition-all">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah User
    </button>
</div>
@endsection

@section('content')
<div x-data="userManagement()">
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 rounded-xl text-sm font-medium flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-500/15 border border-red-500/30 text-red-400 rounded-xl text-sm font-medium flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-500/15 border border-red-500/30 text-red-400 rounded-xl text-sm font-medium">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span class="font-bold">Gagal menyimpan data:</span>
            </div>
            <ul class="list-disc list-inside ml-2 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-card :noPadding="true">
        <div class="px-5 py-4 border-b border-dark-600/40 flex flex-col sm:flex-row gap-3 relative">
            <div class="absolute right-6 top-6" x-show="loading" style="display: none;">
                <i data-lucide="loader-2" class="w-5 h-5 text-brand-500 animate-spin"></i>
            </div>
            <div class="flex-1 flex items-center gap-2 bg-dark-800 rounded-xl px-3 py-2 border border-dark-600/50 focus-within:border-brand-500/50 transition-all">
                <i data-lucide="search" class="w-4 h-4 text-slate-500"></i>
                <input type="text" x-model="search" @input.debounce.300ms="fetchData" placeholder="Cari user (nama, email, role)..." class="bg-transparent text-sm text-slate-300 placeholder-slate-500 outline-none w-full">
            </div>
        </div>

        <div id="data-container" :class="{'opacity-50 pointer-events-none': loading}" class="transition-opacity duration-200">
            @include('pages.users._table')
        </div>
    </x-card>

    {{-- Modal Form (Add & Edit) --}}
    <div x-show="showFormModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-dark-900/80 backdrop-blur-sm" @open-modal.window="if($event.detail === 'add-user') { openAdd(); }">
        <div @click.away="closeForm()" class="bg-dark-800 border border-dark-600/50 rounded-2xl p-6 w-full max-w-md shadow-card">
            <h2 class="text-xl font-bold text-white mb-4" x-text="isEdit ? 'Edit User' : 'Tambah User Baru'"></h2>
            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="id" x-model="form.id">
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="form.name" required class="w-full bg-dark-700 border border-dark-600/50 rounded-xl px-4 py-2.5 text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" x-model="form.email" required class="w-full bg-dark-700 border border-dark-600/50 rounded-xl px-4 py-2.5 text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Role</label>
                    <select name="role" x-model="form.role" required class="w-full bg-dark-700 border border-dark-600/50 rounded-xl px-4 py-2.5 text-white outline-none focus:border-brand-500/50 transition-all appearance-none">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Administrator</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">
                        Password <span x-show="isEdit" class="text-xs text-slate-500 font-normal">(Kosongkan jika tidak ingin diubah)</span>
                    </label>
                    <input type="password" name="password" x-bind:required="!isEdit" class="w-full bg-dark-700 border border-dark-600/50 rounded-xl px-4 py-2.5 text-white outline-none focus:border-brand-500/50 transition-all">
                </div>
                
                <div class="flex gap-3 mt-6">
                    <button type="button" @click="closeForm()" class="flex-1 py-2.5 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-xl transition-colors font-medium">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl shadow-glow transition-all font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-dark-900/80 backdrop-blur-sm">
        <div @click.away="showDeleteModal = false" class="bg-dark-800 border border-dark-600/50 rounded-2xl p-6 w-full max-w-sm shadow-card text-center">
            <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-400"></i>
            </div>
            <h2 class="text-lg font-bold text-white mb-2">Hapus User?</h2>
            <p class="text-sm text-slate-400 mb-6">Tindakan ini tidak dapat dibatalkan. User akan dihapus secara permanen.</p>
            <form :action="deleteAction" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false" class="flex-1 py-2.5 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-xl transition-colors font-medium">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/20 rounded-xl transition-all font-medium">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function userManagement() {
    return {
        search: '{{ request("search") }}',
        loading: false,
        abortController: null,
        showFormModal: false,
        showDeleteModal: false,
        isEdit: false,
        formAction: '{{ old('_method') === 'PUT' && old('id') ? route("users.update", old("id")) : route("users.store") }}',
        deleteAction: '',
        form: { 
            id: '{{ old("id", "") }}', 
            name: '{{ old("name", "") }}', 
            email: '{{ old("email", "") }}', 
            role: '{{ old("role", "kasir") }}' 
        },
        
        init() {
            @if($errors->any())
                this.showFormModal = true;
            @endif
            
            document.getElementById('data-container').addEventListener('click', (e) => {
                let link = e.target.closest('.ajax-pagination a');
                if (link) {
                    e.preventDefault();
                    this.fetchData(link.href);
                }
            });
        },
        
        async fetchData(url = null) {
            this.loading = true;
            
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            
            try {
                let fetchUrl = url instanceof Event ? null : url;
                if (!fetchUrl) {
                    const params = new URLSearchParams();
                    if (this.search) params.append('search', this.search);
                    fetchUrl = `{{ route('users.index') }}?${params.toString()}`;
                    window.history.pushState({}, '', fetchUrl);
                }
                
                const response = await fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.abortController.signal
                });
                
                const html = await response.text();
                document.getElementById('data-container').innerHTML = html;
                
                if (this.search && this.search.length >= 2) {
                    this.highlightText(this.search);
                }
                
                this.$nextTick(() => { lucide.createIcons(); });
            } catch (error) {
                if (error.name !== 'AbortError') console.error(error);
            } finally {
                this.loading = false;
            }
        },
        
        highlightText(keyword) {
            const regex = new RegExp(`(${keyword})`, 'gi');
            document.querySelectorAll('#data-container .highlight-target').forEach(el => {
                const originalText = el.textContent;
                el.innerHTML = originalText.replace(regex, '<mark class="bg-brand-500/30 text-brand-400 rounded px-1">$1</mark>');
            });
        },
        
        openAdd() {
            this.isEdit = false;
            this.formAction = '{{ route("users.store") }}';
            this.form = { id: '', name: '', email: '', role: 'kasir' };
            this.showFormModal = true;
        },
        
        editUser(user) {
            this.isEdit = true;
            this.formAction = `/users/${user.id}`;
            this.form = { id: user.id, name: user.name, email: user.email, role: user.role };
            this.showFormModal = true;
        },
        
        closeForm() {
            this.showFormModal = false;
        },
        
        confirmDelete(id) {
            this.deleteAction = `/users/${id}`;
            this.showDeleteModal = true;
        }
    }
}
</script>
@endpush
@endsection
