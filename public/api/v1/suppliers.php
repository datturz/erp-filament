<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Mock suppliers data
$suppliers = [
    ['id' => 1, 'company' => 'Denim World Ltd', 'contactPerson' => 'Robert Brown', 'email' => 'robert@denimworld.com', 'phone' => '+1234567890', 'categories' => 'Denim, Jeans', 'paymentTerms' => 'Net 30', 'status' => 'active', 'address' => '123 Textile Ave'],
    ['id' => 2, 'company' => 'Fashion Fabrics Inc', 'contactPerson' => 'Sarah Wilson', 'email' => 'sarah@fashionfabrics.com', 'phone' => '+0987654321', 'categories' => 'Casual, Formal', 'paymentTerms' => 'Net 60', 'status' => 'active', 'address' => '456 Fashion St'],
    ['id' => 3, 'company' => 'Quality Textiles', 'contactPerson' => 'James Miller', 'email' => 'james@qualitytextiles.com', 'phone' => '+1122334455', 'categories' => 'All Categories', 'paymentTerms' => 'COD', 'status' => 'active', 'address' => '789 Industry Blvd'],
    ['id' => 4, 'company' => 'SportWear Supplies', 'contactPerson' => 'Emma Davis', 'email' => 'emma@sportwear.com', 'phone' => '+5544332211', 'categories' => 'Sports, Athletic', 'paymentTerms' => 'Prepaid', 'status' => 'pending', 'address' => '321 Sports Way'],
    ['id' => 5, 'company' => 'Premium Materials Co', 'contactPerson' => 'Michael Chen', 'email' => 'michael@premium.com', 'phone' => '+9988776655', 'categories' => 'Premium, Luxury', 'paymentTerms' => 'Net 30', 'status' => 'active', 'address' => '555 Luxury Lane'],
];

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$id = null;

// Parse ID from URL if present
if (preg_match('/\/suppliers\/(\d+)/', $path, $matches)) {
    $id = intval($matches[1]);
}

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single supplier
            $supplier = array_filter($suppliers, fn($s) => $s['id'] === $id);
            if ($supplier) {
                echo json_encode(['success' => true, 'data' => array_values($supplier)[0]]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Supplier not found']);
            }
        } elseif (isset($_GET['status'])) {
            // Filter by status
            $status = $_GET['status'];
            $filtered = array_filter($suppliers, fn($s) => $s['status'] === $status);
            echo json_encode(['success' => true, 'data' => array_values($filtered)]);
        } else {
            // Get all suppliers
            echo json_encode(['success' => true, 'data' => $suppliers]);
        }
        break;
        
    case 'POST':
        // Create new supplier
        $input = json_decode(file_get_contents('php://input'), true);
        $newSupplier = [
            'id' => count($suppliers) + 1,
            'company' => $input['company'] ?? '',
            'contactPerson' => $input['contactPerson'] ?? '',
            'email' => $input['email'] ?? '',
            'phone' => $input['phone'] ?? '',
            'address' => $input['address'] ?? '',
            'categories' => $input['categories'] ?? '',
            'paymentTerms' => $input['paymentTerms'] ?? 'Net 30',
            'status' => $input['status'] ?? 'active'
        ];
        echo json_encode(['success' => true, 'data' => $newSupplier, 'message' => 'Supplier created successfully']);
        break;
        
    case 'PUT':
        // Update supplier
        if ($id) {
            $input = json_decode(file_get_contents('php://input'), true);
            $updated = false;
            foreach ($suppliers as &$supplier) {
                if ($supplier['id'] === $id) {
                    $supplier = array_merge($supplier, $input);
                    $updated = true;
                    echo json_encode(['success' => true, 'data' => $supplier, 'message' => 'Supplier updated successfully']);
                    break;
                }
            }
            if (!$updated) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Supplier not found']);
            }
        }
        break;
        
    case 'DELETE':
        // Delete supplier
        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Supplier deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Supplier ID required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}