#!/bin/sh

# Setup environment with variable substitution
if [ ! -f .env ]; then
    if [ -f .env.railway ]; then
        echo "Setting up environment variables..."
        # Use envsubst to substitute environment variables
        if command -v envsubst >/dev/null 2>&1; then
            envsubst < .env.railway > .env
        else
            # Fallback: manual substitution for Railway variables
            sed "s/\$MYSQLHOST/$MYSQLHOST/g; s/\$MYSQLPORT/$MYSQLPORT/g; s/\$MYSQLDATABASE/$MYSQLDATABASE/g; s/\$MYSQLUSER/$MYSQLUSER/g; s/\$MYSQLPASSWORD/$MYSQLPASSWORD/g" .env.railway > .env
        fi
        echo "Environment setup complete"
    fi
fi

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