<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\InterfaceFirstService;
use App\Services\DependencyTest\InterfaceSecondService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class SharedInterfaceDependencyController extends Controller
{
    use AnalysisHelpersTrait;
    
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
} 