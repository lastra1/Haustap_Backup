<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function health()
    {
        $checks = [];
        $overallStatus = 'healthy';
        
        // Check database connection
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $dbTime = round((microtime(true) - $start) * 1000, 2);
            $checks['database'] = [
                'status' => 'healthy',
                'response_time_ms' => $dbTime,
                'message' => 'Connected successfully'
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'unhealthy',
                'response_time_ms' => null,
                'message' => $e->getMessage()
            ];
            $overallStatus = 'unhealthy';
        }

        // Check cache
        try {
            $start = microtime(true);
            Cache::put('health_check', 'test', 1);
            $value = Cache::get('health_check');
            Cache::forget('health_check');
            $cacheTime = round((microtime(true) - $start) * 1000, 2);
            $checks['cache'] = [
                'status' => $value === 'test' ? 'healthy' : 'unhealthy',
                'response_time_ms' => $cacheTime,
                'message' => $value === 'test' ? 'Cache working' : 'Cache value mismatch'
            ];
            if ($value !== 'test') {
                $overallStatus = 'unhealthy';
            }
        } catch (\Exception $e) {
            $checks['cache'] = [
                'status' => 'unhealthy',
                'response_time_ms' => null,
                'message' => $e->getMessage()
            ];
            $overallStatus = 'unhealthy';
        }

        // Check storage
        try {
            $start = microtime(true);
            Storage::disk('local')->put('health_check.txt', 'test');
            $content = Storage::disk('local')->get('health_check.txt');
            Storage::disk('local')->delete('health_check.txt');
            $storageTime = round((microtime(true) - $start) * 1000, 2);
            $checks['storage'] = [
                'status' => $content === 'test' ? 'healthy' : 'unhealthy',
                'response_time_ms' => $storageTime,
                'message' => $content === 'test' ? 'Storage working' : 'Storage value mismatch'
            ];
            if ($content !== 'test') {
                $overallStatus = 'unhealthy';
            }
        } catch (\Exception $e) {
            $checks['storage'] = [
                'status' => 'unhealthy',
                'response_time_ms' => null,
                'message' => $e->getMessage()
            ];
            $overallStatus = 'unhealthy';
        }

        // Check application status
        $checks['application'] = [
            'status' => 'healthy',
            'version' => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
            'laravel_version' => app()->version()
        ];

        return response()->json([
            'status' => $overallStatus,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
            'services' => [
                'database' => $checks['database']['status'],
                'cache' => $checks['cache']['status'],
                'storage' => $checks['storage']['status'],
                'application' => $checks['application']['status']
            ]
        ], $overallStatus === 'healthy' ? 200 : 503);
    }
}