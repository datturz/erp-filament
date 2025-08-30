<?php
// API Router - handles /api/v1/* endpoints

// Set CORS headers for Vercel
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
if (strpos($origin, 'vercel.app') !== false || strpos($origin, 'localhost') !== false) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse request path
$requestPath = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestPath, PHP_URL_PATH);
$requestPath = str_replace('/api.php', '', $requestPath);

// Route to endpoints
switch ($requestPath) {
    case '/v1/auth/login':
    case '/api/v1/auth/login':
        // Forward to login handler
        require __DIR__ . '/api/v1/auth/login.php';
        exit;
        
    case '/v1/test':
    case '/api/v1/test':
        $response = [
            'status' => 'success',
            'message' => 'API connection successful',
            'timestamp' => date('Y-m-d H:i:s'),
            'server' => 'Railway Laravel API',
            'version' => 'v1',
            'cors_enabled' => true,
            'endpoints' => [
                'test' => '/api.php/v1/test',
                'health' => '/api.php/v1/health',
                'auth' => '/api.php/v1/auth/login',
                'products' => '/api.php/v1/products'
            ]
        ];
        break;
        
    case '/v1/health':
    case '/api/v1/health':
        $response = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'service' => 'Pants ERP API'
        ];
        break;
        
    case '':
    case '/':
        // Get database connection status
        $dbHost = getenv('DB_HOST');
        $dbDatabase = getenv('DB_DATABASE');
        $dbConnected = false;
        
        if ($dbHost && $dbDatabase) {
            try {
                $pdo = new PDO(
                    "mysql:host=$dbHost;dbname=$dbDatabase",
                    getenv('DB_USERNAME'),
                    getenv('DB_PASSWORD')
                );
                $dbConnected = true;
            } catch (Exception $e) {
                $dbConnected = false;
            }
        }
        
        $response = [
            'name' => 'Pants ERP API',
            'version' => 'v1',
            'status' => 'operational',
            'database' => $dbConnected ? 'connected' : 'disconnected',
            'timestamp' => date('Y-m-d H:i:s'),
            'endpoints' => [
                'test' => '/api.php/v1/test',
                'health' => '/api.php/v1/health'
            ]
        ];
        break;
        
    default:
        http_response_code(404);
        $response = [
            'error' => 'Endpoint not found',
            'path' => $requestPath,
            'method' => $_SERVER['REQUEST_METHOD'],
            'available_endpoints' => [
                '/api.php/v1/test',
                '/api.php/v1/health'
            ]
        ];
}

echo json_encode($response, JSON_PRETTY_PRINT);