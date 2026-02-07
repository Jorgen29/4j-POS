<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

try {
    // Create subscriptions table if it doesn't exist
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
        $mysqli->query($createTable);
    }

    // Fetch all subscriptions
    $result = $mysqli->query("SELECT subscription_id, name, duration, price FROM subscriptions ORDER BY subscription_id DESC");
    
    $subscriptions = [];
    while ($row = $result->fetch_assoc()) {
        $subscriptions[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $subscriptions
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
