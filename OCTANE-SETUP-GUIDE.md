# Laravel Octane Setup Guide for Instance Persistence Testing

## 🚀 Why Your Laravel App Creates New Instances

Based on your testing, you're seeing new Laravel application instances on every request. Here's why this happens and how to fix it:

### Current Detection Results
Your application instance tracker will show you exactly what's happening:
- **Server Type**: What server is running your application
- **Is Octane**: Whether Octane is properly detected
- **Detection Method**: How Octane was identified (or why it wasn't)

## 📋 Setup Laravel Octane Properly

### Step 1: Install Laravel Octane
```bash
# Install Octane package
composer require laravel/octane

# Publish Octane configuration
php artisan vendor:publish --provider="Laravel\Octane\OctaneServiceProvider"

# Install Swoole (recommended) or RoadRunner
composer require spiral/roadrunner
# OR for Swoole (requires Swoole extension)
# pecl install swoole
```

### Step 2: Choose Your Server

#### Option A: Swoole (Recommended)
```bash
# Install Swoole extension
pecl install swoole

# Or using Docker
docker run --rm -it -v $(pwd):/app composer:latest composer require swoole/ide-helper
```

#### Option B: RoadRunner
```bash
# Install RoadRunner
composer require spiral/roadrunner

# Download RoadRunner binary
./vendor/bin/rr get-binary
```

### Step 3: Configure Octane

Edit `config/octane.php`:
```php
<?php

return [
    'server' => env('OCTANE_SERVER', 'swoole'), // or 'roadrunner'
    
    'swoole' => [
        'host' => env('OCTANE_HOST', '127.0.0.1'),
        'port' => env('OCTANE_PORT', 8000),
        'workers' => env('OCTANE_WORKERS', 4),
        'task_workers' => env('OCTANE_TASK_WORKERS', 6),
        'max_requests' => env('OCTANE_MAX_REQUESTS', 500),
    ],
    
    // Memory management
    'warm' => [
        ...config('octane.warm'),
        App\Services\DependencyTest\ApplicationInstanceService::class,
    ],
];
```

### Step 4: Update Environment

Add to your `.env`:
```env
OCTANE_SERVER=swoole
OCTANE_HOST=127.0.0.1
OCTANE_PORT=8000
OCTANE_WORKERS=4
OCTANE_MAX_REQUESTS=500
```

## 🏃 Running Laravel Octane

### Start Octane Server
```bash
# Basic start
php artisan octane:start

# With specific options
php artisan octane:start --server=swoole --host=127.0.0.1 --port=8000 --workers=4

# Watch for changes during development
php artisan octane:start --watch
```

### Verify Octane is Running
1. Check the application instance tracker header - it should show:
   - **Server**: Swoole (or RoadRunner)
   - **Is Octane**: Green indicator
   - **Instance ID**: Should remain the same across requests

2. Check logs:
```bash
tail -f storage/logs/laravel.log
```

You should see:
```
Laravel Application instance tracked: [same_id] processed request #1
Laravel Application instance tracked: [same_id] processed request #2
Laravel Application instance tracked: [same_id] processed request #3
```

## 🔍 Troubleshooting Instance Creation

### Why You Might Still See New Instances

1. **Not Running Octane**
   - Check: Server type shows "artisan_serve" or "apache/nginx"
   - Fix: Use `php artisan octane:start` instead of `php artisan serve`

2. **Worker Restarts**
   - Reason: Worker hit max_requests limit
   - Fix: Increase `OCTANE_MAX_REQUESTS` in `.env`

3. **Memory Limits**
   - Reason: Worker restarted due to memory usage
   - Fix: Optimize code or increase memory limits

4. **Code Changes**
   - Reason: File watcher detected changes
   - Expected: During development with `--watch` flag

5. **Load Balancing**
   - Reason: Multiple workers handling requests
   - Expected: Different workers = different instances

### Configuration Issues

#### Check Octane Installation
```bash
# Verify Octane is installed
composer show laravel/octane

# Check if Octane service provider is registered
php artisan route:list | grep octane
```

#### Check Server Extensions
```bash
# For Swoole
php -m | grep swoole

# For RoadRunner
./vendor/bin/rr --version
```

## 🎯 Expected Behavior with Octane

### ✅ Correct Octane Behavior
- **Same Instance ID**: Across multiple requests
- **Incrementing Request Count**: 1, 2, 3, 4...
- **Memory Growth**: Gradual increase, then plateau
- **Server Type**: "swoole" or "roadrunner"
- **Green Octane Indicator**: In the header

### ❌ Traditional PHP Behavior (Not Octane)
- **Different Instance ID**: Every request
- **Request Count**: Always 1
- **Memory Reset**: Fresh memory each request
- **Server Type**: "artisan_serve", "apache", "nginx"
- **Red Traditional Indicator**: In the header

## 🐳 Docker Setup for Octane

If using Docker, here's a sample setup:

### Dockerfile
```dockerfile
FROM php:8.2-cli

# Install Swoole
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . /app
WORKDIR /app

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 8000

# Start Octane
CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8000"]
```

### docker-compose.yml
```yaml
version: '3.8'
services:
  octane:
    build: .
    ports:
      - "8000:8000"
    environment:
      - OCTANE_SERVER=swoole
      - OCTANE_WORKERS=4
      - OCTANE_MAX_REQUESTS=500
    volumes:
      - ./storage/logs:/app/storage/logs
```

## 📊 Monitoring Instance Behavior

Your application tracker provides real-time monitoring:

1. **Instance Analysis**: Shows if instances are being reused
2. **Server Information**: Confirms Octane detection
3. **Memory Tracking**: Shows memory growth patterns
4. **Request Metrics**: Performance indicators

### Reading the Metrics

- **Instance Age**: How long the current instance has been running
- **Memory Growth**: Memory increase since instance start  
- **Requests/Second**: Performance metric
- **New Instance Detected**: Warning when fresh instance is created

## 🎛️ Octane Commands

```bash
# Start Octane
php artisan octane:start

# Start with file watching
php artisan octane:start --watch

# Stop Octane
php artisan octane:stop

# Reload workers
php artisan octane:reload

# Check Octane status
php artisan octane:status
```

## 🔧 Performance Tuning

### Optimize for Instance Persistence
1. **Increase max_requests**: Higher values = longer instance life
2. **Optimize memory usage**: Prevent memory-related restarts
3. **Use appropriate worker count**: Balance between resources and performance
4. **Warm services**: Pre-load frequently used services

### Monitor Performance
- Watch memory usage patterns
- Track request processing time
- Monitor worker restart frequency
- Check for memory leaks

## 🎉 Success Indicators

You'll know Octane is working correctly when:
1. **Instance ID remains constant** across requests
2. **Request count increments** (1, 2, 3, 4...)
3. **Server type shows "swoole"** or "roadrunner"
4. **Green Octane indicator** in the header
5. **Memory usage grows gradually** then stabilizes
6. **No "New Instance Detected" warnings** on subsequent requests

Happy testing! 🚀 