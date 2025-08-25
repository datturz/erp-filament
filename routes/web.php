<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    try {
        // Test database connection
        $dbStatus = 'Connected';
        DB::connection()->getPdo();
    } catch (Exception $e) {
        $dbStatus = 'Failed: ' . $e->getMessage();
    }

    return response()->json([
        'status' => 'Laravel ERP System',
        'message' => 'Pants ERP System is running!',
        'database' => $dbStatus,
        'timestamp' => now()->toDateTimeString(),
        'endpoints' => [
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