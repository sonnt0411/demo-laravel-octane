@extends('layouts.app')

@section('title', 'Laravel Shared Interface Dependency Injection Test')
@section('page_title', 'Shared Interface Dependency Test')

@section('additional_styles')
<style>
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #8b5cf6;
            border-bottom: 3px solid #a855f7;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        
        h2 {
            color: #6d28d9;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .summary {
            background: #f3e8ff;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #a855f7;
        }
        
        .summary h3 {
            margin-top: 0;
            color: #6d28d9;
        }
        
        .test-links {
            margin-bottom: 30px;
            padding: 20px;
            background: #f3e8ff;
            border-radius: 6px;
        }
        
        .test-links a {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 10px;
            padding: 10px 20px;
            background: #a855f7;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .test-links a:hover {
            background: #7c3aed;
        }
        
        .concrete-test {
            background: #3b82f6;
        }
        
        .concrete-test:hover {
            background: #2563eb;
        }
        
        .step-section {
            margin-bottom: 20px;
        }
        
        .step-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .step-card {
            background: #faf5ff;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #7c3aed;
        }
        
        .step-title {
            font-weight: bold;
            color: #6d28d9;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .step-content {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            background: white;
            padding: 10px;
            border-radius: 4px;
        }
        
        .comparison-section {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #0ea5e9;
        }
        
        .verification-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .verification-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #10b981;
        }
        
        .verification-fail {
            border-left-color: #ef4444;
        }
        
        .action-count-analysis {
            background: #fef3c7;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
        }
        
        .conclusion {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }
        
        .conclusion.separate {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        
        .instance-id {
            color: #dc2626;
            font-weight: bold;
        }
        
        .hash-id {
            color: #7c3aed;
            font-weight: bold;
        }
        
        .action-count {
            color: #059669;
            font-weight: bold;
            font-size: 1.2em;
        }
        
        .interface-badge {
            background: #7c3aed;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .shared-badge {
            background: #10b981;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .separate-badge {
            background: #ef4444;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
</style>
@endsection

@section('content')
<div class="container">
        <h1>🔗 Shared Interface Dependency Injection Test</h1>
        
        <div class="summary">
            <h3>Interface Dependency Sharing Analysis</h3>
            <p>This test examines whether Laravel's service container shares the same interface implementation instance between multiple services or creates separate instances for each service.</p>
            <p><strong>Test Scenario:</strong> Two services (InterfaceFirstService & InterfaceSecondService) both depend on BaseServiceInterface. We execute different actions and analyze shared state.</p>
        </div>
        
        <div class="test-links">
            <h3>Available Tests:</h3>
            <h4>🔌 Interface-Based Tests:</h4>
            <a href="{{ route('interface.dependency.test') }}">Interface DI Test</a>
            <a href="{{ route('interface.singleton') }}">Interface Singleton Test</a>
            <a href="{{ route('interface.dependency.shared') }}">Shared Interface Dependency Test</a>
            
            <h4>🏗️ Concrete Class Tests (for comparison):</h4>
            <a href="{{ route('dependency.test') }}" class="concrete-test">Standard DI Test</a>
            <a href="{{ route('dependency.singleton') }}" class="concrete-test">Singleton Test</a>
            <a href="{{ route('dependency.shared') }}" class="concrete-test">Shared Dependency Test</a>
        </div>
        
        <h2>📋 Test Execution Sequence</h2>
        <div class="step-grid">
            @foreach($results['execution_sequence'] as $stepName => $stepData)
                <div class="step-card">
                    <div class="step-title">{{ str_replace('_', ' ', ucfirst($stepName)) }} <span class="interface-badge">INTERFACE</span></div>
                    <div class="step-content">
                        <strong>Service:</strong> {{ $stepData['service_id'] ?? 'N/A' }}<br>
                        <strong>Task/Data:</strong> {{ $stepData['task'] ?? $stepData['data'] ?? 'N/A' }}<br>
                        <strong>Base Service ID:</strong> <span class="instance-id">{{ $stepData['base_service_info']['id'] }}</span><br>
                        <strong>Action Count After:</strong> <span class="action-count">{{ $stepData['base_service_info']['action_count'] }}</span><br>
                        <strong>Timestamp:</strong> {{ number_format($stepData['timestamp'], 6) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="comparison-section">
            <h3>🔍 Interface Instance Comparison Results</h3>
            
            <div class="verification-grid">
                <div class="verification-card {{ $results['dependency_comparison']['is_same_object'] ? '' : 'verification-fail' }}">
                    <h4>🔗 Strict Object Comparison</h4>
                    <p><strong>Result:</strong> {{ $results['dependency_comparison']['is_same_object'] ? 'SAME OBJECT' : 'DIFFERENT OBJECTS' }}</p>
                    <p><strong>Test:</strong> $baseService1 === $baseService2</p>
                    <p><strong>Status:</strong> 
                        @if($results['dependency_comparison']['is_same_object'])
                            <span class="shared-badge">SHARED</span>
                        @else
                            <span class="separate-badge">SEPARATE</span>
                        @endif
                    </p>
                </div>
                
                <div class="verification-card {{ $results['dependency_comparison']['same_object_hash'] ? '' : 'verification-fail' }}">
                    <h4>🏷️ Object Hash Comparison</h4>
                    <p><strong>First Service Hash:</strong> <span class="hash-id">{{ $results['dependency_comparison']['first_base_hash'] }}</span></p>
                    <p><strong>Second Service Hash:</strong> <span class="hash-id">{{ $results['dependency_comparison']['second_base_hash'] }}</span></p>
                    <p><strong>Match:</strong> {{ $results['dependency_comparison']['same_object_hash'] ? 'YES' : 'NO' }}</p>
                </div>
                
                <div class="verification-card {{ $results['dependency_comparison']['same_instance_id'] ? '' : 'verification-fail' }}">
                    <h4>🆔 Instance ID Comparison</h4>
                    <p><strong>First Service ID:</strong> <span class="instance-id">{{ $results['dependency_comparison']['first_base_id'] }}</span></p>
                    <p><strong>Second Service ID:</strong> <span class="instance-id">{{ $results['dependency_comparison']['second_base_id'] }}</span></p>
                    <p><strong>Match:</strong> {{ $results['dependency_comparison']['same_instance_id'] ? 'YES' : 'NO' }}</p>
                </div>
            </div>
        </div>
        
        <div class="action-count-analysis">
            <h3>📊 Shared State Analysis (Action Count Tracking)</h3>
            <p><strong>This is the key test for shared state behavior:</strong></p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 15px 0;">
                <div style="background: white; padding: 15px; border-radius: 6px;">
                    <h4>InterfaceFirstService</h4>
                    <p><strong>Actions Executed:</strong> 3</p>
                    <p><strong>Base Service Count:</strong> <span class="action-count">{{ $results['dependency_comparison']['first_base_action_count'] }}</span></p>
                </div>
                
                <div style="background: white; padding: 15px; border-radius: 6px;">
                    <h4>InterfaceSecondService</h4>
                    <p><strong>Actions Executed:</strong> 3</p>
                    <p><strong>Base Service Count:</strong> <span class="action-count">{{ $results['dependency_comparison']['second_base_action_count'] }}</span></p>
                </div>
                
                <div style="background: white; padding: 15px; border-radius: 6px;">
                    <h4>Expected Results</h4>
                    <p><strong>If Shared:</strong> Both = 6</p>
                    <p><strong>If Separate:</strong> Both = 3</p>
                </div>
            </div>
            
            <p><strong>Actual Behavior:</strong> {{ $summary['action_count_analysis']['actual_behavior'] }}</p>
            <p><strong>Verdict:</strong> {{ $summary['action_count_analysis']['shared_state_verdict'] }}</p>
        </div>
        
        <h2>📈 Service State Information</h2>
        
        <h3>🏁 Initial State</h3>
        <div class="step-grid">
            <div class="step-card">
                <div class="step-title">InterfaceFirstService Initial State</div>
                <div class="step-content">
                    <strong>Service ID:</strong> {{ $results['initial_state']['first_service_info']['service_id'] }}<br>
                    <strong>Dependency Type:</strong> {{ $results['initial_state']['first_service_info']['dependency_type'] }}<br>
                    <strong>Base Service ID:</strong> <span class="instance-id">{{ $results['initial_state']['first_service_info']['base_service_info']['id'] }}</span><br>
                    <strong>Base Action Count:</strong> {{ $results['initial_state']['first_service_info']['base_service_info']['action_count'] }}
                </div>
            </div>
            
            <div class="step-card">
                <div class="step-title">InterfaceSecondService Initial State</div>
                <div class="step-content">
                    <strong>Service ID:</strong> {{ $results['initial_state']['second_service_info']['service_id'] }}<br>
                    <strong>Dependency Type:</strong> {{ $results['initial_state']['second_service_info']['dependency_type'] }}<br>
                    <strong>Base Service ID:</strong> <span class="instance-id">{{ $results['initial_state']['second_service_info']['base_service_info']['id'] }}</span><br>
                    <strong>Base Action Count:</strong> {{ $results['initial_state']['second_service_info']['base_service_info']['action_count'] }}
                </div>
            </div>
        </div>
        
        <h3>🏆 Final State</h3>
        <div class="step-grid">
            <div class="step-card">
                <div class="step-title">InterfaceFirstService Final State</div>
                <div class="step-content">
                    <strong>Service ID:</strong> {{ $results['final_state']['first_service_final']['service_id'] }}<br>
                    <strong>Dependency Type:</strong> {{ $results['final_state']['first_service_final']['dependency_type'] }}<br>
                    <strong>Base Service ID:</strong> <span class="instance-id">{{ $results['final_state']['first_service_final']['base_service_info']['id'] }}</span><br>
                    <strong>Final Action Count:</strong> <span class="action-count">{{ $results['final_state']['first_service_final']['base_service_info']['action_count'] }}</span>
                </div>
            </div>
            
            <div class="step-card">
                <div class="step-title">InterfaceSecondService Final State</div>
                <div class="step-content">
                    <strong>Service ID:</strong> {{ $results['final_state']['second_service_final']['service_id'] }}<br>
                    <strong>Dependency Type:</strong> {{ $results['final_state']['second_service_final']['dependency_type'] }}<br>
                    <strong>Base Service ID:</strong> <span class="instance-id">{{ $results['final_state']['second_service_final']['base_service_info']['id'] }}</span><br>
                    <strong>Final Action Count:</strong> <span class="action-count">{{ $results['final_state']['second_service_final']['base_service_info']['action_count'] }}</span>
                </div>
            </div>
        </div>
        
        <div class="conclusion {{ $summary['is_shared'] ? '' : 'separate' }}">
            <h3>🎯 Interface Dependency Sharing Test Results</h3>
            
            <h4>📋 Summary</h4>
            <p><strong>{{ $summary['conclusion'] }}</strong></p>
            
            <h4>🔍 Verification Methods</h4>
            <ul>
                <li><strong>Strict Comparison:</strong> {{ $summary['verification_methods']['strict_comparison'] }}</li>
                <li><strong>Object Hash:</strong> {{ $summary['verification_methods']['object_hash_comparison'] }}</li>
                <li><strong>Instance ID:</strong> {{ $summary['verification_methods']['instance_id_comparison'] }}</li>
            </ul>
            
            <h4>📊 Action Count Analysis</h4>
            <ul>
                <li><strong>InterfaceFirstService Base Count:</strong> {{ $summary['action_count_analysis']['first_service_count'] }}</li>
                <li><strong>InterfaceSecondService Base Count:</strong> {{ $summary['action_count_analysis']['second_service_count'] }}</li>
                <li><strong>Total Actions Executed:</strong> {{ $summary['action_count_analysis']['total_actions_executed'] }}</li>
                <li><strong>Expected if Shared:</strong> {{ $summary['action_count_analysis']['expected_if_shared'] }}</li>
                <li><strong>Expected if Separate:</strong> {{ $summary['action_count_analysis']['expected_if_separate'] }}</li>
            </ul>
            
            <h4>💡 Key Insights</h4>
            <p><strong>{{ $summary['comparison_with_concrete'] }}</strong></p>
            
            @if($summary['is_shared'])
                <p>✅ Laravel's service container <strong>SHARES</strong> the same BaseServiceInterface implementation instance between InterfaceFirstService and InterfaceSecondService.</p>
                <p>🔍 This means when you inject the same interface into multiple services, they receive the same concrete implementation instance.</p>
            @else
                <p>🆕 Laravel's service container creates <strong>SEPARATE</strong> BaseServiceInterface implementation instances for InterfaceFirstService and InterfaceSecondService.</p>
                <p>🔍 This means each service gets its own fresh instance of the concrete implementation, even when injecting the same interface.</p>
            @endif
        </div>
</div>
@endsection 