#!/bin/bash

# Laravel Docker Setup Script
echo "🐳 Setting up Laravel with Docker..."

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

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file from template..."
    cp env.example.docker .env
    echo "✅ .env file created. Please update it with your specific configuration."
else
    echo "⚠️  .env file already exists. Skipping creation."
fi

# Build and start containers
echo "🏗️  Building Docker containers..."
$DOCKER_COMPOSE build --no-cache

echo "🚀 Starting Docker containers..."
$DOCKER_COMPOSE up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 30

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
$DOCKER_COMPOSE exec app composer install --optimize-autoloader

# Generate application key
echo "🔑 Generating application key..."
$DOCKER_COMPOSE exec app php artisan key:generate

# Run migrations
echo "🗃️  Running database migrations..."
$DOCKER_COMPOSE exec app php artisan migrate --force

# Clear and cache config
echo "🧹 Clearing and caching configuration..."
$DOCKER_COMPOSE exec app php artisan config:clear
$DOCKER_COMPOSE exec app php artisan config:cache
$DOCKER_COMPOSE exec app php artisan route:cache

# Set permissions
echo "🔒 Setting proper permissions..."
$DOCKER_COMPOSE exec app chown -R www-data:www-data /var/www/html/storage
$DOCKER_COMPOSE exec app chown -R www-data:www-data /var/www/html/bootstrap/cache

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Your Laravel application is now running at:"
echo "🌐 Application: http://localhost:8080"
echo "🗄️  phpMyAdmin: http://localhost:8081"
echo ""
echo "Database credentials:"
echo "  Host: localhost"
echo "  Port: 3306"
echo "  Database: laravel"
echo "  Username: laravel"
echo "  Password: secret"
echo ""
echo "To stop the containers, run:"
echo "  $DOCKER_COMPOSE down"
echo ""
echo "To view logs, run:"
echo "  $DOCKER_COMPOSE logs -f" 