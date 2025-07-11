<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class FirstService
{
    public $instanceId;
    public $baseService;
    public $createdAt;
    
    public function __construct(BaseService $baseService)
    {
        $this->instanceId = uniqid('first_');
        $this->baseService = $baseService;
        $this->createdAt = microtime(true);
        
        Log::info("FirstService instance created: {$this->instanceId} with BaseService: {$baseService->instanceId}");
    }
    
    public function executeTask($task = 'first_task')
    {
        Log::info("FirstService {$this->instanceId} executing task: {$task}");
        
        // Use the injected BaseService
        $baseResult = $this->baseService->performAction("from_first_service_{$task}");
        
        return [
            'service_id' => $this->instanceId,
            'task' => $task,
            'timestamp' => microtime(true),
            'base_service_result' => $baseResult,
            'base_service_info' => $this->baseService->getInstanceInfo()
        ];
    }
    
    public function getServiceInfo()
    {
        return [
            'service_id' => $this->instanceId,
            'created_at' => $this->createdAt,
            'object_hash' => spl_object_hash($this),
            'base_service_info' => $this->baseService->getInstanceInfo()
        ];
    }
} 