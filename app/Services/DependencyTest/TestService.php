<?php

namespace App\Services\DependencyTest;

use Illuminate\Support\Facades\Log;

class TestService
{
    public $instanceId;
    public $createdAt;
    
    public function __construct()
    {
        $this->instanceId = uniqid('instance_');
        $this->createdAt = microtime(true);
        
        // Log when instance is created
        Log::info("TestService instance created: {$this->instanceId} at {$this->createdAt}");
    }
    
    public function getData()
    {
        return [
            'id' => $this->instanceId,
            'created_at' => $this->createdAt,
            'message' => 'Hello from TestService'
        ];
    }
    
    public function performAction($action = 'default')
    {
        Log::info("TestService {$this->instanceId} performing action: {$action}");
        
        return [
            'instance_id' => $this->instanceId,
            'action' => $action,
            'timestamp' => microtime(true),
            'message' => "Action '{$action}' performed by instance {$this->instanceId}"
        ];
    }
} 