<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $subscriptionId = isset($_POST['subscription_id']) ? intval($_POST['subscription_id']) : null;

    if (!$userId || !$subscriptionId) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    try {
        // Create user_subscriptions table if it doesn't exist
        $tableCheck = $mysqli->query("SHOW TABLES LIKE 'user_subscriptions'");
        if ($tableCheck->num_rows == 0) {
            $createTable = "
                CREATE TABLE user_subscriptions (
                    user_subscription_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) UNSIGNED NOT NULL,
                    subscription_id INT(11) UNSIGNED NOT NULL,
                    renewal_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                    status INT(1) DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
                    FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id) ON DELETE CASCADE
                )
            ";
            $mysqli->query($createTable);
        }

        // Check if user already has an active subscription - if so, update it
        $checkQuery = $mysqli->prepare("SELECT user_subscription_id, renewal_date FROM user_subscriptions WHERE user_id = ? AND status = 1");
        $checkQuery->bind_param("i", $userId);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        // Get subscription duration for calculating renewal date
        $durationQuery = $mysqli->prepare("SELECT duration FROM subscriptions WHERE subscription_id = ?");
        $durationQuery->bind_param("i", $subscriptionId);
        $durationQuery->execute();
        $durationResult = $durationQuery->get_result();
        $durationRow = $durationResult->fetch_assoc();
        $duration = $durationRow ? $durationRow['duration'] : 1;
        $newSubscriptionDays = $duration * 30;

        if ($checkResult->num_rows > 0) {
            // User has an active subscription, calculate renewal based on remaining days
            $existingRow = $checkResult->fetch_assoc();
            $currentRenewalDate = strtotime($existingRow['renewal_date']);
            $today = strtotime(date('Y-m-d'));
            
            // Calculate remaining days
            $remainingDays = ceil(($currentRenewalDate - $today) / 86400);
            $remainingDays = max(0, $remainingDays); // Don't go below 0
            
            // Total days = remaining days + new subscription duration
            $totalDays = $remainingDays + $newSubscriptionDays;
            
            // Update existing subscription with calculated renewal date
            $updateQuery = $mysqli->prepare("UPDATE user_subscriptions SET subscription_id = ?, renewal_date = DATE_ADD(CURDATE(), INTERVAL ? DAY) WHERE user_id = ? AND status = 1");
            $updateQuery->bind_param("iii", $subscriptionId, $totalDays, $userId);
            $updateQuery->execute();
        } else {
            // Insert new subscription with calculated renewal date (just the new subscription duration)
            $insertQuery = $mysqli->prepare("INSERT INTO user_subscriptions (user_id, subscription_id, renewal_date, status) VALUES (?, ?, DATE_ADD(CURDATE(), INTERVAL ? DAY), 1)");
            $insertQuery->bind_param("iii", $userId, $subscriptionId, $newSubscriptionDays);
            $insertQuery->execute();
        }

        // Get subscription details for response
        $subQuery = $mysqli->prepare("SELECT name, duration, price FROM subscriptions WHERE subscription_id = ?");
        $subQuery->bind_param("i", $subscriptionId);
        $subQuery->execute();
        $subResult = $subQuery->get_result();
        $subscription = $subResult->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'message' => 'Subscription renewed successfully',
            'data' => [
                'user_id' => $userId,
                'subscription_id' => $subscriptionId,
                'subscription_name' => $subscription['name'],
                'duration' => $subscription['duration'],
                'price' => $subscription['price']
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
