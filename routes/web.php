<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

// Halaman awal → redirect ke kalender (publik)
Route::get('/', fn () => redirect()->route('bookings.week'));

// Kalender & CRUD dasar (publik: view/index/create/store)
Route::get('/calendar',        [BookingController::class, 'week'])->name('bookings.week');
Route::get('/bookings',        [BookingController::class, 'index'])->name('bookings.index');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings',       [BookingController::class, 'store'])->name('bookings.store');

// Aksi yang butuh login (edit, update, destroy, cancelByToken)
Route::middleware(['auth'])->group(function () {
    // Edit form
    Route::get('/bookings/{booking}/edit',  [BookingController::class, 'edit'])
        ->whereNumber('booking')
        ->name('bookings.edit');

    // Update data
    Route::match(['put','patch'], '/bookings/{booking}', [BookingController::class, 'update'])
        ->whereNumber('booking')
        ->name('bookings.update');

    // (Opsional) Hapus booking
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])
        ->whereNumber('booking')
        ->name('bookings.destroy');

    // Cancel via token tetap di-protect auth sesuai permintaan sebelumnya
    Route::get('/cancel/{token}', [BookingController::class, 'cancelByToken'])
        ->name('bookings.cancel');
});

// Rute auth dari Laravel Breeze (login, register, verifikasi, dsb.)
require __DIR__ . '/auth.php';
