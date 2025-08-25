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
if (extension_loaded('pdo_mysql')) {
    if (!empty($_ENV['DB_HOST'])) {
        try {
            $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}";
            $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Test query
            $stmt = $pdo->query('SELECT 1');
            $debug['db_test'] = 'SUCCESS - Database connected and responsive!';
            $debug['db_can_query'] = true;
        } catch (PDOException $e) {
            $debug['db_test'] = 'FAILED - ' . $e->getMessage();
            $debug['db_error_code'] = $e->getCode();
        }
    } else {
        $debug['db_test'] = 'DB_HOST not set in environment';
        $debug['db_env_check'] = [
            'DB_HOST' => !empty($_ENV['DB_HOST']) ? 'SET' : 'NOT SET',
            'DB_PORT' => !empty($_ENV['DB_PORT']) ? 'SET' : 'NOT SET',
            'DB_DATABASE' => !empty($_ENV['DB_DATABASE']) ? 'SET' : 'NOT SET',
            'DB_USERNAME' => !empty($_ENV['DB_USERNAME']) ? 'SET' : 'NOT SET',
            'DB_PASSWORD' => !empty($_ENV['DB_PASSWORD']) ? 'SET' : 'NOT SET',
        ];
    }
} else {
    $debug['db_test'] = 'PDO MySQL extension not loaded';
    $debug['loaded_extensions'] = get_loaded_extensions();
}

echo json_encode($debug, JSON_PRETTY_PRINT);