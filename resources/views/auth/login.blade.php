<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Kasir Abi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4">

    {{-- Background decoration --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-700 rounded-2xl flex items-center justify-center shadow-glow mx-auto mb-4">
                <i data-lucide="shopping-bag" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Kasir Abi</h1>
            <p class="text-slate-500 text-sm mt-1">Masuk ke sistem point of sale</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-dark-700 border border-dark-600/50 rounded-2xl shadow-card p-6 md:p-8">
            {{-- Error Message --}}
            @if($errors->any())
                <div class="mb-5 p-3.5 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center gap-2.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                    <div class="flex items-center gap-2 bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 focus-within:border-brand-500/50 focus-within:shadow-glow transition-all">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-500 shrink-0"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@kasirabi.com"
                            class="bg-transparent text-sm text-white placeholder-slate-500 outline-none w-full">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                    <div class="flex items-center gap-2 bg-dark-800 border border-dark-600/50 rounded-xl px-4 py-2.5 focus-within:border-brand-500/50 focus-within:shadow-glow transition-all">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-500 shrink-0"></i>
                        <input type="password" name="password" id="password" required
                            placeholder="••••••••"
                            class="bg-transparent text-sm text-white placeholder-slate-500 outline-none w-full">
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 bg-dark-800 border-dark-500 rounded text-brand-500 focus:ring-brand-500 focus:ring-offset-0">
                        <span class="text-sm text-slate-400">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl transition-all hover:shadow-glow focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-dark-700 cursor-pointer">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk</span>
                </button>
            </form>
        </div>

        {{-- Demo accounts --}}
        <div class="mt-6 p-4 bg-dark-800/50 border border-dark-600/30 rounded-xl">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2.5">Akun Demo</p>
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 bg-brand-500/15 text-brand-400 rounded font-medium text-[10px]">ADMIN</span>
                        <span class="text-slate-400">admin@kasirabi.com</span>
                    </div>
                    <span class="text-slate-500 font-mono">password</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 bg-emerald-500/15 text-emerald-400 rounded font-medium text-[10px]">KASIR</span>
                        <span class="text-slate-400">kasir@kasirabi.com</span>
                    </div>
                    <span class="text-slate-500 font-mono">password</span>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">&copy; {{ date('Y') }} Kasir Abi. All rights reserved.</p>
    </div>

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</body>
</html>
