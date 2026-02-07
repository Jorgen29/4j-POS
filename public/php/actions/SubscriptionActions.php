<?php

class SubscriptionActions {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    // Get all subscriptions
    public function getAllSubscriptions() {
        try {
            $query = $this->mysqli->prepare("SELECT subscription_id, name, duration, price FROM subscriptions ORDER BY subscription_id DESC");
            $query->execute();
            $result = $query->get_result();
            $subscriptions = [];
            
            while ($row = $result->fetch_assoc()) {
                $subscriptions[] = $row;
            }
            
            $query->close();
            
            return [
                'status' => 'success',
                'data' => $subscriptions
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error fetching subscriptions: ' . $e->getMessage()
            ];
        }
    }

    // Create new subscription
    public function createSubscription($name, $duration, $price) {
        try {
            // Validate inputs
            if (empty($name) || empty($duration) || empty($price)) {
                return [
                    'status' => 'error',
                    'message' => 'All fields are required'
                ];
            }

            // Insert subscription
            $query = $this->mysqli->prepare("INSERT INTO subscriptions (name, duration, price) VALUES (?, ?, ?)");
            $query->bind_param("sii", $name, $duration, $price);

            if (!$query->execute()) {
                return [
                    'status' => 'error',
                    'message' => 'Error creating subscription: ' . $query->error
                ];
            }

            $subscription_id = $this->mysqli->insert_id;
            $query->close();

            return [
                'status' => 'success',
                'message' => 'Subscription created successfully',
                'subscription_id' => $subscription_id
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    // Delete subscription
    public function deleteSubscription($subscriptionId) {
        try {
            $query = $this->mysqli->prepare("DELETE FROM subscriptions WHERE subscription_id = ?");
            $query->bind_param("i", $subscriptionId);

            if (!$query->execute()) {
                return [
                    'status' => 'error',
                    'message' => 'Error deleting subscription: ' . $query->error
                ];
            }

            $query->close();

            return [
                'status' => 'success',
                'message' => 'Subscription deleted successfully'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    // Update subscription
    public function updateSubscription($subscriptionId, $name, $duration, $price) {
        try {
            // Validate inputs
            if (empty($name) || empty($duration) || empty($price)) {
                return [
                    'status' => 'error',
                    'message' => 'All fields are required'
                ];
            }

            $query = $this->mysqli->prepare("UPDATE subscriptions SET name = ?, duration = ?, price = ? WHERE subscription_id = ?");
            $query->bind_param("siii", $name, $duration, $price, $subscriptionId);

            if (!$query->execute()) {
                return [
                    'status' => 'error',
                    'message' => 'Error updating subscription: ' . $query->error
                ];
            }

            $query->close();

            return [
                'status' => 'success',
                'message' => 'Subscription updated successfully'
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
