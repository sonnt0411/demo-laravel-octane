<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class DependentService
{
    public $instanceId;
    public $testService;
    
    public function __construct(TestService $testService)
    {
        $this->instanceId = uniqid('dependent_');
        $this->testService = $testService;
        
        Log::info("DependentService instance created: {$this->instanceId} with TestService: {$testService->instanceId}");
    }
    
    public function getData()
    {
        return [
            'id' => $this->instanceId,
            'test_service' => $this->testService->getData()
        ];
    }
    
    public function performCombinedAction($action = 'combined')
    {
        $dependentResult = [
            'instance_id' => $this->instanceId,
            'action' => $action,
            'timestamp' => microtime(true)
        ];
        
        $testServiceResult = $this->testService->performAction($action);
        
        return [
            'dependent_service' => $dependentResult,
            'test_service_result' => $testServiceResult
        ];
    }
} 