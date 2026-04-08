<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama (Tempat Udin naruh tombol upload)
Route::get('/', [AttendanceController::class, 'index'])->name('home');

// 2. Jalur Eksekusi Import (Tugas Sifa)
Route::post('/import-presensi', [AttendanceController::class, 'import'])->name('import');

// 3. Jalur Lihat Hasil Rekap (Untuk Dashboard Wahyu nanti)
Route::get('/rekap', [AttendanceController::class, 'showRekap'])->name('rekap.index');