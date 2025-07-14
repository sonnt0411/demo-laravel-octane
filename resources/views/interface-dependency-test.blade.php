@extends('layouts.app')

@section('title', 'Laravel Interface-Based Dependency Injection Test')
@section('page_title', 'Interface Dependency Test')

@section('additional_styles')
<style>
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
        
        .test-result {
            background: #faf5ff;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #7c3aed;
        }
        
        .test-name {
            font-weight: bold;
            color: #6d28d9;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .instance-info {
            background: white;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        
        .instance-id {
            color: #dc2626;
            font-weight: bold;
        }
        
        .implementation {
            color: #7c3aed;
            font-weight: bold;
        }
        
        .timestamp {
            color: #7f8c8d;
        }
        
        .analysis-item {
            background: white;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #a855f7;
        }
        
        .reused {
            border-left-color: #f59e0b !important;
        }
        
        .unique {
            border-left-color: #10b981 !important;
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
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: #6d28d9;
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .conclusion {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }
        
        .interface-badge {
            background: #7c3aed;
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
        <h1>🔌 Interface-Based Dependency Injection Test Results</h1>
        
        @if(isset($test_type))
            <div class="summary">
                <h3>{{ $test_type }}</h3>
                <p>Testing Laravel's service container behavior when resolving interfaces instead of concrete classes.</p>
            </div>
        @endif
        
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
        
        <div class="summary">
            <h3>Test Summary</h3>
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-number">{{ $summary['total_tests'] }}</div>
                    <div class="stat-label">Total Tests</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ $summary['unique_instances'] }}</div>
                    <div class="stat-label">Unique Instances</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ $summary['same_instances'] }}</div>
                    <div class="stat-label">Reused Instances</div>
                </div>
            </div>
        </div>
        
        <h2>Individual Test Results</h2>
        @foreach($results as $testName => $data)
            <div class="test-result">
                <div class="test-name">{{ str_replace('_', ' ', $testName) }} <span class="interface-badge">INTERFACE</span></div>
                <div class="instance-info">
                    @if(isset($data['test_service']))
                        <!-- This is from InterfaceDependentService -->
                        <strong>Service ID:</strong> <span class="instance-id">{{ $data['id'] }}</span><br>
                        <strong>Created At:</strong> <span class="timestamp">{{ $data['created_at'] }}</span><br>
                        <strong>Message:</strong> {{ $data['message'] }}<br>
                        <strong>Dependency Type:</strong> <span class="implementation">{{ $data['dependency_type'] ?? 'Interface-based' }}</span><br>
                        <br>
                        <strong>🔌 Injected Interface Implementation:</strong><br>
                        &nbsp;&nbsp;<strong>ID:</strong> <span class="instance-id">{{ $data['test_service']['id'] }}</span><br>
                        &nbsp;&nbsp;<strong>Implementation:</strong> <span class="implementation">{{ $data['test_service']['implementation'] ?? 'ConcreteTestService' }}</span><br>
                        &nbsp;&nbsp;<strong>Message:</strong> {{ $data['test_service']['message'] }}<br>
                        &nbsp;&nbsp;<strong>Created At:</strong> <span class="timestamp">{{ $data['test_service']['created_at'] }}</span>
                    @else
                        <!-- Direct interface resolution -->
                        <strong>Instance ID:</strong> <span class="instance-id">{{ $data['id'] }}</span><br>
                        <strong>Implementation:</strong> <span class="implementation">{{ $data['implementation'] ?? 'ConcreteTestService' }}</span><br>
                        <strong>Created At:</strong> <span class="timestamp">{{ $data['created_at'] }}</span><br>
                        <strong>Message:</strong> {{ $data['message'] }}
                    @endif
                </div>
            </div>
        @endforeach
        
        <h2>Instance Analysis</h2>
        <p>This analysis shows how Laravel's service container handles interface-based dependency injection:</p>
        
        @foreach($summary['analysis'] as $analysis)
            <div class="analysis-item {{ $analysis['is_reused'] ? 'reused' : 'unique' }}">
                <strong>Instance ID:</strong> <span class="instance-id">{{ $analysis['instance_id'] }}</span><br>
                <strong>Dependency Type:</strong> <span class="implementation">{{ $analysis['dependency_type'] ?? 'Interface-based' }}</span><br>
                <strong>Usage Count:</strong> {{ $analysis['usage_count'] }} time(s)<br>
                <strong>Used in:</strong> {{ implode(', ', $analysis['used_in_tests']) }}<br>
                <strong>Status:</strong> 
                @if($analysis['is_reused'])
                    <span style="color: #f59e0b;">🔄 REUSED - Same interface implementation instance</span>
                @else
                    <span style="color: #10b981;">✨ UNIQUE - Separate interface implementation instance</span>
                @endif
            </div>
        @endforeach
        
        <div class="conclusion">
            <h3>🎯 Key Findings for Interface-Based Dependency Injection</h3>
            @if($summary['same_instances'] > 0)
                <p><strong>✅ Interface Instance Reuse Detected:</strong> Laravel's service container reused {{ $summary['same_instances'] }} interface implementation instances out of {{ $summary['total_tests'] }} total resolutions.</p>
                <p><strong>🔍 Behavior:</strong> When resolving interfaces, Laravel may reuse the same concrete implementation instances in certain scenarios, similar to concrete class injection.</p>
            @else
                <p><strong>🆕 No Interface Instance Reuse:</strong> Laravel created separate interface implementation instances for each resolution request.</p>
                <p><strong>🔍 Behavior:</strong> Each time an interface is resolved, Laravel creates a new instance of the concrete implementation.</p>
            @endif
            
            <p><strong>💡 Interface vs Concrete Classes:</strong> Compare these results with the concrete class dependency injection tests to see if Laravel treats interface-based injection differently.</p>
            
            <p><strong>📋 Test Details:</strong> 
                {{ $summary['unique_instances'] }} unique interface implementations were created across {{ $summary['total_tests'] }} test scenarios.
            </p>
        </div>
</div>
@endsection 