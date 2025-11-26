<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class EnsureProviderSubscribed
{
    public function handle($request, Closure $next)
    {
        $providerId = (int)$request->input('provider_id', 0);
        if ($providerId > 0) {
            $row = DB::table('providers')->where('id', $providerId)->first();
            $active = $row && (string)($row->subscription_status ?? '') === 'active';
            if (!$active) return response()->json(['message' => 'subscription_required'], 403);
        }
        return $next($request);
    }
}