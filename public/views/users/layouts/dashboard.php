<?php
require_once '../../php/db/connection.php';

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo '<p class="text-red-600">User ID not found in session.</p>';
    exit;
}

// Get user's store information
$storeQuery = $mysqli->prepare("SELECT store_id, store_name FROM stores WHERE store_owner = ? LIMIT 1");
$storeQuery->bind_param("i", $userId);
$storeQuery->execute();
$storeResult = $storeQuery->get_result();
$storeRow = $storeResult->fetch_assoc();
$storeId = $storeRow['store_id'] ?? null;
$storeName = $storeRow['store_name'] ?? 'N/A';

// Get user's subscription status
$subQuery = $mysqli->prepare("
  SELECT us.status, us.renewal_date, s.name, s.duration
  FROM user_subscriptions us
  JOIN subscriptions s ON us.subscription_id = s.subscription_id
  WHERE us.user_id = ?
  ORDER BY us.renewal_date DESC
  LIMIT 1
");
$subQuery->bind_param("i", $userId);
$subQuery->execute();
$subResult = $subQuery->get_result();
$subscription = $subResult->fetch_assoc();

$subscriptionStatus = $subscription && $subscription['status'] == 1 ? 'Active' : 'Inactive';
$subscriptionName = $subscription['name'] ?? 'No Subscription';
$renewalDate = $subscription['renewal_date'] ? date('M d, Y', strtotime($subscription['renewal_date'])) : '-';

// Count products if store exists
$productCount = 0;
if ($storeId) {
    $productQuery = $mysqli->prepare("SELECT COUNT(*) as total FROM products WHERE store_id = ?");
    $productQuery->bind_param("i", $storeId);
    $productQuery->execute();
    $productResult = $productQuery->get_result();
    $productRow = $productResult->fetch_assoc();
    $productCount = $productRow['total'] ?? 0;

    // Get total inventory value
    $inventoryQuery = $mysqli->prepare("
      SELECT SUM(quantity * price) as total_value
      FROM products
      WHERE store_id = ?
    ");
    $inventoryQuery->bind_param("i", $storeId);
    $inventoryQuery->execute();
    $inventoryResult = $inventoryQuery->get_result();
    $inventoryRow = $inventoryResult->fetch_assoc();
    $totalInventoryValue = $inventoryRow['total_value'] ?? 0;

    // Get total quantity
    $quantityQuery = $mysqli->prepare("
      SELECT SUM(quantity) as total_quantity
      FROM products
      WHERE store_id = ?
    ");
    $quantityQuery->bind_param("i", $storeId);
    $quantityQuery->execute();
    $quantityResult = $quantityQuery->get_result();
    $quantityRow = $quantityResult->fetch_assoc();
    $totalQuantity = $quantityRow['total_quantity'] ?? 0;

    // Get today's sales from transactions table
    $todaySalesQuery = $mysqli->prepare("
      SELECT SUM(total_amount) as total_sales
      FROM transactions
      WHERE store_id = ? AND DATE(transaction_date) = CURDATE()
    ");
    $todaySalesQuery->bind_param("i", $storeId);
    $todaySalesQuery->execute();
    $todaySalesResult = $todaySalesQuery->get_result();
    $todaySalesRow = $todaySalesResult->fetch_assoc();
    $todaySales = $todaySalesRow['total_sales'] ?? 0;

    // Get total sales from transactions table
    $totalSalesQuery = $mysqli->prepare("
      SELECT SUM(total_amount) as total_sales
      FROM transactions
      WHERE store_id = ?
    ");
    $totalSalesQuery->bind_param("i", $storeId);
    $totalSalesQuery->execute();
    $totalSalesResult = $totalSalesQuery->get_result();
    $totalSalesRow = $totalSalesResult->fetch_assoc();
    $totalSales = $totalSalesRow['total_sales'] ?? 0;
} else {
    $totalInventoryValue = 0;
    $totalQuantity = 0;
    $todaySales = 0;
    $totalSales = 0;
}
?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Store Name</p>
      <span class="text-lg sm:text-xl font-bold text-green"><?php echo htmlspecialchars($storeName); ?></span>
    </div>
  </div>

  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Subscription Status</p>
      <span class="text-base sm:text-lg font-bold <?php echo $subscriptionStatus === 'Active' ? 'text-green' : 'text-redsoft'; ?>">
        <?php echo $subscriptionStatus; ?>
      </span>
      <p class="text-xs text-gray-500 mt-2"><?php echo htmlspecialchars($subscriptionName); ?></p>
    </div>
  </div>

  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Renewal Date</p>
      <span class="text-lg sm:text-xl font-bold text-gold"><?php echo $renewalDate; ?></span>
    </div>
  </div>

  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Total Products</p>
      <span class="text-lg sm:text-xl font-bold text-green"><?php echo $productCount; ?></span>
    </div>
  </div>

  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Total Inventory</p>
      <span class="text-lg sm:text-xl font-bold text-gold">₱<?php echo number_format($totalInventoryValue, 2); ?></span>
    </div>
  </div>

  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Total Quantity</p>
      <span class="text-lg sm:text-xl font-bold text-green"><?php echo $totalQuantity; ?></span>
    </div>
  </div>

  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Today's Sales</p>
      <span class="text-lg sm:text-xl font-bold text-gold">₱<?php echo number_format($todaySales, 2); ?></span>
    </div>
  </div>

  <div class="stat-card text-center">
    <div class="flex flex-col items-center justify-center">
      <p class="text-gray-600 text-sm mb-2">Total Sales</p>
      <span class="text-lg sm:text-xl font-bold text-green">₱<?php echo number_format($totalSales, 2); ?></span>
    </div>
  </div>
</div>

<!-- Additional Info Section -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
  <div class="bg-white rounded-xl shadow p-4 sm:p-6">
    <h3 class="text-base sm:text-lg font-semibold text-green mb-4 text-center">Account Information</h3>
    <div class="space-y-3">
      <div class="flex flex-col sm:flex-row sm:justify-between">
        <span class="text-gray-600 text-sm sm:text-base">Email:</span>
        <span class="font-medium text-sm sm:text-base"><?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></span>
      </div>
      <div class="flex flex-col sm:flex-row sm:justify-between">
        <span class="text-gray-600 text-sm sm:text-base">User ID:</span>
        <span class="font-medium text-sm sm:text-base">U-<?php echo str_pad($_SESSION['user_id'] ?? 0, 3, '0', STR_PAD_LEFT); ?></span>
      </div>
      <div class="flex flex-col sm:flex-row sm:justify-between">
        <span class="text-gray-600 text-sm sm:text-base">Store ID:</span>
        <span class="font-medium text-sm sm:text-base"><?php echo $storeId ? 'S-' . str_pad($storeId, 3, '0', STR_PAD_LEFT) : 'N/A'; ?></span>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow p-4 sm:p-6">
    <h3 class="text-base sm:text-lg font-semibold text-green mb-4 text-center">Subscription Details</h3>
    <div class="space-y-3">
      <div class="flex flex-col sm:flex-row sm:justify-between">
        <span class="text-gray-600 text-sm sm:text-base">Plan:</span>
        <span class="font-medium text-sm sm:text-base"><?php echo htmlspecialchars($subscriptionName); ?></span>
      </div>
      <div class="flex flex-col sm:flex-row sm:justify-between">
        <span class="text-gray-600 text-sm sm:text-base">Status:</span>
        <span class="font-medium px-2 py-1 rounded text-xs inline-block <?php echo $subscriptionStatus === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
          <?php echo $subscriptionStatus; ?>
        </span>
      </div>
      <div class="flex flex-col sm:flex-row sm:justify-between">
        <span class="text-gray-600 text-sm sm:text-base">Next Renewal:</span>
        <span class="font-medium text-sm sm:text-base"><?php echo $renewalDate; ?></span>
      </div>
    </div>
  </div>
</div>
