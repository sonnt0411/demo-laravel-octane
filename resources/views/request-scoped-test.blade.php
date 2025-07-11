@extends('layouts.app')

@section('title', $test_title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold mb-2">🛡️ {{ $test_title }}</h1>
            <p class="text-lg opacity-90">{{ $description }}</p>
            <div class="mt-4 bg-white/20 rounded-lg p-3">
                <p class="text-sm font-medium">Test executed at: {{ $timestamp }}</p>
                <p class="text-sm">This demonstrates Laravel's <code class="bg-black/30 px-2 py-1 rounded">scoped()</code> binding for Octane-safe singletons</p>
            </div>
        </div>
    </div>

    <!-- Key Concept Explanation -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-6 mb-8 rounded-r-lg">
        <h2 class="text-xl font-semibold text-blue-800 mb-3">🧠 Key Concept: Request-Scoped Singletons</h2>
        <div class="text-blue-700 space-y-2">
            <p><strong>Problem:</strong> Traditional <code>singleton()</code> bindings persist across requests in Octane, causing data leakage.</p>
            <p><strong>Solution:</strong> Use <code>scoped()</code> bindings to create singletons that reset between requests.</p>
            <p><strong>Behavior:</strong> Same instance within a request, fresh instance for each new request.</p>
        </div>
    </div>

    <!-- Test Results -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- First Call Results -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                📋 First Service Call
            </h2>
            <div class="space-y-3">
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Created At:</strong> {{ $results['first_call']['created_at'] }}
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Request ID:</strong> <code>{{ $results['first_call']['request_id'] }}</code>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Action Count:</strong> <span class="badge badge-info">{{ $results['first_call']['action_count'] }}</span>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Instance ID:</strong> {{ $results['first_call']['instance_id'] }}
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Action History:</strong>
                    <ul class="list-disc list-inside mt-2 text-sm">
                        @foreach($results['first_call']['action_history'] as $action)
                            <li>{{ $action }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Second Call Results -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                🔄 Second Service Call (Same Request)
            </h2>
            <div class="space-y-3">
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Created At:</strong> {{ $results['second_call']['created_at'] }}
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Request ID:</strong> <code>{{ $results['second_call']['request_id'] }}</code>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Action Count:</strong> <span class="badge badge-info">{{ $results['second_call']['action_count'] }}</span>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Instance ID:</strong> {{ $results['second_call']['instance_id'] }}
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <strong>Action History:</strong>
                    <ul class="list-disc list-inside mt-2 text-sm">
                        @foreach($results['second_call']['action_history'] as $action)
                            <li>{{ $action }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Method Injection Test -->
    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            💉 Method Injection Verification
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold mb-2">Original Service:</h3>
                <div class="bg-gray-50 p-3 rounded">
                    <p><strong>Action Count:</strong> {{ $results['method_injection']['original_service']['action_count'] }}</p>
                    <p><strong>Instance ID:</strong> {{ $results['method_injection']['original_service']['instance_id'] }}</p>
                </div>
            </div>
            <div>
                <h3 class="font-semibold mb-2">Method Injected Service:</h3>
                <div class="bg-gray-50 p-3 rounded">
                    <p><strong>Action Count:</strong> {{ $results['method_injection']['method_injected']['action_count'] }}</p>
                    <p><strong>Instance ID:</strong> {{ $results['method_injection']['method_injected']['instance_id'] }}</p>
                </div>
            </div>
        </div>
        <div class="mt-4 p-3 bg-blue-50 rounded">
            <p><strong>Same Instance:</strong> 
                @if($results['method_injection']['same_instance'])
                    <span class="text-green-600 font-semibold">✅ Yes - Proper scoped behavior</span>
                @else
                    <span class="text-red-600 font-semibold">❌ No - Multiple instances detected</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Analysis Section -->
    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            📊 Request-Scoped Analysis
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold text-sm text-gray-600 mb-2">Instance Sharing</h3>
                <p class="text-lg">{!! $results['analysis']['instance_sharing'] !!}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold text-sm text-gray-600 mb-2">State Persistence</h3>
                <p class="text-lg">{!! $results['analysis']['state_persistence'] !!}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold text-sm text-gray-600 mb-2">Request Scoped</h3>
                <p class="text-lg">{!! $results['analysis']['request_scoped_behavior'] !!}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold text-sm text-gray-600 mb-2">Octane Safety</h3>
                <p class="text-lg">{!! $results['analysis']['octane_safety'] !!}</p>
            </div>
        </div>

        @if(count($results['analysis']['recommendations']) > 0)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="font-semibold text-green-800 mb-2">💡 Recommendations:</h3>
            <ul class="list-disc list-inside text-green-700 space-y-1">
                @foreach($results['analysis']['recommendations'] as $recommendation)
                    <li>{{ $recommendation }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Code Examples -->
    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            💻 Implementation Examples
        </h2>
        
        <div class="space-y-6">
            <!-- Service Provider Registration -->
            <div>
                <h3 class="font-semibold text-green-600 mb-2">✅ Correct (Octane-Safe):</h3>
                <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>// In AppServiceProvider::register()
$this->app->scoped(RequestScopedService::class);</code></pre>
                <p class="text-sm text-gray-600 mt-2">Creates a new instance per request, safe for Octane</p>
            </div>

            <!-- Wrong Way -->
            <div>
                <h3 class="font-semibold text-red-600 mb-2">❌ Incorrect (Octane-Unsafe):</h3>
                <pre class="bg-gray-900 text-red-400 p-4 rounded-lg overflow-x-auto"><code>// In AppServiceProvider::register()
$this->app->singleton(RequestScopedService::class);</code></pre>
                <p class="text-sm text-gray-600 mt-2">Persists across requests in Octane, can cause data leakage</p>
            </div>

            <!-- Alternative Patterns -->
            <div>
                <h3 class="font-semibold text-blue-600 mb-2">🔧 Alternative Patterns:</h3>
                <pre class="bg-gray-900 text-blue-400 p-4 rounded-lg overflow-x-auto"><code>// 1. Manual cleanup in Octane lifecycle hooks
// 2. Store state in request object
// 3. Use Laravel's context binding
// 4. Implement custom request-aware singletons</code></pre>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="text-center">
        <a href="{{ route('tests.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            ← Back to Test Index
        </a>
        <button onclick="location.reload()" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors ml-4">
            🔄 Run Test Again
        </button>
    </div>
</div>

<style>
.badge {
    @apply inline-block px-3 py-1 text-sm font-semibold rounded-full;
}
.badge-info {
    @apply bg-blue-100 text-blue-800;
}
</style>
@endsection 