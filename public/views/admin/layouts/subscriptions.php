<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold text-green">Subscriptions</h2>
      <button
        onclick="toggleModal('subscriptionModal')"
        class="bg-gold text-white px-4 py-2 rounded-lg"
      >
        + Add / Renew Subscription
      </button>
    </div>

    <table class="w-full text-sm">
      <thead class="border-b text-gray-500">
        <tr>
          <th class="py-2 text-left">User</th>
          <th>Store</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-b hover:bg-cream">
          <td class="py-2">juan@store.com</td>
          <td>Juan Store</td>
          <td>Feb 05, 2026</td>
          <td>Mar 05, 2026</td>
          <td class="text-green font-semibold">Active</td>
        </tr>
      </tbody>
    </table>
  </div>

