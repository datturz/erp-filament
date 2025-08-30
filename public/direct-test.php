<?php
// Direct Laravel test without going through index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Load Laravel
    require '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    // Make kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Create a fake request to "/"
    $request = Illuminate\Http\Request::create('/', 'GET');
    
    // Handle the request
    $response = $kernel->handle($request);
    
    // Send the response
    $response->send();
    
    // Terminate
    $kernel->terminate($request, $response);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Laravel initialization failed',
        'message' => $e->getMessage(),
        'file' => str_replace('/var/www/', '', $e->getFile()),
        'line' => $e->getLine(),
        'class' => get_class($e)
    ], JSON_PRETTY_PRINT);
}