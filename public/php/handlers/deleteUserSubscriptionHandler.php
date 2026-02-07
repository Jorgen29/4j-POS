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
        // Delete the active subscription for the user
        $query = $mysqli->prepare("
            DELETE FROM user_subscriptions
            WHERE user_id = ? AND status = 1
        ");
        $query->bind_param("i", $userId);
        $query->execute();
        
        if ($query->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Subscription deleted successfully'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No active subscription found to delete']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
