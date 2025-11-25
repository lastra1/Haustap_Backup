<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\LocationPinController;

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

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// OTP routes
Route::post('/otp/send', [OtpController::class, 'sendOtp']);
Route::post('/otp/verify', [OtpController::class, 'verifyOtp']);
Route::post('/otp/resend', [OtpController::class, 'resendOtp']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);
    
    // OTP routes
    Route::get('/otp/history', [OtpController::class, 'otpHistory']);
    
    // Chat routes
    Route::prefix('chat')->group(function () {
        Route::get('/conversations', [ChatController::class, 'getConversations']);
        Route::post('/conversations', [ChatController::class, 'createConversation']);
        Route::get('/conversations/{conversationId}', [ChatController::class, 'getConversation']);
        Route::post('/conversations/{conversationId}/messages', [ChatController::class, 'sendMessage']);
        Route::get('/conversations/{conversationId}/messages', [ChatController::class, 'getMessages']);
        Route::put('/messages/{messageId}/read', [ChatController::class, 'markAsRead']);
        Route::delete('/messages/{messageId}', [ChatController::class, 'deleteMessage']);
        Route::get('/unread-count', [ChatController::class, 'getUnreadCount']);
    });
    
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
    
    // User routes
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0.0'
    ]);
});

// API documentation route
Route::get('/docs', function () {
    return response()->json([
        'success' => true,
        'message' => 'HausTap Service Booking Platform API',
        'endpoints' => [
            'Authentication' => [
                'POST /api/auth/register' => 'Register new user',
                'POST /api/auth/login' => 'Login user',
                'GET /api/auth/profile' => 'Get user profile',
                'POST /api/auth/profile' => 'Update user profile',
                'POST /api/auth/logout' => 'Logout user',
                'POST /api/auth/logout-all' => 'Logout from all devices'
            ],
            'OTP' => [
                'POST /api/otp/send' => 'Send OTP',
                'POST /api/otp/verify' => 'Verify OTP',
                'POST /api/otp/resend' => 'Resend OTP',
                'GET /api/otp/history' => 'Get OTP history'
            ],
            'Chat' => [
                'GET /api/chat/conversations' => 'Get conversations',
                'POST /api/chat/conversations' => 'Create conversation',
                'GET /api/chat/conversations/{id}' => 'Get conversation details',
                'POST /api/chat/conversations/{id}/messages' => 'Send message',
                'GET /api/chat/conversations/{id}/messages' => 'Get messages',
                'PUT /api/messages/{id}/read' => 'Mark message as read',
                'DELETE /api/messages/{id}' => 'Delete message',
                'GET /api/chat/unread-count' => 'Get unread count'
            ],
            'Notifications' => [
                'GET /api/notifications' => 'Get notifications',
                'POST /api/notifications' => 'Create notification',
                'POST /api/notifications/bulk' => 'Send bulk notifications',
                'PUT /api/notifications/{id}/read' => 'Mark notification as read',
                'PUT /api/notifications/read-all' => 'Mark all as read',
                'DELETE /api/notifications/{id}' => 'Delete notification',
                'GET /api/notifications/unread/count' => 'Get unread count'
            ],
            'Location Pins' => [
                'GET /api/location-pins' => 'Get location pins',
                'POST /api/location-pins' => 'Create location pin',
                'PUT /api/location-pins/{id}' => 'Update location pin',
                'DELETE /api/location-pins/{id}' => 'Delete location pin',
                'POST /api/location-pins/nearby' => 'Find nearby pins',
                'POST /api/location-pins/geocode' => 'Geocode address',
                'POST /api/location-pins/reverse-geocode' => 'Reverse geocode coordinates'
            ]
        ],
        'authentication' => 'Bearer token required for protected routes',
        'firebase_integration' => 'All data synchronized with Firebase Firestore',
        'database' => 'MySQL with Docker containerization',
        'architecture' => 'MVC pattern maintained'
    ]);
});