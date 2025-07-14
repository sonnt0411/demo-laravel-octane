@extends('layouts.app')

@section('title', 'Laravel Dependency Injection Test')
@section('page_title', 'Basic Dependency Injection Test')

@section('additional_styles')
<style>
        h2 {
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .summary {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #3498db;
        }
        
        .summary h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        
        .test-result {
            background: #f8f9fa;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }
        
        .test-name {
            font-weight: bold;
            color: #2c3e50;
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
            color: #e74c3c;
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
            border-left: 4px solid #9b59b6;
        }
        
        .reused {
            border-left-color: #f39c12 !important;
        }
        
        .unique {
            border-left-color: #27ae60 !important;
        }
        
        .test-links {
            margin-bottom: 30px;
            padding: 20px;
            background: #e8f4f8;
            border-radius: 6px;
        }
        
        .test-links a {
            display: inline-block;
            margin-right: 15px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .test-links a:hover {
            background: #2980b9;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: #34495e;
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
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }
</style>
@endsection

@section('content')
<div class="container">
        <h1>Laravel Dependency Injection Test Results</h1>
        
        @if(isset($test_type))
            <div class="summary">
                <h3>{{ $test_type }}</h3>
            </div>
        @endif
        
        <div class="test-links">
            <h3>Available Tests:</h3>
            <a href="{{ route('dependency.test') }}">Standard DI Test</a>
            <a href="{{ route('dependency.singleton') }}">Singleton Test</a>
            <a href="{{ route('dependency.shared') }}">Shared Dependency Test</a>
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
                <div class="test-name">{{ str_replace('_', ' ', $testName) }}</div>
                <div class="instance-info">
                    @if(isset($data['test_service']))
                        <!-- This is from DependentService -->
                        <strong>Dependent Service ID:</strong> <span class="instance-id">{{ $data['id'] }}</span><br>
                        <strong>Injected TestService ID:</strong> <span class="instance-id">{{ $data['test_service']['id'] }}</span><br>
                        <strong>Created At:</strong> <span class="timestamp">{{ date('H:i:s.v', $data['test_service']['created_at']) }}</span><br>
                        <strong>Message:</strong> {{ $data['test_service']['message'] }}
                    @else
                        <!-- Direct TestService instance -->
                        <strong>Instance ID:</strong> <span class="instance-id">{{ $data['id'] }}</span><br>
                        <strong>Created At:</strong> <span class="timestamp">{{ date('H:i:s.v', $data['created_at']) }}</span><br>
                        <strong>Message:</strong> {{ $data['message'] }}
                    @endif
                </div>
            </div>
        @endforeach
        
        <h2>Instance Analysis</h2>
        @foreach($summary['analysis'] as $analysis)
            <div class="analysis-item {{ $analysis['is_reused'] ? 'reused' : 'unique' }}">
                <strong>Instance:</strong> <span class="instance-id">{{ $analysis['instance_id'] }}</span><br>
                <strong>Usage Count:</strong> {{ $analysis['usage_count'] }}<br>
                <strong>Used in Tests:</strong> {{ implode(', ', $analysis['used_in_tests']) }}<br>
                <strong>Status:</strong> {{ $analysis['is_reused'] ? 'REUSED INSTANCE' : 'UNIQUE INSTANCE' }}
            </div>
        @endforeach
        
        <div class="conclusion">
            <h3>Conclusion</h3>
            @if($summary['same_instances'] > 0)
                <p><strong>Some instances were reused!</strong> This means Laravel's service container is sharing instances in certain scenarios within the same request.</p>
            @else
                <p><strong>All instances were unique!</strong> This means Laravel's service container creates a new instance each time a class is resolved, unless explicitly configured as a singleton.</p>
            @endif
            
            <p>
                <strong>Key Findings:</strong>
            </p>
            <ul>
                <li>Total resolution attempts: {{ $summary['total_tests'] }}</li>
                <li>Unique instances created: {{ $summary['unique_instances'] }}</li>
                <li>Instance reuses: {{ $summary['same_instances'] }}</li>
            </ul>
            
            <p>
                <strong>Expected Behavior:</strong> In standard Laravel DI, each resolution should create a new instance unless:
                <ul>
                    <li>The class is bound as a singleton using <code>app()->singleton()</code></li>
                    <li>The class is resolved as part of constructor injection (same instance for the controller lifetime)</li>
                </ul>
            </p>
        </div>
</div>
@endsection 