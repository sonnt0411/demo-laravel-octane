# Laravel Docker Setup

This project includes a complete Docker setup with Nginx, PHP-FPM, MySQL, and Redis for Laravel development.

## Architecture

- **Nginx**: Web server (port 8080)
- **PHP-FPM**: Laravel application (PHP 8.3)
- **MySQL 8.0**: Primary database (port 3306)
- **Redis**: Caching and sessions (port 6379)
- **phpMyAdmin**: Database management (port 8081)

## Quick Start

### Prerequisites

- Docker
- Docker Compose

### Automated Setup

Run the setup script for a complete automated installation:

```bash
./docker-setup.sh
```

This script will:
1. Create `.env` file from template
2. Build Docker containers
3. Start all services
4. Install Composer dependencies
5. Generate application key
6. Run database migrations
7. Set proper permissions

### Manual Setup

If you prefer manual setup:

1. **Copy environment file:**
   ```bash
   cp env.example.docker .env
   ```

2. **Build and start containers:**
   ```bash
   docker-compose up -d --build
   ```

3. **Install dependencies:**
   ```bash
   docker-compose exec app composer install
   ```

4. **Generate application key:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

5. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

## Services Access

- **Laravel Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## Database Credentials

- **Host**: mysql (from containers) / localhost (from host)
- **Port**: 3306
- **Database**: laravel
- **Username**: laravel
- **Password**: secret

## Common Commands

### Container Management

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f nginx

# Rebuild containers
docker-compose up -d --build
```

### Laravel Commands

```bash
# Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller ExampleController
docker-compose exec app php artisan tinker

# Composer commands
docker-compose exec app composer install
docker-compose exec app composer require package/name

# Cache commands
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Database Operations

```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u laravel -p laravel

# Import SQL file
docker-compose exec mysql mysql -u laravel -p laravel < backup.sql

# Create database backup
docker-compose exec mysql mysqldump -u laravel -p laravel > backup.sql
```

## File Structure

```
project/
├── docker/
│   ├── nginx/
│   │   └── nginx.conf           # Nginx configuration
│   ├── php/
│   │   ├── php.ini              # PHP configuration
│   │   └── php-fpm.conf         # PHP-FPM configuration
│   └── supervisor/
│       └── supervisord.conf     # Supervisor configuration
├── docker-compose.yml           # Docker services definition
├── Dockerfile                   # PHP-FPM container definition
├── .dockerignore               # Docker build ignore patterns
├── env.example.docker          # Environment template
└── docker-setup.sh            # Automated setup script
```

## Development Workflow

1. **Start development environment:**
   ```bash
   docker-compose up -d
   ```

2. **Watch logs during development:**
   ```bash
   docker-compose logs -f app
   ```

3. **Run tests:**
   ```bash
   docker-compose exec app php artisan test
   ```

4. **Access container shell:**
   ```bash
   docker-compose exec app sh
   ```

## Production Considerations

For production deployment:

1. Update environment variables in `.env`
2. Set `APP_ENV=production`
3. Set `APP_DEBUG=false`
4. Use strong passwords
5. Configure SSL/TLS termination
6. Set up proper volume backups
7. Configure log rotation

## Troubleshooting

### Container Issues

```bash
# Check container status
docker-compose ps

# Restart specific service
docker-compose restart app

# Remove containers and start fresh
docker-compose down
docker-compose up -d --build
```

### Permission Issues

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
```

### Database Connection Issues

1. Ensure MySQL container is running: `docker-compose ps`
2. Check database credentials in `.env`
3. Wait for MySQL to be fully initialized (30-60 seconds)

### Clear All Caches

```bash
docker-compose exec app php artisan optimize:clear
```

## Support

For issues specific to the Docker setup, check:
1. Container logs: `docker-compose logs`
2. Ensure all required ports are available
3. Verify `.env` configuration matches `docker-compose.yml` 