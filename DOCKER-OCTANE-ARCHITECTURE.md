# Docker Octane Architecture

## Overview

This Laravel application uses a multi-container Docker setup with **Laravel Octane (FrankenPHP)** for high-performance PHP execution and **Nginx** as a reverse proxy.

## Architecture

### Container Structure

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Nginx       │    │  Laravel Octane │    │     MySQL       │
│  (Reverse Proxy)│────▶│   (FrankenPHP)  │────▶│   (Database)    │
│   Port: 8180    │    │   Port: 8000    │    │   Port: 3406    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌─────────────────┐
                       │      Redis      │
                       │   (Cache/Queue) │
                       │   Port: 6479    │
                       └─────────────────┘
```

### Services

1. **nginx** - Nginx reverse proxy container
   - Handles incoming HTTP requests
   - Serves static files directly
   - Proxies dynamic requests to Laravel Octane
   - Exposed on port **8180**

2. **octane** - Laravel Octane with FrankenPHP
   - Runs Laravel application with FrankenPHP
   - High-performance PHP runtime
   - Internal port **8000** (not exposed externally)
   - Communicates with database and cache

3. **octane-mysql** - MySQL database
   - Persistent data storage
   - Exposed on port **3406** for external access

4. **redis** - Redis cache and session storage
   - Fast in-memory caching
   - Session management
   - Exposed on port **6479** for external access

5. **octane-phpmyadmin** - Database management (optional)
   - Web interface for MySQL
   - Accessible on port **8181**

6. **redis-commander** - Redis management (optional)
   - Web interface for Redis
   - Accessible on port **8182**

## Benefits of Separate Containers

### ✅ **Improved Separation of Concerns**
- Nginx handles only HTTP routing and static files
- Laravel Octane focuses purely on PHP execution
- Each service can be scaled independently

### ✅ **Better Resource Management**
- Nginx container is lightweight (Alpine-based)
- Octane container contains only PHP dependencies
- Easier to allocate resources per service

### ✅ **Enhanced Security**
- Reduced attack surface per container
- Network isolation between services
- Octane not directly exposed to external traffic

### ✅ **Simplified Maintenance**
- Independent updates for Nginx vs PHP/Laravel
- Easier debugging and logging per service
- Standard Nginx configuration management

### ✅ **Production Ready**
- Follows Docker best practices
- Easier horizontal scaling
- Better monitoring and health checks

## Usage

### Development
```bash
docker-compose -f docker-compose.octane.yml up -d
```

### Production
```bash
docker-compose -f docker-compose.octane.yml up -d --build
```

### Access Points
- **Application**: http://localhost:8180
- **phpMyAdmin**: http://localhost:8181
- **Redis Commander**: http://localhost:8182

## Configuration Files

- `docker-compose.octane.yml` - Multi-container orchestration
- `Dockerfile.octane` - Laravel Octane container definition
- `docker/nginx-octane/nginx.conf` - Nginx reverse proxy configuration
- `docker/frankenphp/start-octane.sh` - Octane startup script 