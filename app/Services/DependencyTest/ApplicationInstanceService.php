<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

/**
 * Service to track Laravel Application instance lifecycle and metrics
 * 
 * This service tracks the actual Laravel Application instance (not this service instance)
 * to monitor application persistence across requests, memory usage, and performance metrics.
 */
class ApplicationInstanceService
{
    public $instanceId;         // Laravel Application instance object hash
    public $startTime;          // When tracking started (microtime)
    public $startTimestamp;     // When tracking started (formatted)
    public $requestCount;       // Number of requests processed
    public $processId;          // Operating system process ID
    public $memoryUsage;        // Initial memory usage
    public $createdAt;          // When tracking service was created
    
    public function __construct()
    {
        // Get the Laravel application instance ID
        $laravelApp = app();
        $this->instanceId = spl_object_hash($laravelApp);
        $this->startTime = microtime(true);
        $this->startTimestamp = now()->format('Y-m-d H:i:s.u');
        $this->requestCount = 0;
        $this->processId = getmypid();
        $this->memoryUsage = memory_get_usage(true);
        $this->createdAt = now();
        
        Log::info("Laravel Application instance tracked: {$this->instanceId} at {$this->startTimestamp} (PID: {$this->processId})");
    }
    
    public function incrementRequestCount()
    {
        $this->requestCount++;
        Log::info("Laravel application instance {$this->instanceId} processed request #{$this->requestCount}");
        return $this->requestCount;
    }
    
    public function getInstanceInfo()
    {
        $currentMemory = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        $uptime = microtime(true) - $this->startTime;
        $laravelApp = app();
        
        // Detect if Octane is running
        $isOctane = $this->detectOctane();
        $serverInfo = $this->getServerInfo();
        
        return [
            'laravel_app_instance_id' => $this->instanceId,
            'laravel_app_object_hash' => spl_object_hash($laravelApp),
            'laravel_app_object_id' => spl_object_id($laravelApp),
            'start_time' => $this->startTimestamp,
            'uptime_seconds' => round($uptime, 3),
            'uptime_formatted' => $this->formatUptime($uptime),
            'request_count' => $this->requestCount,
            'process_id' => $this->processId,
            'memory_usage' => [
                'current' => $this->formatBytes($currentMemory),
                'current_bytes' => $currentMemory,
                'peak' => $this->formatBytes($peakMemory),
                'peak_bytes' => $peakMemory,
                'initial' => $this->formatBytes($this->memoryUsage),
                'initial_bytes' => $this->memoryUsage
            ],
            'tracker_service_hash' => spl_object_hash($this),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s.u'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_environment' => app()->environment(),
            'app_debug' => app()->hasDebugModeEnabled(),
            'octane_info' => [
                'is_octane' => $isOctane['is_octane'],
                'server_type' => $isOctane['server_type'],
                'detection_method' => $isOctane['detection_method'],
                'swoole_available' => extension_loaded('swoole'),
                'roadrunner_available' => class_exists('\Spiral\RoadRunner\Http\PSR7Worker'),
                'frankenphp_available' => function_exists('frankenphp_handle_request'),
            ],
            'server_info' => $serverInfo,
            'instance_analysis' => $this->analyzeInstanceBehavior()
        ];
    }
    
    public function getRequestMetrics()
    {
        return [
            'laravel_app_instance_id' => $this->instanceId,
            'request_count' => $this->requestCount,
            'average_memory_per_request' => $this->requestCount > 0 ? 
                round((memory_get_usage(true) - $this->memoryUsage) / $this->requestCount) : 0,
            'requests_per_second' => $this->requestCount > 0 ? 
                round($this->requestCount / (microtime(true) - $this->startTime), 2) : 0
        ];
    }
    
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    private function formatUptime($seconds)
    {
        if ($seconds < 60) {
            return round($seconds, 1) . 's';
        } elseif ($seconds < 3600) {
            return round($seconds / 60, 1) . 'm';
        } else {
            return round($seconds / 3600, 1) . 'h';
        }
    }
    
    public function logRequestDetails($requestPath = null, $requestMethod = null)
    {
        $this->incrementRequestCount();
        $octaneInfo = $this->detectOctane();
        
        Log::info("Request #{$this->requestCount} processed by Laravel application instance {$this->instanceId}", [
            'path' => $requestPath,
            'method' => $requestMethod,
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'uptime' => $this->formatUptime(microtime(true) - $this->startTime),
            'laravel_app_hash' => spl_object_hash(app()),
            'laravel_app_id' => spl_object_id(app()),
            'is_octane' => $octaneInfo['is_octane'],
            'server_type' => $octaneInfo['server_type'],
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
        ]);
        
        // Log instance change detection
        if ($this->requestCount === 1) {
            Log::warning("NEW Laravel Application Instance Created", [
                'instance_id' => $this->instanceId,
                'server_type' => $octaneInfo['server_type'],
                'is_octane' => $octaneInfo['is_octane'],
                'process_id' => $this->processId,
                'possible_reasons' => $this->getPossibleReasons($octaneInfo)
            ]);
        }
    }
    
    private function detectOctane()
    {
        // Multiple detection methods for Octane
        $detectionMethods = [];
        
        // Method 1: Check for Octane-specific classes
        if (class_exists('\Laravel\Octane\Octane')) {
            $detectionMethods[] = 'Octane class exists';
        }
        
        // Method 2: Check SERVER variables
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';
        if (str_contains(strtolower($serverSoftware), 'swoole')) {
            $detectionMethods[] = 'Swoole in SERVER_SOFTWARE';
        }
        if (str_contains(strtolower($serverSoftware), 'roadrunner')) {
            $detectionMethods[] = 'RoadRunner in SERVER_SOFTWARE';
        }
        if (str_contains(strtolower($serverSoftware), 'caddy') || str_contains(strtolower($serverSoftware), 'frankenphp')) {
            $detectionMethods[] = 'FrankenPHP/Caddy in SERVER_SOFTWARE';
        }
        
        // Method 3: Check for Octane environment variables
        if (isset($_SERVER['LARAVEL_OCTANE'])) {
            $detectionMethods[] = 'LARAVEL_OCTANE env var';
        }
        
        // Method 4: Check for FrankenPHP-specific environment variables
        if (isset($_SERVER['FRANKENPHP_CONFIG']) || isset($_SERVER['FRANKENPHP_NUM_WORKERS'])) {
            $detectionMethods[] = 'FrankenPHP environment variables';
        }
        
        // Method 5: Check for Swoole/RoadRunner specific globals
        if (function_exists('swoole_version')) {
            $detectionMethods[] = 'Swoole functions available';
        }
        
        // Method 6: Check application bound instances
        if (app()->bound('octane')) {
            $detectionMethods[] = 'Octane service bound';
        }
        
        // Method 7: Check for FrankenPHP specific functions/classes
        if (function_exists('frankenphp_handle_request')) {
            $detectionMethods[] = 'FrankenPHP functions available';
        }
        
        // Determine server type
        $serverType = 'unknown';
        if (str_contains(strtolower($serverSoftware), 'frankenphp') || 
            str_contains(strtolower($serverSoftware), 'caddy') ||
            isset($_SERVER['FRANKENPHP_CONFIG']) ||
            function_exists('frankenphp_handle_request')) {
            $serverType = 'frankenphp';
        } elseif (extension_loaded('swoole') || function_exists('swoole_version')) {
            $serverType = 'swoole';
        } elseif (class_exists('\Spiral\RoadRunner\Http\PSR7Worker')) {
            $serverType = 'roadrunner';
        } elseif (str_contains(strtolower($serverSoftware), 'apache')) {
            $serverType = 'apache';
        } elseif (str_contains(strtolower($serverSoftware), 'nginx')) {
            $serverType = 'nginx';
        } elseif (str_contains(strtolower($serverSoftware), 'development server')) {
            $serverType = 'artisan_serve';
        }
        
        return [
            'is_octane' => !empty($detectionMethods),
            'server_type' => $serverType,
            'detection_method' => implode(', ', $detectionMethods),
            'server_software' => $serverSoftware
        ];
    }
    
    private function getServerInfo()
    {
        return [
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'http_host' => $_SERVER['HTTP_HOST'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'php_sapi' => php_sapi_name(),
            'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown'
        ];
    }
    
    private function analyzeInstanceBehavior()
    {
        $currentAppId = spl_object_id(app());
        $isNewInstance = $this->requestCount === 1;
        
        return [
            'current_app_id' => $currentAppId,
            'is_new_instance' => $isNewInstance,
            'instance_age_seconds' => microtime(true) - $this->startTime,
            'requests_processed' => $this->requestCount,
            'memory_growth' => memory_get_usage(true) - $this->memoryUsage,
            'memory_growth_formatted' => $this->formatBytes(memory_get_usage(true) - $this->memoryUsage)
        ];
    }
    
    private function getPossibleReasons($octaneInfo)
    {
        $reasons = [];
        
        if (!$octaneInfo['is_octane']) {
            $reasons[] = 'Not running under Octane - using traditional PHP lifecycle';
        }
        
        if ($octaneInfo['server_type'] === 'artisan_serve') {
            $reasons[] = 'Using artisan serve - creates new instances per request';
        }
        
        if ($octaneInfo['server_type'] === 'apache' || $octaneInfo['server_type'] === 'nginx') {
            $reasons[] = 'Traditional web server - new process/instance per request';
        }
        
        if ($octaneInfo['server_type'] === 'frankenphp') {
            if ($this->requestCount === 1) {
                $reasons[] = 'FrankenPHP worker restart (memory limit, max requests, or error recovery)';
                $reasons[] = 'First request to this FrankenPHP worker';
                $reasons[] = 'FrankenPHP configuration may need adjustment (check FRANKENPHP_NUM_WORKERS)';
            } else {
                $reasons[] = 'FrankenPHP is properly persisting application instances';
            }
        }
        
        if ($octaneInfo['is_octane'] && $this->requestCount === 1 && $octaneInfo['server_type'] !== 'frankenphp') {
            $reasons[] = 'Octane worker restart (memory limit, max requests, or error recovery)';
            $reasons[] = 'First request to this Octane worker';
            $reasons[] = 'Octane configuration may need adjustment';
        }
        
        return $reasons;
    }
} 