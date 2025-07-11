<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class ConcreteBaseService implements BaseServiceInterface
{
    public $instanceId;
    public $createdAt;
    public $actionCount;
    
    public function __construct()
    {
        $this->instanceId = uniqid('concrete_base_');
        $this->createdAt = microtime(true);
        $this->actionCount = 0;
        
        Log::info("ConcreteBaseService instance created: {$this->instanceId} at {$this->createdAt}");
    }
    
    public function performAction($action = 'default')
    {
        $this->actionCount++;
        
        Log::info("ConcreteBaseService {$this->instanceId} performing action: {$action} (count: {$this->actionCount})");
        
        return [
            'instance_id' => $this->instanceId,
            'action' => $action,
            'action_count' => $this->actionCount,
            'timestamp' => microtime(true),
            'message' => "ConcreteBaseService {$this->instanceId} executed '{$action}'",
            'implementation' => 'ConcreteBaseService'
        ];
    }
    
    public function getInstanceInfo()
    {
        return [
            'id' => $this->instanceId,
            'created_at' => $this->createdAt,
            'action_count' => $this->actionCount,
            'object_hash' => spl_object_hash($this),
            'memory_address' => sprintf('%x', spl_object_id($this)),
            'implementation' => 'ConcreteBaseService'
        ];
    }
} 