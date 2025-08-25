<?php
// Debug endpoint to check Laravel configuration
header('Content-Type: application/json');

// Check if Laravel is loaded
if (!file_exists('../vendor/autoload.php')) {
    die(json_encode(['error' => 'Vendor autoload not found']));
}

require '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

// Get environment info
$debug = [
    'status' => 'Laravel Debug Info',
    'php_version' => phpversion(),
    'app_env' => $_ENV['APP_ENV'] ?? 'not set',
    'app_key' => !empty($_ENV['APP_KEY']) ? 'SET (hidden)' : 'NOT SET',
    'app_url' => $_ENV['APP_URL'] ?? 'not set',
    
    // Database config
    'database' => [
        'connection' => $_ENV['DB_CONNECTION'] ?? 'not set',
        'host' => $_ENV['DB_HOST'] ?? 'not set',
        'port' => $_ENV['DB_PORT'] ?? 'not set',
        'database' => $_ENV['DB_DATABASE'] ?? 'not set',
        'username' => !empty($_ENV['DB_USERNAME']) ? 'SET (hidden)' : 'NOT SET',
        'password' => !empty($_ENV['DB_PASSWORD']) ? 'SET (hidden)' : 'NOT SET',
    ],
    
    // Test database connection
    'db_test' => 'Not tested',
    
    // Server info
    'server' => [
        'port' => $_SERVER['SERVER_PORT'] ?? 'unknown',
        'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    ]
];

// Try database connection if PDO exists
if (extension_loaded('pdo_mysql') && !empty($_ENV['DB_HOST'])) {
    try {
        $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}";
        $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        $debug['db_test'] = 'SUCCESS - Database connected!';
    } catch (Exception $e) {
        $debug['db_test'] = 'FAILED - ' . $e->getMessage();
    }
} else {
    $debug['db_test'] = 'PDO MySQL extension not loaded or DB_HOST not set';
}

echo json_encode($debug, JSON_PRETTY_PRINT);