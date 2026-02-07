<?php
session_start();
require_once '../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get current user ID and role from session
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'] ?? null;
        
        // If admin (role = 0), get all stores
        if ($userRole == 0) {
            $query = $mysqli->prepare("
                SELECT store_id, store_name 
                FROM stores 
                ORDER BY store_name ASC
            ");
            $query->execute();
        } else {
            // For regular users, get only stores they are subscribed to
            $query = $mysqli->prepare("
                SELECT DISTINCT s.store_id, s.store_name 
                FROM stores s
                INNER JOIN user_subscriptions us ON s.store_id = us.store_id
                WHERE us.user_id = ?
                ORDER BY s.store_name ASC
            ");
            $query->bind_param("i", $userId);
            $query->execute();
        }
        
        $result = $query->get_result();
        $stores = [];

        while ($row = $result->fetch_assoc()) {
            $stores[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $stores
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
