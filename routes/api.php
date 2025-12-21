<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- PENTING: Import Controller agar tidak Error 500 ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
// Import Controller Cek Ketersediaan
use App\Http\Controllers\Api\AvailabilityController;
// ------------------------------------------------------

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === PUBLIC ROUTES (Bisa diakses tanpa login) ===
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Ambil Data Kamar
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{slug}', [RoomController::class, 'show']);

// Cek Ketersediaan Kamar (Public - Dipanggil Booking Bar Frontend)
Route::get('/check-availability', [AvailabilityController::class, 'check']);


// === PROTECTED ROUTES (Harus Login / Punya Token) ===
Route::middleware('auth:sanctum')->group(function () {

    // User & Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userProfile']);

    // Booking (User Biasa)
    Route::get('/my-bookings', [BookingController::class, 'index']); // Lihat history
    Route::post('/bookings', [BookingController::class, 'store']);   // Bikin booking baru

    // --- KHUSUS ADMIN ---
    // 1. Kelola Booking
    Route::get('/admin/bookings', [App\Http\Controllers\AdminBookingController::class, 'index']);
    Route::put('/admin/bookings/{id}', [App\Http\Controllers\AdminBookingController::class, 'updateStatus']);

    // 2. Kelola Kamar (Edit Stok & Harga) -> INI YANG BARU DITAMBAHKAN
    Route::put('/admin/rooms/{id}', [RoomController::class, 'update']);
});