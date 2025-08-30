#!/bin/sh

echo "Starting Pants ERP System..."
echo "Environment: ${APP_ENV:-not-set}"
echo "APP_KEY status: $(if [ -n "$APP_KEY" ]; then echo "SET"; else echo "NOT SET - GENERATING"; fi)"
echo "Database: ${MYSQLDATABASE:-not-set}"
echo "Database Host: ${MYSQLHOST:-not-set}"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating Laravel APP_KEY..."
    export APP_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
    echo "Generated APP_KEY: ${APP_KEY:0:20}..."
fi

# Ensure storage directories exist and are writable
mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/cache/redis \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/logs \
    bootstrap/cache

# Ensure Laravel can write to storage
chmod -R 777 storage bootstrap/cache

# Clear any cached config/routes
echo "Clearing Laravel caches..."
php artisan config:clear 2>&1 || echo "Config clear skipped"
php artisan route:clear 2>&1 || echo "Route clear skipped"
php artisan view:clear 2>&1 || echo "View clear skipped"

# Run composer scripts that were skipped during build
echo "Running post-install scripts..."
php artisan package:discover --ansi 2>&1 || echo "Package discovery completed or skipped"

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "ERROR: vendor directory not found! Composer install may have failed."
    echo "Contents of /var/www:"
    ls -la /var/www/
    exit 1
fi

# Test database connection if credentials available
if [ -n "$MYSQLHOST" ] && [ -n "$MYSQLDATABASE" ]; then
    echo "Testing database connection..."
    php -r "
    try {
        \$pdo = new PDO('mysql:host='.\$_ENV['MYSQLHOST'].';port='.\$_ENV['MYSQLPORT'].';dbname='.\$_ENV['MYSQLDATABASE'], \$_ENV['MYSQLUSER'], \$_ENV['MYSQLPASSWORD']);
        echo 'Database connection: SUCCESS\n';
    } catch(Exception \$e) {
        echo 'Database connection: FAILED - ' . \$e->getMessage() . '\n';
        echo 'Will continue without database...\n';
    }
    "
else
    echo "Database credentials not available, running without database..."
fi

# Run migrations if database is available
if [ -n "$MYSQLHOST" ] && [ -n "$MYSQLDATABASE" ]; then
    echo "Running database migrations..."
    php artisan migrate --force 2>&1 || echo "Migration skipped or already done"
    
    # Create roles and permissions
    php artisan db:seed --class=RoleAndPermissionSeeder --force 2>&1 || echo "Role seeding skipped"
    
    # Create default users
    php artisan db:seed --class=DefaultUserSeeder --force 2>&1 || echo "User seeding skipped"
fi

# Create simple router for PHP built-in server
cat > public/router.php << 'EOF'
<?php
// Router for PHP built-in server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Handle API routes
if (strpos($uri, '/api.php') === 0 || strpos($uri, '/api/') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/api.php';
    require __DIR__ . '/api.php';
    return true;
}

// Serve existing files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP serve the file
}

// Everything else goes to index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
EOF

# Start PHP server with router
echo "Starting PHP server on port ${PORT:-8080}..."
exec php -S 0.0.0.0:${PORT:-8080} -t public public/router.php