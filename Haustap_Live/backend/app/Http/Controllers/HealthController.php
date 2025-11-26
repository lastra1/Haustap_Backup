<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController
{
    public function health(): JsonResponse
    {
        return response()->json(['ok' => true]);
    }

    public function ping(): JsonResponse
    {
        return response()->json(['pong' => true]);
    }
}