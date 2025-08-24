#!/bin/bash
# exit on error
set -e

# Buat semua folder yang Laravel butuhkan
mkdir -p bootstrap/cache
mkdir -p storage/app
mkdir -p storage/framework
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Set permission
chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Serve app
php artisan serve --host=0.0.0.0 --port=$PORT
