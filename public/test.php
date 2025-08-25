<?php
// Simple test endpoint to verify service is running
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'php_version' => phpversion(),
    'server_time' => date('Y-m-d H:i:s'),
    'environment' => $_SERVER['APP_ENV'] ?? 'unknown'
]);