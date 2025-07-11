<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class InterfaceDependentService
{
    public $instanceId;
    private $testService;
    public $createdAt;
    
    public function __construct(TestServiceInterface $testService)
    {
        $this->instanceId = uniqid('interface_dependent_');
        $this->testService = $testService;
        $this->createdAt = microtime(true);
        
        Log::info("InterfaceDependentService instance created: {$this->instanceId} with TestService: {$testService->instanceId}");
    }
    
    public function getData()
    {
        $testServiceData = $this->testService->getData();
        
        return [
            'id' => $this->instanceId,
            'created_at' => $this->createdAt,
            'message' => 'Data from InterfaceDependentService',
            'test_service' => $testServiceData,
            'dependency_type' => 'Interface-based'
        ];
    }
    
    public function performComplexAction($action = 'complex_task')
    {
        Log::info("InterfaceDependentService {$this->instanceId} performing complex action: {$action}");
        
        $testResult = $this->testService->performAction("from_interface_dependent_{$action}");
        
        return [
            'service_id' => $this->instanceId,
            'action' => $action,
            'timestamp' => microtime(true),
            'test_service_result' => $testResult,
            'dependency_type' => 'Interface-based'
        ];
    }
} 