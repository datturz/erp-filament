<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Mock categories data
$categories = [
    ['id' => 1, 'name' => 'Denim', 'description' => 'Denim jeans and jackets', 'parent' => '', 'productCount' => 45, 'status' => 'active'],
    ['id' => 2, 'name' => 'Casual', 'description' => 'Casual wear pants', 'parent' => '', 'productCount' => 32, 'status' => 'active'],
    ['id' => 3, 'name' => 'Formal', 'description' => 'Formal trousers and suits', 'parent' => '', 'productCount' => 28, 'status' => 'active'],
    ['id' => 4, 'name' => 'Sports', 'description' => 'Sports and athletic wear', 'parent' => '', 'productCount' => 15, 'status' => 'active'],
    ['id' => 5, 'name' => 'Utility', 'description' => 'Cargo and utility pants', 'parent' => '', 'productCount' => 12, 'status' => 'active'],
];

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$id = null;

// Parse ID from URL if present
if (preg_match('/\/categories\/(\d+)/', $path, $matches)) {
    $id = intval($matches[1]);
}

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single category
            $category = array_filter($categories, fn($c) => $c['id'] === $id);
            if ($category) {
                echo json_encode(['success' => true, 'data' => array_values($category)[0]]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Category not found']);
            }
        } else {
            // Get all categories
            echo json_encode(['success' => true, 'data' => $categories]);
        }
        break;
        
    case 'POST':
        // Create new category
        $input = json_decode(file_get_contents('php://input'), true);
        $newCategory = [
            'id' => count($categories) + 1,
            'name' => $input['name'] ?? '',
            'description' => $input['description'] ?? '',
            'parent' => $input['parent'] ?? '',
            'productCount' => intval($input['productCount'] ?? 0),
            'status' => $input['status'] ?? 'active'
        ];
        echo json_encode(['success' => true, 'data' => $newCategory, 'message' => 'Category created successfully']);
        break;
        
    case 'PUT':
        // Update category
        if ($id) {
            $input = json_decode(file_get_contents('php://input'), true);
            $updated = false;
            foreach ($categories as &$category) {
                if ($category['id'] === $id) {
                    $category = array_merge($category, $input);
                    $updated = true;
                    echo json_encode(['success' => true, 'data' => $category, 'message' => 'Category updated successfully']);
                    break;
                }
            }
            if (!$updated) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Category not found']);
            }
        }
        break;
        
    case 'DELETE':
        // Delete category
        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category ID required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}