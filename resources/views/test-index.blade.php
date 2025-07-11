@extends('layouts.app')

@section('title', 'Laravel Dependency Injection Test Suite')

@section('additional_styles')
    <style>
        .intro {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 40px;
            border-left: 5px solid #3498db;
        }
        
        .test-category {
            margin-bottom: 40px;
        }
        
        .category-title {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.5em;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }
        
        .concrete-tests .category-title {
            color: #2980b9;
            border-bottom-color: #3498db;
        }
        
        .interface-tests .category-title {
            color: #8b5cf6;
            border-bottom-color: #a855f7;
        }
        
        .persistence-tests .category-title {
            color: #059669;
            border-bottom-color: #10b981;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .test-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .test-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        .concrete-tests .test-card {
            border-left: 4px solid #3498db;
        }
        
        .interface-tests .test-card {
            border-left: 4px solid #a855f7;
        }
        
        .persistence-tests .test-card {
            border-left: 4px solid #10b981;
        }
        
        .test-title {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .test-description {
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .test-link {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .concrete-tests .test-link {
            background: #3498db;
            color: white;
        }
        
        .concrete-tests .test-link:hover {
            background: #2980b9;
        }
        
        .interface-tests .test-link {
            background: #a855f7;
            color: white;
        }
        
        .interface-tests .test-link:hover {
            background: #7c3aed;
        }
        
        .persistence-tests .test-link {
            background: #10b981;
            color: white;
        }
        
        .persistence-tests .test-link:hover {
            background: #059669;
        }
        
        .clear-cache-link {
            background: #f59e0b !important;
        }
        
        .clear-cache-link:hover {
            background: #d97706 !important;
        }
        
        .comparison-section {
            background: linear-gradient(135deg, #f3e8ff 0%, #e8f4f8 100%);
            padding: 30px;
            border-radius: 10px;
            margin-top: 40px;
            text-align: center;
        }
        
        .comparison-title {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .key-findings {
            background: #e8f6f3;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
            border-left: 5px solid #16a085;
        }
        
        .key-findings h3 {
            color: #16a085;
            margin-top: 0;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .concrete-badge {
            background: #3498db;
            color: white;
        }
        
        .interface-badge {
            background: #a855f7;
            color: white;
        }
        
        .persistence-badge {
            background: #10b981;
            color: white;
        }
        
        .application-tests .category-title {
            color: #dc2626;
            border-bottom-color: #ef4444;
        }
        
        .application-tests .test-link {
            background: #ef4444;
            color: white;
        }
        
        .application-tests .test-link:hover {
            background: #dc2626;
        }
        
        .application-badge, .advanced-badge {
            background: #ef4444;
            color: white;
        }
        
        /* Octane-Safe Test Cards */
        .test-card.octane-safe {
            border-left: 4px solid #16a34a;
            background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
            position: relative;
        }
        
        .test-card.octane-safe:hover {
            box-shadow: 0 4px 20px rgba(22, 163, 74, 0.15);
        }
        
        .octane-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.3);
        }
        
        .test-card.octane-safe .test-link {
            background: linear-gradient(135deg, #16a34a, #15803d);
            border: none;
        }
        
        .test-card.octane-safe .test-link:hover {
            background: linear-gradient(135deg, #15803d, #166534);
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h1>🧪 Laravel Dependency Injection Test Suite</h1>
        
        <div class="intro">
            <h3>📊 Comprehensive Dependency Injection Testing Framework</h3>
            <p>This test suite explores Laravel's service container behavior when resolving dependencies. It includes both concrete class injection and interface-based injection tests to help you understand how Laravel creates, shares, and manages object instances.</p>
            
            <p><strong>🎯 Purpose:</strong> Determine whether Laravel creates separate instances for each resolution or shares the same instances across multiple injection points.</p>
        </div>
        
        <div class="test-category concrete-tests">
            <h2 class="category-title">🏗️ Concrete Class Dependency Injection Tests <span class="badge concrete-badge">CONCRETE</span></h2>
            
            <div class="test-grid">
                <div class="test-card">
                    <div class="test-title">Standard Dependency Injection Test</div>
                    <div class="test-description">
                        Tests Laravel's default behavior when resolving concrete classes multiple times. Examines constructor injection, manual resolution, method injection, and dependent services.
                    </div>
                    <a href="{{ route('dependency.test') }}" class="test-link">Run Standard DI Test</a>
                </div>
                
                <div class="test-card">
                    <div class="test-title">Singleton Dependency Test</div>
                    <div class="test-description">
                        Tests singleton binding behavior. When services are bound as singletons, Laravel should return the same instance for every resolution request.
                    </div>
                    <a href="{{ route('dependency.singleton') }}" class="test-link">Run Singleton Test</a>
                </div>
                
                <div class="test-card">
                    <div class="test-title">Shared Dependency Test</div>
                    <div class="test-description">
                        Tests whether multiple services that depend on the same concrete class receive the same instance or separate instances. Uses action count tracking to verify shared state.
                    </div>
                    <a href="{{ route('dependency.shared') }}" class="test-link">Run Shared Dependency Test</a>
                </div>
            </div>
        </div>
        
        <div class="test-category interface-tests">
            <h2 class="category-title">🔌 Interface-Based Dependency Injection Tests <span class="badge interface-badge">INTERFACE</span></h2>
            
            <div class="test-grid">
                <div class="test-card">
                    <div class="test-title">Interface Dependency Injection Test</div>
                    <div class="test-description">
                        Tests Laravel's behavior when resolving interfaces bound to concrete implementations. Compares interface resolution patterns with concrete class resolution.
                    </div>
                    <a href="{{ route('interface.dependency.test') }}" class="test-link">Run Interface DI Test</a>
                </div>
                
                <div class="test-card">
                    <div class="test-title">Interface Singleton Test</div>
                    <div class="test-description">
                        Tests singleton binding behavior with interfaces. When interfaces are bound as singletons, Laravel should return the same concrete implementation instance.
                    </div>
                    <a href="{{ route('interface.singleton') }}" class="test-link">Run Interface Singleton Test</a>
                </div>
                
                <div class="test-card">
                    <div class="test-title">Shared Interface Dependency Test</div>
                    <div class="test-description">
                        Tests whether multiple services that depend on the same interface receive the same concrete implementation instance. Critical for understanding interface-based dependency sharing.
                    </div>
                    <a href="{{ route('interface.dependency.shared') }}" class="test-link">Run Shared Interface Test</a>
                </div>
            </div>
        </div>
        
        <div class="test-category persistence-tests">
            <h2 class="category-title">🔄 Request Persistence Tests <span class="badge persistence-badge">PERSISTENCE</span></h2>
            
            <div class="test-grid">
                <div class="test-card">
                    <div class="test-title">Concrete Class Request Persistence Test</div>
                    <div class="test-description">
                        Tests whether concrete service instances persist between different HTTP requests. Tracks instance IDs and object hashes across multiple requests to determine if Laravel reuses instances.
                    </div>
                    <a href="{{ route('request.persistence.test') }}" class="test-link">Run Concrete Persistence Test</a>
                </div>
                
                <div class="test-card">
                    <div class="test-title">Interface Request Persistence Test</div>
                    <div class="test-description">
                        Tests whether interface-based service instances persist between different HTTP requests. Since interfaces are typically singletons, they should maintain the same instance across requests.
                    </div>
                    <a href="{{ route('interface.request.persistence.test') }}" class="test-link">Run Interface Persistence Test</a>
                </div>
                
                <div class="test-card">
                    <div class="test-title">Clear Persistence Cache</div>
                    <div class="test-description">
                        Clears the cached persistence test data to start fresh testing. Useful for resetting the test history when analyzing persistence behavior.
                    </div>
                    <a href="{{ route('persistence.cache.clear') }}" class="test-link clear-cache-link" onclick="clearCache(event)">Clear Cache</a>
                </div>
            </div>
        </div>
        
        <div class="test-category application-tests">
            <h2 class="category-title">⚙️ Advanced Pattern Tests <span class="badge application-badge">ADVANCED</span></h2>
            
            <div class="test-grid">
                <div class="test-card">
                    <div class="test-title">Application Injection Test</div>
                    <div class="test-description">
                        Tests Laravel's Application object injection behavior. Verifies if the Application object persists across requests and how it behaves when injected into singleton services via closure registration.
                    </div>
                    <a href="{{ route('application.injection.test') }}" class="test-link">Run Application Injection Test</a>
                </div>
                
                <div class="test-card">
                    <div class="test-title">Pure Singleton Pattern Test</div>
                    <div class="test-description">
                        Tests traditional PHP singleton pattern (not Laravel's service container). Verifies if pure singleton instances persist between HTTP requests and examines singleton integrity across multiple access points.
                    </div>
                    <a href="{{ route('pure.singleton.test') }}" class="test-link">Run Pure Singleton Test</a>
                </div>
                
                <div class="test-card octane-safe">
                    <div class="test-title">🛡️ Request-Scoped Service Test (Octane-Safe)</div>
                    <div class="test-description">
                        Tests Laravel's <code>scoped()</code> binding for request-level singletons. Demonstrates how to create singletons that persist within a single request but reset between requests - safe for Laravel Octane environments.
                    </div>
                    <a href="{{ route('request.scoped.test') }}" class="test-link">Run Request-Scoped Test</a>
                    <div class="octane-badge">Octane Compatible</div>
                </div>
            </div>
        </div>
        
        <div class="comparison-section">
            <h2 class="comparison-title">🔬 Interface vs Concrete Class Comparison</h2>
            <p>Run both concrete class tests and interface-based tests to compare how Laravel's service container handles different types of dependency injection. This comparison helps understand:</p>
            <ul style="text-align: left; max-width: 600px; margin: 0 auto;">
                <li>Whether interface resolution behaves differently from concrete class resolution</li>
                <li>How singleton bindings work with interfaces vs concrete classes</li>
                <li>Whether shared dependencies behave consistently across both patterns</li>
                <li>Performance and memory implications of each approach</li>
            </ul>
        </div>
        
        <div class="key-findings">
            <h3>🎯 Expected Key Findings</h3>
            <p><strong>Hypothesis:</strong> Laravel should create separate instances by default for both concrete classes and interfaces, unless explicitly bound as singletons.</p>
            
            <p><strong>🔍 What to Look For:</strong></p>
            <ul>
                <li><strong>Instance Reuse:</strong> Check if the same instance is reused across multiple resolutions</li>
                <li><strong>Shared State:</strong> Verify through action count tracking whether services share the same dependency instances</li>
                <li><strong>Object Hashes:</strong> Compare object hashes and instance IDs to confirm object identity</li>
                <li><strong>Interface vs Concrete Behavior:</strong> Compare results between interface-based and concrete class tests</li>
            </ul>
            
            <p><strong>📊 Test Validation Methods:</strong></p>
            <ul>
                <li>Strict object comparison (===)</li>
                <li>Object hash comparison (spl_object_hash)</li>
                <li>Instance ID comparison (unique identifiers)</li>
                <li>Shared state analysis (action count tracking)</li>
            </ul>
        </div>
    </div>
@endsection

@section('additional_scripts')
    <script>
        function clearCache(event) {
            event.preventDefault();
            fetch('{{ route('persistence.cache.clear') }}')
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error clearing cache');
                });
        }
    </script>
@endsection 