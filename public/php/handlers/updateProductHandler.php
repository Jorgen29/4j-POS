<?php
require_once '../db/connection.php';
require_once '../actions/ProductActions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : null;
    $productName = isset($_POST['product_name']) ? trim($_POST['product_name']) : null;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
    $storeId = isset($_POST['store_id']) ? intval($_POST['store_id']) : null;

    if (!$productId || !$barcode || !$productName || !$price || $quantity === null || !$storeId) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    try {
        $productActions = new ProductActions($mysqli);
        $result = $productActions->updateProduct($productId, $barcode, '', $productName, $price, $quantity, $storeId);

        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
