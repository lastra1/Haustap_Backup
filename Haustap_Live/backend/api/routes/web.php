<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\FirebaseController;

Route::get('/', function () {
    $base = env('LEGACY_BASE_URL', 'http://localhost:5001');
    return redirect()->away($base . '/guest/homepage.php');
});

// Admin dashboard served via Blade wrapper, preserving legacy UI
Route::view('/admin', 'admin.dashboard');

// Guest-facing pages served via Blade views
Route::get('/home', function () {
    $base = env('LEGACY_BASE_URL', 'http://localhost:5001');
    return redirect()->away($base . '/guest/homepage.php');
});
Route::get('/login', [GuestController::class, 'login']);
Route::get('/register', [GuestController::class, 'register']);

// Firebase-backed API (Laravel MVC)
Route::get('/api/firebase/categories', [FirebaseController::class, 'categories']);
Route::get('/api/firebase/providers', [FirebaseController::class, 'providers']);
Route::get('/api/firebase/bookings', [FirebaseController::class, 'bookingsList']);
Route::post('/api/firebase/bookings', [FirebaseController::class, 'bookingsCreate']);
Route::get('/api/firebase/bookings/{id}', [FirebaseController::class, 'bookingsGet']);
Route::post('/api/firebase/bookings/{id}/status', [FirebaseController::class, 'bookingsStatus']);
Route::post('/api/firebase/bookings/{id}/cancel', [FirebaseController::class, 'bookingsCancel']);
Route::post('/api/firebase/bookings/{id}/rate', [FirebaseController::class, 'bookingsRate']);
Route::post('/api/firebase/migrate/providers', [FirebaseController::class, 'migrateProviders']);
