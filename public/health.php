<?php
// Simple health check for Railway
http_response_code(200);
header('Content-Type: application/json');

echo json_encode([
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'Laravel API'
]);