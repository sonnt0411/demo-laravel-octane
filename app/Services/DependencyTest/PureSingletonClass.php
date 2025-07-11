<?php

namespace App\Services\DependencyTest;

class PureSingletonClass
{
    private static ?PureSingletonClass $instance = null;
    private static int $totalInstances = 0;
    private int $instanceId;
    private int $actionCount = 0;
    private string $createdAt;
    private array $actionHistory = [];
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        self::$totalInstances++;
        $this->instanceId = self::$totalInstances;
        $this->createdAt = now()->toISOString();
        $this->actionHistory[] = [
            'action' => 'instance_created',
            'timestamp' => $this->createdAt,
            'instance_id' => $this->instanceId
        ];
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    /**
     * Get the singleton instance
     */
    public static function getInstance(): PureSingletonClass
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Check if instance exists
     */
    public static function hasInstance(): bool
    {
        return self::$instance !== null;
    }
    
    /**
     * Get instance information
     */
    public function getInstanceInfo(): array
    {
        return [
            'instance_id' => $this->instanceId,
            'object_id' => spl_object_id($this),
            'object_hash' => spl_object_hash($this),
            'created_at' => $this->createdAt,
            'action_count' => $this->actionCount,
            'total_instances_created' => self::$totalInstances,
            'memory_address' => sprintf('%016X', self::getObjectAddress($this)),
            'is_same_instance' => self::$instance === $this
        ];
    }
    
    /**
     * Perform an action to test state persistence
     */
    public function performAction(string $actionName = 'generic_action'): void
    {
        $this->actionCount++;
        $this->actionHistory[] = [
            'action' => $actionName,
            'timestamp' => now()->toISOString(),
            'action_number' => $this->actionCount,
            'instance_id' => $this->instanceId
        ];
    }
    
    /**
     * Get action count
     */
    public function getActionCount(): int
    {
        return $this->actionCount;
    }
    
    /**
     * Get action history
     */
    public function getActionHistory(): array
    {
        return $this->actionHistory;
    }
    
    /**
     * Get total number of instances created
     */
    public static function getTotalInstancesCreated(): int
    {
        return self::$totalInstances;
    }
    
    /**
     * Reset singleton instance (for testing purposes only)
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
    
    /**
     * Get some data to verify instance identity
     */
    public function getData(): array
    {
        return [
            'message' => 'Data from PureSingletonClass',
            'instance_info' => $this->getInstanceInfo(),
            'action_history_count' => count($this->actionHistory),
            'last_action' => end($this->actionHistory) ?: null
        ];
    }
    
    /**
     * Get memory address of object (for debugging)
     */
    private static function getObjectAddress($object): int
    {
        ob_start();
        var_dump($object);
        $dump = ob_get_clean();
        
        if (preg_match('/object\([^)]+\)#(\d+)/', $dump, $matches)) {
            return (int)$matches[1];
        }
        
        return 0;
    }
    
    /**
     * Test method to verify singleton behavior
     */
    public function testSingletonBehavior(): array
    {
        $instance1 = self::getInstance();
        $instance2 = self::getInstance();
        
        return [
            'same_instance_reference' => $instance1 === $instance2,
            'same_object_id' => spl_object_id($instance1) === spl_object_id($instance2),
            'same_object_hash' => spl_object_hash($instance1) === spl_object_hash($instance2),
            'instance1_id' => $instance1->getInstanceId(),
            'instance2_id' => $instance2->getInstanceId(),
            'total_instances' => self::$totalInstances
        ];
    }
    
    /**
     * Get instance ID
     */
    public function getInstanceId(): int
    {
        return $this->instanceId;
    }
} 