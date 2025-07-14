<?php

namespace App\Http\Controllers\Traits;

trait AnalysisHelpersTrait
{
    /**
     * Analyze dependency injection test results
     */
    private function analyzResults($results)
    {
        $instances = [];
        $summary = [
            'total_tests' => count($results),
            'unique_instances' => 0,
            'same_instances' => 0,
            'analysis' => []
        ];
        
        foreach ($results as $testName => $data) {
            if (isset($data['test_service'])) {
                // This is from DependentService
                $instanceId = $data['test_service']['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'nested'];
            } else {
                // Direct TestService instance
                $instanceId = $data['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'direct'];
            }
        }
        
        // Count unique instances
        $uniqueIds = array_unique(array_column($instances, 'id'));
        $summary['unique_instances'] = count($uniqueIds);
        $summary['same_instances'] = count($instances) - count($uniqueIds);
        
        // Detailed analysis
        $idCounts = array_count_values(array_column($instances, 'id'));
        foreach ($idCounts as $id => $count) {
            $testsWithSameId = array_filter($instances, fn($instance) => $instance['id'] === $id);
            $testNames = array_column($testsWithSameId, 'test');
            
            $summary['analysis'][] = [
                'instance_id' => $id,
                'used_in_tests' => $testNames,
                'usage_count' => $count,
                'is_reused' => $count > 1
            ];
        }
        
        return $summary;
    }

    /**
     * Analyze shared dependency test results
     */
    private function analyzeSharedDependency($results)
    {
        $comparison = $results['dependency_comparison'];
        $firstCount = $comparison['first_base_action_count'];
        $secondCount = $comparison['second_base_action_count'];
        $totalActions = $comparison['total_actions_executed'];
        
        // Analyze shared state based on action counts
        $sharedStateAnalysis = '';
        if ($comparison['is_same_object']) {
            $sharedStateAnalysis = "SHARED INSTANCE: Both services should show actionCount = {$totalActions}";
        } else {
            $sharedStateAnalysis = "SEPARATE INSTANCES: FirstService BaseService = 3 actions, SecondService BaseService = 3 actions";
        }
        
        return [
            'is_shared' => $comparison['is_same_object'],
            'verification_methods' => [
                'strict_comparison' => $comparison['is_same_object'] ? 'SAME OBJECT' : 'DIFFERENT OBJECTS',
                'object_hash_comparison' => $comparison['same_object_hash'] ? 'SAME HASH' : 'DIFFERENT HASH',
                'instance_id_comparison' => $comparison['same_instance_id'] ? 'SAME ID' : 'DIFFERENT ID'
            ],
            'action_count_analysis' => [
                'first_service_count' => $firstCount,
                'second_service_count' => $secondCount,
                'total_actions_executed' => $totalActions,
                'expected_if_shared' => $totalActions,
                'expected_if_separate' => '3 each',
                'actual_behavior' => "First: {$firstCount}, Second: {$secondCount}",
                'shared_state_verdict' => $sharedStateAnalysis
            ],
            'conclusion' => $comparison['is_same_object'] 
                ? "Laravel's service container SHARES the same BaseService instance. Both services show actionCount = {$firstCount}"
                : "Laravel's service container creates SEPARATE BaseService instances. First: {$firstCount} actions, Second: {$secondCount} actions"
        ];
    }

    /**
     * Analyze interface dependency test results
     */
    private function analyzeInterfaceResults($results)
    {
        $instances = [];
        $summary = [
            'total_tests' => count($results),
            'unique_instances' => 0,
            'same_instances' => 0,
            'analysis' => []
        ];
        
        foreach ($results as $testName => $data) {
            if (isset($data['test_service'])) {
                // This is from InterfaceDependentService
                $instanceId = $data['test_service']['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'interface_nested'];
            } else {
                // Direct interface instance
                $instanceId = $data['id'];
                $instances[] = ['test' => $testName, 'id' => $instanceId, 'type' => 'interface_direct'];
            }
        }
        
        // Count unique instances
        $uniqueIds = array_unique(array_column($instances, 'id'));
        $summary['unique_instances'] = count($uniqueIds);
        $summary['same_instances'] = count($instances) - count($uniqueIds);
        
        // Detailed analysis
        $idCounts = array_count_values(array_column($instances, 'id'));
        foreach ($idCounts as $id => $count) {
            $testsWithSameId = array_filter($instances, fn($instance) => $instance['id'] === $id);
            $testNames = array_column($testsWithSameId, 'test');
            
            $summary['analysis'][] = [
                'instance_id' => $id,
                'used_in_tests' => $testNames,
                'usage_count' => $count,
                'is_reused' => $count > 1,
                'dependency_type' => 'Interface-based'
            ];
        }
        
        return $summary;
    }

    /**
     * Analyze shared interface dependency test results
     */
    private function analyzeSharedInterfaceDependency($results)
    {
        $comparison = $results['dependency_comparison'];
        $firstCount = $comparison['first_base_action_count'];
        $secondCount = $comparison['second_base_action_count'];
        $totalActions = $comparison['total_actions_executed'];
        
        // Analyze shared state based on action counts
        $sharedStateAnalysis = '';
        if ($comparison['is_same_object']) {
            $sharedStateAnalysis = "SHARED INTERFACE INSTANCE: Both services should show actionCount = {$totalActions}";
        } else {
            $sharedStateAnalysis = "SEPARATE INTERFACE INSTANCES: InterfaceFirstService BaseService = 3 actions, InterfaceSecondService BaseService = 3 actions";
        }
        
        return [
            'is_shared' => $comparison['is_same_object'],
            'verification_methods' => [
                'strict_comparison' => $comparison['is_same_object'] ? 'SAME OBJECT' : 'DIFFERENT OBJECTS',
                'object_hash_comparison' => $comparison['same_object_hash'] ? 'SAME HASH' : 'DIFFERENT HASH',
                'instance_id_comparison' => $comparison['same_instance_id'] ? 'SAME ID' : 'DIFFERENT ID'
            ],
            'action_count_analysis' => [
                'first_service_count' => $firstCount,
                'second_service_count' => $secondCount,
                'total_actions_executed' => $totalActions,
                'expected_if_shared' => $totalActions,
                'expected_if_separate' => '3 each',
                'actual_behavior' => "First: {$firstCount}, Second: {$secondCount}",
                'shared_state_verdict' => $sharedStateAnalysis
            ],
            'conclusion' => $comparison['is_same_object'] 
                ? "Laravel's service container SHARES the same BaseServiceInterface instance. Both services show actionCount = {$firstCount}"
                : "Laravel's service container creates SEPARATE BaseServiceInterface instances. First: {$firstCount} actions, Second: {$secondCount} actions",
            'dependency_type' => 'Interface-based',
            'comparison_with_concrete' => 'Compare this with concrete class dependency injection to see if Laravel handles interfaces differently'
        ];
    }

    /**
     * Analyze service persistence across requests
     */
    private function analyzeServicePersistence($requests, $serviceKey)
    {
        $persistence = [
            'service_name' => $serviceKey,
            'score' => 0,
            'persistence_score' => 0,
            'details' => [],
            'analysis' => '',
            'conclusion' => '',
            'persists' => false
        ];
        
        // Extract service-specific data from requests
        $serviceData = [];
        foreach ($requests as $request) {
            if (isset($request[$serviceKey])) {
                $serviceData[] = $request[$serviceKey];
            }
        }
        
        // Add template-required keys
        $persistence['total_requests'] = count($serviceData);
        
        if (empty($serviceData)) {
            $persistence['unique_instance_ids'] = 0;
            $persistence['unique_object_hashes'] = 0;
            $persistence['details'][] = "No data found for service: {$serviceKey}";
            return $persistence;
        }
        
        $instanceIds = array_column($serviceData, 'instance_id');
        $objectHashes = array_column($serviceData, 'object_hash');
        $actionCounts = array_column($serviceData, 'action_count'); // This may have null values
        
        $persistence['unique_instance_ids'] = count(array_unique($instanceIds));
        $persistence['unique_object_hashes'] = count(array_unique($objectHashes));
        $persistence['instance_ids'] = $instanceIds; // Add the instance IDs array for template
        $persistence['object_hashes'] = $objectHashes; // Add object hashes array for template
        
        // Check if all instance IDs are the same
        $uniqueInstanceIds = array_unique($instanceIds);
        if (count($uniqueInstanceIds) === 1) {
            $persistence['score'] += 25;
            $persistence['details'][] = "All instance IDs are identical";
        } else {
            $persistence['details'][] = "Different instance IDs detected";
        }
        
        // Check if all object hashes are the same
        $uniqueObjectHashes = array_unique($objectHashes);
        if (count($uniqueObjectHashes) === 1) {
            $persistence['score'] += 25;
            $persistence['details'][] = "All object hashes are identical";
        } else {
            $persistence['details'][] = "Different object hashes detected";
        }
        
        // Check action count progression (only if action counts exist)
        $validActionCounts = array_filter($actionCounts, function($count) {
            return !is_null($count) && is_numeric($count);
        });
        
        if (count($validActionCounts) > 1) {
            $isProgressive = true;
            $sortedCounts = array_values($validActionCounts);
            for ($i = 1; $i < count($sortedCounts); $i++) {
                if ($sortedCounts[$i] <= $sortedCounts[$i-1]) {
                    $isProgressive = false;
                    break;
                }
            }
            
            if ($isProgressive) {
                $persistence['score'] += 50;
                $persistence['details'][] = "Action counts show progressive state sharing";
            } else {
                $persistence['details'][] = "Action counts indicate separate instances";
            }
        } else {
            $persistence['details'][] = "Action count data not available or insufficient";
        }
        
        // Determine if service persists based on score
        $persistence['persists'] = $persistence['score'] >= 75;
        
        // Set persistence_score to the same value as score for template compatibility
        $persistence['persistence_score'] = $persistence['score'];
        
        // Analysis
        if ($persistence['score'] >= 75) {
            $persistence['analysis'] = "High persistence - Service instances are likely shared across requests";
        } else if ($persistence['score'] >= 50) {
            $persistence['analysis'] = "Medium persistence - Some sharing detected but not consistent";
        } else if ($persistence['score'] >= 25) {
            $persistence['analysis'] = "Low persistence - Minimal sharing detected";
        } else {
            $persistence['analysis'] = "No persistence - New instances created for each request";
        }
        
        // Set conclusion to the same value as analysis for template compatibility
        $persistence['conclusion'] = $persistence['analysis'];
        
        return $persistence;
    }

    /**
     * Analyze request persistence test results
     */
    private function analyzeRequestPersistence($requests)
    {
        if (count($requests) < 2) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 2 requests to analyze persistence',
                'total_requests' => count($requests)
            ];
        }
        
        $analysis = [
            'total_requests' => count($requests),
            'test_service_analysis' => $this->analyzeServicePersistence($requests, 'test_service'),
            'base_service_analysis' => $this->analyzeServicePersistence($requests, 'base_service'),
            'dependent_service_analysis' => $this->analyzeServicePersistence($requests, 'dependent_service'),
            'overall_conclusion' => ''
        ];
        
        // Determine overall conclusion
        $testPersists = $analysis['test_service_analysis']['persists'];
        $basePersists = $analysis['base_service_analysis']['persists'];
        $dependentPersists = $analysis['dependent_service_analysis']['persists'];
        
        if ($testPersists && $basePersists && $dependentPersists) {
            $analysis['overall_conclusion'] = 'All services persist between requests (likely singleton behavior)';
        } elseif (!$testPersists && !$basePersists && !$dependentPersists) {
            $analysis['overall_conclusion'] = 'No services persist between requests (fresh instances each time)';
        } else {
            $analysis['overall_conclusion'] = 'Mixed behavior: some services persist, others don\'t';
        }
        
        return $analysis;
    }

    /**
     * Analyze interface request persistence test results
     */
    private function analyzeInterfaceRequestPersistence($requests)
    {
        if (count($requests) < 2) {
            return [
                'status' => 'insufficient_data',
                'message' => 'Need at least 2 requests to analyze interface persistence',
                'total_requests' => count($requests)
            ];
        }
        
        $analysis = [
            'total_requests' => count($requests),
            'test_service_interface_analysis' => $this->analyzeServicePersistence($requests, 'test_service_interface'),
            'base_service_interface_analysis' => $this->analyzeServicePersistence($requests, 'base_service_interface'),
            'dependent_service_interface_analysis' => $this->analyzeServicePersistence($requests, 'dependent_service_interface'),
            'overall_conclusion' => ''
        ];
        
        // Determine overall conclusion
        $testPersists = $analysis['test_service_interface_analysis']['persists'];
        $basePersists = $analysis['base_service_interface_analysis']['persists'];
        $dependentPersists = $analysis['dependent_service_interface_analysis']['persists'];
        
        if ($testPersists && $basePersists && $dependentPersists) {
            $analysis['overall_conclusion'] = 'All interface services persist between requests (singleton behavior confirmed)';
        } elseif (!$testPersists && !$basePersists && !$dependentPersists) {
            $analysis['overall_conclusion'] = 'No interface services persist between requests (fresh instances each time)';
        } else {
            $analysis['overall_conclusion'] = 'Mixed interface behavior: some services persist, others don\'t';
        }
        
        return $analysis;
    }

    /**
     * Analyze application injection test results
     */
    private function analyzeApplicationInjection($results)
    {
        $analysis = [
            'singleton_score' => 0,
            'app_persistence_score' => 0,
            'overall_score' => 0,
            'issues' => [],
            'insights' => []
        ];
        
        // Check singleton behavior
        if ($results['singleton_verification']['same_service_instance']) {
            $analysis['singleton_score'] += 50;
            $analysis['insights'][] = '✓ Service instance is properly shared (singleton behavior confirmed)';
        } else {
            $analysis['issues'][] = '✗ Service instance is not shared (singleton registration failed)';
        }
        
        if ($results['singleton_verification']['same_app_instance']) {
            $analysis['singleton_score'] += 30;
            $analysis['insights'][] = '✓ Application instance is shared between service resolutions';
        } else {
            $analysis['issues'][] = '✗ Different Application instances detected between resolutions';
        }
        
        if ($results['singleton_verification']['shared_action_count']) {
            $analysis['singleton_score'] += 20;
            $analysis['insights'][] = '✓ Action count persists across resolutions (shared state confirmed)';
        } else {
            $analysis['issues'][] = '✗ Action count doesn\'t persist (state not shared)';
        }
        
        // Check application persistence across requests
        $requests = $results['all_requests'];
        if (count($requests) > 1) {
            $appIds = array_column($requests, 'app_object_id');
            $uniqueAppIds = array_unique($appIds);
            
            if (count($uniqueAppIds) === 1) {
                $analysis['app_persistence_score'] = 100;
                $analysis['insights'][] = '✓ Application object ID persists across ALL requests';
            } else {
                $persistenceRatio = (count($requests) - count($uniqueAppIds) + 1) / count($requests);
                $analysis['app_persistence_score'] = round($persistenceRatio * 100);
                $analysis['insights'][] = "◐ Application object persists in {$analysis['app_persistence_score']}% of requests";
            }
        } else {
            $analysis['insights'][] = '? Single request - cannot test cross-request persistence yet';
        }
        
        // Calculate overall score
        $analysis['overall_score'] = round(($analysis['singleton_score'] + $analysis['app_persistence_score']) / 2);
        
        // Add summary
        if ($analysis['overall_score'] >= 90) {
            $analysis['summary'] = 'Excellent: Perfect singleton behavior and application persistence';
        } elseif ($analysis['overall_score'] >= 70) {
            $analysis['summary'] = 'Good: Singleton working well, some persistence detected';
        } elseif ($analysis['overall_score'] >= 50) {
            $analysis['summary'] = 'Moderate: Some issues with singleton or persistence behavior';
        } else {
            $analysis['summary'] = 'Poor: Significant issues with singleton registration or application persistence';
        }
        
        return $analysis;
    }

    /**
     * Analyze pure singleton test results
     */
    private function analyzePureSingleton($results)
    {
        $analysis = [
            'singleton_integrity_score' => 0,
            'request_persistence_score' => 0,
            'overall_score' => 0,
            'issues' => [],
            'insights' => []
        ];
        
        // Check singleton integrity within request
        $basicTest = $results['basic_test'];
        $directTest = $results['direct_comparison'];
        
        if ($basicTest['references_comparison']['ref1_vs_ref2_same'] && 
            $basicTest['references_comparison']['ref2_vs_ref3_same'] && 
            $basicTest['references_comparison']['ref1_vs_ref3_same']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ All singleton references point to the same instance';
        } else {
            $analysis['issues'][] = '✗ Singleton references are not the same instance';
        }
        
        if ($basicTest['references_comparison']['all_same_object_id']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ All singleton references have the same object ID';
        } else {
            $analysis['issues'][] = '✗ Singleton references have different object IDs';
        }
        
        if ($directTest['same_instance']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ Direct singleton access returns same instance';
        } else {
            $analysis['issues'][] = '✗ Direct singleton access returns different instances';
        }
        
        if ($results['multiple_services']['singletons_are_same']) {
            $analysis['singleton_integrity_score'] += 25;
            $analysis['insights'][] = '✓ Multiple services access the same singleton instance';
        } else {
            $analysis['issues'][] = '✗ Multiple services access different singleton instances';
        }
        
        // Check request persistence
        $requests = $results['all_requests'];
        if (count($requests) > 1) {
            $objectIds = array_column($requests, 'singleton_object_id');
            $uniqueObjectIds = array_unique($objectIds);
            
            if (count($uniqueObjectIds) === 1) {
                $analysis['request_persistence_score'] = 100;
                $analysis['insights'][] = '✓ Singleton object ID is IDENTICAL across ALL requests (PERSISTENT!)';
            } else {
                $analysis['request_persistence_score'] = 0;
                $analysis['insights'][] = '✗ Singleton object ID differs between requests (NOT PERSISTENT)';
                
                // Check if it's always the first instance
                $instanceIds = array_column($requests, 'singleton_instance_id');
                $uniqueInstanceIds = array_unique($instanceIds);
                if (count($uniqueInstanceIds) === 1 && $uniqueInstanceIds[0] === 1) {
                    $analysis['insights'][] = '◐ Always gets first instance, but different object each request';
                }
            }
            
            // Check total instances created
            $totalInstances = array_column($requests, 'total_instances_created');
            $maxInstances = max($totalInstances);
            if ($maxInstances === 1) {
                $analysis['insights'][] = '✓ Only one singleton instance ever created';
            } else {
                $analysis['insights'][] = "⚠ Multiple singleton instances created across requests: {$maxInstances}";
            }
        } else {
            $analysis['insights'][] = '? Single request - cannot test cross-request persistence yet';
        }
        
        // Calculate overall score
        $analysis['overall_score'] = round(($analysis['singleton_integrity_score'] + $analysis['request_persistence_score']) / 2);
        
        // Add summary
        if ($analysis['overall_score'] >= 90) {
            $analysis['summary'] = 'Excellent: Perfect singleton behavior with cross-request persistence';
        } elseif ($analysis['overall_score'] >= 70) {
            $analysis['summary'] = 'Good: Singleton working within request, some persistence detected';
        } elseif ($analysis['overall_score'] >= 50) {
            $analysis['summary'] = 'Moderate: Singleton working within request, limited persistence';
        } else {
            $analysis['summary'] = 'Poor: Singleton pattern broken or no cross-request persistence';
        }
        
        // Expected behavior note
        $analysis['expected_behavior'] = 'PHP singletons typically do NOT persist between HTTP requests in traditional web servers. Each request starts with a fresh PHP process.';
        
        return $analysis;
    }

    /**
     * Analyze request scoped service test results
     */
    private function analyzeRequestScoped($results)
    {
        $analysis = [
            'request_scoped_behavior' => 'Unknown',
            'instance_sharing' => 'Unknown',
            'state_persistence' => 'Unknown',
            'octane_safety' => 'Unknown',
            'recommendations' => []
        ];

        // Check if same instance is shared within request
        $firstCall = $results['first_call'];
        $secondCall = $results['second_call'];
        $methodInjection = $results['method_injection'];

        // Instance sharing analysis
        if ($firstCall['instance_id'] === $secondCall['instance_id']) {
            $analysis['instance_sharing'] = 'Shared within request ✅';
        } else {
            $analysis['instance_sharing'] = 'Separate instances ❌';
        }

        // State persistence analysis
        if ($secondCall['action_count'] > $firstCall['action_count']) {
            $analysis['state_persistence'] = 'State persists within request ✅';
        } else {
            $analysis['state_persistence'] = 'State does not persist ❌';
        }

        // Request scoped behavior
        if ($firstCall['instance_id'] === $secondCall['instance_id'] && 
            $secondCall['action_count'] > $firstCall['action_count']) {
            $analysis['request_scoped_behavior'] = 'Properly scoped to request ✅';
            $analysis['octane_safety'] = 'Safe for Octane ✅';
            $analysis['recommendations'][] = 'This pattern is safe for Laravel Octane';
            $analysis['recommendations'][] = 'Instance will reset between requests';
        } else {
            $analysis['request_scoped_behavior'] = 'Not properly scoped ❌';
            $analysis['octane_safety'] = 'Potential issues ⚠️';
            $analysis['recommendations'][] = 'Review service binding configuration';
        }

        // Method injection verification
        if ($methodInjection['same_instance']) {
            $analysis['recommendations'][] = 'Method injection correctly resolves same instance';
        } else {
            $analysis['recommendations'][] = 'Method injection creates separate instance - review binding';
        }

        return $analysis;
    }
} 