<?php
require_once '../db/connection.php';
require_once '../actions/SubscriptionActions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$subscriptionId = isset($_POST['subscription_id']) ? (int)$_POST['subscription_id'] : null;
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : null;
$price = isset($_POST['price']) ? (int)$_POST['price'] : null;

// Validate inputs
if (!$subscriptionId || !$name || !$duration || !$price) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Update subscription using SubscriptionActions class
$subscriptionActions = new SubscriptionActions($mysqli);
$result = $subscriptionActions->updateSubscription($subscriptionId, $name, $duration, $price);

// Send response
if ($result['status'] === 'success') {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($result);

?>
