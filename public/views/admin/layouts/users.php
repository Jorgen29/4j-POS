<?php
require_once __DIR__ . '/../../../php/db/connection.php';
require_once __DIR__ . '/../../../php/actions/UserActions.php';
require_once __DIR__ . '/../../../php/helper.php';

// Get all users
$users = [];
try {
    $userActions = new UserActions($mysqli);
    $usersResult = $userActions->getAllUsers();
    if ($usersResult['status'] === 'success') {
        $users = $usersResult['data'] ?? [];
    }
} catch (Exception $e) {
    error_log('Error fetching users: ' . $e->getMessage());
}
?>

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-green">Users</h2>
      <button
        onclick="toggleModal('userModal')"
        class="bg-gold text-white px-4 py-2 rounded-lg"
      >
        + Create User
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b text-gray-500 bg-gray-50">
          <tr>
            <th class="py-3 px-4 text-left">User ID</th>
            <th class="py-3 px-4 text-left">Email</th>
            <th class="py-3 px-4 text-left">Role</th>
            <th class="py-3 px-4 text-left">Store Name</th>
            <th class="py-3 px-4 text-left">Created Date</th>
            <th class="py-3 px-4 text-left">Status</th>
            <th class="py-3 px-4 text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr class="border-b hover:bg-cream">
              <td colspan="7" class="py-4 px-4 text-center text-gray-500">No users found</td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $user): ?>
              <tr class="border-b hover:bg-cream">
                <td class="py-3 px-4">
                  <span class="text-blue-600 font-medium">U-<?php echo str_pad($user['user_id'], 3, '0', STR_PAD_LEFT); ?></span>
                </td>
                <td class="py-3 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="py-3 px-4"><?php echo getRoleBadge($user['role']); ?></td>
                <td class="py-3 px-4">
                  <?php 
                  echo !empty($user['store_name']) ? htmlspecialchars($user['store_name']) : '<span class="text-gray-400">NA</span>';
                  ?>
                </td>
                <td class="py-3 px-4"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                <td class="py-3 px-4"><?php echo getStatusBadge($user['status']); ?></td>
                <td class="py-3 px-4 text-center">
                  <div class="flex gap-2 justify-center">
                    <button 
                      onclick="editUser(<?php echo $user['user_id']; ?>)"
                      class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs"
                      title="Edit"
                    >
                      Edit
                    </button>
                    <button 
                      onclick="deleteUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['email']); ?>')"
                      class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs"
                      title="Delete"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>


