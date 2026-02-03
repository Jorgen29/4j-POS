<?php
require_once '../db/connection.php';
require_once '../actions/UserActions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

// Validate input
if (!$userId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

// Delete user using UserActions class
$userActions = new UserActions($mysqli);
$result = $userActions->deleteUser($userId);

// Send response
if ($result['status'] === 'success') {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($result);

?>
