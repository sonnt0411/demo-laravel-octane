<?php

namespace App\Services\DependencyTest;

interface TestServiceInterface
{
    /**
     * Get service data
     */
    public function getData();
    
    /**
     * Perform an action
     */
    public function performAction($action = 'default');
} 