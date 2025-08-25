#!/bin/sh

# Setup environment
if [ ! -f .env ]; then
    if [ -f .env.railway ]; then
        cp .env.railway .env
    fi
fi

# Ensure Laravel can write to storage
chmod -R 777 storage bootstrap/cache

# Start PHP server
exec php -S 0.0.0.0:${PORT:-8080} -t public