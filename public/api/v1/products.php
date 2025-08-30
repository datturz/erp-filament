<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Mock products data
$products = [
    ['id' => 1, 'sku' => 'SKU-001', 'name' => 'Classic Jeans', 'category' => 'Denim', 'price' => 89.99, 'stock' => 245, 'size' => 'M', 'color' => 'Blue'],
    ['id' => 2, 'sku' => 'SKU-002', 'name' => 'Slim Fit Chinos', 'category' => 'Casual', 'price' => 69.99, 'stock' => 189, 'size' => 'L', 'color' => 'Khaki'],
    ['id' => 3, 'sku' => 'SKU-003', 'name' => 'Cargo Pants', 'category' => 'Utility', 'price' => 79.99, 'stock' => 12, 'size' => 'S', 'color' => 'Green'],
    ['id' => 4, 'sku' => 'SKU-004', 'name' => 'Formal Trousers', 'category' => 'Formal', 'price' => 99.99, 'stock' => 67, 'size' => 'M', 'color' => 'Black'],
    ['id' => 5, 'sku' => 'SKU-005', 'name' => 'Jogger Pants', 'category' => 'Sports', 'price' => 59.99, 'stock' => 134, 'size' => 'XL', 'color' => 'Gray'],
];

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$id = null;

// Parse ID from URL if present
if (preg_match('/\/products\/(\d+)/', $path, $matches)) {
    $id = intval($matches[1]);
}

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single product
            $product = array_filter($products, fn($p) => $p['id'] === $id);
            if ($product) {
                echo json_encode(['success' => true, 'data' => array_values($product)[0]]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
        } elseif (isset($_GET['q'])) {
            // Search products
            $query = strtolower($_GET['q']);
            $filtered = array_filter($products, function($p) use ($query) {
                return strpos(strtolower($p['name']), $query) !== false || 
                       strpos(strtolower($p['sku']), $query) !== false;
            });
            echo json_encode(['success' => true, 'data' => array_values($filtered)]);
        } else {
            // Get all products
            echo json_encode(['success' => true, 'data' => $products]);
        }
        break;
        
    case 'POST':
        // Create new product
        $input = json_decode(file_get_contents('php://input'), true);
        $newProduct = [
            'id' => count($products) + 1,
            'sku' => $input['sku'] ?? 'SKU-' . str_pad(count($products) + 1, 3, '0', STR_PAD_LEFT),
            'name' => $input['name'] ?? '',
            'category' => $input['category'] ?? '',
            'price' => floatval($input['price'] ?? 0),
            'stock' => intval($input['stock'] ?? 0),
            'size' => $input['size'] ?? '',
            'color' => $input['color'] ?? ''
        ];
        echo json_encode(['success' => true, 'data' => $newProduct, 'message' => 'Product created successfully']);
        break;
        
    case 'PUT':
        // Update product
        if ($id) {
            $input = json_decode(file_get_contents('php://input'), true);
            $updated = false;
            foreach ($products as &$product) {
                if ($product['id'] === $id) {
                    $product = array_merge($product, $input);
                    $updated = true;
                    echo json_encode(['success' => true, 'data' => $product, 'message' => 'Product updated successfully']);
                    break;
                }
            }
            if (!$updated) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
        }
        break;
        
    case 'DELETE':
        // Delete product
        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}