# Laravel Dependency Injection Testing

This project includes comprehensive tests to verify how Laravel's service container handles dependency injection when a concrete class needs to be resolved multiple times in a single request.

## Test Overview

The test setup includes:

1. **TestService** - A simple service class that logs its creation and provides unique instance identification
2. **DependentService** - A service that depends on TestService to test nested dependency injection
3. **BaseService** - A shared dependency service for testing multi-service injection
4. **FirstService** - A service that depends on BaseService
5. **SecondService** - Another service that depends on BaseService
6. **PostController** - Controller with multiple test scenarios

## Available Test Routes

### 1. Standard Dependency Injection Test
- **URL**: `/dependency-test`
- **Route Name**: `dependency.test`
- **Description**: Tests various ways of resolving services in a single request

### 2. Singleton Test
- **URL**: `/dependency-singleton-test`
- **Route Name**: `dependency.singleton`
- **Description**: Tests how singleton binding affects service resolution

### 3. Shared Dependency Test
- **URL**: `/shared-dependency-test`
- **Route Name**: `dependency.shared`
- **Description**: Tests if Laravel shares the same dependency instance between multiple services

## Test Scenarios

### Standard DI Test Includes:
1. **Constructor Injection** - Service injected into controller constructor
2. **Manual Resolution 1** - Using `app(TestService::class)`
3. **Manual Resolution 2** - Another `app(TestService::class)` call
4. **Make Resolution** - Using `app()->make(TestService::class)`
5. **Dependent Service 1** - Resolving service that depends on TestService
6. **Dependent Service 2** - Another dependent service resolution
7. **Method Injection** - Service injected into controller method
8. **Custom Instance** - Direct instantiation with `new TestService()`

### Singleton Test Includes:
1. **Singleton Resolution 1** - First singleton resolution
2. **Singleton Resolution 2** - Second singleton resolution
3. **Singleton Make** - Using make() with singleton
4. **Dependent Singleton 1** - Dependent service with singleton TestService
5. **Dependent Singleton 2** - Another dependent service with singleton

### Shared Dependency Test Includes:
1. **FirstService Injection** - Service with BaseService dependency
2. **SecondService Injection** - Another service with BaseService dependency
3. **Object Identity Verification** - Multiple verification methods:
   - Strict comparison (`===`)
   - Object hash comparison (`spl_object_hash()`)
   - Instance ID comparison
   - Shared state analysis (action count tracking)

## Expected Results

### Standard DI (Non-Singleton):
- Each resolution should create a **new instance**
- Only the constructor-injected service should be reused within the controller
- Total unique instances should equal the number of resolutions

### Singleton DI:
- All resolutions should return the **same instance**
- TestService should have the same instance ID across all tests
- Dependent services should receive the same TestService instance

### Shared Dependency Test:
- **Expected**: Laravel creates **separate BaseService instances** for each service
- **Verification**: Multiple methods confirm object identity
- **State Tracking**: Action count helps verify if instances are shared

## Key Laravel DI Principles Tested

1. **Default Behavior**: Laravel creates new instances for each resolution unless bound as singleton
2. **Constructor Injection**: Same instance used throughout controller lifetime
3. **Singleton Binding**: `app()->singleton()` ensures single instance per request
4. **Nested Dependencies**: How dependency injection works with dependent services
5. **Shared Dependencies**: Whether Laravel shares dependency instances between services

## Files Structure

```
app/
├── Http/Controllers/PostController.php           # Test controller
└── Services/
    └── DependencyTest/
        ├── TestService.php                      # Original test service
        ├── DependentService.php                 # Dependent service
        ├── BaseService.php                      # Shared dependency service
        ├── FirstService.php                     # First service depending on BaseService
        └── SecondService.php                    # Second service depending on BaseService

resources/views/
├── dependency-test.blade.php                    # Standard test results view
└── shared-dependency-test.blade.php             # Shared dependency test results view

routes/web.php                                   # Test routes
```

## Service Classes

Each service class is organized in its own file within the `App\Services\DependencyTest` namespace:

- **TestService**: Located at `app/Services/DependencyTest/TestService.php`
- **DependentService**: Located at `app/Services/DependencyTest/DependentService.php`
- **BaseService**: Located at `app/Services/DependencyTest/BaseService.php`
- **FirstService**: Located at `app/Services/DependencyTest/FirstService.php`
- **SecondService**: Located at `app/Services/DependencyTest/SecondService.php`

This follows the single responsibility principle where each file contains only one class.

## Running the Tests

1. Start your Laravel application
2. Visit `/dependency-test` for standard DI test
3. Visit `/dependency-singleton-test` for singleton test
4. Visit `/shared-dependency-test` for shared dependency test
5. Check the application logs for detailed instance creation logs

## Understanding the Shared Dependency Test

The shared dependency test creates this architecture:

```
BaseService (shared dependency)
    ↑                    ↑
    |                    |
FirstService         SecondService
    ↑                    ↑
    |                    |
    +----Controller------+
```

**Key Questions Answered:**
- Does Laravel create one BaseService instance or two?
- Are FirstService and SecondService sharing the same BaseService?
- How can we verify object identity in PHP?

**Verification Methods:**
- **Strict Comparison**: `$service1 === $service2`
- **Object Hash**: `spl_object_hash()` comparison
- **Instance ID**: Custom unique identifiers
- **Shared State**: Action count tracking

## Logs

The tests log instance creation to help track the behavior:
- Check `storage/logs/laravel.log` for detailed instance creation logs
- Each service logs when it's instantiated with unique identifiers
- BaseService logs every action performed with count tracking

## Understanding the Results

- **Green borders**: Unique instances (expected in standard DI)
- **Orange borders**: Reused instances (expected in singleton or constructor injection)
- **Red/Green conclusions**: Clear verdict on dependency sharing behavior
- **Instance Analysis**: Shows which tests share the same instances
- **Technical Details**: Object hashes and memory addresses for verification

This testing framework demonstrates Laravel's service container behavior and helps understand when instances are shared vs. when new instances are created. 