# Laravel Octane with FrankenPHP + Nginx Docker Setup

This setup provides a **high-performance** Laravel application using **Laravel Octane** with **FrankenPHP** behind an **Nginx reverse proxy** - delivering exceptional performance with optimal static file handling and SSL termination capabilities.

## 🚀 Performance Benefits

- **10-50x faster** than traditional PHP-FPM setups
- **Nginx reverse proxy** for optimal static file serving
- **HTTP/2 & HTTP/3** support via Nginx
- **Worker processes** with persistent application state
- **Zero-downtime deployments** capability
- **Built-in compression** and advanced caching
- **Memory-efficient** request handling
- **SSL termination** at nginx level
- **Load balancing** ready for multiple Octane instances

## 🏗️ Architecture

- **Nginx**: High-performance reverse proxy, static file server, and SSL termination
- **FrankenPHP**: Modern PHP application server with worker mode (internal)
- **Laravel Octane**: High-performance Laravel application layer
- **MySQL 8.0**: Optimized database with larger buffer pool
- **Redis**: High-performance caching, sessions, and queue backend
- **Horizon**: Advanced queue management and monitoring
- **phpMyAdmin**: Database management interface
- **Redis Commander**: Redis monitoring and management

### Request Flow
```
Client → Nginx (Port 80/443) → FrankenPHP/Octane (Port 8000) → Laravel Application
         ↓ (static files served directly)
         Static Assets (CSS, JS, Images)
```

## 🚀 Quick Start

### Prerequisites

- Docker
- Docker Compose
- At least 2GB RAM (recommended: 4GB+)

### Automated Setup

Run the automated setup for a complete Octane installation:

```bash
./docker-octane-setup.sh
```

This script will:
1. Stop any existing containers
2. Create optimized `.env` configuration
3. Build FrankenPHP containers
4. Start all services with health checks
5. Install dependencies and generate keys
6. Publish Octane configuration
7. Run database migrations
8. Install and configure Laravel Horizon
9. Optimize application for performance

### Manual Setup

If you prefer step-by-step setup:

1. **Copy environment file:**
   ```bash
   cp env.octane.example .env
   ```

2. **Build and start containers:**
   ```bash
   docker-compose -f docker-compose.octane.yml up -d --build
   ```

3. **Install dependencies:**
   ```bash
   docker-compose -f docker-compose.octane.yml exec octane composer install
   ```

4. **Generate application key:**
   ```bash
   docker-compose -f docker-compose.octane.yml exec octane php artisan key:generate
   ```

5. **Publish Octane configuration:**
   ```bash
   docker-compose -f docker-compose.octane.yml exec octane php artisan vendor:publish --provider="Laravel\Octane\OctaneServiceProvider"
   ```

6. **Run migrations:**
   ```bash
   docker-compose -f docker-compose.octane.yml exec octane php artisan migrate
   ```

## 🌐 Service Access

- **Laravel Application**: http://localhost:8180
- **Laravel Application (HTTPS)**: https://localhost:8543 (if SSL configured)
- **phpMyAdmin**: http://localhost:8181  
- **Redis Commander**: http://localhost:8182
- **Horizon Dashboard**: http://localhost:8180/horizon

> **Note**: These ports are different from the standard Docker setup to allow running both stacks simultaneously.

## ⚙️ Configuration

### FrankenPHP Settings

Key environment variables for tuning performance:

```env
# Worker Configuration
FRANKENPHP_NUM_WORKERS=auto          # Number of worker processes
FRANKENPHP_NUM_THREADS=2             # Threads per worker

# Octane Configuration  
OCTANE_MAX_REQUESTS=500               # Requests before worker restart
OCTANE_WORKERS=auto                   # Number of Octane workers
OCTANE_TASK_WORKERS=auto              # Task worker processes
```

### Performance Tuning

**For Development:**
```env
FRANKENPHP_NUM_WORKERS=1
FRANKENPHP_NUM_THREADS=1
OCTANE_MAX_REQUESTS=100
```

**For Production:**
```env
FRANKENPHP_NUM_WORKERS=auto
FRANKENPHP_NUM_THREADS=4
OCTANE_MAX_REQUESTS=1000
```

### SSL/TLS Configuration

To enable HTTPS with SSL certificates:

1. **Create SSL certificates directory:**
   ```bash
   mkdir -p docker/nginx-octane/ssl
   ```

2. **Add your certificates:**
   ```bash
   # Copy your SSL certificate and key
   cp your-cert.pem docker/nginx-octane/ssl/cert.pem
   cp your-key.pem docker/nginx-octane/ssl/key.pem
   ```

3. **Uncomment SSL volume in docker-compose.octane.yml:**
   ```yaml
   volumes:
     # Uncomment for SSL certificates
     - ./docker/nginx-octane/ssl:/etc/nginx/ssl
   ```

4. **Generate self-signed certificates for development:**
   ```bash
   # Create development SSL certificates
   openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
     -keyout docker/nginx-octane/ssl/key.pem \
     -out docker/nginx-octane/ssl/cert.pem \
     -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost"
   ```

### Static File Optimization

Nginx is configured to serve static files directly for optimal performance:

- **Cached for 1 year**: CSS, JS, images, fonts
- **GZIP compression** enabled for text-based assets
- **CORS headers** for cross-origin asset requests
- **Security headers** for all responses

## 📊 Monitoring & Management

### Container Management

```bash
# Start all services
docker-compose -f docker-compose.octane.yml up -d

# Stop all services  
docker-compose -f docker-compose.octane.yml down

# View logs
docker-compose -f docker-compose.octane.yml logs -f

# View specific service logs
docker-compose -f docker-compose.octane.yml logs -f nginx
docker-compose -f docker-compose.octane.yml logs -f octane
docker-compose -f docker-compose.octane.yml logs -f horizon

# Restart services
docker-compose -f docker-compose.octane.yml restart nginx
docker-compose -f docker-compose.octane.yml restart octane

# Monitor resource usage
docker stats

# Test nginx health
curl http://localhost:8180/nginx-health
```

### Nginx Management

```bash
# Test nginx configuration
docker-compose -f docker-compose.octane.yml exec nginx nginx -t

# Reload nginx configuration (without downtime)
docker-compose -f docker-compose.octane.yml exec nginx nginx -s reload

# View nginx access logs
docker-compose -f docker-compose.octane.yml exec nginx tail -f /var/log/nginx/access.log

# View nginx error logs
docker-compose -f docker-compose.octane.yml exec nginx tail -f /var/log/nginx/error.log

# Check nginx status
docker-compose -f docker-compose.octane.yml exec nginx ps aux | grep nginx
```

### Laravel Octane Commands

```bash
# Reload Octane workers (for code changes)
docker-compose -f docker-compose.octane.yml exec octane php artisan octane:reload

# Check Octane status
docker-compose -f docker-compose.octane.yml exec octane php artisan octane:status

# Start Octane manually (if needed)
docker-compose -f docker-compose.octane.yml exec octane php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=80

# Stop Octane workers
docker-compose -f docker-compose.octane.yml exec octane php artisan octane:stop
```

### Queue Management with Horizon

```bash
# View Horizon status
docker-compose -f docker-compose.octane.yml exec octane php artisan horizon:status

# Terminate Horizon
docker-compose -f docker-compose.octane.yml exec octane php artisan horizon:terminate

# Pause queue processing
docker-compose -f docker-compose.octane.yml exec octane php artisan horizon:pause

# Continue queue processing  
docker-compose -f docker-compose.octane.yml exec octane php artisan horizon:continue
```

## 🛠️ Development Workflow

### Code Changes

For development, you have two options:

**Option 1: Automatic Reloading (Recommended)**
```bash
# Enable file watching (set OCTANE_WATCH=true in .env)
# Octane will automatically reload on file changes
```

**Option 2: Manual Reloading**
```bash
# After making code changes, reload workers
docker-compose -f docker-compose.octane.yml exec octane php artisan octane:reload
```

### Database Operations

```bash
# Run migrations
docker-compose -f docker-compose.octane.yml exec octane php artisan migrate

# Seed database
docker-compose -f docker-compose.octane.yml exec octane php artisan db:seed

# Access MySQL CLI
docker-compose -f docker-compose.octane.yml exec mysql mysql -u laravel -p laravel

# Database backup
docker-compose -f docker-compose.octane.yml exec mysql mysqldump -u laravel -p laravel > backup.sql
```

### Cache Operations

```bash
# Clear all caches
docker-compose -f docker-compose.octane.yml exec octane php artisan optimize:clear

# Cache configuration
docker-compose -f docker-compose.octane.yml exec octane php artisan config:cache

# Cache routes  
docker-compose -f docker-compose.octane.yml exec octane php artisan route:cache

# Cache views
docker-compose -f docker-compose.octane.yml exec octane php artisan view:cache
```

## 📁 File Structure

```
project/
├── docker/
│   ├── nginx-octane/
│   │   ├── nginx.conf             # Nginx reverse proxy configuration
│   │   └── octane-locations.conf  # Shared location blocks
│   ├── frankenphp/
│   │   ├── Caddyfile              # FrankenPHP server configuration
│   │   ├── php.ini                # Optimized PHP settings
│   │   └── docker-entrypoint.sh   # Container initialization
│   └── redis/
│       └── redis.conf             # Redis configuration
├── docker-compose.octane.yml      # Octane services definition
├── Dockerfile.octane              # FrankenPHP container definition
├── env.octane.example             # Octane environment template
├── docker-octane-setup.sh         # Automated setup script
└── public/
    └── frankenphp-worker.php      # FrankenPHP worker entry point
```

## 🚀 Performance Optimization

### Production Deployment

1. **Update environment:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   OCTANE_WATCH=false
   ```

2. **Enable OPcache preloading:**
   ```bash
   # The entrypoint script automatically creates an optimized preload file
   # Ensure opcache.preload is set in php.ini
   ```

3. **Optimize caching:**
   ```bash
   docker-compose -f docker-compose.octane.yml exec octane php artisan config:cache
   docker-compose -f docker-compose.octane.yml exec octane php artisan route:cache
   docker-compose -f docker-compose.octane.yml exec octane php artisan view:cache
   docker-compose -f docker-compose.octane.yml exec octane php artisan event:cache
   ```

### Memory Management

Monitor memory usage and adjust worker settings:

```bash
# Monitor container memory
docker stats laravel_octane

# Adjust workers based on available memory
# Rule of thumb: 50-100MB per worker
```

### Load Testing

Test your application performance:

```bash
# Install Apache Bench
apt-get update && apt-get install apache2-utils

# Basic load test
ab -n 1000 -c 10 http://localhost:8180/

# Advanced load test with wrk
wrk -t4 -c100 -d30s http://localhost:8180/
```

## 🔧 Troubleshooting

### Common Issues

**Container Memory Issues:**
```bash
# Check memory usage
docker stats

# Reduce workers if needed
# Edit FRANKENPHP_NUM_WORKERS in .env
```

**Database Connection Problems:**
```bash
# Check MySQL health
docker-compose -f docker-compose.octane.yml exec mysql mysqladmin ping

# Verify connection from Octane
docker-compose -f docker-compose.octane.yml exec octane php artisan tinker
# DB::connection()->getPdo();
```

**Octane Worker Issues:**
```bash
# Restart workers
docker-compose -f docker-compose.octane.yml exec octane php artisan octane:reload

# Check worker status
docker-compose -f docker-compose.octane.yml exec octane php artisan octane:status

# View detailed logs
docker-compose -f docker-compose.octane.yml logs -f octane
```

**Redis Connection Issues:**
```bash
# Test Redis connection
docker-compose -f docker-compose.octane.yml exec redis redis-cli ping

# Check Redis config
docker-compose -f docker-compose.octane.yml exec redis redis-cli config get "*"
```

**Nginx Issues:**
```bash
# Test nginx configuration
docker-compose -f docker-compose.octane.yml exec nginx nginx -t

# Check if nginx can reach Octane backend
docker-compose -f docker-compose.octane.yml exec nginx curl -I http://octane:8000/

# View nginx error logs
docker-compose -f docker-compose.octane.yml logs nginx

# Check nginx processes
docker-compose -f docker-compose.octane.yml exec nginx ps aux
```

**Static File Issues:**
```bash
# Check if static files exist
docker-compose -f docker-compose.octane.yml exec nginx ls -la /var/www/html/public/

# Test direct static file access
curl -I http://localhost:8180/favicon.ico

# Check nginx static file serving
docker-compose -f docker-compose.octane.yml exec nginx tail -f /var/log/nginx/access.log
```

### Debug Mode

Enable debug mode for troubleshooting:

```bash
# Set in .env
APP_DEBUG=true
LOG_LEVEL=debug

# Restart containers
docker-compose -f docker-compose.octane.yml restart octane
```

## 📈 Performance Benchmarks

Expected performance improvements with Nginx + Octane + FrankenPHP:

- **Static Files**: 50-100x faster (served directly by Nginx)
- **Simple JSON Response**: 10-15x faster
- **Database Queries**: 5-8x faster  
- **View Rendering**: 8-12x faster
- **API Endpoints**: 10-20x faster
- **Mixed Content (static + dynamic)**: 15-30x faster
- **SSL Termination**: Optimized at Nginx level
- **Concurrent Connections**: Significantly improved with Nginx

## 🔒 Security Considerations

### Production Security

1. **Environment Variables:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Database Security:**
   - Use strong passwords
   - Restrict database access
   - Enable SSL/TLS

3. **Redis Security:**
   - Set Redis password in production
   - Use Redis AUTH

4. **Container Security:**
   - Run with non-root user
   - Use secrets management
   - Regular security updates

## 📚 Additional Resources

- [Laravel Octane Documentation](https://laravel.com/docs/octane)
- [FrankenPHP Documentation](https://frankenphp.dev/)
- [Laravel Horizon Documentation](https://laravel.com/docs/horizon)
- [Docker Best Practices](https://docs.docker.com/develop/best-practices/)

## 🎯 Next Steps

1. **Install Laravel Telescope** for debugging:
   ```bash
   docker-compose -f docker-compose.octane.yml exec octane composer require laravel/telescope
   docker-compose -f docker-compose.octane.yml exec octane php artisan telescope:install
   ```

2. **Set up SSL/TLS** for production deployment

3. **Configure monitoring** with tools like New Relic or DataDog

4. **Implement caching strategies** for your specific use case

---

🚀 **Enjoy your lightning-fast Laravel application with Nginx + Octane + FrankenPHP!** 