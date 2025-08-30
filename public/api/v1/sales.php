<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Mock sales data
$sales = [
    ['id' => 1, 'transaction_id' => 'TRX-001', 'store' => 'Main Store', 'customer_id' => 1, 'total' => 269.97, 'payment_method' => 'cash', 'status' => 'completed', 'timestamp' => '2024-01-15 10:30:00'],
    ['id' => 2, 'transaction_id' => 'TRX-002', 'store' => 'Branch 1', 'customer_id' => 2, 'total' => 149.98, 'payment_method' => 'credit_card', 'status' => 'completed', 'timestamp' => '2024-01-15 11:45:00'],
    ['id' => 3, 'transaction_id' => 'TRX-003', 'store' => 'Main Store', 'customer_id' => null, 'total' => 89.99, 'payment_method' => 'cash', 'status' => 'completed', 'timestamp' => '2024-01-15 13:20:00'],
];

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$id = null;

// Parse ID from URL if present
if (preg_match('/\/sales\/(\d+)/', $path, $matches)) {
    $id = intval($matches[1]);
}

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single sale
            $sale = array_filter($sales, fn($s) => $s['id'] === $id);
            if ($sale) {
                echo json_encode(['success' => true, 'data' => array_values($sale)[0]]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Sale not found']);
            }
        } elseif (isset($_GET['store'])) {
            // Filter by store
            $store = $_GET['store'];
            $filtered = array_filter($sales, fn($s) => $s['store'] === $store);
            echo json_encode(['success' => true, 'data' => array_values($filtered)]);
        } elseif (isset($_GET['date'])) {
            // Filter by date
            $date = $_GET['date'];
            $filtered = array_filter($sales, fn($s) => strpos($s['timestamp'], $date) === 0);
            echo json_encode(['success' => true, 'data' => array_values($filtered)]);
        } else {
            // Get all sales
            echo json_encode(['success' => true, 'data' => $sales]);
        }
        break;
        
    case 'POST':
        // Create new sale
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Generate transaction ID
        $transaction_id = 'TRX-' . str_pad(count($sales) + 1, 3, '0', STR_PAD_LEFT);
        
        $newSale = [
            'id' => count($sales) + 1,
            'transaction_id' => $transaction_id,
            'store' => $input['store'] ?? 'Main Store',
            'customer_id' => $input['customer_id'] ?? null,
            'items' => $input['items'] ?? [],
            'subtotal' => floatval($input['subtotal'] ?? 0),
            'tax' => floatval($input['tax'] ?? 0),
            'total' => floatval($input['total'] ?? 0),
            'payment_method' => $input['payment_method'] ?? 'cash',
            'status' => 'completed',
            'timestamp' => $input['timestamp'] ?? date('Y-m-d H:i:s')
        ];
        
        echo json_encode([
            'success' => true, 
            'data' => $newSale, 
            'transaction_id' => $transaction_id,
            'message' => 'Sale processed successfully'
        ]);
        break;
        
    case 'PUT':
        // Update sale (for refunds or corrections)
        if ($id) {
            $input = json_decode(file_get_contents('php://input'), true);
            $updated = false;
            foreach ($sales as &$sale) {
                if ($sale['id'] === $id) {
                    $sale = array_merge($sale, $input);
                    $updated = true;
                    echo json_encode(['success' => true, 'data' => $sale, 'message' => 'Sale updated successfully']);
                    break;
                }
            }
            if (!$updated) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Sale not found']);
            }
        }
        break;
        
    case 'DELETE':
        // Void sale
        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Sale voided successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Sale ID required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}