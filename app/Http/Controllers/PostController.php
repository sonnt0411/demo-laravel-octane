<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DependencyTest\TestService;
use App\Services\DependencyTest\DependentService;
use App\Services\DependencyTest\BaseService;
use App\Services\DependencyTest\FirstService;
use App\Services\DependencyTest\SecondService;
use App\Services\DependencyTest\TestServiceInterface;
use App\Services\DependencyTest\InterfaceDependentService;
use App\Services\DependencyTest\BaseServiceInterface;
use App\Services\DependencyTest\InterfaceFirstService;
use App\Services\DependencyTest\InterfaceSecondService;
use App\Services\DependencyTest\ApplicationInjectedService;
use App\Services\DependencyTest\PureSingletonClass;
use App\Services\DependencyTest\PureSingletonTestService;
use App\Services\DependencyTest\RequestScopedService;

class PostController extends Controller
{
    private $testService;
    
    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }
    
    public function index()
    {
        $results = [];
        
        // Test 1: Using constructor injection (already resolved)
        $results['constructor_injection'] = $this->testService->getData();
        
        // Test 2: Manual resolution from container
        $manualResolve1 = app(TestService::class);
        $results['manual_resolve_1'] = $manualResolve1->getData();
        
        // Test 3: Another manual resolution
        $manualResolve2 = app(TestService::class);
        $results['manual_resolve_2'] = $manualResolve2->getData();
        
        // Test 4: Using make() method
        $makeResolve = app()->make(TestService::class);
        $results['make_resolve'] = $makeResolve->getData();
        
        // Test 5: Resolving dependent service (which injects TestService)
        $dependentService1 = app(DependentService::class);
        $results['dependent_service_1'] = $dependentService1->getData();
        
        // Test 6: Another dependent service resolution
        $dependentService2 = app(DependentService::class);
        $results['dependent_service_2'] = $dependentService2->getData();
        
        // Test 7: Using method injection
        return $this->testMethodInjection($results);
    }
    
    public function testMethodInjection($previousResults, TestService $methodInjectedService = null)
    {
        if ($methodInjectedService) {
            $previousResults['method_injection'] = $methodInjectedService->getData();
        }
        
        // Test 8: Resolve with custom parameters (this should always create new instance)
        $customInstance = new TestService();
        $previousResults['custom_instance'] = $customInstance->getData();
        
        return view('dependency-test', [
            'results' => $previousResults,
            'summary' => $this->analyzResults($previousResults)
        ]);
    }
    
    // NEW METHOD: Test shared dependency injection
    public function testSharedDependency(FirstService $firstService, SecondService $secondService)
    {
        $results = [];
        
        // Get initial service information
        $firstInfo = $firstService->getServiceInfo();
        $secondInfo = $secondService->getServiceInfo();
        
        // PROPER SHARED STATE TEST: Execute different numbers of actions
        
        // Step 1: Execute 2 actions on FirstService
        $firstTask1 = $firstService->executeTask('test_task_1');
        $firstTask2 = $firstService->executeTask('test_task_2');
        
        // Step 2: Execute 1 action on SecondService
        $secondTask1 = $secondService->processData('test_data_1');
        
        // Step 3: Execute 1 more action on FirstService  
        $firstTask3 = $firstService->executeTask('test_task_3');
        
        // Step 4: Execute 2 more actions on SecondService
        $secondTask2 = $secondService->processData('test_data_2');
        $secondTask3 = $secondService->processData('test_data_3');
        
        // Check if BaseService instances are the same
        $baseService1 = $firstService->baseService;
        $baseService2 = $secondService->baseService;
        
        $isSameObject = ($baseService1 === $baseService2);
        $sameObjectHash = (spl_object_hash($baseService1) === spl_object_hash($baseService2));
        $sameInstanceId = ($baseService1->instanceId === $baseService2->instanceId);
        
        // Expected action counts:
        // If separate instances: FirstService BaseService = 3, SecondService BaseService = 3
        // If shared instance: Both would have total = 6 (3 + 3)
        
        $results = [
            'initial_state' => [
                'first_service_info' => $firstInfo,
                'second_service_info' => $secondInfo,
            ],
            'execution_sequence' => [
                'step_1_first_task_1' => $firstTask1,
                'step_2_first_task_2' => $firstTask2,
                'step_3_second_task_1' => $secondTask1,
                'step_4_first_task_3' => $firstTask3,
                'step_5_second_task_2' => $secondTask2,
                'step_6_second_task_3' => $secondTask3,
            ],
            'final_state' => [
                'first_service_final' => $firstService->getServiceInfo(),
                'second_service_final' => $secondService->getServiceInfo(),
            ],
            'dependency_comparison' => [
                'is_same_object' => $isSameObject,
                'same_object_hash' => $sameObjectHash,
                'same_instance_id' => $sameInstanceId,
                'first_base_hash' => spl_object_hash($baseService1),
                'second_base_hash' => spl_object_hash($baseService2),
                'first_base_id' => $baseService1->instanceId,
                'second_base_id' => $baseService2->instanceId,
                'first_base_action_count' => $baseService1->actionCount,
                'second_base_action_count' => $baseService2->actionCount,
                'total_actions_executed' => 6,
                'expected_if_shared' => 6,
                'expected_if_separate' => 'First: 3, Second: 3'
            ]
        ];
        
        return view('shared-dependency-test', [
            'results' => $results,
            'summary' => $this->analyzeSharedDependency($results)
        ]);
    }
    
    private function analyzeSharedDependency($results)
    {
        $comparison = $results['dependency_comparison'];
        $firstCount = $comparison['first_base_action_count'];
        $secondCount = $comparison['second_base_action_count'];
        $totalActions = $comparison['total_actions_executed'];
        
        // Analyze shared state based on action counts
        $sharedStateAnalysis = '';
        if ($comparison['is_same_object']) {
            $sharedStateAnalysis = "SHARED INSTANCE: Both services should show actionCount = {$totalActions}";
        } else {
            $sharedStateAnalysis = "SEPARATE INSTANCES: FirstService BaseService = 3 actions, SecondService BaseService = 3 actions";
        }
        
        return [
            'is_shared' => $comparison['is_same_object'],
            'verification_methods' => [
                'strict_comparison' => $comparison['is_same_object'] ? 'SAME OBJECT' : 'DIFFERENT OBJECTS',
                'object_hash_comparison' => $comparison['same_object_hash'] ? 'SAME HASH' : 'DIFFERENT HASH',
                'instance_id_comparison' => $comparison['same_instance_id'] ? 'SAME ID' : 'DIFFERENT ID'
            ],
            'action_count_analysis' => [
                'first_service_count' => $firstCount,
                'second_service_count' => $secondCount,
                'total_actions_executed' => $totalActions,
                'expected_if_shared' => $totalActions,
                'expected_if_separate' => '3 each',
                'actual_behavior' => "First: {$firstCount}, Second: {$secondCount}",
                'shared_state_verdict' => $sharedStateAnalysis
            ],
            'conclusion' => $comparison['is_same_object'] 
                ? "Laravel's service container SHARES the same BaseService instance. Both services show actionCount = {$firstCount}"
                : "Laravel's service container creates SEPARATE BaseService instances. First: {$firstCount} actions, Second: {$secondCount} actions"
        ];
    }
    
    private function analyzResults($results)
    {
        $instances = [];
        $summary = [
            'total_tests' => count($results),
            'unique_instances' => 0,
            'same_instances' => 0,
            'analysis' => []
        ];
        
        foreach ($results as $testName => $data) {
            if (isset($data['test_service'])) {
                // This is from DependentService
                $instanceId = $data['test_service']['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'nested'];
            } else {
                // Direct TestService instance
                $instanceId = $data['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'direct'];
            }
        }
        
        // Count unique instances
        $uniqueIds = array_unique(array_column($instances, 'id'));
        $summary['unique_instances'] = count($uniqueIds);
        $summary['same_instances'] = count($instances) - count($uniqueIds);
        
        // Detailed analysis
        $idCounts = array_count_values(array_column($instances, 'id'));
        foreach ($idCounts as $id => $count) {
            $testsWithSameId = array_filter($instances, fn($instance) => $instance['id'] === $id);
            $testNames = array_column($testsWithSameId, 'test');
            
            $summary['analysis'][] = [
                'instance_id' => $id,
                'used_in_tests' => $testNames,
                'usage_count' => $count,
                'is_reused' => $count > 1
            ];
        }
        
        return $summary;
    }
    
    // Additional test route for singleton behavior
    public function testSingleton()
    {
        // First, let's bind TestService as singleton
        app()->singleton(TestService::class, function ($app) {
            return new TestService();
        });
        
        $results = [];
        
        // Test singleton behavior
        $singleton1 = app(TestService::class);
        $results['singleton_1'] = $singleton1->getData();
        
        $singleton2 = app(TestService::class);
        $results['singleton_2'] = $singleton2->getData();
        
        $singleton3 = app()->make(TestService::class);
        $results['singleton_3'] = $singleton3->getData();
        
        // Test with dependent services
        $dependentSingleton1 = app(DependentService::class);
        $results['dependent_singleton_1'] = $dependentSingleton1->getData();
        
        $dependentSingleton2 = app(DependentService::class);
        $results['dependent_singleton_2'] = $dependentSingleton2->getData();
        
        return view('dependency-test', [
            'results' => $results,
            'summary' => $this->analyzResults($results),
            'test_type' => 'Singleton Test'
        ]);
    }
    
    // INTERFACE-BASED DEPENDENCY INJECTION TESTS
    
    /**
     * Test interface-based dependency injection
     */
    public function testInterfaceDependency()
    {
        $results = [];
        
        // Test 1: Direct interface resolution
        $interface1 = app(TestServiceInterface::class);
        $results['interface_resolve_1'] = $interface1->getData();
        
        // Test 2: Another interface resolution
        $interface2 = app(TestServiceInterface::class);
        $results['interface_resolve_2'] = $interface2->getData();
        
        // Test 3: Using make() method
        $interfaceMake = app()->make(TestServiceInterface::class);
        $results['interface_make'] = $interfaceMake->getData();
        
        // Test 4: Resolving service that depends on interface
        $interfaceDependent1 = app(InterfaceDependentService::class);
        $results['interface_dependent_1'] = $interfaceDependent1->getData();
        
        // Test 5: Another dependent service resolution
        $interfaceDependent2 = app(InterfaceDependentService::class);
        $results['interface_dependent_2'] = $interfaceDependent2->getData();
        
        // Test 6: Method injection with interface
        return $this->testInterfaceMethodInjection($results);
    }
    
    public function testInterfaceMethodInjection($previousResults, TestServiceInterface $methodInjectedInterface = null)
    {
        if ($methodInjectedInterface) {
            $previousResults['interface_method_injection'] = $methodInjectedInterface->getData();
        }
        
        return view('interface-dependency-test', [
            'results' => $previousResults,
            'summary' => $this->analyzeInterfaceResults($previousResults)
        ]);
    }
    
    /**
     * Test shared interface dependency injection
     */
    public function testSharedInterfaceDependency(InterfaceFirstService $firstService, InterfaceSecondService $secondService)
    {
        $results = [];
        
        // Get initial service information
        $firstInfo = $firstService->getServiceInfo();
        $secondInfo = $secondService->getServiceInfo();
        
        // Execute different numbers of actions to test shared state
        
        // Step 1: Execute 2 actions on InterfaceFirstService
        $firstTask1 = $firstService->executeTask('interface_test_task_1');
        $firstTask2 = $firstService->executeTask('interface_test_task_2');
        
        // Step 2: Execute 1 action on InterfaceSecondService
        $secondTask1 = $secondService->processData('interface_test_data_1');
        
        // Step 3: Execute 1 more action on InterfaceFirstService  
        $firstTask3 = $firstService->executeTask('interface_test_task_3');
        
        // Step 4: Execute 2 more actions on InterfaceSecondService
        $secondTask2 = $secondService->processData('interface_test_data_2');
        $secondTask3 = $secondService->processData('interface_test_data_3');
        
        // Check if BaseServiceInterface instances are the same
        $baseService1 = $firstService->baseService;
        $baseService2 = $secondService->baseService;
        
        $isSameObject = ($baseService1 === $baseService2);
        $sameObjectHash = (spl_object_hash($baseService1) === spl_object_hash($baseService2));
        $sameInstanceId = ($baseService1->instanceId === $baseService2->instanceId);
        
        $results = [
            'initial_state' => [
                'first_service_info' => $firstInfo,
                'second_service_info' => $secondInfo,
            ],
            'execution_sequence' => [
                'step_1_first_task_1' => $firstTask1,
                'step_2_first_task_2' => $firstTask2,
                'step_3_second_task_1' => $secondTask1,
                'step_4_first_task_3' => $firstTask3,
                'step_5_second_task_2' => $secondTask2,
                'step_6_second_task_3' => $secondTask3,
            ],
            'final_state' => [
                'first_service_final' => $firstService->getServiceInfo(),
                'second_service_final' => $secondService->getServiceInfo(),
            ],
            'dependency_comparison' => [
                'is_same_object' => $isSameObject,
                'same_object_hash' => $sameObjectHash,
                'same_instance_id' => $sameInstanceId,
                'first_base_hash' => spl_object_hash($baseService1),
                'second_base_hash' => spl_object_hash($baseService2),
                'first_base_id' => $baseService1->instanceId,
                'second_base_id' => $baseService2->instanceId,
                'first_base_action_count' => $baseService1->actionCount,
                'second_base_action_count' => $baseService2->actionCount,
                'total_actions_executed' => 6,
                'expected_if_shared' => 6,
                'expected_if_separate' => 'First: 3, Second: 3',
                'dependency_type' => 'Interface-based'
            ]
        ];
        
        return view('shared-interface-dependency-test', [
            'results' => $results,
            'summary' => $this->analyzeSharedInterfaceDependency($results)
        ]);
    }
    
    /**
     * Test interface singleton behavior
     * Note: Interfaces are registered as singletons in AppServiceProvider
     */
    public function testInterfaceSingleton()
    {
        $results = [];
        
        // Test singleton behavior - interfaces are already registered as singletons in AppServiceProvider
        $singleton1 = app(TestServiceInterface::class);
        $results['interface_singleton_1'] = $singleton1->getData();
        
        $singleton2 = app(TestServiceInterface::class);
        $results['interface_singleton_2'] = $singleton2->getData();
        
        $singleton3 = app()->make(TestServiceInterface::class);
        $results['interface_singleton_3'] = $singleton3->getData();
        
        // Test with dependent services
        $dependentSingleton1 = app(InterfaceDependentService::class);
        $results['interface_dependent_singleton_1'] = $dependentSingleton1->getData();
        
        $dependentSingleton2 = app(InterfaceDependentService::class);
        $results['interface_dependent_singleton_2'] = $dependentSingleton2->getData();
        
        return view('interface-dependency-test', [
            'results' => $results,
            'summary' => $this->analyzeInterfaceResults($results),
            'test_type' => 'Interface Singleton Test'
        ]);
    }
    
    private function analyzeInterfaceResults($results)
    {
        $instances = [];
        $summary = [
            'total_tests' => count($results),
            'unique_instances' => 0,
            'same_instances' => 0,
            'analysis' => []
        ];
        
        foreach ($results as $testName => $data) {
            if (isset($data['test_service'])) {
                // This is from InterfaceDependentService
                $instanceId = $data['test_service']['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'interface_nested'];
            } else {
                // Direct interface instance
                $instanceId = $data['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'interface_direct'];
            }
        }
        
        // Count unique instances
        $uniqueIds = array_unique(array_column($instances, 'id'));
        $summary['unique_instances'] = count($uniqueIds);
        $summary['same_instances'] = count($instances) - count($uniqueIds);
        
        // Detailed analysis
        $idCounts = array_count_values(array_column($instances, 'id'));
        foreach ($idCounts as $id => $count) {
            $testsWithSameId = array_filter($instances, fn($instance) => $instance['id'] === $id);
            $testNames = array_column($testsWithSameId, 'test');
            
            $summary['analysis'][] = [
                'instance_id' => $id,
                'used_in_tests' => $testNames,
                'usage_count' => $count,
                'is_reused' => $count > 1,
                'dependency_type' => 'Interface-based'
            ];
        }
        
        return $summary;
    }
    
    private function analyzeSharedInterfaceDependency($results)
    {
        $comparison = $results['dependency_comparison'];
        $firstCount = $comparison['first_base_action_count'];
        $secondCount = $comparison['second_base_action_count'];
        $totalActions = $comparison['total_actions_executed'];
        
        // Analyze shared state based on action counts
        $sharedStateAnalysis = '';
        if ($comparison['is_same_object']) {
            $sharedStateAnalysis = "SHARED INTERFACE INSTANCE: Both services should show actionCount = {$totalActions}";
        } else {
            $sharedStateAnalysis = "SEPARATE INTERFACE INSTANCES: InterfaceFirstService BaseService = 3 actions, InterfaceSecondService BaseService = 3 actions";
        }
        
        return [
            'is_shared' => $comparison['is_same_object'],
            'verification_methods' => [
                'strict_comparison' => $comparison['is_same_object'] ? 'SAME OBJECT' : 'DIFFERENT OBJECTS',
                'object_hash_comparison' => $comparison['same_object_hash'] ? 'SAME HASH' : 'DIFFERENT HASH',
                'instance_id_comparison' => $comparison['same_instance_id'] ? 'SAME ID' : 'DIFFERENT ID'
            ],
            'action_count_analysis' => [
                'first_service_count' => $firstCount,
                'second_service_count' => $secondCount,
                'total_actions_executed' => $totalActions,
                'expected_if_shared' => $totalActions,
                'expected_if_separate' => '3 each',
                'actual_behavior' => "First: {$firstCount}, Second: {$secondCount}",
                'shared_state_verdict' => $sharedStateAnalysis
            ],
            'conclusion' => $comparison['is_same_object'] 
                ? "Laravel's service container SHARES the same BaseServiceInterface instance. Both services show actionCount = {$firstCount}"
                : "Laravel's service container creates SEPARATE BaseServiceInterface instances. First: {$firstCount} actions, Second: {$secondCount} actions",
            'dependency_type' => 'Interface-based',
            'comparison_with_concrete' => 'Compare this with concrete class dependency injection to see if Laravel handles interfaces differently'
        ];
    }
    
    // REQUEST PERSISTENCE TESTS
    
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
    
    /**
     * Clear persistence cache for fresh testing
     */
    public function clearPersistenceCache()
    {
        cache()->forget('request_persistence_test');
        cache()->forget('interface_request_persistence_test');
        cache()->forget('app_injection_requests');
        cache()->forget('pure_singleton_requests');
        
        return response()->json([
            'message' => 'All persistence cache cleared successfully (including Application injection and Pure singleton tests)',
            'timestamp' => now()->format('Y-m-d H:i:s.u')
        ]);
    }
    
    private function analyzeRequestPersistence($requests)
    {
        if (count($requests) < 2) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 2 requests to analyze persistence',
                'total_requests' => count($requests)
            ];
        }
        
        $analysis = [
            'total_requests' => count($requests),
            'test_service_analysis' => $this->analyzeServicePersistence($requests, 'test_service'),
            'base_service_analysis' => $this->analyzeServicePersistence($requests, 'base_service'),
            'dependent_service_analysis' => $this->analyzeServicePersistence($requests, 'dependent_service'),
            'overall_conclusion' => ''
        ];
        
        // Determine overall conclusion
        $testPersists = $analysis['test_service_analysis']['persists'];
        $basePersists = $analysis['base_service_analysis']['persists'];
        $dependentPersists = $analysis['dependent_service_analysis']['persists'];
        
        if ($testPersists && $basePersists && $dependentPersists) {
            $analysis['overall_conclusion'] = 'All services persist between requests (likely singleton behavior)';
        } elseif (!$testPersists && !$basePersists && !$dependentPersists) {
            $analysis['overall_conclusion'] = 'No services persist between requests (fresh instances each time)';
        } else {
            $analysis['overall_conclusion'] = 'Mixed behavior: some services persist, others don\'t';
        }
        
        return $analysis;
    }
    
    private function analyzeInterfaceRequestPersistence($requests)
    {
        if (count($requests) < 2) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 2 requests to analyze interface persistence',
                'total_requests' => count($requests)
            ];
        }
        
        $analysis = [
            'total_requests' => count($requests),
            'test_service_interface_analysis' => $this->analyzeServicePersistence($requests, 'test_service_interface'),
            'base_service_interface_analysis' => $this->analyzeServicePersistence($requests, 'base_service_interface'),
            'dependent_service_interface_analysis' => $this->analyzeServicePersistence($requests, 'dependent_service_interface'),
            'overall_conclusion' => ''
        ];
        
        // Determine overall conclusion
        $testPersists = $analysis['test_service_interface_analysis']['persists'];
        $basePersists = $analysis['base_service_interface_analysis']['persists'];
        $dependentPersists = $analysis['dependent_service_interface_analysis']['persists'];
        
        if ($testPersists && $basePersists && $dependentPersists) {
            $analysis['overall_conclusion'] = 'All interface services persist between requests (singleton behavior confirmed)';
        } elseif (!$testPersists && !$basePersists && !$dependentPersists) {
            $analysis['overall_conclusion'] = 'No interface services persist between requests (fresh instances each time)';
        } else {
            $analysis['overall_conclusion'] = 'Mixed interface behavior: some services persist, others don\'t';
        }
        
        return $analysis;
    }
    
    private function analyzeServicePersistence($requests, $serviceKey)
    {
        $persistence = [
            'service_name' => $serviceKey,
            'score' => 0,
            'persistence_score' => 0,
            'details' => [],
            'analysis' => '',
            'conclusion' => '',
            'persists' => false
        ];
        
        // Extract service-specific data from requests
        $serviceData = [];
        foreach ($requests as $request) {
            if (isset($request[$serviceKey])) {
                $serviceData[] = $request[$serviceKey];
            }
        }
        
        // Add template-required keys
        $persistence['total_requests'] = count($serviceData);
        
        if (empty($serviceData)) {
            $persistence['unique_instance_ids'] = 0;
            $persistence['unique_object_hashes'] = 0;
            $persistence['details'][] = "No data found for service: {$serviceKey}";
            return $persistence;
        }
        
        $instanceIds = array_column($serviceData, 'instance_id');
        $objectHashes = array_column($serviceData, 'object_hash');
        $actionCounts = array_column($serviceData, 'action_count'); // This may have null values
        
        $persistence['unique_instance_ids'] = count(array_unique($instanceIds));
        $persistence['unique_object_hashes'] = count(array_unique($objectHashes));
        $persistence['instance_ids'] = $instanceIds; // Add the instance IDs array for template
        $persistence['object_hashes'] = $objectHashes; // Add object hashes array for template
        
        // Check if all instance IDs are the same
        $uniqueInstanceIds = array_unique($instanceIds);
        if (count($uniqueInstanceIds) === 1) {
            $persistence['score'] += 25;
            $persistence['details'][] = "All instance IDs are identical";
        } else {
            $persistence['details'][] = "Different instance IDs detected";
        }
        
        // Check if all object hashes are the same
        $uniqueObjectHashes = array_unique($objectHashes);
        if (count($uniqueObjectHashes) === 1) {
            $persistence['score'] += 25;
            $persistence['details'][] = "All object hashes are identical";
        } else {
            $persistence['details'][] = "Different object hashes detected";
        }
        
        // Check action count progression (only if action counts exist)
        $validActionCounts = array_filter($actionCounts, function($count) {
            return !is_null($count) && is_numeric($count);
        });
        
        if (count($validActionCounts) > 1) {
            $isProgressive = true;
            $sortedCounts = array_values($validActionCounts);
            for ($i = 1; $i < count($sortedCounts); $i++) {
                if ($sortedCounts[$i] <= $sortedCounts[$i-1]) {
                    $isProgressive = false;
                    break;
                }
            }
            
            if ($isProgressive) {
                $persistence['score'] += 50;
                $persistence['details'][] = "Action counts show progressive state sharing";
            } else {
                $persistence['details'][] = "Action counts indicate separate instances";
            }
        } else {
            $persistence['details'][] = "Action count data not available or insufficient";
        }
        
        // Determine if service persists based on score
        $persistence['persists'] = $persistence['score'] >= 75;
        
        // Set persistence_score to the same value as score for template compatibility
        $persistence['persistence_score'] = $persistence['score'];
        
        // Analysis
        if ($persistence['score'] >= 75) {
            $persistence['analysis'] = "High persistence - Service instances are likely shared across requests";
        } else if ($persistence['score'] >= 50) {
            $persistence['analysis'] = "Medium persistence - Some sharing detected but not consistent";
        } else if ($persistence['score'] >= 25) {
            $persistence['analysis'] = "Low persistence - Minimal sharing detected";
        } else {
            $persistence['analysis'] = "No persistence - New instances created for each request";
        }
        
        // Set conclusion to the same value as analysis for template compatibility
        $persistence['conclusion'] = $persistence['analysis'];
        
        return $persistence;
    }

    /**
     * Test Application Injection - tests if the Application object ID persists between requests
     */
    public function testApplicationInjection(ApplicationInjectedService $appService)
    {
        $results = [];
        
        // Get initial service state
        $initialInfo = $appService->getApplicationInfo();
        $appFeatures = $appService->testApplicationFeatures();
        
        // Perform some actions to test state persistence
        $appService->performAction();
        $appService->performAction();
        $appService->performAction();
        
        // Get information after actions
        $afterActionsInfo = $appService->getApplicationInfo();
        
        // Test multiple resolutions to verify singleton behavior
        $secondResolve = app(ApplicationInjectedService::class);
        $secondResolveInfo = $secondResolve->getApplicationInfo();
        
        // Perform action on second resolution
        $secondResolve->performAction();
        $finalInfo = $appService->getApplicationInfo();
        
        // Store request data for persistence analysis
        $requestId = uniqid('app_injection_', true);
        $requestData = [
            'request_id' => $requestId,
            'timestamp' => now()->toISOString(),
            'app_object_id' => $initialInfo['app_object_id'],
            'service_object_id' => $initialInfo['service_object_id'],
            'service_instance_id' => $initialInfo['service_instance_id'],
            'total_instance_count' => ApplicationInjectedService::getTotalInstanceCount(),
            'final_action_count' => $finalInfo['action_count'],
            'app_features' => $appFeatures
        ];
        
        // Store in cache for cross-request comparison
        $allRequests = cache()->get('app_injection_requests', []);
        $allRequests[] = $requestData;
        cache()->put('app_injection_requests', $allRequests, 3600); // Store for 1 hour
        
        $results = [
            'initial_info' => $initialInfo,
            'after_actions_info' => $afterActionsInfo,
            'second_resolve_info' => $secondResolveInfo,
            'final_info' => $finalInfo,
            'current_request' => $requestData,
            'all_requests' => $allRequests,
            'app_features' => $appFeatures,
            'singleton_verification' => [
                'same_service_instance' => $initialInfo['service_object_id'] === $secondResolveInfo['service_object_id'],
                'same_app_instance' => $initialInfo['app_object_id'] === $secondResolveInfo['app_object_id'],
                'shared_action_count' => $finalInfo['action_count'] === 4, // Should be 4 if singleton
            ]
        ];
        
        return view('application-injection-test', [
            'results' => $results,
            'analysis' => $this->analyzeApplicationInjection($results)
        ]);
    }
    
    /**
     * Analyze application injection test results
     */
    private function analyzeApplicationInjection($results)
    {
        $analysis = [
            'singleton_score' => 0,
            'app_persistence_score' => 0,
            'overall_score' => 0,
            'issues' => [],
            'insights' => []
        ];
        
        // Check singleton behavior
        if ($results['singleton_verification']['same_service_instance']) {
            $analysis['singleton_score'] += 50;
            $analysis['insights'][] = '✓ Service instance is properly shared (singleton behavior confirmed)';
        } else {
            $analysis['issues'][] = '✗ Service instance is not shared (singleton registration failed)';
        }
        
        if ($results['singleton_verification']['same_app_instance']) {
            $analysis['singleton_score'] += 30;
            $analysis['insights'][] = '✓ Application instance is shared between service resolutions';
        } else {
            $analysis['issues'][] = '✗ Different Application instances detected between resolutions';
        }
        
        if ($results['singleton_verification']['shared_action_count']) {
            $analysis['singleton_score'] += 20;
            $analysis['insights'][] = '✓ Action count persists across resolutions (shared state confirmed)';
        } else {
            $analysis['issues'][] = '✗ Action count doesn\'t persist (state not shared)';
        }
        
        // Check application persistence across requests
        $requests = $results['all_requests'];
        if (count($requests) > 1) {
            $appIds = array_column($requests, 'app_object_id');
            $uniqueAppIds = array_unique($appIds);
            
            if (count($uniqueAppIds) === 1) {
                $analysis['app_persistence_score'] = 100;
                $analysis['insights'][] = '✓ Application object ID persists across ALL requests';
            } else {
                $persistenceRatio = (count($requests) - count($uniqueAppIds) + 1) / count($requests);
                $analysis['app_persistence_score'] = round($persistenceRatio * 100);
                $analysis['insights'][] = "◐ Application object persists in {$analysis['app_persistence_score']}% of requests";
            }
        } else {
            $analysis['insights'][] = '? Single request - cannot test cross-request persistence yet';
        }
        
        // Calculate overall score
        $analysis['overall_score'] = round(($analysis['singleton_score'] + $analysis['app_persistence_score']) / 2);
        
        // Add summary
        if ($analysis['overall_score'] >= 90) {
            $analysis['summary'] = 'Excellent: Perfect singleton behavior and application persistence';
        } elseif ($analysis['overall_score'] >= 70) {
            $analysis['summary'] = 'Good: Singleton working well, some persistence detected';
        } elseif ($analysis['overall_score'] >= 50) {
            $analysis['summary'] = 'Moderate: Some issues with singleton or persistence behavior';
        } else {
            $analysis['summary'] = 'Poor: Significant issues with singleton registration or application persistence';
        }
        
        return $analysis;
    }

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
    
    /**
     * Analyze pure singleton test results
     */
    private function analyzePureSingleton($results)
    {
        $analysis = [
            'singleton_integrity_score' => 0,
            'request_persistence_score' => 0,
            'overall_score' => 0,
            'issues' => [],
            'insights' => []
        ];
        
        // Check singleton integrity within request
        $basicTest = $results['basic_test'];
        $directTest = $results['direct_comparison'];
        
        if ($basicTest['references_comparison']['ref1_vs_ref2_same'] && 
            $basicTest['references_comparison']['ref2_vs_ref3_same'] && 
            $basicTest['references_comparison']['ref1_vs_ref3_same']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ All singleton references point to the same instance';
        } else {
            $analysis['issues'][] = '✗ Singleton references are not the same instance';
        }
        
        if ($basicTest['references_comparison']['all_same_object_id']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ All singleton references have the same object ID';
        } else {
            $analysis['issues'][] = '✗ Singleton references have different object IDs';
        }
        
        if ($directTest['same_instance']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ Direct singleton access returns same instance';
        } else {
            $analysis['issues'][] = '✗ Direct singleton access returns different instances';
        }
        
        if ($results['multiple_services']['singletons_are_same']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ Multiple services access the same singleton instance';
        } else {
            $analysis['issues'][] = '✗ Multiple services access different singleton instances';
        }
        
        // Check request persistence
        $requests = $results['all_requests'];
        if (count($requests) > 1) {
            $objectIds = array_column($requests, 'singleton_object_id');
            $uniqueObjectIds = array_unique($objectIds);
            
            if (count($uniqueObjectIds) === 1) {
                $analysis['request_persistence_score'] = 100;
                $analysis['insights'][] = '✓ Singleton object ID is IDENTICAL across ALL requests (PERSISTENT!)';
            } else {
                $analysis['request_persistence_score'] = 0;
                $analysis['insights'][] = '✗ Singleton object ID differs between requests (NOT PERSISTENT)';
                
                // Check if it's always the first instance
                $instanceIds = array_column($requests, 'singleton_instance_id');
                $uniqueInstanceIds = array_unique($instanceIds);
                if (count($uniqueInstanceIds) === 1 && $uniqueInstanceIds[0] === 1) {
                    $analysis['insights'][] = '◐ Always gets first instance, but different object each request';
                }
            }
            
            // Check total instances created
            $totalInstances = array_column($requests, 'total_instances_created');
            $maxInstances = max($totalInstances);
            if ($maxInstances === 1) {
                $analysis['insights'][] = '✓ Only one singleton instance ever created';
            } else {
                $analysis['insights'][] = "⚠ Multiple singleton instances created across requests: {$maxInstances}";
            }
        } else {
            $analysis['insights'][] = '? Single request - cannot test cross-request persistence yet';
        }
        
        // Calculate overall score
        $analysis['overall_score'] = round(($analysis['singleton_integrity_score'] + $analysis['request_persistence_score']) / 2);
        
        // Add summary
        if ($analysis['overall_score'] >= 90) {
            $analysis['summary'] = 'Excellent: Perfect singleton behavior with cross-request persistence';
        } elseif ($analysis['overall_score'] >= 70) {
            $analysis['summary'] = 'Good: Singleton working within request, some persistence detected';
        } elseif ($analysis['overall_score'] >= 50) {
            $analysis['summary'] = 'Moderate: Singleton working within request, limited persistence';
        } else {
            $analysis['summary'] = 'Poor: Singleton pattern broken or no cross-request persistence';
        }
        
        // Expected behavior note
        $analysis['expected_behavior'] = 'PHP singletons typically do NOT persist between HTTP requests in traditional web servers. Each request starts with a fresh PHP process.';
        
        return $analysis;
    }

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
            \DB::connection()->getPdo();
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

    /**
     * Test Request-Scoped Service (Octane-Safe Singleton Pattern)
     * Demonstrates how to use scoped() bindings for request-level singletons
     */
    public function testRequestScoped(RequestScopedService $scopedService)
    {
        $results = [];
        
        // Test 1: Initial service state
        $scopedService->performAction('Initial test action');
        $results['first_call'] = $scopedService->getStatus();
        
        // Test 2: Resolve the same service again within the same request
        $anotherReference = app(RequestScopedService::class);
        $anotherReference->performAction('Second test action');
        $results['second_call'] = $anotherReference->getStatus();
        
        // Test 3: Method injection to confirm same instance
        $results['method_injection'] = $this->testRequestScopedMethodInjection($scopedService);
        
        // Analysis
        $results['analysis'] = $this->analyzeRequestScoped($results);
        
        return view('request-scoped-test', [
            'results' => $results,
            'timestamp' => now()->format('H:i:s.u'),
            'test_title' => 'Request-Scoped Service Test (Octane-Safe)',
            'description' => 'Tests Laravel\'s scoped() binding for request-level singletons that reset between requests'
        ]);
    }

    private function testRequestScopedMethodInjection(RequestScopedService $originalService, RequestScopedService $methodInjectedService = null)
    {
        if ($methodInjectedService === null) {
            $methodInjectedService = app(RequestScopedService::class);
        }
        
        $methodInjectedService->performAction('Method injection action');
        
        return [
            'original_service' => $originalService->getStatus(),
            'method_injected' => $methodInjectedService->getStatus(),
            'same_instance' => $originalService === $methodInjectedService,
            'same_object_id' => spl_object_id($originalService) === spl_object_id($methodInjectedService),
        ];
    }

    private function analyzeRequestScoped($results)
    {
        $analysis = [
            'request_scoped_behavior' => 'Unknown',
            'instance_sharing' => 'Unknown',
            'state_persistence' => 'Unknown',
            'octane_safety' => 'Unknown',
            'recommendations' => []
        ];

        // Check if same instance is shared within request
        $firstCall = $results['first_call'];
        $secondCall = $results['second_call'];
        $methodInjection = $results['method_injection'];

        // Instance sharing analysis
        if ($firstCall['instance_id'] === $secondCall['instance_id']) {
            $analysis['instance_sharing'] = 'Shared within request ✅';
        } else {
            $analysis['instance_sharing'] = 'Separate instances ❌';
        }

        // State persistence analysis
        if ($secondCall['action_count'] > $firstCall['action_count']) {
            $analysis['state_persistence'] = 'State persists within request ✅';
        } else {
            $analysis['state_persistence'] = 'State does not persist ❌';
        }

        // Request scoped behavior
        if ($firstCall['instance_id'] === $secondCall['instance_id'] && 
            $secondCall['action_count'] > $firstCall['action_count']) {
            $analysis['request_scoped_behavior'] = 'Properly scoped to request ✅';
            $analysis['octane_safety'] = 'Safe for Octane ✅';
            $analysis['recommendations'][] = 'This pattern is safe for Laravel Octane';
            $analysis['recommendations'][] = 'Instance will reset between requests';
        } else {
            $analysis['request_scoped_behavior'] = 'Not properly scoped ❌';
            $analysis['octane_safety'] = 'Potential issues ⚠️';
            $analysis['recommendations'][] = 'Review service binding configuration';
        }

        // Method injection verification
        if ($methodInjection['same_instance']) {
            $analysis['recommendations'][] = 'Method injection correctly resolves same instance';
        } else {
            $analysis['recommendations'][] = 'Method injection creates separate instance - review binding';
        }

        return $analysis;
    }
}
