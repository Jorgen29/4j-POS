<?php
require_once '../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
        exit;
    }

    try {
        // Fetch the current active subscription for the user
        $query = $mysqli->prepare("
            SELECT 
                s.subscription_id,
                s.name,
                s.duration,
                s.price
            FROM user_subscriptions us
            JOIN subscriptions s ON us.subscription_id = s.subscription_id
            WHERE us.user_id = ? AND us.status = 1
            ORDER BY us.renewal_date DESC
            LIMIT 1
        ");
        $query->bind_param("i", $userId);
        $query->execute();
        $result = $query->get_result();
        
        if ($result->num_rows > 0) {
            $subscription = $result->fetch_assoc();
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'subscription_id' => $subscription['subscription_id'],
                    'name' => $subscription['name'],
                    'duration' => $subscription['duration'],
                    'price' => $subscription['price']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No active subscription found']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
