<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PaymentsController
{
    public function subscribe(Request $request): JsonResponse
    {
        $providerId = (int)$request->input('provider_id');
        if ($providerId <= 0) return response()->json(['success' => false], 422);
        $days = (int)env('SUBSCRIPTION_DURATION_DAYS', 30);
        $expires = now()->addDays($days);
        DB::table('providers')->where('id', $providerId)->update(['subscription_status' => 'active', 'subscription_expires_at' => $expires]);
        return response()->json(['success' => true, 'expires_at' => $expires->toDateTimeString()]);
    }
}