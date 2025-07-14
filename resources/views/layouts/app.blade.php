<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Dependency Injection Test Suite')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .app-instance-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 0;
            font-size: 12px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .app-instance-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .app-instance-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .app-metric {
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .app-metric-label {
            opacity: 0.8;
            font-size: 11px;
        }
        
        .app-metric-value {
            font-weight: bold;
            color: #ffffff;
        }
        
        .octane-active {
            background: rgba(34, 197, 94, 0.2) !important;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .octane-inactive {
            background: rgba(239, 68, 68, 0.2) !important;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .app-instance-id {
            font-family: 'Courier New', monospace;
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .app-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .status-indicator {
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .refresh-indicator {
            color: #ffc107;
            font-weight: bold;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        
        .test-navigation {
            background: #2c3e50;
            border-bottom: 3px solid #34495e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .nav-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            padding: 15px 0 0 0;
        }
        
        .nav-category {
            margin-bottom: 2px;
        }
        
        .nav-category-title {
            color: #bdc3c7;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            padding: 0 10px;
        }
        
        .nav-group {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            margin-bottom: 12px;
        }
        
        .nav-item {
            display: inline-block;
            text-decoration: none;
            color: #ecf0f1;
            padding: 8px 16px;
            background: #34495e;
            border-radius: 6px 6px 0 0;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            border-bottom: 3px solid transparent;
        }
        
        .nav-item:hover {
            background: #3498db;
            color: white;
            transform: translateY(-2px);
            border-bottom-color: #2980b9;
        }
        
        .nav-item.active {
            background: #3498db;
            color: white;
            border-bottom-color: #f39c12;
            font-weight: 600;
        }
        
        .nav-item.home {
            background: #27ae60;
            color: white;
            font-weight: 600;
        }
        
        .nav-item.home:hover,
        .nav-item.home.active {
            background: #229954;
            border-bottom-color: #f1c40f;
        }
        
        .nav-item.utility {
            background: #8e44ad;
        }
        
        .nav-item.utility:hover,
        .nav-item.utility.active {
            background: #9b59b6;
            border-bottom-color: #e74c3c;
        }
        
        .nav-breadcrumb {
            background: #34495e;
            padding: 8px 0;
            font-size: 12px;
            color: #95a5a6;
        }
        
        .breadcrumb-path {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .breadcrumb-sep {
            color: #7f8c8d;
        }
        
        .breadcrumb-current {
            color: #3498db;
            font-weight: 600;
        }
        
        .main-content {
            padding: 20px 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
        }
        
        @media (max-width: 768px) {
            .nav-tabs {
                flex-direction: column;
                gap: 8px;
            }
            
            .nav-group {
                flex-direction: column;
                gap: 4px;
            }
            
            .nav-item {
                border-radius: 6px;
                text-align: center;
            }
        }
        
        /* Base styles for all test views */
        h1 {
            color: #2c3e50;
            border-bottom: 4px solid #3498db;
            padding-bottom: 15px;
            margin-bottom: 40px;
            text-align: center;
        }
        
        h2 {
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .test-overview {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 40px;
            border-left: 5px solid #3498db;
        }
        
        .nav-links {
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .nav-links a {
            color: #495057;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        
        .nav-links a:hover {
            color: #007bff;
        }
        
        /* Toggle for application instance info */
        .toggle-app-info {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .toggle-app-info:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .app-details {
            display: none;
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 11px;
        }
        
        .app-details.show {
            display: block;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .detail-item {
            background: rgba(255,255,255,0.1);
            padding: 8px;
            border-radius: 4px;
        }
        
        .detail-label {
            opacity: 0.8;
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .detail-value {
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }
        
        @media (max-width: 768px) {
            .app-instance-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .app-instance-info {
                justify-content: center;
            }
            
            .app-metric {
                font-size: 11px;
            }
        }
    </style>
    @yield('additional_styles')
</head>
<body>
    @php
        $appInstance = app(\App\Services\DependencyTest\ApplicationInstanceService::class);
        $appInstance->logRequestDetails(request()->path(), request()->method());
        $instanceInfo = $appInstance->getInstanceInfo();
        $requestMetrics = $appInstance->getRequestMetrics();
    @endphp
    
    <div class="app-instance-bar">
        <div class="app-instance-container">
            <div class="app-instance-info">
                <div class="app-status">
                    <div class="status-indicator"></div>
                    <span class="app-instance-id">{{ $instanceInfo['laravel_app_object_id'] }}</span>
                </div>
                
                <div class="app-metric">
                    <span class="app-metric-label">Uptime:</span>
                    <span class="app-metric-value">{{ $instanceInfo['uptime_formatted'] }}</span>
                </div>
                
                <div class="app-metric">
                    <span class="app-metric-label">Requests:</span>
                    <span class="app-metric-value">{{ $instanceInfo['request_count'] }}</span>
                </div>
                
                <div class="app-metric">
                    <span class="app-metric-label">Memory:</span>
                    <span class="app-metric-value">{{ $instanceInfo['memory_usage']['current'] }}</span>
                </div>
                
                <div class="app-metric">
                    <span class="app-metric-label">PID:</span>
                    <span class="app-metric-value">{{ $instanceInfo['process_id'] }}</span>
                </div>
                
                <div class="app-metric {{ $instanceInfo['octane_info']['is_octane'] ? 'octane-active' : 'octane-inactive' }}">
                    <span class="app-metric-label">Server:</span>
                    <span class="app-metric-value">{{ 
                        $instanceInfo['octane_info']['server_type'] === 'frankenphp' ? 'FrankenPHP' : 
                        ucfirst($instanceInfo['octane_info']['server_type']) 
                    }}</span>
                </div>
            </div>
            
            <div class="app-status">
                <button class="toggle-app-info" onclick="toggleAppDetails()">
                    📊 Details
                </button>
            </div>
        </div>
        
        <div class="app-details" id="appDetails">
            <!-- Instance Information -->
            <div class="detail-section">
                <h4 style="color: white; margin-bottom: 10px;">🔍 Instance Analysis</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Laravel App ID</div>
                        <div class="detail-value">{{ $instanceInfo['laravel_app_object_id'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Laravel App Hash</div>
                        <div class="detail-value">{{ substr($instanceInfo['laravel_app_object_hash'], 0, 16) }}...</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Instance Age</div>
                        <div class="detail-value">{{ $instanceInfo['instance_analysis']['instance_age_seconds'] }}s</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Memory Growth</div>
                        <div class="detail-value">{{ $instanceInfo['instance_analysis']['memory_growth_formatted'] }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Octane Information -->
            <div class="detail-section" style="margin-top: 15px;">
                <h4 style="color: white; margin-bottom: 10px;">
                    🚀 Server Information 
                    <span style="color: {{ $instanceInfo['octane_info']['is_octane'] ? '#22c55e' : '#ef4444' }};">
                        ({{ $instanceInfo['octane_info']['is_octane'] ? 'Octane' : 'Traditional' }})
                    </span>
                </h4>
                <div class="detail-grid">
                    <div class="detail-item">
                                        <div class="detail-label">Server Type</div>
                <div class="detail-value">{{ 
                    $instanceInfo['octane_info']['server_type'] === 'frankenphp' ? 'FrankenPHP' : 
                    ucfirst($instanceInfo['octane_info']['server_type']) 
                }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Is Octane</div>
                        <div class="detail-value">{{ $instanceInfo['octane_info']['is_octane'] ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Detection</div>
                        <div class="detail-value">{{ $instanceInfo['octane_info']['detection_method'] ?: 'None' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">PHP SAPI</div>
                        <div class="detail-value">{{ $instanceInfo['server_info']['php_sapi'] }}</div>
                    </div>
                </div>
            </div>
            
            <!-- System Information -->
            <div class="detail-section" style="margin-top: 15px;">
                <h4 style="color: white; margin-bottom: 10px;">⚙️ System Information</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Start Time</div>
                        <div class="detail-value">{{ $instanceInfo['start_time'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Peak Memory</div>
                        <div class="detail-value">{{ $instanceInfo['memory_usage']['peak'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Req/sec</div>
                        <div class="detail-value">{{ $requestMetrics['requests_per_second'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Environment</div>
                        <div class="detail-value">{{ $instanceInfo['app_environment'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Debug Mode</div>
                        <div class="detail-value">{{ $instanceInfo['app_debug'] ? 'On' : 'Off' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">PHP Version</div>
                        <div class="detail-value">{{ $instanceInfo['php_version'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Laravel Version</div>
                        <div class="detail-value">{{ $instanceInfo['laravel_version'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Server Software</div>
                        <div class="detail-value">{{ $instanceInfo['server_info']['server_software'] }}</div>
                    </div>
                </div>
            </div>
            
            @if($instanceInfo['instance_analysis']['is_new_instance'])
                <div class="detail-section" style="margin-top: 15px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); padding: 10px; border-radius: 6px;">
                    <h4 style="color: #ef4444; margin-bottom: 10px;">⚠️ New Instance Detected</h4>
                    <div style="font-size: 11px; color: rgba(255,255,255,0.9);">
                        This is a fresh Laravel application instance. Possible reasons:<br>
                        • Not running under Octane<br>
                        • Using artisan serve (creates new instances per request)<br>
                        • Octane worker restart or first request to worker<br>
                        • Traditional web server (Apache/Nginx + PHP-FPM)
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Test Navigation -->
    <nav class="test-navigation">
        <div class="nav-container">
            <div class="nav-tabs">
                <div class="nav-category" style="width: 100%;">
                    
                    <!-- Home Section -->
                    <div class="nav-group">
                        <a href="{{ route('tests.index') }}" class="nav-item home {{ request()->routeIs('tests.index') ? 'active' : '' }}">
                            🏠 Test Suite Home
                        </a>
                    </div>
                    
                    <!-- Basic Dependency Tests -->
                    <div class="nav-category-title">Basic Dependency Injection</div>
                    <div class="nav-group">
                        <a href="{{ route('dependency.test') }}" class="nav-item {{ request()->routeIs('dependency.test') ? 'active' : '' }}">
                            🔧 Basic DI Test
                        </a>
                        <a href="{{ route('dependency.singleton') }}" class="nav-item {{ request()->routeIs('dependency.singleton') ? 'active' : '' }}">
                            🔒 Singleton Test
                        </a>
                        <a href="{{ route('dependency.shared') }}" class="nav-item {{ request()->routeIs('dependency.shared') ? 'active' : '' }}">
                            🤝 Shared Dependency Test
                        </a>
                    </div>
                    
                    <!-- Interface Tests -->
                    <div class="nav-category-title">Interface-Based Tests</div>
                    <div class="nav-group">
                        <a href="{{ route('interface.dependency.test') }}" class="nav-item {{ request()->routeIs('interface.dependency.test') ? 'active' : '' }}">
                            🔌 Interface DI Test
                        </a>
                        <a href="{{ route('interface.singleton') }}" class="nav-item {{ request()->routeIs('interface.singleton') ? 'active' : '' }}">
                            🔐 Interface Singleton
                        </a>
                        <a href="{{ route('interface.dependency.shared') }}" class="nav-item {{ request()->routeIs('interface.dependency.shared') ? 'active' : '' }}">
                            🤝 Interface Shared Test
                        </a>
                    </div>
                    
                    <!-- Persistence Tests -->
                    <div class="nav-category-title">Request Persistence</div>
                    <div class="nav-group">
                        <a href="{{ route('request.persistence.test') }}" class="nav-item {{ request()->routeIs('request.persistence.test') ? 'active' : '' }}">
                            📊 Concrete Persistence
                        </a>
                        <a href="{{ route('interface.request.persistence.test') }}" class="nav-item {{ request()->routeIs('interface.request.persistence.test') ? 'active' : '' }}">
                            📈 Interface Persistence
                        </a>
                    </div>
                    
                    <!-- Advanced Tests -->
                    <div class="nav-category-title">Advanced Patterns</div>
                    <div class="nav-group">
                        <a href="{{ route('application.injection.test') }}" class="nav-item {{ request()->routeIs('application.injection.test') ? 'active' : '' }}">
                            🚀 Application Injection
                        </a>
                        <a href="{{ route('pure.singleton.test') }}" class="nav-item {{ request()->routeIs('pure.singleton.test') ? 'active' : '' }}">
                            ⭐ Pure Singleton
                        </a>
                        <a href="{{ route('request.scoped.test') }}" class="nav-item {{ request()->routeIs('request.scoped.test') ? 'active' : '' }}">
                            🎯 Request Scoped (Octane Safe)
                        </a>
                    </div>
                    
                    <!-- Utility Actions -->
                    <div class="nav-category-title">Utilities</div>
                    <div class="nav-group">
                        <a href="{{ route('healthcheck') }}" class="nav-item utility {{ request()->routeIs('healthcheck') ? 'active' : '' }}">
                            ❤️ Health Check
                        </a>
                        <a href="{{ route('persistence.cache.clear') }}" class="nav-item utility" onclick="clearCache(event)">
                            🗑️ Clear Cache
                        </a>
                    </div>
                    
                </div>
            </div>
            
            <!-- Breadcrumb -->
            @if(!request()->routeIs('tests.index'))
            <div class="nav-breadcrumb">
                <div class="breadcrumb-path">
                    <a href="{{ route('tests.index') }}" style="color: #95a5a6; text-decoration: none;">Test Suite</a>
                    <span class="breadcrumb-sep">→</span>
                    <span class="breadcrumb-current">@yield('page_title', 'Current Test')</span>
                </div>
            </div>
            @endif
        </div>
    </nav>
    
    <div class="main-content">
        @yield('content')
    </div>
    
    <script>
        function toggleAppDetails() {
            const details = document.getElementById('appDetails');
            details.classList.toggle('show');
        }
        
        function clearCache(event) {
            event.preventDefault();
            if (confirm('Clear all test cache data? This will reset persistence test history.')) {
                fetch('{{ route("persistence.cache.clear") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    // Optionally reload the current page to show updated data
                    if (window.location.pathname.includes('persistence')) {
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Error clearing cache: ' + error.message);
                });
            }
        }
        
        // Auto-refresh page every 30 seconds to show live application instance metrics
        // Can be disabled by setting a flag in localStorage
        if (!localStorage.getItem('disableAutoRefresh')) {
            // Show refresh indicator for 1 second every 30 seconds
            setInterval(() => {
                const indicator = document.createElement('span');
                indicator.className = 'refresh-indicator';
                indicator.textContent = '🔄';
                document.querySelector('.app-status').appendChild(indicator);
                
                setTimeout(() => {
                    indicator.remove();
                }, 1000);
            }, 30000);
        }
        
        // Add keyboard shortcut to toggle details (Ctrl+D)
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                toggleAppDetails();
            }
        });
    </script>
    
    @yield('additional_scripts')
</body>
</html> 