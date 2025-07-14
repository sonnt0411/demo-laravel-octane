<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DependencyTest\TestService;
use App\Services\DependencyTest\DependentService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class BasicDependencyController extends Controller
{
    use AnalysisHelpersTrait;

    private $testService;
    
    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }
    
    public function index()
    {
        $results = [];
        
        // Test 1: Using constructor injection (already resolved)
        $results['constructor_injection'] = $this->testService->getData();
        
        // Test 2: Manual resolution from container
        $manualResolve1 = app(TestService::class);
        $results['manual_resolve_1'] = $manualResolve1->getData();
        
        // Test 3: Another manual resolution
        $manualResolve2 = app(TestService::class);
        $results['manual_resolve_2'] = $manualResolve2->getData();
        
        // Test 4: Using make() method
        $makeResolve = app()->make(TestService::class);
        $results['make_resolve'] = $makeResolve->getData();
        
        // Test 5: Resolving dependent service (which injects TestService)
        $dependentService1 = app(DependentService::class);
        $results['dependent_service_1'] = $dependentService1->getData();
        
        // Test 6: Another dependent service resolution
        $dependentService2 = app(DependentService::class);
        $results['dependent_service_2'] = $dependentService2->getData();
        
        // Test 7: Using method injection
        return $this->testMethodInjection($results);
    }
    
    public function testMethodInjection($previousResults, TestService $methodInjectedService = null)
    {
        if ($methodInjectedService) {
            $previousResults['method_injection'] = $methodInjectedService->getData();
        }
        
        // Test 8: Resolve with custom parameters (this should always create new instance)
        $customInstance = new TestService();
        $previousResults['custom_instance'] = $customInstance->getData();
        
        return view('dependency-test', [
            'results' => $previousResults,
            'summary' => $this->analyzResults($previousResults)
        ]);
    }
} 