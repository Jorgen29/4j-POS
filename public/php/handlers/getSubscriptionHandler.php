<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get subscription ID
$subscriptionId = isset($_GET['subscription_id']) ? (int)$_GET['subscription_id'] : null;

// Validate input
if (!$subscriptionId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Subscription ID is required']);
    exit;
}

try {
    // Query the database for the subscription
    $query = $mysqli->prepare("SELECT subscription_id, name, duration, price FROM subscriptions WHERE subscription_id = ?");
    $query->bind_param("i", $subscriptionId);
    $query->execute();
    $result = $query->get_result();
    $subscription = $result->fetch_assoc();
    $query->close();

    if ($subscription) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $subscription
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Subscription not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}
?>
