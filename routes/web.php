<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════
// AUTH ROUTES
// ═══════════════════════════════════════════════════
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ═══════════════════════════════════════════════════
// PUBLIC ROUTE — tanpa login, bisa diakses siapa saja
// Akses: yourdomain.com/absensi
// ═══════════════════════════════════════════════════
Route::get('/karyawan', [PublicDashboardController::class, 'index'])->name('public.dashboard');

// ═══════════════════════════════════════════════════
// ADMIN ROUTES — harus login dulu
// ═══════════════════════════════════════════════════
Route::middleware('auth')->group(function () {

    // Dashboard + data presensi
    Route::get('/', [AttendanceController::class, 'index'])->name('dashboard');

    // Upload CSV presensi
    Route::post('/import-presensi', [AttendanceController::class, 'importCsv'])->name('import.presensi');

});