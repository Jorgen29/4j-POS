<?php
require_once '../db/connection.php';
require_once '../actions/SubscriptionActions.php';

header('Content-Type: application/json');

// Ensure subscriptions table exists
$tableCheck = $mysqli->query("SHOW TABLES LIKE 'subscriptions'");
if ($tableCheck->num_rows == 0) {
    $createTable = "
        CREATE TABLE subscriptions (
            subscription_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            duration INT(255) NOT NULL,
            price INT(255) NOT NULL
        )
    ";
    if (!$mysqli->query($createTable)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error creating subscriptions table: ' . $mysqli->error]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : null;
$price = isset($_POST['price']) ? (int)$_POST['price'] : null;

// Validate inputs
if (!$name || !$duration || !$price) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Create subscription using SubscriptionActions class
$subscriptionActions = new SubscriptionActions($mysqli);
$result = $subscriptionActions->createSubscription($name, $duration, $price);

// Send response
if ($result['status'] === 'success') {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($result);

?>
