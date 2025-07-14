<?php

namespace App\Http\Controllers;

use App\Services\DependencyTest\ApplicationInjectedService;
use App\Http\Controllers\Traits\AnalysisHelpersTrait;

class ApplicationInjectionController extends Controller
{
    use AnalysisHelpersTrait;
    
    /**
     * Test Application Injection - tests if the Application object ID persists between requests
     */
    public function testApplicationInjection(ApplicationInjectedService $appService)
    {
        $results = [];
        
        // Get initial service state
        $initialInfo = $appService->getApplicationInfo();
        $appFeatures = $appService->testApplicationFeatures();
        
        // Perform some actions to test state persistence
        $appService->performAction();
        $appService->performAction();
        $appService->performAction();
        
        // Get information after actions
        $afterActionsInfo = $appService->getApplicationInfo();
        
        // Test multiple resolutions to verify singleton behavior
        $secondResolve = app(ApplicationInjectedService::class);
        $secondResolveInfo = $secondResolve->getApplicationInfo();
        
        // Perform action on second resolution
        $secondResolve->performAction();
        $finalInfo = $appService->getApplicationInfo();
        
        // Store request data for persistence analysis
        $requestId = uniqid('app_injection_', true);
        $requestData = [
            'request_id' => $requestId,
            'timestamp' => now()->toISOString(),
            'app_object_id' => $initialInfo['app_object_id'],
            'service_object_id' => $initialInfo['service_object_id'],
            'service_instance_id' => $initialInfo['service_instance_id'],
            'total_instance_count' => ApplicationInjectedService::getTotalInstanceCount(),
            'final_action_count' => $finalInfo['action_count'],
            'app_features' => $appFeatures
        ];
        
        // Store in cache for cross-request comparison
        $allRequests = cache()->get('app_injection_requests', []);
        $allRequests[] = $requestData;
        cache()->put('app_injection_requests', $allRequests, 3600); // Store for 1 hour
        
        $results = [
            'initial_info' => $initialInfo,
            'after_actions_info' => $afterActionsInfo,
            'second_resolve_info' => $secondResolveInfo,
            'final_info' => $finalInfo,
            'current_request' => $requestData,
            'all_requests' => $allRequests,
            'app_features' => $appFeatures,
            'singleton_verification' => [
                'same_service_instance' => $initialInfo['service_object_id'] === $secondResolveInfo['service_object_id'],
                'same_app_instance' => $initialInfo['app_object_id'] === $secondResolveInfo['app_object_id'],
                'shared_action_count' => $finalInfo['action_count'] === 4, // Should be 4 if singleton
            ]
        ];
        
        return view('application-injection-test', [
            'results' => $results,
            'analysis' => $this->analyzeApplicationInjection($results)
        ]);
    }
} 