<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class InterfaceFirstService
{
    public $instanceId;
    public $baseService;
    public $createdAt;
    
    public function __construct(BaseServiceInterface $baseService)
    {
        $this->instanceId = uniqid('interface_first_');
        $this->baseService = $baseService;
        $this->createdAt = microtime(true);
        
        Log::info("InterfaceFirstService instance created: {$this->instanceId} with BaseService: {$baseService->instanceId}");
    }
    
    public function executeTask($task = 'interface_first_task')
    {
        Log::info("InterfaceFirstService {$this->instanceId} executing task: {$task}");
        
        $baseResult = $this->baseService->performAction("from_interface_first_{$task}");
        
        return [
            'service_id' => $this->instanceId,
            'task' => $task,
            'timestamp' => microtime(true),
            'base_service_result' => $baseResult,
            'base_service_info' => $this->baseService->getInstanceInfo(),
            'dependency_type' => 'Interface-based'
        ];
    }
    
    public function getServiceInfo()
    {
        return [
            'service_id' => $this->instanceId,
            'created_at' => $this->createdAt,
            'object_hash' => spl_object_hash($this),
            'base_service_info' => $this->baseService->getInstanceInfo(),
            'dependency_type' => 'Interface-based'
        ];
    }
} 