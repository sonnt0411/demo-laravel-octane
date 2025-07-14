<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\PureSingletonClass;
use App\Services\DependencyTest\PureSingletonTestService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class PureSingletonController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test Pure Singleton Pattern - tests if pure PHP singleton instances persist between requests
     */
    public function testPureSingleton()
    {
        $results = [];
        
        // Check if singleton already exists before we start
        $preTestState = PureSingletonClass::hasInstance();
        $preTestTotalInstances = PureSingletonClass::getTotalInstancesCreated();
        
        // Create a test service instance
        $testService = new PureSingletonTestService();
        
        // Test 1: Basic singleton behavior
        $basicTest = $testService->testPureSingleton();
        
        // Test 2: Current singleton state
        $currentState = $testService->getCurrentSingletonState();
        
        // Test 3: Service interaction with singleton
        $serviceInteraction = $testService->testSingletonWithServiceInteraction();
        
        // Test 4: Multiple service instances accessing singleton
        $multipleServices = PureSingletonTestService::compareMultipleServiceInstances();
        
        // Test 5: Direct singleton access
        $directSingleton1 = PureSingletonClass::getInstance();
        $directSingleton2 = PureSingletonClass::getInstance();
        
        $directSingleton1->performAction('direct_access_1');
        $directSingleton2->performAction('direct_access_2');
        
        $directComparison = [
            'same_instance' => $directSingleton1 === $directSingleton2,
            'object_id_1' => spl_object_id($directSingleton1),
            'object_id_2' => spl_object_id($directSingleton2),
            'same_object_id' => spl_object_id($directSingleton1) === spl_object_id($directSingleton2),
            'final_action_count' => $directSingleton1->getActionCount(),
            'instance_info' => $directSingleton1->getInstanceInfo()
        ];
        
        // Store request data for persistence analysis
        $requestId = uniqid('pure_singleton_', true);
        $requestData = [
            'request_id' => $requestId,
            'timestamp' => now()->toISOString(),
            'pre_test_had_instance' => $preTestState,
            'pre_test_total_instances' => $preTestTotalInstances,
            'singleton_object_id' => spl_object_id($directSingleton1),
            'singleton_object_hash' => spl_object_hash($directSingleton1),
            'singleton_instance_id' => $directSingleton1->getInstanceId(),
            'final_action_count' => $directSingleton1->getActionCount(),
            'total_instances_created' => PureSingletonClass::getTotalInstancesCreated(),
            'final_singleton_info' => $directSingleton1->getInstanceInfo()
        ];
        
        // Store in cache for cross-request comparison
        $allRequests = cache()->get('pure_singleton_requests', []);
        $allRequests[] = $requestData;
        cache()->put('pure_singleton_requests', $allRequests, 3600); // Store for 1 hour
        
        $results = [
            'pre_test_state' => [
                'had_instance' => $preTestState,
                'total_instances' => $preTestTotalInstances
            ],
            'basic_test' => $basicTest,
            'current_state' => $currentState,
            'service_interaction' => $serviceInteraction,
            'multiple_services' => $multipleServices,
            'direct_comparison' => $directComparison,
            'current_request' => $requestData,
            'all_requests' => $allRequests,
            'test_service_info' => $testService->getServiceInfo()
        ];
        
        return view('pure-singleton-test', [
            'results' => $results,
            'analysis' => $this->analyzePureSingleton($results)
        ]);
    }
} 