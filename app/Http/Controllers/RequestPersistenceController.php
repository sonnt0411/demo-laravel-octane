<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\TestService;
use App\Services\DependencyTest\BaseService;
use App\Services\DependencyTest\DependentService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class RequestPersistenceController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test if service instances persist between requests
     * This method tracks instance information across multiple HTTP requests
     */
    public function testRequestPersistence()
    {
        $cacheKey = 'request_persistence_test';
        $currentRequestId = uniqid('req_');
        
        // Get current instances
        $testService = app(TestService::class);
        $baseService = app(BaseService::class);
        $dependentService = app(DependentService::class);
        
        // Current request data
        $currentData = [
            'request_id' => $currentRequestId,
            'timestamp' => now()->format('Y-m-d H:i:s.u'),
            'test_service' => [
                'instance_id' => $testService->instanceId,
                'object_hash' => spl_object_hash($testService),
                'data' => $testService->getData()
            ],
            'base_service' => [
                'instance_id' => $baseService->instanceId,
                'object_hash' => spl_object_hash($baseService),
                'action_count' => $baseService->actionCount
            ],
            'dependent_service' => [
                'instance_id' => $dependentService->instanceId,
                'object_hash' => spl_object_hash($dependentService),
                'data' => $dependentService->getData()
            ]
        ];
        
        // Execute some actions to modify state
        $testService->performAction('request_' . $currentRequestId);
        $baseService->performAction('test_action_' . $currentRequestId);
        
        // Get previous requests from cache
        $previousRequests = cache()->get($cacheKey, []);
        
        // Add current request to history
        $previousRequests[] = $currentData;
        
        // Keep only last 10 requests
        if (count($previousRequests) > 10) {
            $previousRequests = array_slice($previousRequests, -10);
        }
        
        // Store in cache for 1 hour
        cache()->put($cacheKey, $previousRequests, now()->addHour());
        
        // Analyze persistence
        $analysis = $this->analyzeRequestPersistence($previousRequests);
        
        return view('request-persistence-test', [
            'current_request' => $currentData,
            'request_history' => $previousRequests,
            'analysis' => $analysis,
            'test_type' => 'Concrete Class Request Persistence'
        ]);
    }
} 