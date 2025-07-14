<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\TestServiceInterface;
use App\Services\DependencyTest\BaseServiceInterface;
use App\Services\DependencyTest\InterfaceDependentService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class InterfaceRequestPersistenceController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test if interface-based service instances persist between requests
     */
    public function testInterfaceRequestPersistence()
    {
        $cacheKey = 'interface_request_persistence_test';
        $currentRequestId = uniqid('int_req_');
        
        // Get current interface instances
        $testServiceInterface = app(TestServiceInterface::class);
        $baseServiceInterface = app(BaseServiceInterface::class);
        $dependentServiceInterface = app(InterfaceDependentService::class);
        
        // Current request data
        $currentData = [
            'request_id' => $currentRequestId,
            'timestamp' => now()->format('Y-m-d H:i:s.u'),
            'test_service_interface' => [
                'instance_id' => $testServiceInterface->instanceId,
                'object_hash' => spl_object_hash($testServiceInterface),
                'data' => $testServiceInterface->getData()
            ],
            'base_service_interface' => [
                'instance_id' => $baseServiceInterface->instanceId,
                'object_hash' => spl_object_hash($baseServiceInterface),
                'action_count' => $baseServiceInterface->actionCount
            ],
            'dependent_service_interface' => [
                'instance_id' => $dependentServiceInterface->instanceId,
                'object_hash' => spl_object_hash($dependentServiceInterface),
                'data' => $dependentServiceInterface->getData()
            ]
        ];
        
        // Execute some actions to modify state
        $testServiceInterface->performAction('interface_request_' . $currentRequestId);
        $baseServiceInterface->performAction('interface_test_action_' . $currentRequestId);
        
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
        $analysis = $this->analyzeInterfaceRequestPersistence($previousRequests);
        
        return view('interface-request-persistence-test', [
            'current_request' => $currentData,
            'request_history' => $previousRequests,
            'analysis' => $analysis,
            'test_type' => 'Interface-based Request Persistence'
        ]);
    }
} 