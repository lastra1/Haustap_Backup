<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller {
    public function register(Request $request) {
        $data = $request->all();
        // Combine names if provided separately
        $name = $data['name'] ?? trim(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? ''));
        $validator = Validator::make([
            'name' => $name,
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ?? null,
            'confirmPassword' => $data['confirmPassword'] ?? null,
        ], [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'confirmPassword' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::create([
            'name' => $name,
            'email' => $data['email'],
            'password' => $data['password'], // hashed via model cast
        ]);
        $token = $user->createToken('web')->plainTextToken;
        // Eager load role for consistency
        $user->load('role');
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $user->role ? ['id' => $user->role->id, 'name' => $user->role->name] : null,
            ]
        ], 201);
    }

    public function login(Request $request) {
        // Validation for login input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Include role info in response
        $user->load('role');
        $token = $user->createToken('web')->plainTextToken;
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $user->role ? ['id' => $user->role->id, 'name' => $user->role->name] : null,
            ]
        ]);
    }

    // Return the authenticated user (requires Bearer token)
    public function me(Request $request) {
        $user = $request->user();
        $user?->load('role');
        return response()->json($user);
    }

    // Revoke the current access token (logout)
    public function logout(Request $request) {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['success' => true]);
    }

    /**
     * Send OTP email via Laravel mailer (used by mobile & web clients).
     * Accepts: { email: string, otp: string }
     * Returns: { success: boolean }
     */
    public function sendOtp(Request $request) {
        // Validate email and OTP format
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->input('email');
        $otp = $request->input('otp');
        try {
            Mail::raw("Your HausTap OTP is: {$otp}", function ($message) use ($email) {
                $message->to($email)->subject('HausTap OTP Verification');
            });
            Log::info('OTP email queued', ['email' => $email]);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send OTP email; logged instead', [
                'email' => $email,
                'otp' => $otp,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Mailer not configured; OTP logged server-side.'
            ]);
        }
    }
}
