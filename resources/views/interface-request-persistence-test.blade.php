@extends('layouts.app')

@section('title', 'Interface Request Persistence Test')
@section('page_title', 'Interface Request Persistence Test')

@section('additional_styles')
    <style>
        h1 {
            border-bottom: 3px solid #8e44ad;
        }
        
        .test-overview {
            background: #f8e8ff;
            border-left: 4px solid #8e44ad;
        }
        
        .conclusion {
            background: {{ isset($analysis['overall_conclusion']) && str_contains($analysis['overall_conclusion'], 'persist') ? '#d4edda' : '#f8d7da' }};
            border: 1px solid {{ isset($analysis['overall_conclusion']) && str_contains($analysis['overall_conclusion'], 'persist') ? '#c3e6cb' : '#f5c6cb' }};
            color: {{ isset($analysis['overall_conclusion']) && str_contains($analysis['overall_conclusion'], 'persist') ? '#155724' : '#721c24' }};
            padding: 25px;
            border-radius: 6px;
            margin: 30px 0;
        }
        
        .request-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .current-request {
            background: #fff3cd;
            border: 2px solid #ffc107;
        }
        
        .service-data {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .service-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
        }
        
        .service-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .interface-badge {
            background: #8e44ad;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            margin-left: 5px;
        }
        
        .property {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .property-label {
            font-weight: 500;
            color: #6c757d;
        }
        
        .property-value {
            font-family: 'Courier New', monospace;
            color: #495057;
        }
        
        .analysis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .analysis-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
        }
        
        .persistence-score {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .score-red { color: #dc3545; }
        .score-yellow { color: #ffc107; }
        .score-green { color: #28a745; }
        

        
        .refresh-btn {
            background: #8e44ad;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
        }
        
        .refresh-btn:hover {
            background: #7d3c98;
        }
        
        .clear-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .clear-btn:hover {
            background: #c82333;
        }
        
        .instance-tracking {
            background: #f1f3f4;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
        
        .instance-list {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #6c757d;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h1>{{ $test_type ?? 'Interface Request Persistence Test' }}</h1>
        
        <div class="nav-links">
            <a href="{{ route('tests.index') }}">← Back to Tests</a>
            <a href="{{ route('request.persistence.test') }}">Concrete Classes</a>
            <a href="{{ route('interface.request.persistence.test') }}">Interface Classes</a>
            <a href="{{ route('persistence.cache.clear') }}" onclick="clearCache()">Clear Cache</a>
        </div>
        
        <div class="test-overview">
            <h3>🔍 Interface Test Overview</h3>
            <p>This test verifies whether interface-based service instances persist between different HTTP requests. Interface services are typically registered as singletons in Laravel's service container, so they should maintain the same instance across requests.</p>
            
            <div style="margin-top: 15px;">
                <button class="refresh-btn" onclick="window.location.reload()">🔄 Make New Request</button>
                <button class="clear-btn" onclick="clearCache()">🗑 Clear Test History</button>
            </div>
        </div>
        
        @if(isset($analysis['status']) && $analysis['status'] === 'insufficient_data')
            <div class="conclusion">
                <h3>📊 Analysis Status</h3>
                <p><strong>{{ $analysis['message'] }}</strong></p>
                <p>Current requests: {{ $analysis['total_requests'] }}</p>
                <p>Please refresh the page to make more requests and analyze interface persistence behavior.</p>
            </div>
        @else
            <div class="conclusion">
                <h3>🎯 Overall Conclusion</h3>
                <p><strong>{{ $analysis['overall_conclusion'] }}</strong></p>
                <p>Based on {{ $analysis['total_requests'] }} requests analyzed</p>
            </div>
        @endif
        
        <h2>📋 Current Request Data</h2>
        <div class="request-card current-request">
            <h3>Request: {{ $current_request['request_id'] }}</h3>
            <p><strong>Timestamp:</strong> {{ $current_request['timestamp'] }}</p>
            
            <div class="service-data">
                <div class="service-card">
                    <div class="service-title">TestServiceInterface<span class="interface-badge">Interface</span></div>
                    <div class="property">
                        <span class="property-label">Instance ID:</span>
                        <span class="property-value">{{ $current_request['test_service_interface']['instance_id'] }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Object Hash:</span>
                        <span class="property-value">{{ $current_request['test_service_interface']['object_hash'] }}</span>
                    </div>
                                         <div class="property">
                         <span class="property-label">Message:</span>
                         <span class="property-value">{{ $current_request['test_service_interface']['data']['message'] ?? 'None' }}</span>
                     </div>
                </div>
                
                <div class="service-card">
                    <div class="service-title">BaseServiceInterface<span class="interface-badge">Interface</span></div>
                    <div class="property">
                        <span class="property-label">Instance ID:</span>
                        <span class="property-value">{{ $current_request['base_service_interface']['instance_id'] }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Object Hash:</span>
                        <span class="property-value">{{ $current_request['base_service_interface']['object_hash'] }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Action Count:</span>
                        <span class="property-value">{{ $current_request['base_service_interface']['action_count'] }}</span>
                    </div>
                </div>
                
                <div class="service-card">
                    <div class="service-title">InterfaceDependentService<span class="interface-badge">Interface</span></div>
                    <div class="property">
                        <span class="property-label">Instance ID:</span>
                        <span class="property-value">{{ $current_request['dependent_service_interface']['instance_id'] }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Object Hash:</span>
                        <span class="property-value">{{ $current_request['dependent_service_interface']['object_hash'] }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Test Service ID:</span>
                        <span class="property-value">{{ $current_request['dependent_service_interface']['data']['test_service']['id'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        @if(isset($analysis['test_service_interface_analysis']))
            <h2>📊 Interface Persistence Analysis</h2>
            <div class="analysis-grid">
                @foreach(['test_service_interface_analysis', 'base_service_interface_analysis', 'dependent_service_interface_analysis'] as $key)
                    @if(isset($analysis[$key]))
                        @php
                            $serviceAnalysis = $analysis[$key];
                            $scoreClass = $serviceAnalysis['persistence_score'] == 100 ? 'score-green' : 
                                         ($serviceAnalysis['persistence_score'] > 50 ? 'score-yellow' : 'score-red');
                        @endphp
                        <div class="analysis-card">
                            <h3>{{ ucfirst(str_replace('_', ' ', $serviceAnalysis['service_name'])) }} <span class="interface-badge">Interface</span></h3>
                            <div class="persistence-score {{ $scoreClass }}">
                                {{ $serviceAnalysis['persistence_score'] }}%
                            </div>
                            <p><strong>{{ $serviceAnalysis['conclusion'] }}</strong></p>
                            
                            <div class="property">
                                <span class="property-label">Total Requests:</span>
                                <span class="property-value">{{ $serviceAnalysis['total_requests'] }}</span>
                            </div>
                            <div class="property">
                                <span class="property-label">Unique Instance IDs:</span>
                                <span class="property-value">{{ $serviceAnalysis['unique_instance_ids'] }}</span>
                            </div>
                            <div class="property">
                                <span class="property-label">Unique Object Hashes:</span>
                                <span class="property-value">{{ $serviceAnalysis['unique_object_hashes'] }}</span>
                            </div>
                            <div class="property">
                                <span class="property-label">Persists:</span>
                                <span class="property-value">{{ $serviceAnalysis['persists'] ? 'Yes' : 'No' }}</span>
                            </div>
                            
                            <div class="instance-tracking">
                                <strong>Instance ID History:</strong>
                                <div class="instance-list">
                                    @foreach($serviceAnalysis['instance_ids'] as $index => $instanceId)
                                        Request {{ $index + 1 }}: {{ $instanceId }}<br>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
        
        <h2>📚 Interface Request History</h2>
        <p>Showing last {{ count($request_history) }} requests:</p>
        
        @foreach(array_reverse($request_history) as $index => $request)
            <div class="request-card">
                <h3>Request {{ count($request_history) - $index }}: {{ $request['request_id'] }}</h3>
                <p><strong>Timestamp:</strong> {{ $request['timestamp'] }}</p>
                
                <div class="service-data">
                    <div class="service-card">
                        <div class="service-title">TestServiceInterface<span class="interface-badge">Interface</span></div>
                        <div class="property">
                            <span class="property-label">Instance ID:</span>
                            <span class="property-value">{{ $request['test_service_interface']['instance_id'] }}</span>
                        </div>
                        <div class="property">
                            <span class="property-label">Object Hash:</span>
                            <span class="property-value">{{ $request['test_service_interface']['object_hash'] }}</span>
                        </div>
                    </div>
                    
                    <div class="service-card">
                        <div class="service-title">BaseServiceInterface<span class="interface-badge">Interface</span></div>
                        <div class="property">
                            <span class="property-label">Instance ID:</span>
                            <span class="property-value">{{ $request['base_service_interface']['instance_id'] }}</span>
                        </div>
                        <div class="property">
                            <span class="property-label">Object Hash:</span>
                            <span class="property-value">{{ $request['base_service_interface']['object_hash'] }}</span>
                        </div>
                        <div class="property">
                            <span class="property-label">Action Count:</span>
                            <span class="property-value">{{ $request['base_service_interface']['action_count'] }}</span>
                        </div>
                    </div>
                    
                    <div class="service-card">
                        <div class="service-title">InterfaceDependentService<span class="interface-badge">Interface</span></div>
                        <div class="property">
                            <span class="property-label">Instance ID:</span>
                            <span class="property-value">{{ $request['dependent_service_interface']['instance_id'] }}</span>
                        </div>
                        <div class="property">
                            <span class="property-label">Object Hash:</span>
                            <span class="property-value">{{ $request['dependent_service_interface']['object_hash'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <div style="margin-top: 40px; text-align: center;">
            <button class="refresh-btn" onclick="window.location.reload()">🔄 Make Another Request</button>
        </div>
    </div>
@endsection

@section('additional_scripts')
    <script>
        function clearCache() {
            fetch('{{ route('persistence.cache.clear') }}')
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error clearing cache');
                });
        }
    </script>
@endsection 