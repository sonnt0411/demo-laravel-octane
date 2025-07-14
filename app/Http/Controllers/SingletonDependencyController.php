<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\TestService;
use App\Services\DependencyTest\DependentService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class SingletonDependencyController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test singleton behavior by binding TestService as singleton
     */
    public function testSingleton()
    {
        // First, let's bind TestService as singleton
        app()->singleton(TestService::class, function ($app) {
            return new TestService();
        });
        
        $results = [];
        
        // Test singleton behavior
        $singleton1 = app(TestService::class);
        $results['singleton_1'] = $singleton1->getData();
        
        $singleton2 = app(TestService::class);
        $results['singleton_2'] = $singleton2->getData();
        
        $singleton3 = app()->make(TestService::class);
        $results['singleton_3'] = $singleton3->getData();
        
        // Test with dependent services
        $dependentSingleton1 = app(DependentService::class);
        $results['dependent_singleton_1'] = $dependentSingleton1->getData();
        
        $dependentSingleton2 = app(DependentService::class);
        $results['dependent_singleton_2'] = $dependentSingleton2->getData();
        
        return view('dependency-test', [
            'results' => $results,
            'summary' => $this->analyzResults($results),
            'test_type' => 'Singleton Test'
        ]);
    }
} 