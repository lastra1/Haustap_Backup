<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class PasswordController
{
    public function register(Request $request): JsonResponse
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');
        $name = trim((string)$request->input('name'));
        if ($email === '' || $password === '') return response()->json(['success' => false], 422);
        $hash = Hash::make($password);
        DB::table('users')->insert(['name' => ($name !== '' ? $name : $email), 'email' => $email, 'password' => $hash, 'role' => 'client', 'created_at' => now(), 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function login(Request $request): JsonResponse
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');
        $row = DB::table('users')->where('email', $email)->first();
        if (!$row || !Hash::check($password, (string)$row->password)) return response()->json(['success' => false], 401);
        return response()->json(['success' => true, 'token' => 'dev-'.bin2hex(random_bytes(16)), 'role' => (string)($row->role ?? '')]);
    }

    public function reset(Request $request): JsonResponse
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');
        if ($email === '' || $password === '') return response()->json(['success' => false], 422);
        $hash = Hash::make($password);
        DB::table('users')->where('email', $email)->update(['password' => $hash, 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }
}