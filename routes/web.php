<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

// 🔥 route untuk tampilkan dashboard + data
Route::get('/', [AttendanceController::class, 'index']);

// 🔥 route untuk upload CSV
Route::post('/import-presensi', [AttendanceController::class, 'importCsv'])->name('import.presensi');