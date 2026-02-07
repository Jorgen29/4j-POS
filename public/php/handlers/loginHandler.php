<?php
session_start();

require_once '../db/connection.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../../index.php');
    exit;
}

// Get email and password from POST
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate inputs
if (empty($email) || empty($password)) {
    header('Location: ../../../index.php?error=empty');
    exit;
}

try {
    // Query the database for the user
    $query = $mysqli->prepare("SELECT user_id, email, password, role, status FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();
    $query->close();

    // Check if user exists and password is correct
    if ($user && hash('sha256', $password) === $user['password']) {
        // Check if user is active
        if ($user['status'] != 1) {
            header('Location: ../../../index.php?error=inactive');
            exit;
        }

        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        // Get role name
        $roleName = ($user['role'] == 0) ? 'Admin' : 'User';
        $_SESSION['role_name'] = $roleName;

        // Redirect based on user role
        if ($user['role'] == 0) {
            // Admin user
            header('Location: ../../views/admin/admin-dashboard.php');
        } else {
            // Regular user
            header('Location: ../../views/users/user-dashboard.php');
        }
        exit;
    } else {
        // Invalid credentials
        header('Location: ../../../index.php?error=invalid');
        exit;
    }
} catch (Exception $e) {
    header('Location: ../../../index.php?error=exception');
    exit;
}
?>
