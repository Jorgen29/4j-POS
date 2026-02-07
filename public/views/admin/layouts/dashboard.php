<?php
require_once '../../php/db/connection.php';

// Get total users (non-admin only)
$userQuery = $mysqli->prepare("SELECT COUNT(*) as total FROM users WHERE role != 0");
$userQuery->execute();
$userResult = $userQuery->get_result();
$userRow = $userResult->fetch_assoc();
$totalUsers = $userRow['total'] ?? 0;

// Get active subscriptions count
$activeSubQuery = $mysqli->prepare("SELECT COUNT(*) as total FROM user_subscriptions WHERE status = 1");
$activeSubQuery->execute();
$activeSubResult = $activeSubQuery->get_result();
$activeSubRow = $activeSubResult->fetch_assoc();
$activeSubscriptions = $activeSubRow['total'] ?? 0;

// Get total revenue
$totalRevenueQuery = $mysqli->prepare("
  SELECT SUM(s.price) as total 
  FROM user_subscriptions us
  JOIN subscriptions s ON us.subscription_id = s.subscription_id
");
$totalRevenueQuery->execute();
$totalRevenueResult = $totalRevenueQuery->get_result();
$totalRevenueRow = $totalRevenueResult->fetch_assoc();
$totalRevenue = $totalRevenueRow['total'] ?? 0;

// Get today's revenue
$todayRevenueQuery = $mysqli->prepare("
  SELECT SUM(s.price) as total 
  FROM user_subscriptions us
  JOIN subscriptions s ON us.subscription_id = s.subscription_id
  WHERE DATE(us.created_at) = CURDATE()
");
$todayRevenueQuery->execute();
$todayRevenueResult = $todayRevenueQuery->get_result();
$todayRevenueRow = $todayRevenueResult->fetch_assoc();
$todayRevenue = $todayRevenueRow['total'] ?? 0;
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
  <div class="stat-card">Total Users <span><?php echo $totalUsers; ?></span></div>
  <div class="stat-card">Active Subscriptions <span><?php echo $activeSubscriptions; ?></span></div>
  <div class="stat-card">
    Today's Revenue <span class="text-gold">₱<?php echo number_format($todayRevenue, 2); ?></span>
  </div>
  <div class="stat-card">Total Revenue <span class="text-gold">₱<?php echo number_format($totalRevenue, 2); ?></span></div>
</div>
