<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CollieController;
use App\Http\Controllers\SppbController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DistributorController;

// Rute Autentikasi (Tamu / Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Rute Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Aplikasi Utama (Wajib Login / Auth Middleware)
Route::middleware('auth')->group(function () {
    
    // Halaman Utama: Dashboard & Grafik
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

    // Pengaturan Profil & Ganti Password
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Modul Collie
    Route::get('/collie', [CollieController::class, 'index'])->name('collie.index');
    Route::post('/collie', [CollieController::class, 'store'])->name('collie.store');
    Route::post('/collie/batch-update', [CollieController::class, 'batchUpdate'])->name('collie.batch-update');
    Route::delete('/collie/{id}', [CollieController::class, 'destroy'])->name('collie.destroy');

    // Modul SPPB
    Route::get('/sppb', [SppbController::class, 'index'])->name('sppb.index');
    Route::post('/sppb', [SppbController::class, 'store'])->name('sppb.store');

    // Modul Master Distributor
    Route::get('/distributor', [DistributorController::class, 'index'])->name('distributor.index');
    Route::post('/distributor', [DistributorController::class, 'store'])->name('distributor.store');
    Route::put('/distributor/{id}', [DistributorController::class, 'update'])->name('distributor.update');
    Route::delete('/distributor/{id}', [DistributorController::class, 'destroy'])->name('distributor.destroy');

    // API Helper
    Route::get('/api/pl-collies', [CollieController::class, 'getPlCollies'])->name('api.pl-collies');
    Route::get('/api/item-stock/{code?}', [CollieController::class, 'getItemStock'])->name('api.item-stock');
    Route::get('/api/master-stock/search', [CollieController::class, 'searchItemStock'])->name('api.master-stock.search');
    Route::get('/api/sm-detail', [SppbController::class, 'getSmDetail'])->name('api.sm-detail');
    Route::get('/api/sm-oracle', [SppbController::class, 'getSmOracleList'])->name('api.sm-oracle');
    Route::get('/api/pl-detail', [SppbController::class, 'getPlDetail'])->name('api.pl-detail');
    Route::get('/sppb/print/{no_pl}', [SppbController::class, 'print'])->name('sppb.print')->where('no_pl', '.*');

});
