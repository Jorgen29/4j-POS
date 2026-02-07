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

// Validate input
if (!$subscriptionId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Subscription ID is required']);
    exit;
}

// Delete subscription using SubscriptionActions class
$subscriptionActions = new SubscriptionActions($mysqli);
$result = $subscriptionActions->deleteSubscription($subscriptionId);

// Send response
if ($result['status'] === 'success') {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($result);

?>
