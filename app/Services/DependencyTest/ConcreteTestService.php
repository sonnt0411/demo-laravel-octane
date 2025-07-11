<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class ConcreteTestService implements TestServiceInterface
{
    public $instanceId;
    public $createdAt;
    
    public function __construct()
    {
        $this->instanceId = uniqid('concrete_test_');
        $this->createdAt = microtime(true);
        
        Log::info("ConcreteTestService instance created: {$this->instanceId} at {$this->createdAt}");
    }
    
    public function getData()
    {
        return [
            'id' => $this->instanceId,
            'created_at' => $this->createdAt,
            'message' => 'Hello from ConcreteTestService (Interface Implementation)',
            'implementation' => 'ConcreteTestService'
        ];
    }
    
    public function performAction($action = 'default')
    {
        Log::info("ConcreteTestService {$this->instanceId} performing action: {$action}");
        
        return [
            'instance_id' => $this->instanceId,
            'action' => $action,
            'timestamp' => microtime(true),
            'message' => "Action '{$action}' performed by ConcreteTestService instance {$this->instanceId}",
            'implementation' => 'ConcreteTestService'
        ];
    }
} 