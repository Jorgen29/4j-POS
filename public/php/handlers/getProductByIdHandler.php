<?php
require_once '../db/connection.php';
require_once '../actions/ProductActions.php';

header('Content-Type: application/json');

$productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;

if (!$productId) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit;
}

try {
    $productActions = new ProductActions($mysqli);
    $result = $productActions->getProductById($productId);
    
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
