<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use App\Http\Controllers\SSOController;

Route::get('/', function () {
    return view('welcome');
});

// Minimal auth endpoints for UI integration (CSRF disabled for mobile clients)
Route::post('/auth/register', [AuthController::class, 'register'])
    ->withoutMiddleware([VerifyCsrfToken::class]);


// Unified OTP sending endpoint for web & mobile clients
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

// Login endpoint for web & mobile clients
Route::post('/auth/login', [AuthController::class, 'login'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Booking endpoints (protected by Sanctum; CSRF disabled for clients)
    Route::get('/bookings', [BookingController::class, 'index'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/bookings', [BookingController::class, 'store'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::get('/bookings/{id}', [BookingController::class, 'show'])
        ->whereNumber('id')
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->whereNumber('id')
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])
        ->whereNumber('id')
        ->withoutMiddleware([VerifyCsrfToken::class]);
    Route::post('/bookings/{id}/rate', [BookingController::class, 'rate'])
        ->whereNumber('id')
        ->withoutMiddleware([VerifyCsrfToken::class]);
});

// Admin SSO endpoint for bridging from PHP admin panel (GET or POST)
Route::match(['get', 'post'], '/sso/admin', [SSOController::class, 'admin'])
    ->withoutMiddleware([VerifyCsrfToken::class]);


// Fallback route to return consistent JSON for missing endpoints
Route::fallback(function (Request $request) {
    if ($request->expectsJson() || str_contains($request->header('Accept', ''), 'application/json')) {
        return response()->json([
            'success' => false,
            'message' => 'Not Found',
            'path' => $request->path(),
            'method' => $request->method(),
        ], 404);
    }
    return response('Not Found', 404);
});
