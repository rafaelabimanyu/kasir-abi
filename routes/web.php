<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Kasir Abi - Web Routes
|--------------------------------------------------------------------------
*/

// ─── Guest Routes ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ─── Authenticated Routes ────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/search/global', [\App\Http\Controllers\SearchController::class, 'globalSearch'])->name('search.global');

    // Redirect root
    Route::get('/', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->isKasir() ? redirect()->route('pos') : redirect()->route('dashboard');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Chat
    Route::get('/chat', [\App\Http\Controllers\MessageController::class, 'index'])->name('chat');
    Route::get('/chat/messages/{user}', [\App\Http\Controllers\MessageController::class, 'fetchMessages'])->name('chat.fetch');
    Route::post('/chat/send', [\App\Http\Controllers\MessageController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/poll/{user}', [\App\Http\Controllers\MessageController::class, 'poll'])->name('chat.poll');
    Route::get('/chat/status', [\App\Http\Controllers\MessageController::class, 'getStatus'])->name('chat.status');
    Route::post('/chat/typing', [\App\Http\Controllers\MessageController::class, 'setTyping'])->name('chat.typing');
    Route::post('/chat/delete/{message}', [\App\Http\Controllers\MessageController::class, 'deleteMessage'])->name('chat.delete');

    // Shortcuts
    Route::view('/shortcuts', 'pages.shortcuts')->name('shortcuts');

    // ── Admin Only ──────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('produk', ProductController::class)->parameters(['produk' => 'product']);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        
        // User Management
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'show', 'edit']);
        
        Route::get('/pengaturan', [SettingsController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan', [SettingsController::class, 'update'])->name('pengaturan.update');
        Route::post('/pengaturan/password', [SettingsController::class, 'updatePassword'])->name('pengaturan.password');
        Route::get('/pengaturan/backup', [SettingsController::class, 'backup'])->name('pengaturan.backup');
    });

    // ── Admin & Kasir ───────────────────────────────────────────────
    Route::middleware('role:admin,kasir')->group(function () {
        // POS
        Route::get('/pos', [TransactionController::class, 'posIndex'])->name('pos');
        Route::post('/pos/checkout', [TransactionController::class, 'store'])->name('pos.checkout');

        // Transaksi
        Route::get('/transaksi', [TransactionController::class, 'history'])->name('transaksi.history');
        Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transaksi.show');
        Route::get('/transaksi/{transaction}/print', [TransactionController::class, 'print'])->name('transaksi.print');
        Route::post('/transaksi/{transaction}/void', [TransactionController::class, 'voidTransaction'])
            ->middleware('role:admin')
            ->name('transaksi.void');

        // Shift
        Route::post('/shift/start', [ShiftController::class, 'start'])->name('shift.start');
        Route::post('/shift/close', [ShiftController::class, 'close'])->name('shift.close');
        Route::post('/shift/expense', [ShiftController::class, 'addExpense'])->name('shift.expense');
        Route::get('/shift/active', [ShiftController::class, 'active'])->name('shift.active');
        Route::get('/shift/saya', [ShiftController::class, 'myShifts'])->name('shift.saya');
    });
});
