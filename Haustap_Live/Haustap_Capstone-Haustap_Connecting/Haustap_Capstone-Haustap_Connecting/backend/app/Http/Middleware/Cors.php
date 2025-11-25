<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('Origin');
        $allowedOrigin = $origin ?: '*';
        $allowCredentials = $origin ? 'true' : 'false';
        $allowedHeaders = 'Content-Type, Authorization, X-Requested-With, Accept, Origin';
        $allowedMethods = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';

        // Handle preflight
        if ($request->getMethod() === 'OPTIONS') {
            return response('OK', 200)
                ->withHeaders([
                    'Access-Control-Allow-Origin' => $allowedOrigin,
                    'Access-Control-Allow-Methods' => $allowedMethods,
                    'Access-Control-Allow-Headers' => $allowedHeaders,
                    'Access-Control-Allow-Credentials' => $allowCredentials,
                    'Access-Control-Max-Age' => '86400',
                    'Vary' => 'Origin',
                ]);
        }

        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
        $response->headers->set('Access-Control-Allow-Methods', $allowedMethods);
        $response->headers->set('Access-Control-Allow-Headers', $allowedHeaders);
        $response->headers->set('Access-Control-Allow-Credentials', $allowCredentials);
        $response->headers->set('Vary', 'Origin');

        return $response;
    }

    private function allowedOrigin(): string
    {
        // Allow local dev servers
        $origin = request()->headers->get('Origin');
        return $origin ?: '*';
    }
}