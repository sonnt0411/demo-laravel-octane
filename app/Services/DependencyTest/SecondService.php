<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class SecondService
{
    public $instanceId;
    public $baseService;
    public $createdAt;
    
    public function __construct(BaseService $baseService)
    {
        $this->instanceId = uniqid('second_');
        $this->baseService = $baseService;
        $this->createdAt = microtime(true);
        
        Log::info("SecondService instance created: {$this->instanceId} with BaseService: {$baseService->instanceId}");
    }
    
    public function processData($data = 'second_data')
    {
        Log::info("SecondService {$this->instanceId} processing data: {$data}");
        
        // Use the injected BaseService
        $baseResult = $this->baseService->performAction("from_second_service_{$data}");
        
        return [
            'service_id' => $this->instanceId,
            'data' => $data,
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