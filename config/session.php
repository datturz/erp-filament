<?php

return [
    'driver' => 'file', // Force file driver, ignore env
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => null,
    'secure' => false, // Set to true in production with HTTPS
    'http_only' => true,
    'same_site' => 'lax',
];