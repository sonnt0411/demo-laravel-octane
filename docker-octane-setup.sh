#!/bin/bash

# Laravel Octane with FrankenPHP + Nginx Docker Setup Script
echo "🐳🚀 Setting up Laravel Octane with FrankenPHP + Nginx reverse proxy..."

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check if Docker is installed
if ! command_exists docker; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command_exists docker-compose && ! docker compose version >/dev/null 2>&1; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Set Docker Compose command
if docker compose version >/dev/null 2>&1; then
    DOCKER_COMPOSE="docker compose"
else
    DOCKER_COMPOSE="docker-compose"
fi

# Stop any running containers from the regular setup
echo "🛑 Stopping any existing containers..."
$DOCKER_COMPOSE down 2>/dev/null || true

# Create .env file for Octane if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file from Octane template..."
    cp env.octane.example .env
    echo "✅ .env file created with Octane configuration."
else
    echo "⚠️  .env file already exists. You may want to check Octane-specific settings."
fi

# Create directories for Docker volumes
echo "📁 Creating necessary directories..."
mkdir -p docker/mysql/init
mkdir -p storage/logs

# Build and start Octane containers
echo "🏗️  Building Docker containers for Octane..."
$DOCKER_COMPOSE -f docker-compose.octane.yml build --no-cache

echo "🚀 Starting Laravel Octane with FrankenPHP..."
$DOCKER_COMPOSE -f docker-compose.octane.yml up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 30

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane composer install --optimize-autoloader

# Generate application key
echo "🔑 Generating application key..."
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan key:generate

# Publish Octane configuration
echo "⚙️  Publishing Octane configuration..."
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan vendor:publish --provider="Laravel\\Octane\\OctaneServiceProvider" --force

# Run migrations
echo "🗃️  Running database migrations..."
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan migrate --force

# Install Laravel Horizon (optional but recommended for queue management)
echo "🌅 Setting up Laravel Horizon..."
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane composer require laravel/horizon --no-interaction || echo "Horizon already installed or failed to install"
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan horizon:install || echo "Horizon config already exists"

# Clear and optimize caches
echo "🧹 Optimizing application..."
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan config:clear
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan route:clear
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan view:clear
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane php artisan optimize

# Set permissions
echo "🔒 Setting proper permissions..."
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane chown -R www-data:www-data /app/storage
$DOCKER_COMPOSE -f docker-compose.octane.yml exec octane chown -R www-data:www-data /app/bootstrap/cache

# Test the application through nginx
echo "🧪 Testing application response through nginx..."
sleep 5
if curl -sf http://localhost:8180 > /dev/null; then
    echo "✅ Application is responding through nginx!"
    echo "✅ Testing nginx health check..."
    curl -sf http://localhost:8180/nginx-health > /dev/null && echo "✅ Nginx health check passed!"
else
    echo "⚠️  Application might still be starting up. Check logs if needed."
    echo "    Try: docker-compose -f docker-compose.octane.yml logs -f"
fi

echo ""
echo "🎉 Laravel Octane with FrankenPHP setup complete!"
echo ""
echo "🌟 Your high-performance Laravel application is now running!"
echo ""
echo "📊 Performance Features Enabled:"
echo "  🌐 Nginx - High-performance reverse proxy & static file server"
echo "  ⚡ FrankenPHP - Modern PHP application server"
echo "  🚀 Laravel Octane - Supercharged performance"
echo "  📱 HTTP/2 & HTTP/3 support via Nginx"
echo "  🔄 Worker processes for persistent state"
echo "  💾 Redis for caching, sessions, and queues"
echo "  🌅 Laravel Horizon for queue monitoring"
echo "  🚀 Static file serving optimized by Nginx"
echo ""
echo "🌐 Access Points:"
echo "  📱 Application: http://localhost:8180"
echo "  🗄️  phpMyAdmin: http://localhost:8181"
echo "  📊 Redis Commander: http://localhost:8182"
echo "  🌅 Horizon Dashboard: http://localhost:8180/horizon"
echo ""
echo "💾 Database Credentials:"
echo "  Host: localhost:3406"
echo "  Database: laravel"
echo "  Username: laravel"
echo "  Password: secret"
echo ""
echo "🔧 Management Commands:"
echo "  Stop services: $DOCKER_COMPOSE -f docker-compose.octane.yml down"
echo "  View logs: $DOCKER_COMPOSE -f docker-compose.octane.yml logs -f"
echo "  View nginx logs: $DOCKER_COMPOSE -f docker-compose.octane.yml logs -f nginx"
echo "  View octane logs: $DOCKER_COMPOSE -f docker-compose.octane.yml logs -f octane"
echo "  Restart Nginx: $DOCKER_COMPOSE -f docker-compose.octane.yml restart nginx"
echo "  Restart Octane: $DOCKER_COMPOSE -f docker-compose.octane.yml restart octane"
echo "  Monitor queues: $DOCKER_COMPOSE -f docker-compose.octane.yml logs -f horizon"
echo "  Test nginx health: curl http://localhost:8180/nginx-health"
echo ""
echo "📈 Performance Tips:"
echo "  - Monitor memory usage with: docker stats"
echo "  - Check Octane stats at: http://localhost:8180/octane/status"
echo "  - Use Horizon dashboard for queue monitoring"
echo "  - Enable OPcache preloading in production"
echo ""
echo "⚡ Enjoy your lightning-fast Laravel application!" 