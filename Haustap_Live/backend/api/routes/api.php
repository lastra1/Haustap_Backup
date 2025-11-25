<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\Auth\ModeController;
use App\Http\Controllers\Provider\ProviderController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\LocationPinController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\HealthController;

// Stateless API endpoints (no CSRF)
Route::get('/health', [HealthController::class, 'health']);
Route::get('/ping', [HealthController::class, 'ping']);

Route::post('/auth/otp/send', [OtpController::class, 'send']);
Route::post('/auth/otp/verify', [OtpController::class, 'verify']);
Route::post('/auth/register', [PasswordController::class, 'register']);
Route::post('/auth/login', [PasswordController::class, 'login']);
Route::post('/auth/password/reset', [PasswordController::class, 'reset']);
Route::get('/auth/mode', [ModeController::class, 'get'])->middleware('role');
Route::post('/auth/mode', [ModeController::class, 'save'])->middleware('role');

// Provider application and status
Route::get('/providers/status', [ProviderController::class, 'status'])->middleware('role');
Route::post('/providers/apply', [ProviderController::class, 'apply'])->middleware('role');
Route::post('/admin/providers/approve', [ProviderController::class, 'approve'])->middleware('role:admin');
Route::post('/admin/providers/revoke', [ProviderController::class, 'revoke'])->middleware('role:admin');

Route::get('/bookings/', [BookingsController::class, 'index'])->middleware('role');
Route::post('/bookings/', [BookingsController::class, 'store'])->middleware('role:client');
Route::get('/bookings/{id}', [BookingsController::class, 'show'])->middleware('role');
Route::post('/bookings/{id}/cancel', [BookingsController::class, 'cancel'])->middleware('role:client');
Route::post('/bookings/{id}/status', [BookingsController::class, 'updateStatus'])->middleware('role:provider');
Route::post('/bookings/{id}/rate', [BookingsController::class, 'rate'])->middleware('role:client');
Route::post('/bookings/{id}/return', [BookingsController::class, 'requestReturn'])->whereNumber('id')->middleware('role:client');
Route::get('/bookings/returns', [BookingsController::class, 'listReturns'])->middleware('role:admin');

Route::post('/chat/open', [ChatController::class, 'open'])->middleware('role');
Route::get('/chat/{booking_id}/messages', [ChatController::class, 'listMessages'])->whereNumber('booking_id')->middleware('role');
Route::post('/chat/{booking_id}/messages', [ChatController::class, 'sendMessage'])->whereNumber('booking_id')->middleware('role');

Route::get('/admin/settings', [AdminSettingsController::class, 'get'])->middleware('role:admin');
Route::post('/admin/settings', [AdminSettingsController::class, 'save'])->middleware('role:admin');

// Firebase-backed endpoints (stateless)
Route::get('/firebase/firebase-config', [FirebaseController::class, 'firebaseConfig']);
Route::get('/firebase/categories', [FirebaseController::class, 'categories']);
Route::post('/firebase/categories', [FirebaseController::class, 'categoriesCreate']);
Route::get('/firebase/categories/seed', [FirebaseController::class, 'categoriesSeed']);
Route::get('/firebase/categories/{slug}', [FirebaseController::class, 'categoryGet']);
Route::post('/firebase/categories/{slug}/price', [FirebaseController::class, 'categorySetPrice']);
Route::get('/firebase/categories/aggregate/providers', [FirebaseController::class, 'categoriesAggregate']);
Route::get('/firebase/categories/seed-ui/ac-cleaning', [FirebaseController::class, 'categoriesSeedFromUIAcCleaning']);
Route::get('/firebase/categories/seed-ui/beauty', [FirebaseController::class, 'categoriesSeedFromUIBeauty']);
Route::get('/firebase/services', [FirebaseController::class, 'services']);
Route::post('/firebase/services', [FirebaseController::class, 'servicesCreate']);
Route::get('/firebase/services/seed', [FirebaseController::class, 'servicesSeed']);
Route::get('/firebase/services/seed-ui/cleaning', [FirebaseController::class, 'servicesSeedFromUICleaning']);
Route::get('/firebase/services/seed-ui/beauty', [FirebaseController::class, 'servicesSeedFromUIBeauty']);
Route::get('/firebase/providers', [FirebaseController::class, 'providers']);
Route::get('/firebase/providers/search', [FirebaseController::class, 'providersSearch']);
Route::get('/firebase/bookings', [FirebaseController::class, 'bookingsList']);
Route::post('/firebase/bookings', [FirebaseController::class, 'bookingsCreate'])->middleware('role');
Route::get('/firebase/test/create', [FirebaseController::class, 'bookingsTestCreate']);
Route::post('/firebase/bookings/{id}/return', [FirebaseController::class, 'bookingsReturn']);
Route::get('/firebase/bookings/returns', [FirebaseController::class, 'bookingsReturns']);
Route::get('/firebase/applicants', [FirebaseController::class, 'applicants']);
Route::post('/firebase/applicants', [FirebaseController::class, 'applicantsCreate']);
Route::get('/firebase/applicants/test/create', [FirebaseController::class, 'applicantsTestCreate']);
Route::post('/firebase/applicants/{id}/status', [FirebaseController::class, 'applicantsStatus']);
Route::post('/firebase/applicants/{id}/approve', [FirebaseController::class, 'applicantsApprove']);
Route::post('/firebase/applicants/{id}/reject', [FirebaseController::class, 'applicantsReject']);
Route::post('/firebase/applicants/{id}/promote', [FirebaseController::class, 'applicantsPromote']);
Route::get('/firebase/users', [FirebaseController::class, 'users']);
Route::post('/firebase/users', [FirebaseController::class, 'usersCreate']);
Route::get('/firebase/vouchers', [FirebaseController::class, 'vouchers']);
Route::get('/firebase/users/test/create', [FirebaseController::class, 'usersTestCreate']);
Route::get('/firebase/users/seed/basic', [FirebaseController::class, 'usersSeedBasic']);
Route::get('/firebase/bookings/{id}', [FirebaseController::class, 'bookingsGet']);
Route::post('/firebase/bookings/{id}/status', [FirebaseController::class, 'bookingsStatus'])->middleware('role:provider');
Route::post('/firebase/bookings/{id}/cancel', [FirebaseController::class, 'bookingsCancel'])->middleware('role:client');
Route::post('/firebase/bookings/{id}/rate', [FirebaseController::class, 'bookingsRate'])->middleware('role:client');
Route::post('/firebase/migrate/providers', [FirebaseController::class, 'migrateProviders']);
Route::get('/firebase/migrate/providers', [FirebaseController::class, 'migrateProviders']);

// MySQL-Firebase Sync Routes
Route::prefix('sync')->group(function () {
    Route::get('/status', [SyncController::class, 'status']);
    Route::post('/users/to-firebase', [SyncController::class, 'syncUsersToFirebase']);
    Route::post('/users/from-firebase', [SyncController::class, 'syncUsersFromFirebase']);
    Route::post('/bookings', [SyncController::class, 'syncBookings']);
    Route::post('/full', [SyncController::class, 'fullSync']);
});

// New API Routes with Firebase Integration
Route::prefix('v2')->group(function () {
    // API Documentation
    Route::get('/docs', function () {
        return response()->json([
            'success' => true,
            'message' => 'HausTap Service Booking Platform API v2',
            'endpoints' => [
                'Authentication' => [
                    'POST /api/v2/auth/register' => 'Register new user',
                    'POST /api/v2/auth/login' => 'Login user',
                    'GET /api/v2/auth/profile' => 'Get user profile',
                    'POST /api/v2/auth/profile' => 'Update user profile',
                    'POST /api/v2/auth/logout' => 'Logout user',
                    'POST /api/v2/auth/logout-all' => 'Logout from all devices'
                ],
                'Notifications' => [
                    'GET /api/v2/notifications' => 'Get notifications',
                    'POST /api/v2/notifications' => 'Create notification',
                    'POST /api/v2/notifications/bulk' => 'Send bulk notifications',
                    'PUT /api/v2/notifications/{id}/read' => 'Mark notification as read',
                    'PUT /api/v2/notifications/read-all' => 'Mark all as read',
                    'DELETE /api/v2/notifications/{id}' => 'Delete notification',
                    'GET /api/v2/notifications/unread/count' => 'Get unread count'
                ],
                'Location Pins' => [
                    'GET /api/v2/location-pins' => 'Get location pins',
                    'POST /api/v2/location-pins' => 'Create location pin',
                    'PUT /api/v2/location-pins/{id}' => 'Update location pin',
                    'DELETE /api/v2/location-pins/{id}' => 'Delete location pin',
                    'POST /api/v2/location-pins/nearby' => 'Find nearby pins',
                    'POST /api/v2/location-pins/geocode' => 'Geocode address',
                    'POST /api/v2/location-pins/reverse-geocode' => 'Reverse geocode coordinates'
                ]
            ],
            'authentication' => 'Bearer token required for protected routes',
            'firebase_integration' => 'All data synchronized with Firebase Firestore',
            'database' => 'MySQL with Docker containerization',
            'architecture' => 'MVC pattern maintained'
        ]);
    });

    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
        
        // Notification routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/', [NotificationController::class, 'store']);
            Route::post('/bulk', [NotificationController::class, 'sendBulkNotifications']);
            Route::get('/{id}', [NotificationController::class, 'show']);
            Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
            Route::delete('/delete-all', [NotificationController::class, 'destroyAll']);
            Route::get('/unread/count', [NotificationController::class, 'unreadCount']);
        });
        
        // Location Pin routes
        Route::prefix('location-pins')->group(function () {
            Route::get('/', [LocationPinController::class, 'index']);
            Route::post('/', [LocationPinController::class, 'store']);
            Route::get('/{id}', [LocationPinController::class, 'show']);
            Route::put('/{id}', [LocationPinController::class, 'update']);
            Route::delete('/{id}', [LocationPinController::class, 'destroy']);
            Route::post('/nearby', [LocationPinController::class, 'nearby']);
            Route::post('/geocode', [LocationPinController::class, 'geocode']);
            Route::post('/reverse-geocode', [LocationPinController::class, 'reverseGeocode']);
        });
    });
});
