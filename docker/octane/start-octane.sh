#!/bin/bash
set -e

# Function to handle graceful shutdown
cleanup() {
    echo "Shutting down services..."
    pkill -TERM nginx || true
    pkill -TERM php || true
    wait
    exit 0
}

# Set up signal handlers
trap cleanup SIGTERM SIGINT

# Check if APP_ENV is not local, then cache configs and routes
if [[ "${APP_ENV}" != "local" ]]; then
    echo "Caching Laravel configuration for ${APP_ENV} environment..."
    php artisan config:cache
    php artisan route:cache
fi

# Check if first argument is a custom command (not octane options)
if [ $# -gt 0 ] && [[ "$1" != --* ]]; then
    # Execute the custom command
    exec "$@"
else
    echo "Starting Nginx..."
    nginx -g "daemon off;" &
    
    # Wait a moment for nginx to start
    sleep 2
    
    # Start Octane with FrankenPHP on all interfaces
    echo "Starting Laravel Octane with FrankenPHP with options: $@"
    php artisan octane:frankenphp --host=0.0.0.0 --port=8000 "$@" &
    
    # Wait for any process to exit
    wait -n
    
    # Exit with status of process that exited first
    exit $?
fi 