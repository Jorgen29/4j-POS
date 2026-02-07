<?php
require_once '../db/connection.php';
require_once '../actions/ProductActions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode = isset($_POST['barcode']) ? trim($_POST['barcode']) : null;
    $productName = isset($_POST['product_name']) ? trim($_POST['product_name']) : null;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
    $storeId = isset($_POST['store_id']) ? intval($_POST['store_id']) : null;

    // SKU is no longer required - it will be auto-generated from store owner
    if (!$barcode || !$productName || !$price || $quantity === null || !$storeId) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    try {
        // Ensure products table exists
        $tableCheck = $mysqli->query("SHOW TABLES LIKE 'products'");
        if ($tableCheck->num_rows == 0) {
            $createTable = "
                CREATE TABLE products (
                    product_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    barcode VARCHAR(100) NOT NULL,
                    sku VARCHAR(100),
                    product_name VARCHAR(255) NOT NULL,
                    price DECIMAL(10, 2) NOT NULL,
                    quantity INT(11) DEFAULT 0,
                    store_id BIGINT(20) UNSIGNED,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (store_id) REFERENCES stores(store_id) ON DELETE CASCADE
                )
            ";
            $mysqli->query($createTable);
        }

        $productActions = new ProductActions($mysqli);
        
        // Create product with empty SKU (will be generated below)
        $result = $productActions->createProduct($barcode, '', $productName, $price, $quantity, $storeId);

        if ($result['status'] === 'success') {
            $productId = $result['data']['product_id'];
            
            // Generate and update SKU using store owner ID from stores table
            $skuResult = $productActions->generateAndUpdateSKU($productId, $storeId);
            
            if ($skuResult['status'] === 'success') {
                echo json_encode($skuResult);
            } else {
                // Product created but SKU generation failed
                echo json_encode([
                    'status' => 'warning',
                    'message' => 'Product created but SKU generation failed',
                    'data' => $result['data']
                ]);
            }
        } else {
            echo json_encode($result);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

?>
