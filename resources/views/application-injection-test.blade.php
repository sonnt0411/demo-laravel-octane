@extends('layouts.app')

@section('title', 'Application Injection Test')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">
            Application Injection Test
        </h1>
        
        <div class="mb-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">Test Overview</h2>
            <p class="text-blue-700 mb-2">
                This test verifies Laravel's Application object injection and persistence behavior:
            </p>
            <ul class="list-disc list-inside text-blue-700 space-y-1">
                <li><strong>Singleton Registration:</strong> Service registered with Application injection closure</li>
                <li><strong>Application Persistence:</strong> Tests if the same Application object persists across requests</li>
                <li><strong>Service Behavior:</strong> Verifies singleton behavior and shared state</li>
                <li><strong>Cross-Request Analysis:</strong> Tracks Application object IDs across multiple requests</li>
            </ul>
        </div>

        <!-- Analysis Summary -->
        <div class="mb-8 p-6 rounded-lg shadow-lg {{ $analysis['overall_score'] >= 70 ? 'bg-green-50 border border-green-200' : ($analysis['overall_score'] >= 50 ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200') }}">
            <h2 class="text-2xl font-semibold mb-4 {{ $analysis['overall_score'] >= 70 ? 'text-green-800' : ($analysis['overall_score'] >= 50 ? 'text-yellow-800' : 'text-red-800') }}">
                Analysis Summary
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $analysis['singleton_score'] >= 70 ? 'text-green-600' : ($analysis['singleton_score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $analysis['singleton_score'] }}%
                    </div>
                    <div class="text-sm font-medium text-gray-600">Singleton Score</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $analysis['app_persistence_score'] >= 70 ? 'text-green-600' : ($analysis['app_persistence_score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $analysis['app_persistence_score'] }}%
                    </div>
                    <div class="text-sm font-medium text-gray-600">App Persistence Score</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $analysis['overall_score'] >= 70 ? 'text-green-600' : ($analysis['overall_score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $analysis['overall_score'] }}%
                    </div>
                    <div class="text-sm font-medium text-gray-600">Overall Score</div>
                </div>
            </div>
            <p class="text-lg font-medium {{ $analysis['overall_score'] >= 70 ? 'text-green-700' : ($analysis['overall_score'] >= 50 ? 'text-yellow-700' : 'text-red-700') }}">
                {{ $analysis['summary'] }}
            </p>
        </div>

        <!-- Key Insights -->
        @if(!empty($analysis['insights']))
        <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-lg">
            <h3 class="text-lg font-semibold mb-3 text-green-800">✓ Key Insights</h3>
            <ul class="space-y-2">
                @foreach($analysis['insights'] as $insight)
                <li class="text-green-700">{{ $insight }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Issues Found -->
        @if(!empty($analysis['issues']))
        <div class="mb-8 p-6 bg-red-50 border border-red-200 rounded-lg">
            <h3 class="text-lg font-semibold mb-3 text-red-800">⚠ Issues Found</h3>
            <ul class="space-y-2">
                @foreach($analysis['issues'] as $issue)
                <li class="text-red-700">{{ $issue }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Singleton Verification -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Singleton Verification</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded {{ $results['singleton_verification']['same_service_instance'] ? 'bg-green-100' : 'bg-red-100' }}">
                    <div class="font-semibold {{ $results['singleton_verification']['same_service_instance'] ? 'text-green-800' : 'text-red-800' }}">
                        Service Instance
                    </div>
                    <div class="text-sm {{ $results['singleton_verification']['same_service_instance'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $results['singleton_verification']['same_service_instance'] ? 'Same Instance ✓' : 'Different Instance ✗' }}
                    </div>
                </div>
                <div class="p-4 rounded {{ $results['singleton_verification']['same_app_instance'] ? 'bg-green-100' : 'bg-red-100' }}">
                    <div class="font-semibold {{ $results['singleton_verification']['same_app_instance'] ? 'text-green-800' : 'text-red-800' }}">
                        Application Instance
                    </div>
                    <div class="text-sm {{ $results['singleton_verification']['same_app_instance'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $results['singleton_verification']['same_app_instance'] ? 'Same Instance ✓' : 'Different Instance ✗' }}
                    </div>
                </div>
                <div class="p-4 rounded {{ $results['singleton_verification']['shared_action_count'] ? 'bg-green-100' : 'bg-red-100' }}">
                    <div class="font-semibold {{ $results['singleton_verification']['shared_action_count'] ? 'text-green-800' : 'text-red-800' }}">
                        Shared State
                    </div>
                    <div class="text-sm {{ $results['singleton_verification']['shared_action_count'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $results['singleton_verification']['shared_action_count'] ? 'State Shared ✓' : 'State Not Shared ✗' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Features -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Application Features</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($results['app_features'] as $feature => $value)
                <div class="p-3 bg-gray-50 rounded">
                    <div class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $feature)) }}</div>
                    <div class="text-sm text-gray-600">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Current Request Details -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Current Request Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Service Information</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Request ID:</span> {{ $results['current_request']['request_id'] }}</div>
                        <div><span class="font-medium">Service Instance ID:</span> {{ $results['current_request']['service_instance_id'] }}</div>
                        <div><span class="font-medium">Service Object ID:</span> <code class="bg-gray-100 px-1 rounded">{{ $results['current_request']['service_object_id'] }}</code></div>
                        <div><span class="font-medium">Total Instance Count:</span> {{ $results['current_request']['total_instance_count'] }}</div>
                        <div><span class="font-medium">Final Action Count:</span> {{ $results['current_request']['final_action_count'] }}</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Application Information</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">App Object ID:</span> <code class="bg-gray-100 px-1 rounded">{{ $results['current_request']['app_object_id'] }}</code></div>
                        <div><span class="font-medium">App Class:</span> {{ $results['initial_info']['app_class'] }}</div>
                        <div><span class="font-medium">Timestamp:</span> {{ $results['current_request']['timestamp'] }}</div>
                        <div><span class="font-medium">Is Singleton:</span> {{ $results['initial_info']['is_singleton_in_container'] ? 'Yes' : 'No' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step-by-Step Execution -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Step-by-Step Execution</h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-4 p-3 bg-blue-50 rounded">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <div class="flex-1">
                        <div class="font-medium">Initial Service Resolution</div>
                        <div class="text-sm text-gray-600">Service ID: {{ $results['initial_info']['service_instance_id'] }}, Actions: {{ $results['initial_info']['action_count'] }}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-3 bg-blue-50 rounded">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <div class="flex-1">
                        <div class="font-medium">Performed 3 Actions</div>
                        <div class="text-sm text-gray-600">Actions: {{ $results['after_actions_info']['action_count'] }}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-3 bg-green-50 rounded">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <div class="flex-1">
                        <div class="font-medium">Second Service Resolution</div>
                        <div class="text-sm text-gray-600">
                            Service ID: {{ $results['second_resolve_info']['service_instance_id'] }}, 
                            Same Instance: {{ $results['singleton_verification']['same_service_instance'] ? 'Yes' : 'No' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-3 bg-green-50 rounded">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                    <div class="flex-1">
                        <div class="font-medium">Final State Check</div>
                        <div class="text-sm text-gray-600">Final Actions: {{ $results['final_info']['action_count'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cross-Request Analysis -->
        @if(count($results['all_requests']) > 1)
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Cross-Request Analysis</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App Object ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Instance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results['all_requests'] as $index => $request)
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Request {{ $index + 1 }}
                                @if($request['request_id'] === $results['current_request']['request_id'])
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Current</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <code class="bg-gray-100 px-1 rounded">{{ substr($request['app_object_id'], 0, 8) }}...</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request['service_instance_id'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($request['timestamp'])->format('H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                Total Requests: {{ count($results['all_requests']) }}, 
                Unique App Object IDs: {{ count(array_unique(array_column($results['all_requests'], 'app_object_id'))) }}
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('application.injection.test') }}" 
               class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Run Test Again
            </a>
            <a href="{{ route('tests.index') }}" 
               class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back to Tests
            </a>
            <a href="{{ route('persistence.cache.clear') }}" 
               class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Clear Cache
            </a>
        </div>
    </div>
</div>
@endsection 