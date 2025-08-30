<?php
// Simple login endpoint
header('Content-Type: application/json');

// Set CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

// For demo, accept these credentials
$validUsers = [
    'admin@pantsrp.com' => 'admin123',
    'store@pantsrp.com' => 'store123',
    'warehouse@pantsrp.com' => 'warehouse123'
];

// Check credentials
if (isset($validUsers[$email]) && $validUsers[$email] === $password) {
    // Generate simple token (for demo)
    $token = base64_encode($email . ':' . time());
    
    echo json_encode([
        'success' => true,
        'user' => [
            'email' => $email,
            'name' => ucfirst(explode('@', $email)[0]) . ' User',
            'role' => strpos($email, 'admin') !== false ? 'admin' : 
                     (strpos($email, 'store') !== false ? 'store_manager' : 'warehouse_worker')
        ],
        'token' => $token,
        'message' => 'Login successful'
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid credentials'
    ]);
}