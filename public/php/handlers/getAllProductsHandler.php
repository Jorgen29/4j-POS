<?php
header('Content-Type: application/json');

require_once '../db/connection.php';
require_once '../actions/ProductActions.php';

try {
    $productActions = new ProductActions($mysqli);
    $response = $productActions->getAllProducts();
    
    // ProductActions already wraps the response, so just echo it
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

