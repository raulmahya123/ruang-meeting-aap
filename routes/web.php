<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

// Halaman awal → redirect ke kalender (publik)
Route::get('/', fn () => redirect()->route('bookings.week'));

// Kalender & CRUD dasar (publik)
Route::get('/calendar',        [BookingController::class, 'week'])->name('bookings.week');
Route::get('/bookings',        [BookingController::class, 'index'])->name('bookings.index');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings',       [BookingController::class, 'store'])->name('bookings.store');

// HANYA cancel yang butuh auth
Route::middleware(['auth'])->group(function () {
    Route::get('/cancel/{token}', [BookingController::class, 'cancelByToken'])->name('bookings.cancel');
});

// Rute auth dari Laravel Breeze (login, register, verifikasi, dsb.)
require __DIR__ . '/auth.php';
