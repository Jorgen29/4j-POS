<?php
require_once '../../php/db/connection.php';
require_once '../../php/actions/SubscriptionActions.php';

// Ensure subscriptions table exists
$tableCheck = $mysqli->query("SHOW TABLES LIKE 'subscriptions'");
if ($tableCheck->num_rows == 0) {
    $createTable = "
        CREATE TABLE subscriptions (
            subscription_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            duration INT(255) NOT NULL,
            price INT(255) NOT NULL
        )
    ";
    $mysqli->query($createTable);
}

$subscriptionActions = new SubscriptionActions($mysqli);
$subscriptionsResult = $subscriptionActions->getAllSubscriptions();
$subscriptions = $subscriptionsResult['data'] ?? [];
?>

<div class="max-w-12xl">
  <div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-semibold text-green">Available Subscriptions</h2>
      <button
        onclick="toggleModal('subscriptionModal')"
        class="bg-gold text-white px-4 py-2 rounded-lg hover:bg-gold/90"
      >
        + New Subscription
      </button>
    </div>

    <!-- Subscriptions Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b text-gray-600 bg-gray-50">
          <tr>
            <th class="py-3 px-4 text-left font-semibold">Subscription ID</th>
            <th class="py-3 px-4 text-left font-semibold">Subscription Name</th>
            <th class="py-3 px-4 text-left font-semibold">Duration</th>
            <th class="py-3 px-4 text-center font-semibold">Price</th>
            <th class="py-3 px-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($subscriptions) > 0): ?>
            <?php foreach ($subscriptions as $sub): ?>
              <tr class="border-b hover:bg-cream transition">
                <td class="py-3 px-4">
                  <span class="font-semibold text-gray-700">SUB-<?php echo str_pad($sub['subscription_id'], 3, '0', STR_PAD_LEFT); ?></span>
                </td>
                <td class="py-3 px-4">
                  <span class="text-gray-700"><?php echo htmlspecialchars($sub['name']); ?></span>
                </td>
                <td class="py-3 px-4">
                  <span class="text-gray-700"><?php echo $sub['duration']; ?> Month<?php echo $sub['duration'] != 1 ? 's' : ''; ?></span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="font-semibold">PHP <?php echo number_format($sub['price']); ?></span>
                </td>
                <td class="py-3 px-4 text-center">
                  <button onclick="editSubscription(<?php echo $sub['subscription_id']; ?>)" class="text-blue-500 hover:text-blue-700 mr-2">
                    <span class="material-icons text-sm">edit</span>
                  </button>
                  <button onclick="deleteSubscription(<?php echo $sub['subscription_id']; ?>, '<?php echo htmlspecialchars($sub['name']); ?>')" class="text-redsoft hover:text-red-700">
                    <span class="material-icons text-sm">delete</span>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                No subscriptions found. Create one to get started.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

