<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Dependency Injection Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        
        h2 {
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .test-overview {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #3498db;
        }
        
        .conclusion {
            background: {{ $summary['is_shared'] ? '#d4edda' : '#f8d7da' }};
            border: 1px solid {{ $summary['is_shared'] ? '#c3e6cb' : '#f5c6cb' }};
            color: {{ $summary['is_shared'] ? '#155724' : '#721c24' }};
            padding: 25px;
            border-radius: 6px;
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        
        .execution-sequence {
            background: #fff3cd;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #ffc107;
        }
        
        .sequence-step {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 10px;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #17a2b8;
        }
        
        .step-number {
            background: #17a2b8;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .step-details {
            flex-grow: 1;
        }
        
        .step-action {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .step-result {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6c757d;
        }
        
        .comparison-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .service-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #17a2b8;
        }
        
        .service-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
            text-transform: uppercase;
        }
        
        .info-item {
            margin-bottom: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            padding: 5px 10px;
            background: white;
            border-radius: 3px;
        }
        
        .verification-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #ffc107;
        }
        
        .verification-method {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 10px;
            background: white;
            border-radius: 4px;
        }
        
        .method-name {
            font-weight: bold;
            color: #495057;
        }
        
        .method-result {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
        }
        
        .same {
            background: #d4edda;
            color: #155724;
        }
        
        .different {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-count-analysis {
            background: #e9ecef;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #6f42c1;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .back-link:hover {
            background: #5a6268;
        }
        
        .object-hash {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 2px 4px;
            border-radius: 2px;
        }
        
        .action-count {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .expected-behavior {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 3px solid #bee5eb;
        }
        
        .actual-behavior {
            background: #f8d7da;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Shared Dependency Injection Test</h1>
        
        <div class="test-overview">
            <h3>Test Overview</h3>
            <p>This test verifies whether Laravel's service container shares the same instance of <strong>BaseService</strong> when it's injected into multiple services (<strong>FirstService</strong> and <strong>SecondService</strong>) within the same request.</p>
            
            <p><strong>Test Strategy:</strong> Execute different numbers of actions on each service to verify if they share state.</p>
            <ul>
                <li><strong>FirstService</strong> executes 3 actions on its BaseService</li>
                <li><strong>SecondService</strong> executes 3 actions on its BaseService</li>
                <li><strong>If shared:</strong> Both would show actionCount = 6 (cumulative)</li>
                <li><strong>If separate:</strong> Each would show actionCount = 3 (independent)</li>
            </ul>
        </div>
        
        <div class="conclusion">
            {{ $summary['conclusion'] }}
        </div>
        
        <div class="action-count-analysis">
            <h3>Action Count Analysis - The Key Test</h3>
            
            <div class="expected-behavior">
                <strong>Expected if SHARED instance:</strong><br>
                Both services would show actionCount = {{ $summary['action_count_analysis']['expected_if_shared'] }} (cumulative across all actions)
            </div>
            
            <div class="expected-behavior">
                <strong>Expected if SEPARATE instances:</strong><br>
                FirstService BaseService = 3, SecondService BaseService = 3 (each tracking its own actions)
            </div>
            
            <div class="actual-behavior">
                <strong>Actual Result:</strong><br>
                {{ $summary['action_count_analysis']['actual_behavior'] }}<br>
                <em>{{ $summary['action_count_analysis']['shared_state_verdict'] }}</em>
            </div>
        </div>
        
        <div class="execution-sequence">
            <h3>Execution Sequence</h3>
            <p>The test executes actions in this specific order to verify shared state:</p>
            
            @php
                $stepCounter = 1;
                $stepLabels = [
                    'step_1_first_task_1' => 'FirstService → executeTask("test_task_1")',
                    'step_2_first_task_2' => 'FirstService → executeTask("test_task_2")',
                    'step_3_second_task_1' => 'SecondService → processData("test_data_1")',
                    'step_4_first_task_3' => 'FirstService → executeTask("test_task_3")',
                    'step_5_second_task_2' => 'SecondService → processData("test_data_2")',
                    'step_6_second_task_3' => 'SecondService → processData("test_data_3")'
                ];
            @endphp
            
            @foreach($results['execution_sequence'] as $stepKey => $stepResult)
                <div class="sequence-step">
                    <div class="step-number">{{ $stepCounter++ }}</div>
                    <div class="step-details">
                        <div class="step-action">{{ $stepLabels[$stepKey] }}</div>
                        <div class="step-result">
                            BaseService actionCount after: {{ $stepResult['base_service_result']['action_count'] }}
                            | Action: {{ $stepResult['base_service_result']['action'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="verification-section">
            <h3>Verification Methods</h3>
            @foreach($summary['verification_methods'] as $method => $result)
                <div class="verification-method">
                    <span class="method-name">{{ str_replace('_', ' ', ucfirst($method)) }}</span>
                    <span class="method-result {{ str_contains($result, 'SAME') ? 'same' : 'different' }}">
                        {{ $result }}
                    </span>
                </div>
            @endforeach
        </div>
        
        <div class="comparison-grid">
            <div class="service-info">
                <div class="service-title">FirstService Final State</div>
                <div class="info-item">
                    <strong>Service ID:</strong> {{ $results['final_state']['first_service_final']['service_id'] }}
                </div>
                <div class="info-item">
                    <strong>Object Hash:</strong> <span class="object-hash">{{ $results['final_state']['first_service_final']['object_hash'] }}</span>
                </div>
                <div class="info-item">
                    <strong>BaseService ID:</strong> {{ $results['final_state']['first_service_final']['base_service_info']['id'] }}
                </div>
                <div class="info-item">
                    <strong>BaseService Hash:</strong> <span class="object-hash">{{ $results['final_state']['first_service_final']['base_service_info']['object_hash'] }}</span>
                </div>
                <div class="info-item">
                    <strong>BaseService Actions:</strong> <span class="action-count">{{ $results['final_state']['first_service_final']['base_service_info']['action_count'] }}</span>
                </div>
            </div>
            
            <div class="service-info">
                <div class="service-title">SecondService Final State</div>
                <div class="info-item">
                    <strong>Service ID:</strong> {{ $results['final_state']['second_service_final']['service_id'] }}
                </div>
                <div class="info-item">
                    <strong>Object Hash:</strong> <span class="object-hash">{{ $results['final_state']['second_service_final']['object_hash'] }}</span>
                </div>
                <div class="info-item">
                    <strong>BaseService ID:</strong> {{ $results['final_state']['second_service_final']['base_service_info']['id'] }}
                </div>
                <div class="info-item">
                    <strong>BaseService Hash:</strong> <span class="object-hash">{{ $results['final_state']['second_service_final']['base_service_info']['object_hash'] }}</span>
                </div>
                <div class="info-item">
                    <strong>BaseService Actions:</strong> <span class="action-count">{{ $results['final_state']['second_service_final']['base_service_info']['action_count'] }}</span>
                </div>
            </div>
        </div>
        
        <div class="verification-section">
            <h3>Technical Details</h3>
            <div class="info-item">
                <strong>First BaseService Hash:</strong> <span class="object-hash">{{ $results['dependency_comparison']['first_base_hash'] }}</span>
            </div>
            <div class="info-item">
                <strong>Second BaseService Hash:</strong> <span class="object-hash">{{ $results['dependency_comparison']['second_base_hash'] }}</span>
            </div>
            <div class="info-item">
                <strong>Hashes Match:</strong> {{ $results['dependency_comparison']['same_object_hash'] ? 'YES' : 'NO' }}
            </div>
            <div class="info-item">
                <strong>Strict Comparison (===):</strong> {{ $results['dependency_comparison']['is_same_object'] ? 'SAME OBJECT' : 'DIFFERENT OBJECTS' }}
            </div>
            <div class="info-item">
                <strong>Total Actions Executed:</strong> {{ $results['dependency_comparison']['total_actions_executed'] }}
            </div>
        </div>
        
        <a href="{{ route('dependency.test') }}" class="back-link">← Back to Main Tests</a>
    </div>
</body>
</html> 