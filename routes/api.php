<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminBookingController; // PERBAIKAN: use huruf kecil
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Api\AvailabilityController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === PUBLIC ROUTES ===
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{slug}', [RoomController::class, 'show']);
Route::get('/check-availability', [AvailabilityController::class, 'check']);

// === PROTECTED ROUTES ===
Route::middleware('auth:sanctum')->group(function () {

    // User & Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userProfile']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);

    // Booking (User Biasa)
    Route::get('/my-bookings', [BookingController::class, 'index']); 
    Route::post('/bookings', [BookingController::class, 'store']);   

    // --- KHUSUS ADMIN ---
    // 1. Kelola Booking
    Route::get('/admin/bookings', [AdminBookingController::class, 'index']);
    Route::put('/admin/bookings/{id}', [AdminBookingController::class, 'updateStatus']);

    // 2. Kelola Kamar
    Route::put('/admin/rooms/{id}', [RoomController::class, 'update']);

    // 3. Super Admin (User Management)
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::put('/admin/users/{id}/role', [AdminUserController::class, 'updateRole']);
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
});