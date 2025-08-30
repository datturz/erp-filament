<?php

// Set CORS headers manually for this test endpoint
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Return test response
echo json_encode([
    'status' => 'success',
    'message' => 'API connection successful',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => 'Railway Laravel API',
    'version' => 'v1',
    'endpoints' => [
        'auth' => '/api/v1/auth/login',
        'products' => '/api/v1/products',
        'inventory' => '/api/v1/inventory',
        'transfers' => '/api/v1/transfers'
    ]
]);