<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\RequestScopedService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class RequestScopedController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test Request-Scoped Service (Octane-Safe Singleton Pattern)
     * Demonstrates how to use scoped() bindings for request-level singletons
     */
    public function testRequestScoped(RequestScopedService $scopedService)
    {
        $results = [];
        
        // Test 1: Initial service state
        $scopedService->performAction('Initial test action');
        $results['first_call'] = $scopedService->getStatus();
        
        // Test 2: Resolve the same service again within the same request
        $anotherReference = app(RequestScopedService::class);
        $anotherReference->performAction('Second test action');
        $results['second_call'] = $anotherReference->getStatus();
        
        // Test 3: Method injection to confirm same instance
        $results['method_injection'] = $this->testRequestScopedMethodInjection($scopedService);
        
        // Analysis
        $results['analysis'] = $this->analyzeRequestScoped($results);
        
        return view('request-scoped-test', [
            'results' => $results,
            'timestamp' => now()->format('H:i:s.u'),
            'test_title' => 'Request-Scoped Service Test (Octane-Safe)',
            'description' => 'Tests Laravel\'s scoped() binding for request-level singletons that reset between requests'
        ]);
    }

    private function testRequestScopedMethodInjection(RequestScopedService $originalService, RequestScopedService $methodInjectedService = null)
    {
        if ($methodInjectedService === null) {
            $methodInjectedService = app(RequestScopedService::class);
        }
        
        $methodInjectedService->performAction('Method injection action');
        
        return [
            'original_service' => $originalService->getStatus(),
            'method_injected' => $methodInjectedService->getStatus(),
            'same_instance' => $originalService === $methodInjectedService,
            'same_object_id' => spl_object_id($originalService) === spl_object_id($methodInjectedService),
        ];
    }
} 