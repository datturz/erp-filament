<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'api.php', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001', 
        'https://*.vercel.app',
        'https://erp-filament.vercel.app',
        'https://erp-filament-*.vercel.app',
        env('FRONTEND_URL', 'http://localhost:3000')
    ],

    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
        '#^http://localhost:\d+$#'
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];