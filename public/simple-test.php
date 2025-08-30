<?php
// Simple test to bypass Laravel and check basic functionality
header('Content-Type: application/json');

// Test 1: Basic PHP works
$response = [
    'status' => 'Simple Test',
    'php_works' => true,
    'timestamp' => date('Y-m-d H:i:s')
];

// Test 2: Can we load Laravel bootstrap?
try {
    require '../vendor/autoload.php';
    $response['autoload'] = 'SUCCESS';
    
    // Test 3: Can we load the app?
    $app = require_once '../bootstrap/app.php';
    $response['bootstrap'] = 'SUCCESS';
    
    // Test 4: Can we get the kernel?
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response['kernel'] = 'SUCCESS';
    
    // Test 5: What routes are registered?
    $router = $app->make('router');
    $routes = collect($router->getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'action' => $route->getActionName()
        ];
    })->all();
    $response['routes_count'] = count($routes);
    $response['routes'] = array_slice($routes, 0, 5); // First 5 routes only
    
} catch (Exception $e) {
    $response['error'] = [
        'message' => $e->getMessage(),
        'file' => str_replace('/var/www/', '', $e->getFile()),
        'line' => $e->getLine(),
        'trace' => array_slice($e->getTrace(), 0, 3)
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);