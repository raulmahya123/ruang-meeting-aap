<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/', fn() => redirect()->route('bookings.week'));
Route::get('/calendar',        [BookingController::class, 'week'])->name('bookings.week');
Route::get('/bookings',        [BookingController::class, 'index'])->name('bookings.index');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings',       [BookingController::class, 'store'])->name('bookings.store');
Route::get('/cancel/{token}',  [BookingController::class, 'cancelByToken'])->name('bookings.cancel');
