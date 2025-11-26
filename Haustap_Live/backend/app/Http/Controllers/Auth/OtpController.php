<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class OtpController
{
    public function send(Request $request): JsonResponse
    {
        $email = trim((string)$request->input('email'));
        if ($email === '') return response()->json(['success' => false], 422);
        $code = (string)random_int(100000, 999999);
        $path = 'data/otp.json';
        $raw = Storage::disk('local')->exists($path) ? Storage::disk('local')->get($path) : '[]';
        $data = json_decode($raw, true) ?: [];
        $data[$email] = ['code' => $code, 'ts' => time()];
        Storage::disk('local')->put($path, json_encode($data));
        return response()->json(['success' => true, 'code' => $code]);
    }

    public function verify(Request $request): JsonResponse
    {
        $email = trim((string)$request->input('email'));
        $code = trim((string)$request->input('code'));
        $path = 'data/otp.json';
        $raw = Storage::disk('local')->exists($path) ? Storage::disk('local')->get($path) : '[]';
        $data = json_decode($raw, true) ?: [];
        $ok = isset($data[$email]) && (string)($data[$email]['code'] ?? '') === $code;
        return response()->json(['success' => $ok]);
    }
}