<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\TestServiceInterface;
use App\Services\DependencyTest\InterfaceDependentService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class InterfaceDependencyController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test interface-based dependency injection
     */
    public function testInterfaceDependency()
    {
        $results = [];
        
        // Test 1: Direct interface resolution
        $interface1 = app(TestServiceInterface::class);
        $results['interface_resolve_1'] = $interface1->getData();
        
        // Test 2: Another interface resolution
        $interface2 = app(TestServiceInterface::class);
        $results['interface_resolve_2'] = $interface2->getData();
        
        // Test 3: Using make() method
        $interfaceMake = app()->make(TestServiceInterface::class);
        $results['interface_make'] = $interfaceMake->getData();
        
        // Test 4: Resolving service that depends on interface
        $interfaceDependent1 = app(InterfaceDependentService::class);
        $results['interface_dependent_1'] = $interfaceDependent1->getData();
        
        // Test 5: Another dependent service resolution
        $interfaceDependent2 = app(InterfaceDependentService::class);
        $results['interface_dependent_2'] = $interfaceDependent2->getData();
        
        // Test 6: Method injection with interface
        return $this->testInterfaceMethodInjection($results);
    }
    
    public function testInterfaceMethodInjection($previousResults, TestServiceInterface $methodInjectedInterface = null)
    {
        if ($methodInjectedInterface) {
            $previousResults['interface_method_injection'] = $methodInjectedInterface->getData();
        }
        
        return view('interface-dependency-test', [
            'results' => $previousResults,
            'summary' => $this->analyzeInterfaceResults($previousResults)
        ]);
    }
} 