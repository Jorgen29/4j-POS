<?php
require_once 'db/connection.php';

echo "<h1>Database Connection Test</h1>";

if ($mysqli) {
    echo "<p style='color: green;'><strong>✓ Database connection successful!</strong></p>";
    echo "<p>Database: " . DB_NAME . "</p>";
    
    // Test if users table exists
    $result = $mysqli->query("SELECT COUNT(*) as user_count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total users in database: " . $row['user_count'] . "</p>";
    } else {
        echo "<p style='color: red;'><strong>✗ Error querying users table: " . $mysqli->error . "</strong></p>";
    }
} else {
    echo "<p style='color: red;'><strong>✗ Database connection failed!</strong></p>";
}
?>
