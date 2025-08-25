#!/bin/sh

echo "Using Railway environment variables directly - no .env file needed"
echo "APP_KEY: $(echo $APP_KEY | cut -c1-20)..."
echo "DB_HOST: $DB_HOST"
echo "DB_DATABASE: $DB_DATABASE"

# Ensure Laravel can write to storage
chmod -R 777 storage bootstrap/cache

# Bootstrap Laravel (only if artisan exists and can run)
if [ -f artisan ]; then
    echo "Bootstrapping Laravel..."
    php artisan config:clear || true
    php artisan route:clear || true  
    php artisan view:clear || true
    php artisan cache:clear || true
    echo "Laravel bootstrap complete"
fi

# Start PHP server
echo "Starting Laravel server on port ${PORT:-8080}..."
exec php -S 0.0.0.0:${PORT:-8080} -t public