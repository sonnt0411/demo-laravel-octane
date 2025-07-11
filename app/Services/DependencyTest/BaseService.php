<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class BaseService
{
    public $instanceId;
    public $createdAt;
    public $actionCount;
    
    public function __construct()
    {
        $this->instanceId = uniqid('base_');
        $this->createdAt = microtime(true);
        $this->actionCount = 0;
        
        Log::info("BaseService instance created: {$this->instanceId} at {$this->createdAt}");
    }
    
    public function performAction($action = 'default')
    {
        $this->actionCount++;
        
        Log::info("BaseService {$this->instanceId} performing action: {$action} (count: {$this->actionCount})");
        
        return [
            'instance_id' => $this->instanceId,
            'action' => $action,
            'action_count' => $this->actionCount,
            'timestamp' => microtime(true),
            'message' => "BaseService {$this->instanceId} executed '{$action}'"
        ];
    }
    
    public function getInstanceInfo()
    {
        return [
            'id' => $this->instanceId,
            'created_at' => $this->createdAt,
            'action_count' => $this->actionCount,
            'object_hash' => spl_object_hash($this),
            'memory_address' => sprintf('%x', spl_object_id($this))
        ];
    }
} 