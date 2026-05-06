<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicDashboardController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/karyawan', [PublicDashboardController::class, 'index'])->name('public.dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/', [AttendanceController::class, 'index'])->name('dashboard');

    // ← HANYA BARIS INI YANG DIUBAH (importCsv → import)
    Route::post('/import-presensi', [AttendanceController::class, 'import'])->name('import.presensi');

});