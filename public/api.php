<?php
// Simple API endpoint without Laravel framework
header('Content-Type: application/json');

// Get database connection from environment
$dbHost = getenv('DB_HOST');
$dbDatabase = getenv('DB_DATABASE');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');

$response = [
    'status' => 'Pants ERP System API',
    'message' => 'System is running (Simple Mode)',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'endpoints' => [
        '/api.php' => 'This API endpoint',
        '/debug.php' => 'Debug information',
        '/health.php' => 'Health check',
        '/env-check.php' => 'Environment variables'
    ],
    'database' => [
        'connected' => false,
        'message' => 'Not tested'
    ]
];

// Test database connection
if ($dbHost && $dbDatabase && $dbUsername) {
    try {
        $pdo = new PDO(
            "mysql:host=$dbHost;dbname=$dbDatabase",
            $dbUsername,
            $dbPassword
        );
        $response['database']['connected'] = true;
        $response['database']['message'] = 'Database connected successfully';
    } catch (Exception $e) {
        $response['database']['message'] = 'Connection failed: ' . $e->getMessage();
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);