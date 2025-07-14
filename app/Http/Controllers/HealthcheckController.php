<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Services\DependencyTest\TestService;

class HealthcheckController extends Controller
{
    /**
     * Healthcheck endpoint to verify application status
     */
    public function healthcheck()
    {
        $status = [
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'services' => []
        ];

        try {
            // Test database connectivity
            DB::connection()->getPdo();
            $status['services']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $status['services']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
            $status['status'] = 'degraded';
        }

        try {
            // Test cache connectivity
            cache()->put('healthcheck_test', 'ok', 1);
            $cacheTest = cache()->get('healthcheck_test');
            
            if ($cacheTest === 'ok') {
                $status['services']['cache'] = [
                    'status' => 'ok',
                    'message' => 'Cache is working properly'
                ];
            } else {
                throw new \Exception('Cache test failed');
            }
        } catch (\Exception $e) {
            $status['services']['cache'] = [
                'status' => 'error',
                'message' => 'Cache connection failed: ' . $e->getMessage()
            ];
            $status['status'] = 'degraded';
        }

        // Test basic service container
        try {
            $testService = app(TestService::class);
            $status['services']['container'] = [
                'status' => 'ok',
                'message' => 'Service container is working properly'
            ];
        } catch (\Exception $e) {
            $status['services']['container'] = [
                'status' => 'error',
                'message' => 'Service container failed: ' . $e->getMessage()
            ];
            $status['status'] = 'degraded';
        }

        // Set HTTP status code based on overall health
        $httpStatus = $status['status'] === 'ok' ? 200 : 503;

        return response()->json($status, $httpStatus);
    }
} 