@extends('layouts.app')

@section('title', 'Pure Singleton Pattern Test')
@section('page_title', 'Pure Singleton Pattern Test')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">
            Pure Singleton Pattern Test
        </h1>
        
        <div class="mb-8 p-6 bg-purple-50 border border-purple-200 rounded-lg">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">Test Overview</h2>
            <p class="text-purple-700 mb-2">
                This test examines the traditional PHP singleton pattern (not Laravel's service container):
            </p>
            <ul class="list-disc list-inside text-purple-700 space-y-1">
                <li><strong>Pure Singleton:</strong> Classic PHP singleton with private constructor and static getInstance()</li>
                <li><strong>Cross-Request Persistence:</strong> Tests if singleton instances survive between HTTP requests</li>
                <li><strong>Multiple Access Points:</strong> Verifies singleton behavior from different service instances</li>
                <li><strong>State Persistence:</strong> Tracks action counts and instance data across requests</li>
            </ul>
        </div>

        <!-- Expected Behavior Notice -->
        <div class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h3 class="text-lg font-semibold mb-2 text-yellow-800">Expected Behavior</h3>
            <p class="text-yellow-700">{{ $analysis['expected_behavior'] }}</p>
        </div>

        <!-- Analysis Summary -->
        <div class="mb-8 p-6 rounded-lg shadow-lg {{ $analysis['overall_score'] >= 70 ? 'bg-green-50 border border-green-200' : ($analysis['overall_score'] >= 50 ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200') }}">
            <h2 class="text-2xl font-semibold mb-4 {{ $analysis['overall_score'] >= 70 ? 'text-green-800' : ($analysis['overall_score'] >= 50 ? 'text-yellow-800' : 'text-red-800') }}">
                Analysis Summary
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $analysis['singleton_integrity_score'] >= 70 ? 'text-green-600' : ($analysis['singleton_integrity_score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $analysis['singleton_integrity_score'] }}%
                    </div>
                    <div class="text-sm font-medium text-gray-600">Singleton Integrity</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $analysis['request_persistence_score'] >= 70 ? 'text-green-600' : ($analysis['request_persistence_score'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $analysis['request_persistence_score'] }}%
                    </div>
                    <div class="text-sm font-medium text-gray-600">Request Persistence</div>
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

        <!-- Pre-Test State -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Pre-Test State</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded {{ $results['pre_test_state']['had_instance'] ? 'bg-blue-100' : 'bg-gray-100' }}">
                    <div class="font-semibold {{ $results['pre_test_state']['had_instance'] ? 'text-blue-800' : 'text-gray-800' }}">
                        Singleton Existed
                    </div>
                    <div class="text-sm {{ $results['pre_test_state']['had_instance'] ? 'text-blue-600' : 'text-gray-600' }}">
                        {{ $results['pre_test_state']['had_instance'] ? 'Yes - Found existing instance' : 'No - Fresh start' }}
                    </div>
                </div>
                <div class="p-4 bg-gray-100 rounded">
                    <div class="font-semibold text-gray-800">Total Instances Created</div>
                    <div class="text-sm text-gray-600">{{ $results['pre_test_state']['total_instances'] }}</div>
                </div>
            </div>
        </div>

        <!-- Singleton Integrity Test -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Singleton Integrity Test</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="p-4 rounded {{ $results['basic_test']['references_comparison']['ref1_vs_ref2_same'] ? 'bg-green-100' : 'bg-red-100' }}">
                    <div class="font-semibold {{ $results['basic_test']['references_comparison']['ref1_vs_ref2_same'] ? 'text-green-800' : 'text-red-800' }}">
                        Reference 1 vs 2
                    </div>
                    <div class="text-sm {{ $results['basic_test']['references_comparison']['ref1_vs_ref2_same'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $results['basic_test']['references_comparison']['ref1_vs_ref2_same'] ? 'Same Instance ✓' : 'Different Instance ✗' }}
                    </div>
                </div>
                <div class="p-4 rounded {{ $results['basic_test']['references_comparison']['ref2_vs_ref3_same'] ? 'bg-green-100' : 'bg-red-100' }}">
                    <div class="font-semibold {{ $results['basic_test']['references_comparison']['ref2_vs_ref3_same'] ? 'text-green-800' : 'text-red-800' }}">
                        Reference 2 vs 3
                    </div>
                    <div class="text-sm {{ $results['basic_test']['references_comparison']['ref2_vs_ref3_same'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $results['basic_test']['references_comparison']['ref2_vs_ref3_same'] ? 'Same Instance ✓' : 'Different Instance ✗' }}
                    </div>
                </div>
                <div class="p-4 rounded {{ $results['basic_test']['references_comparison']['all_same_object_id'] ? 'bg-green-100' : 'bg-red-100' }}">
                    <div class="font-semibold {{ $results['basic_test']['references_comparison']['all_same_object_id'] ? 'text-green-800' : 'text-red-800' }}">
                        Object IDs
                    </div>
                    <div class="text-sm {{ $results['basic_test']['references_comparison']['all_same_object_id'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $results['basic_test']['references_comparison']['all_same_object_id'] ? 'All Same ✓' : 'Different ✗' }}
                    </div>
                </div>
            </div>
            <div class="text-sm text-gray-600">
                Final Action Count: {{ $results['basic_test']['final_action_count'] }} 
                (should be 3 if working correctly)
            </div>
        </div>

        <!-- Multiple Services Test -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Multiple Services Accessing Singleton</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Service Instances</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Service 1 ID:</span> {{ $results['multiple_services']['service1_info']['service_instance_id'] }}</div>
                        <div><span class="font-medium">Service 2 ID:</span> {{ $results['multiple_services']['service2_info']['service_instance_id'] }}</div>
                        <div><span class="font-medium">Services Different:</span> 
                            <span class="{{ $results['multiple_services']['services_are_different'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $results['multiple_services']['services_are_different'] ? 'Yes ✓' : 'No ✗' }}
                            </span>
                        </div>
                        <div><span class="font-medium">Service 1 Actions:</span> {{ $results['multiple_services']['service1_actions'] }}</div>
                        <div><span class="font-medium">Service 2 Actions:</span> {{ $results['multiple_services']['service2_actions'] }}</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Singleton Access</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Singletons Same:</span> 
                            <span class="{{ $results['multiple_services']['singletons_are_same'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $results['multiple_services']['singletons_are_same'] ? 'Yes ✓' : 'No ✗' }}
                            </span>
                        </div>
                        <div><span class="font-medium">Final Singleton Actions:</span> {{ $results['multiple_services']['final_singleton_actions'] }}</div>
                        <div><span class="font-medium">Singleton Object ID:</span> 
                            <code class="bg-gray-100 px-1 rounded">{{ $results['multiple_services']['singleton_from_service1']['object_id'] }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Request Details -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Current Request Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Singleton Information</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Request ID:</span> {{ $results['current_request']['request_id'] }}</div>
                        <div><span class="font-medium">Singleton Instance ID:</span> {{ $results['current_request']['singleton_instance_id'] }}</div>
                        <div><span class="font-medium">Object ID:</span> <code class="bg-gray-100 px-1 rounded">{{ $results['current_request']['singleton_object_id'] }}</code></div>
                        <div><span class="font-medium">Object Hash:</span> <code class="bg-gray-100 px-1 rounded">{{ substr($results['current_request']['singleton_object_hash'], 0, 16) }}...</code></div>
                        <div><span class="font-medium">Final Action Count:</span> {{ $results['current_request']['final_action_count'] }}</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Creation Details</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium">Created At:</span> {{ \Carbon\Carbon::parse($results['current_request']['final_singleton_info']['created_at'])->format('H:i:s') }}</div>
                        <div><span class="font-medium">Total Instances Created:</span> {{ $results['current_request']['total_instances_created'] }}</div>
                        <div><span class="font-medium">Memory Address:</span> 
                            <code class="bg-gray-100 px-1 rounded">{{ $results['current_request']['final_singleton_info']['memory_address'] }}</code>
                        </div>
                        <div><span class="font-medium">Timestamp:</span> {{ \Carbon\Carbon::parse($results['current_request']['timestamp'])->format('H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action History -->
        @if(!empty($results['basic_test']['action_history']))
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Action History</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instance ID</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results['basic_test']['action_history'] as $index => $action)
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $action['action_number'] ?? ($index + 1) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $action['action'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($action['timestamp'])->format('H:i:s.u') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $action['instance_id'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Cross-Request Analysis -->
        @if(count($results['all_requests']) > 1)
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Cross-Request Analysis</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Object ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instance ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results['all_requests'] as $index => $request)
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Request {{ $index + 1 }}
                                @if($request['request_id'] === $results['current_request']['request_id'])
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Current</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <code class="bg-gray-100 px-1 rounded">{{ $request['singleton_object_id'] }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request['singleton_instance_id'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request['final_action_count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request['total_instances_created'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($request['timestamp'])->format('H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                Total Requests: {{ count($results['all_requests']) }}, 
                Unique Object IDs: {{ count(array_unique(array_column($results['all_requests'], 'singleton_object_id'))) }},
                Unique Instance IDs: {{ count(array_unique(array_column($results['all_requests'], 'singleton_instance_id'))) }}
            </div>
        </div>
        @endif

        <!-- Technical Details -->
        <div class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded-lg">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Technical Implementation</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Singleton Pattern Features</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>✓ Private constructor</li>
                        <li>✓ Private clone method</li>
                        <li>✓ Protected against unserialization</li>
                        <li>✓ Static getInstance() method</li>
                        <li>✓ Instance state tracking</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Test Coverage</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>✓ Multiple getInstance() calls</li>
                        <li>✓ Service-to-singleton interaction</li>
                        <li>✓ Cross-request persistence</li>
                        <li>✓ Object identity verification</li>
                        <li>✓ State persistence tracking</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('pure.singleton.test') }}" 
               class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
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