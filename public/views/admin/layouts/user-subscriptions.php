<?php
require_once '../../php/db/connection.php';
require_once '../../php/actions/UserActions.php';

// Get all users
$userActions = new UserActions($mysqli);
$users = $userActions->getAllUsers();
?>

<div class="max-w-12xl">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-green">User Subscriptions</h2>
        </div>

        <!-- Users Subscriptions Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b text-gray-500 bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">User ID</th>
                        <th class="py-3 px-4 text-left font-semibold">Email</th>
                        <th class="py-3 px-4 text-left font-semibold">Store</th>
                        <th class="py-3 px-4 text-left font-semibold">Current Subscription</th>
                        <th class="py-3 px-4 text-left font-semibold">Renewal Date</th>
                        <th class="py-3 px-4 text-left font-semibold">Status</th>
                        <th class="py-3 px-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($users['status'] === 'success' && !empty($users['data'])) {
                        foreach ($users['data'] as $user) {
                            // Only display regular users, not admins
                            if ($user['role'] == 0) {
                                continue;
                            }
                            
                            $userId = $user['user_id'];
                            $email = htmlspecialchars($user['email']);
                            
                            // Get store name
                            $storeQuery = $mysqli->prepare("SELECT store_name FROM stores WHERE store_owner = ? LIMIT 1");
                            $storeQuery->bind_param("i", $userId);
                            $storeQuery->execute();
                            $storeResult = $storeQuery->get_result();
                            $storeRow = $storeResult->fetch_assoc();
                            $storeName = $storeRow ? htmlspecialchars($storeRow['store_name']) : 'N/A';
                            
                            // Get user's current subscription and its status
                            $subQuery = $mysqli->prepare("
                                SELECT s.name, s.duration, s.price, us.status, us.renewal_date FROM user_subscriptions us
                                JOIN subscriptions s ON us.subscription_id = s.subscription_id
                                WHERE us.user_id = ?
                                ORDER BY us.renewal_date DESC
                                LIMIT 1
                            ");
                            $subQuery->bind_param("i", $userId);
                            $subQuery->execute();
                            $subResult = $subQuery->get_result();
                            $userSubscription = $subResult->fetch_assoc();
                            
                            // Set subscription display
                            $subscriptionDisplay = $userSubscription 
                                ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">' . htmlspecialchars($userSubscription['name']) . '</span>'
                                : '<span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-medium">No Subscription</span>';
                            
                            // Format renewal date
                            $renewalDateDisplay = $userSubscription && $userSubscription['renewal_date']
                                ? date('M d, Y', strtotime($userSubscription['renewal_date']))
                                : '-';
                            
                            // Set subscription status (from user_subscriptions table)
                            $subscriptionStatus = ($userSubscription && $userSubscription['status'] == 1) 
                                ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Active</span>'
                                : '<span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">Inactive</span>';
                            
                            echo "
                            <tr class='border-b hover:bg-cream transition'>
                                <td class='py-3 px-4 font-semibold'>U-" . str_pad($userId, 3, '0', STR_PAD_LEFT) . "</td>
                                <td class='py-3 px-4'>$email</td>
                                <td class='py-3 px-4'>$storeName</td>
                                <td class='py-3 px-4'>
                                    $subscriptionDisplay
                                </td>
                                <td class='py-3 px-4'>$renewalDateDisplay</td>
                                <td class='py-3 px-4'>
                                    $subscriptionStatus
                                </td>
                                <td class='py-3 px-4 text-center'>";
                            $addButtonDisabled = ($userSubscription && $userSubscription['status'] == 1) ? "opacity-50 cursor-not-allowed" : "";
                            $addButtonAttr = ($userSubscription && $userSubscription['status'] == 1) ? "disabled" : "";
                            $showResetButton = ($userSubscription && $userSubscription['status'] == 1) ? true : false;
                            $showRenewButton = ($userSubscription && $userSubscription['status'] == 1) ? true : false;
                            echo "
                                    <div class='flex gap-2 justify-center'>";
                            if ($showRenewButton) {
                                echo "
                                        <button onclick=\"renewUserSubscription($userId, '$email')\" class='bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs' style='background-color: #10b981 !important;' title='Renew'>
                                            Renew
                                        </button>";
                            }
                            echo "
                                        <button onclick=\"addUserSubscription($userId, '$email')\" class='bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs $addButtonDisabled' $addButtonAttr title='Add'>
                                            Add
                                        </button>";
                            if ($showResetButton) {
                                echo "
                                        <button onclick=\"resetUserSubscription($userId, '$email')\" class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs' title='Reset'>
                                            Reset
                                        </button>";
                            }
                            echo "
                                    </div>
                                </td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='py-4 px-4 text-center text-gray-500'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
