<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'Laravel ERP System',
        'message' => 'Pants ERP System is running!',
        'timestamp' => now()->toDateTimeString(),
        'endpoints' => [
            'api' => '/api.php',
            'debug' => '/debug.php',
            'health' => '/health.php',
            'env-check' => '/env-check.php'
        ]
    ]);
});

Route::get('/admin', function () {
    return response()->json([
        'message' => 'Filament Admin will be available here',
        'path' => '/admin',
        'status' => 'Coming soon'
    ]);
});