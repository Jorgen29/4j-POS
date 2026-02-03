<?php
require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get user ID
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

// Validate input
if (!$userId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

// Fetch user data
$query = "
    SELECT 
        u.user_id,
        u.email,
        u.role,
        u.status,
        s.store_name
    FROM users u
    LEFT JOIN stores s ON u.user_id = s.store_owner
    WHERE u.user_id = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

echo json_encode([
    'status' => 'success',
    'data' => $user
]);

?>
