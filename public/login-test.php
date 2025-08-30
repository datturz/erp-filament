<?php
// Direct login test endpoint
header('Content-Type: application/json');

// Set CORS headers for Vercel
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
if (strpos($origin, 'vercel.app') !== false || strpos($origin, 'localhost') !== false) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Test GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'status' => 'Login endpoint ready',
        'method' => 'Use POST with email and password',
        'test_users' => [
            'admin@pantsrp.com / admin123',
            'store@pantsrp.com / store123',
            'warehouse@pantsrp.com / warehouse123'
        ]
    ]);
    exit;
}

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    // Valid users
    $validUsers = [
        'admin@pantsrp.com' => 'admin123',
        'store@pantsrp.com' => 'store123',
        'warehouse@pantsrp.com' => 'warehouse123'
    ];
    
    if (isset($validUsers[$email]) && $validUsers[$email] === $password) {
        echo json_encode([
            'success' => true,
            'user' => [
                'email' => $email,
                'name' => ucfirst(explode('@', $email)[0]) . ' User',
                'role' => strpos($email, 'admin') !== false ? 'admin' : 
                         (strpos($email, 'store') !== false ? 'store_manager' : 'warehouse_worker')
            ],
            'token' => base64_encode($email . ':' . time()),
            'message' => 'Login successful'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid credentials'
        ]);
    }
}