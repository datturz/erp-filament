#!/bin/sh

echo "Starting Pants ERP System..."
echo "APP_KEY: $(echo $APP_KEY | cut -c1-20)..."
echo "DB_DATABASE: $DB_DATABASE"

# Ensure Laravel can write to storage
chmod -R 777 storage bootstrap/cache

# Start PHP server without artisan commands that might fail
echo "Starting PHP server on port ${PORT:-8080}..."
exec php -S 0.0.0.0:${PORT:-8080} -t public