<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\TestServiceInterface;
use App\Services\DependencyTest\InterfaceDependentService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class InterfaceSingletonController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test interface singleton behavior
     * Note: Interfaces are registered as singletons in AppServiceProvider
     */
    public function testInterfaceSingleton()
    {
        $results = [];
        
        // Test singleton behavior - interfaces are already registered as singletons in AppServiceProvider
        $singleton1 = app(TestServiceInterface::class);
        $results['interface_singleton_1'] = $singleton1->getData();
        
        $singleton2 = app(TestServiceInterface::class);
        $results['interface_singleton_2'] = $singleton2->getData();
        
        $singleton3 = app()->make(TestServiceInterface::class);
        $results['interface_singleton_3'] = $singleton3->getData();
        
        // Test with dependent services
        $dependentSingleton1 = app(InterfaceDependentService::class);
        $results['interface_dependent_singleton_1'] = $dependentSingleton1->getData();
        
        $dependentSingleton2 = app(InterfaceDependentService::class);
        $results['interface_dependent_singleton_2'] = $dependentSingleton2->getData();
        
        return view('interface-dependency-test', [
            'results' => $results,
            'summary' => $this->analyzeInterfaceResults($results),
            'test_type' => 'Interface Singleton Test'
        ]);
    }
} 