# 🛡️ Laravel Octane-Safe Singleton Patterns

## Overview

In traditional PHP-FPM, the entire process dies after each request, automatically resetting all singletons. However, Laravel Octane keeps the application in memory between requests, causing traditional singletons to persist across requests and potentially leak data between users.

## ⚠️ The Problem

```php
// ❌ DANGEROUS in Octane - Persists across requests
$this->app->singleton(UserService::class);
```

**Issues:**
- User data leakage between requests
- Memory accumulation over time
- Race conditions in concurrent requests
- Security vulnerabilities

## ✅ Safe Solutions

### 1. Request-Scoped Singletons (Recommended)

```php
// ✅ SAFE: Fresh instance per request
$this->app->scoped(UserService::class);
```

**Benefits:**
- Same instance within a request
- Automatically resets between requests
- No memory leaks
- Maintains performance benefits

### 2. Manual Octane Lifecycle Management

```php
use Laravel\Octane\Facades\Octane;

// Reset singleton state on request termination
Octane::tick('requestTerminated', function () {
    app()->forgetInstance(UserService::class);
});
```

### 3. Request-Aware Singletons

```php
class RequestAwareService
{
    private static array $instances = [];
    
    public static function getInstance(): self
    {
        $requestId = request()->id() ?? 'default';
        
        if (!isset(self::$instances[$requestId])) {
            self::$instances[$requestId] = new self();
        }
        
        return self::$instances[$requestId];
    }
    
    public static function clearRequest(string $requestId): void
    {
        unset(self::$instances[$requestId]);
    }
}
```

### 4. Context-Based Singletons

```php
// Store state in request context
$this->app->singleton(UserService::class, function ($app) {
    return new UserService($app['request']);
});
```

## 🧪 Testing Your Implementation

### Test Request Isolation

```php
// Test multiple requests to verify reset behavior
Route::get('/test-singleton', function (YourService $service) {
    $service->incrementCounter();
    return response()->json([
        'counter' => $service->getCounter(),
        'timestamp' => now(),
        'request_id' => request()->id()
    ]);
});
```

**Expected Results:**
- **Octane-Safe**: Counter resets to 1 on each request
- **Octane-Unsafe**: Counter keeps incrementing across requests

## 📋 Best Practices

### ✅ Do:
- Use `scoped()` for request-level singletons
- Store user-specific data in session/cache with proper keys
- Implement proper cleanup in Octane lifecycle hooks
- Test your singletons with multiple requests

### ❌ Don't:
- Use `singleton()` for user-specific services
- Store request data in static properties
- Assume garbage collection will clean up for you
- Ignore memory usage in long-running processes

## 🔧 Common Patterns by Use Case

### User Authentication Services
```php
// ✅ Safe: Request-scoped
$this->app->scoped(AuthService::class);

// ✅ Alternative: Store in session
$this->app->bind(AuthService::class, function () {
    return new AuthService(session('user_id'));
});
```

### Configuration Services
```php
// ✅ Safe: Configuration is shared across requests
$this->app->singleton(ConfigService::class);
```

### Database Connections
```php
// ✅ Safe: Laravel handles connection lifecycle
$this->app->singleton(DatabaseService::class);
```

### Caching Services
```php
// ✅ Safe: Cache is external to process
$this->app->singleton(CacheService::class);
```

## 🚀 Performance Considerations

| Pattern | Memory Usage | Performance | Octane Safety |
|---------|--------------|-------------|---------------|
| `singleton()` | High (accumulates) | Fastest | ❌ Unsafe |
| `scoped()` | Medium (per request) | Fast | ✅ Safe |
| `bind()` | Low (per resolve) | Slower | ✅ Safe |
| Manual cleanup | Variable | Medium | ✅ Safe (if implemented correctly) |

## 📊 Testing Results

Run the request-scoped test to see the behavior:

- **Classic Stack**: http://localhost:8080/request-scoped-test
- **Octane Stack**: http://localhost:8180/request-scoped-test

Both environments will show the same request-scoped behavior, proving Octane safety.

## 🎯 Key Takeaway

**Use `scoped()` instead of `singleton()` for any service that should reset between requests.** This is the simplest and most reliable way to create Octane-safe singletons that maintain performance benefits while preventing data leakage. 