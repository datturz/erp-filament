<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Mock customers data
$customers = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+1234567890', 'type' => 'vip', 'totalPurchases' => 5280, 'store' => 'Main Store', 'address' => '123 Main St'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '+0987654321', 'type' => 'regular', 'totalPurchases' => 1250, 'store' => 'Branch 1', 'address' => '456 Oak Ave'],
    ['id' => 3, 'name' => 'ABC Retail', 'email' => 'contact@abcretail.com', 'phone' => '+1122334455', 'type' => 'wholesale', 'totalPurchases' => 15680, 'store' => 'Main Store', 'address' => '789 Business Blvd'],
    ['id' => 4, 'name' => 'Mike Johnson', 'email' => 'mike@example.com', 'phone' => '+5544332211', 'type' => 'regular', 'totalPurchases' => 890, 'store' => 'Branch 2', 'address' => '321 Pine St'],
    ['id' => 5, 'name' => 'XYZ Corp', 'email' => 'orders@xyzcorp.com', 'phone' => '+9988776655', 'type' => 'wholesale', 'totalPurchases' => 28450, 'store' => 'Main Store', 'address' => '555 Corporate Way'],
];

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$id = null;

// Parse ID from URL if present
if (preg_match('/\/customers\/(\d+)/', $path, $matches)) {
    $id = intval($matches[1]);
}

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single customer
            $customer = array_filter($customers, fn($c) => $c['id'] === $id);
            if ($customer) {
                echo json_encode(['success' => true, 'data' => array_values($customer)[0]]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
            }
        } elseif (isset($_GET['type'])) {
            // Filter by type
            $type = $_GET['type'];
            $filtered = array_filter($customers, fn($c) => $c['type'] === $type);
            echo json_encode(['success' => true, 'data' => array_values($filtered)]);
        } else {
            // Get all customers
            echo json_encode(['success' => true, 'data' => $customers]);
        }
        break;
        
    case 'POST':
        // Create new customer
        $input = json_decode(file_get_contents('php://input'), true);
        $newCustomer = [
            'id' => count($customers) + 1,
            'name' => $input['name'] ?? '',
            'email' => $input['email'] ?? '',
            'phone' => $input['phone'] ?? '',
            'address' => $input['address'] ?? '',
            'type' => $input['type'] ?? 'regular',
            'store' => $input['store'] ?? 'Main Store',
            'totalPurchases' => floatval($input['totalPurchases'] ?? 0)
        ];
        echo json_encode(['success' => true, 'data' => $newCustomer, 'message' => 'Customer created successfully']);
        break;
        
    case 'PUT':
        // Update customer
        if ($id) {
            $input = json_decode(file_get_contents('php://input'), true);
            $updated = false;
            foreach ($customers as &$customer) {
                if ($customer['id'] === $id) {
                    $customer = array_merge($customer, $input);
                    $updated = true;
                    echo json_encode(['success' => true, 'data' => $customer, 'message' => 'Customer updated successfully']);
                    break;
                }
            }
            if (!$updated) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
            }
        }
        break;
        
    case 'DELETE':
        // Delete customer
        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Customer deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Customer ID required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}