<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint
Route::get('/health', [HealthController::class, 'health']);

// Basic API info
Route::get('/', function () {
    return response()->json([
        'message' => 'HausTap Service Booking API',
        'version' => '1.0.0',
        'framework' => 'Laravel ' . app()->version(),
        'environment' => app()->environment(),
        'timestamp' => now()->toIso8601String()
    ]);
});

// API routes that don't require authentication
Route::prefix('v1')->group(function () {
    // Public endpoints
    Route::get('/status', function () {
        return response()->json([
            'status' => 'operational',
            'services' => ['api', 'database', 'cache']
        ]);
    });
});

// Public booking availability endpoint used by client applications
Route::get('/bookings/availability', [BookingController::class, 'availability']);
