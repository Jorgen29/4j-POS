<?php
require_once __DIR__ . '/../db/connection.php';

class UserActions {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    /**
     * Create a new user
     * @param string $email User email
     * @param string $password User password
     * @param int $role User role (0 = admin, 1 = user)
     * @param string $storeName Store name (only if role is 1 'user')
     * @return array Response array with status and message
     */
    public function createUser($email, $password, $role, $storeName = null) {
        try {
            // Validate inputs
            if (empty($email) || empty($password) || $role === null) {
                return [
                    'status' => 'error',
                    'message' => 'Email, password, and role are required'
                ];
            }

            // Validate role (0 = admin, 1 = user)
            if (!in_array($role, [0, 1])) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid role. Use 0 for admin or 1 for user'
                ];
            }

            // Check if email already exists
            $checkEmail = $this->mysqli->prepare("SELECT user_id FROM users WHERE email = ?");
            $checkEmail->bind_param("s", $email);
            $checkEmail->execute();
            $checkEmail->store_result();

            if ($checkEmail->num_rows > 0) {
                $checkEmail->close();
                return [
                    'status' => 'error',
                    'message' => 'Email already exists'
                ];
            }
            $checkEmail->close();

            // Hash password with SHA256
            $hashedPassword = hash('sha256', $password);

            // Set status to active (1) by default
            $status = 1;

            // Insert user
            $insertUser = $this->mysqli->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, ?)");
            $insertUser->bind_param("ssii", $email, $hashedPassword, $role, $status);

            if (!$insertUser->execute()) {
                return [
                    'status' => 'error',
                    'message' => 'Error creating user: ' . $insertUser->error
                ];
            }

            $userId = $insertUser->insert_id;
            $insertUser->close();

            // If role is 1 (user), create store
            if ($role === 1) {
                if (empty($storeName)) {
                    // Rollback user creation if store name is missing
                    $this->mysqli->query("DELETE FROM users WHERE user_id = $userId");
                    return [
                        'status' => 'error',
                        'message' => 'Store name is required for user role'
                    ];
                }

                $insertStore = $this->mysqli->prepare("INSERT INTO stores (store_name, store_owner) VALUES (?, ?)");
                $insertStore->bind_param("si", $storeName, $userId);

                if (!$insertStore->execute()) {
                    // Rollback user creation if store creation fails
                    $this->mysqli->query("DELETE FROM users WHERE user_id = $userId");
                    return [
                        'status' => 'error',
                        'message' => 'Error creating store: ' . $insertStore->error
                    ];
                }

                $insertStore->close();
            }

            return [
                'status' => 'success',
                'message' => 'User created successfully',
                'user_id' => $userId
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all users with their store information
     * @return array Array of users
     */
    public function getAllUsers() {
        try {
            $query = "
                SELECT 
                    u.user_id,
                    u.email,
                    u.role,
                    s.store_name,
                    u.created_at,
                    u.status
                FROM users u
                LEFT JOIN stores s ON u.user_id = s.store_owner
                ORDER BY u.created_at DESC
            ";

            $result = $this->mysqli->query($query);

            if (!$result) {
                return [
                    'status' => 'error',
                    'message' => 'Error fetching users: ' . $this->mysqli->error,
                    'data' => []
                ];
            }

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            return [
                'status' => 'success',
                'message' => 'Users fetched successfully',
                'data' => $users
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Delete a user
     * @param int $userId User ID to delete
     * @return array Response array with status and message
     */
    public function deleteUser($userId) {
        try {
            // First, get user's role to check if we need to delete associated store
            $getUser = $this->mysqli->prepare("SELECT role FROM users WHERE user_id = ?");
            $getUser->bind_param("i", $userId);
            $getUser->execute();
            $result = $getUser->get_result();
            $user = $result->fetch_assoc();
            $getUser->close();

            if ($user && $user['role'] == 1) {
                // If user is a regular user (role = 1), delete their store
                $deleteStore = $this->mysqli->prepare("DELETE FROM stores WHERE store_owner = ?");
                $deleteStore->bind_param("i", $userId);
                if (!$deleteStore->execute()) {
                    return [
                        'status' => 'error',
                        'message' => 'Error deleting store: ' . $deleteStore->error
                    ];
                }
                $deleteStore->close();
            }

            // Delete user
            $deleteUser = $this->mysqli->prepare("DELETE FROM users WHERE user_id = ?");
            $deleteUser->bind_param("i", $userId);

            if (!$deleteUser->execute()) {
                return [
                    'status' => 'error',
                    'message' => 'Error deleting user: ' . $deleteUser->error
                ];
            }

            $deleteUser->close();

            return [
                'status' => 'success',
                'message' => 'User deleted successfully'
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}

?>
