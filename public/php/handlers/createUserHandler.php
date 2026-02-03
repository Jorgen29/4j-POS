<?php
require_once '../db/connection.php';
require_once '../actions/UserActions.php';
require_once '../helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$role = isset($_POST['role']) ? (int)$_POST['role'] : null;
$storeName = isset($_POST['storeName']) ? trim($_POST['storeName']) : null;

// Validate inputs
if (empty($email) || empty($password) || $role === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Create user using UserActions class
$userActions = new UserActions($mysqli);
$result = $userActions->createUser($email, $password, $role, $storeName);

// Send response
if ($result['status'] === 'success') {
    http_response_code(201);
} else {
    http_response_code(400);
}

echo json_encode($result);

?>
