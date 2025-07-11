<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class InterfaceSecondService
{
    public $instanceId;
    public $baseService;
    public $createdAt;
    
    public function __construct(BaseServiceInterface $baseService)
    {
        $this->instanceId = uniqid('interface_second_');
        $this->baseService = $baseService;
        $this->createdAt = microtime(true);
        
        Log::info("InterfaceSecondService instance created: {$this->instanceId} with BaseService: {$baseService->instanceId}");
    }
    
    public function processData($data = 'interface_data')
    {
        Log::info("InterfaceSecondService {$this->instanceId} processing data: {$data}");
        
        $baseResult = $this->baseService->performAction("from_interface_second_{$data}");
        
        return [
            'service_id' => $this->instanceId,
            'data' => $data,
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