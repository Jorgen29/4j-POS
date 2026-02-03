<div
  id="subscriptionModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold text-green mb-4">
      Add / Renew Subscription
    </h3>

    <select class="w-full mb-3 px-4 py-2 border rounded">
      <option>Select User</option>
      <option>juan@store.com</option>
      <option>maria@mart.com</option>
    </select>

    <input type="date" class="w-full mb-3 px-4 py-2 border rounded" />

    <div class="flex justify-end gap-2">
      <button onclick="toggleModal('subscriptionModal')" class="px-4 py-2">
        Cancel
      </button>
      <button class="bg-gold text-white px-4 py-2 rounded">Save</button>
    </div>
  </div>
</div>
