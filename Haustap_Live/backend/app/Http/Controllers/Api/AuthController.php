<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController
{
    public function register(Request $request): JsonResponse
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');
        $name = trim((string)$request->input('name'));
        if ($email === '' || $password === '') return response()->json(['success' => false], 422);
        $u = new User();
        $u->name = $name !== '' ? $name : $email;
        $u->email = $email;
        $u->password = Hash::make($password);
        $u->role = 'client';
        $u->save();
        return response()->json(['success' => true]);
    }

    public function login(Request $request): JsonResponse
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');
        $u = User::where('email', $email)->first();
        if (!$u || !Hash::check($password, (string)$u->password)) return response()->json(['success' => false], 401);
        $token = method_exists($u, 'createToken') ? $u->createToken('api')->plainTextToken : ('dev-'.bin2hex(random_bytes(16)));
        return response()->json(['success' => true, 'token' => $token]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json($user ? $user->only(['id','name','email','role','status']) : []);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) return response()->json(['success' => false], 401);
        $user->name = trim((string)$request->input('name', $user->name));
        $user->save();
        return response()->json(['success' => true]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && method_exists($user, 'currentAccessToken')) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['success' => true]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }
        return response()->json(['success' => true]);
    }
}