<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit;
}

$storeId = intval($input['store_id'] ?? 0);
$totalAmount = floatval($input['total_amount'] ?? 0);
$paymentMethod = trim($input['payment_method'] ?? 'cash');
$items = $input['items'] ?? [];

if (!$storeId || !$totalAmount || empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

try {
    // Start transaction
    $mysqli->begin_transaction();

    // Insert into transactions table
    $transactionQuery = $mysqli->prepare("
        INSERT INTO transactions (store_id, total_amount, transaction_date)
        VALUES (?, ?, NOW())
    ");
    $transactionQuery->bind_param("id", $storeId, $totalAmount);
    
    if (!$transactionQuery->execute()) {
        throw new Exception('Failed to create transaction: ' . $transactionQuery->error);
    }

    $orderId = $mysqli->insert_id;

    // Insert each item into sales table
    $salesQuery = $mysqli->prepare("
        INSERT INTO sales (order_id, product_id, quantity, price, sale_date)
        VALUES (?, ?, ?, ?, NOW())
    ");

    // Prepare update stock query
    $updateStockQuery = $mysqli->prepare("
        UPDATE products SET quantity = quantity - ? WHERE product_id = ?
    ");

    foreach ($items as $item) {
        $productId = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);

        $salesQuery->bind_param("iiid", $orderId, $productId, $quantity, $price);
        
        if (!$salesQuery->execute()) {
            throw new Exception('Failed to add item to sales: ' . $salesQuery->error);
        }

        // Update product stock
        $updateStockQuery->bind_param("ii", $quantity, $productId);
        if (!$updateStockQuery->execute()) {
            throw new Exception('Failed to update product stock: ' . $updateStockQuery->error);
        }
    }

    // Commit transaction
    $mysqli->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Transaction processed successfully',
        'data' => [
            'order_id' => $orderId,
            'total_amount' => $totalAmount,
            'items_count' => count($items)
        ]
    ]);

} catch (Exception $e) {
    // Rollback on error
    if ($mysqli) {
        $mysqli->rollback();
    }

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$mysqli->close();
?>
