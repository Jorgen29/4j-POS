<?php
require_once 'db/connection.php';
require_once 'actions/SubscriptionActions.php';

// Test database connection
echo "Testing database connection...<br>";

// Check if subscriptions table exists
$result = $mysqli->query("SHOW TABLES LIKE 'subscriptions'");
if ($result->num_rows > 0) {
    echo "✓ Subscriptions table exists<br>";
} else {
    echo "✗ Subscriptions table does NOT exist<br>";
    echo "Creating subscriptions table...<br>";
    
    $createTable = "
        CREATE TABLE subscriptions (
            subscription_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            duration INT(255) NOT NULL,
            price INT(255) NOT NULL
        )
    ";
    
    if ($mysqli->query($createTable)) {
        echo "✓ Subscriptions table created successfully<br>";
    } else {
        echo "✗ Error creating table: " . $mysqli->error . "<br>";
    }
}

// Test creating a subscription
echo "<br>Testing subscription creation...<br>";
$subscriptionActions = new SubscriptionActions($mysqli);
$result = $subscriptionActions->createSubscription("Test Subscription", 1, 500);
echo "Result: " . json_encode($result) . "<br>";

// Get all subscriptions
echo "<br>All subscriptions:<br>";
$allResult = $subscriptionActions->getAllSubscriptions();
echo "<pre>";
print_r($allResult);
echo "</pre>";
?>
