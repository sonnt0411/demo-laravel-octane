<?php

namespace App\Services\DependencyTest;

interface BaseServiceInterface
{
    /**
     * Perform an action and track it
     */
    public function performAction($action = 'default');
    
    /**
     * Get instance information
     */
    public function getInstanceInfo();
} 