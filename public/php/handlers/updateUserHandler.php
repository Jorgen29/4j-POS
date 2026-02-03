<?php
require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
$role = isset($_POST['role']) ? (int)$_POST['role'] : null;
$status = isset($_POST['status']) ? (int)$_POST['status'] : null;
$password = isset($_POST['password']) ? trim($_POST['password']) : null;
$storeName = isset($_POST['storeName']) ? trim($_POST['storeName']) : null;

// Validate inputs
if (!$userId || $role === null || $status === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Update user (with or without password)
if (!empty($password)) {
    // Hash new password with SHA256
    $hashedPassword = hash('sha256', $password);
    $updateUser = $mysqli->prepare("UPDATE users SET role = ?, status = ?, password = ? WHERE user_id = ?");
    $updateUser->bind_param("iisi", $role, $status, $hashedPassword, $userId);
} else {
    // Don't change password
    $updateUser = $mysqli->prepare("UPDATE users SET role = ?, status = ? WHERE user_id = ?");
    $updateUser->bind_param("iii", $role, $status, $userId);
}

if (!$updateUser->execute()) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Error updating user: ' . $updateUser->error]);
    $updateUser->close();
    exit;
}

$updateUser->close();

// Update store name if role is user
if ($role == 1 && !empty($storeName)) {
    // Check if store exists
    $checkStore = $mysqli->prepare("SELECT store_id FROM stores WHERE store_owner = ?");
    $checkStore->bind_param("i", $userId);
    $checkStore->execute();
    $checkStore->store_result();

    if ($checkStore->num_rows > 0) {
        // Update existing store
        $updateStore = $mysqli->prepare("UPDATE stores SET store_name = ? WHERE store_owner = ?");
        $updateStore->bind_param("si", $storeName, $userId);
        $updateStore->execute();
        $updateStore->close();
    } else {
        // Create new store
        $insertStore = $mysqli->prepare("INSERT INTO stores (store_name, store_owner) VALUES (?, ?)");
        $insertStore->bind_param("si", $storeName, $userId);
        $insertStore->execute();
        $insertStore->close();
    }
    $checkStore->close();
}

echo json_encode([
    'status' => 'success',
    'message' => 'User updated successfully'
]);

?>
