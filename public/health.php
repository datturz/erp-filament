<?php
// Health check for Railway deployment
header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'Pants ERP System',
    'php_version' => phpversion(),
    'environment' => $_ENV['APP_ENV'] ?? 'unknown',
    'checks' => []
];

// Check if Laravel can be loaded
try {
    if (file_exists('../vendor/autoload.php')) {
        require_once '../vendor/autoload.php';
        $health['checks']['laravel_autoload'] = 'ok';
        
        if (file_exists('../bootstrap/app.php')) {
            $health['checks']['laravel_bootstrap'] = 'ok';
        } else {
            $health['checks']['laravel_bootstrap'] = 'missing';
        }
    } else {
        $health['checks']['laravel_autoload'] = 'missing';
        $health['status'] = 'error';
    }
} catch (Exception $e) {
    $health['checks']['laravel_autoload'] = 'error: ' . $e->getMessage();
    $health['status'] = 'error';
}

// Check storage permissions
$storagePath = '../storage';
if (is_dir($storagePath) && is_writable($storagePath)) {
    $health['checks']['storage_writable'] = 'ok';
} else {
    $health['checks']['storage_writable'] = 'not writable';
    $health['status'] = 'warning';
}

// Check database connection if configured
if (!empty($_ENV['MYSQLHOST'])) {
    try {
        $dsn = "mysql:host={$_ENV['MYSQLHOST']};port={$_ENV['MYSQLPORT']};dbname={$_ENV['MYSQLDATABASE']}";
        $pdo = new PDO($dsn, $_ENV['MYSQLUSER'], $_ENV['MYSQLPASSWORD']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query('SELECT 1');
        $health['checks']['database'] = 'connected';
    } catch (Exception $e) {
        $health['checks']['database'] = 'failed: ' . $e->getMessage();
        $health['status'] = 'warning'; // Don't fail health check for DB issues
    }
} else {
    $health['checks']['database'] = 'not configured';
}

// Set HTTP response code based on health status
switch ($health['status']) {
    case 'ok':
        http_response_code(200);
        break;
    case 'warning':
        http_response_code(200); // Still healthy for Railway
        break;
    case 'error':
        http_response_code(503);
        break;
}

echo json_encode($health, JSON_PRETTY_PRINT);