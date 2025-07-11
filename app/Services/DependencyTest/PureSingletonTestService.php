<?php

namespace App\Services\DependencyTest;

class PureSingletonTestService
{
    private int $serviceInstanceId;
    private static int $serviceInstanceCount = 0;
    private int $actionCount = 0;
    private string $createdAt;
    
    public function __construct()
    {
        self::$serviceInstanceCount++;
        $this->serviceInstanceId = self::$serviceInstanceCount;
        $this->createdAt = now()->toISOString();
    }
    
    /**
     * Get service information
     */
    public function getServiceInfo(): array
    {
        return [
            'service_instance_id' => $this->serviceInstanceId,
            'service_object_id' => spl_object_id($this),
            'service_object_hash' => spl_object_hash($this),
            'service_created_at' => $this->createdAt,
            'service_action_count' => $this->actionCount,
            'total_service_instances' => self::$serviceInstanceCount
        ];
    }
    
    /**
     * Test pure singleton behavior
     */
    public function testPureSingleton(): array
    {
        // Get singleton instance multiple times
        $singleton1 = PureSingletonClass::getInstance();
        $singleton2 = PureSingletonClass::getInstance();
        $singleton3 = PureSingletonClass::getInstance();
        
        // Perform actions on different references
        $singleton1->performAction('action_from_reference_1');
        $singleton2->performAction('action_from_reference_2');
        $singleton3->performAction('action_from_reference_3');
        
        // Get information from each reference
        $info1 = $singleton1->getInstanceInfo();
        $info2 = $singleton2->getInstanceInfo();
        $info3 = $singleton3->getInstanceInfo();
        
        return [
            'singleton_info_1' => $info1,
            'singleton_info_2' => $info2,
            'singleton_info_3' => $info3,
            'references_comparison' => [
                'ref1_vs_ref2_same' => $singleton1 === $singleton2,
                'ref2_vs_ref3_same' => $singleton2 === $singleton3,
                'ref1_vs_ref3_same' => $singleton1 === $singleton3,
                'all_same_object_id' => $info1['object_id'] === $info2['object_id'] && $info2['object_id'] === $info3['object_id'],
                'all_same_object_hash' => $info1['object_hash'] === $info2['object_hash'] && $info2['object_hash'] === $info3['object_hash']
            ],
            'singleton_behavior_test' => $singleton1->testSingletonBehavior(),
            'final_action_count' => $singleton1->getActionCount(),
            'action_history' => $singleton1->getActionHistory()
        ];
    }
    
    /**
     * Get current singleton state
     */
    public function getCurrentSingletonState(): array
    {
        if (PureSingletonClass::hasInstance()) {
            $singleton = PureSingletonClass::getInstance();
            return [
                'has_instance' => true,
                'instance_info' => $singleton->getInstanceInfo(),
                'action_count' => $singleton->getActionCount(),
                'action_history' => $singleton->getActionHistory(),
                'data' => $singleton->getData()
            ];
        }
        
        return [
            'has_instance' => false,
            'total_instances_ever_created' => PureSingletonClass::getTotalInstancesCreated()
        ];
    }
    
    /**
     * Perform action on service
     */
    public function performServiceAction(): void
    {
        $this->actionCount++;
    }
    
    /**
     * Get service action count
     */
    public function getServiceActionCount(): int
    {
        return $this->actionCount;
    }
    
    /**
     * Test singleton with service interaction
     */
    public function testSingletonWithServiceInteraction(): array
    {
        $this->performServiceAction();
        
        $singleton = PureSingletonClass::getInstance();
        $singleton->performAction('service_interaction_test');
        
        return [
            'service_info' => $this->getServiceInfo(),
            'singleton_info' => $singleton->getInstanceInfo(),
            'interaction_successful' => true,
            'service_actions' => $this->actionCount,
            'singleton_actions' => $singleton->getActionCount()
        ];
    }
    
    /**
     * Compare multiple service instances accessing singleton
     */
    public static function compareMultipleServiceInstances(): array
    {
        $service1 = new self();
        $service2 = new self();
        
        $service1->performServiceAction();
        $service1->performServiceAction();
        
        $service2->performServiceAction();
        
        $singleton1 = PureSingletonClass::getInstance();
        $singleton2 = PureSingletonClass::getInstance();
        
        $singleton1->performAction('from_service1');
        $singleton2->performAction('from_service2');
        
        return [
            'service1_info' => $service1->getServiceInfo(),
            'service2_info' => $service2->getServiceInfo(),
            'services_are_different' => $service1 !== $service2,
            'singleton_from_service1' => $singleton1->getInstanceInfo(),
            'singleton_from_service2' => $singleton2->getInstanceInfo(),
            'singletons_are_same' => $singleton1 === $singleton2,
            'final_singleton_actions' => $singleton1->getActionCount(),
            'service1_actions' => $service1->getServiceActionCount(),
            'service2_actions' => $service2->getServiceActionCount()
        ];
    }
} 