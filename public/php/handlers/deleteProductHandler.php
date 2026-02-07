<?php
require_once '../db/connection.php';
require_once '../actions/ProductActions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;

    if (!$productId) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
        exit;
    }

    try {
        $productActions = new ProductActions($mysqli);
        $result = $productActions->deleteProduct($productId);

        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
