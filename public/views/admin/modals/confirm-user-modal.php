<div
  id="confirmUserModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold text-green mb-4">Confirm Account Creation</h3>
    <p class="text-gray-700 mb-6">
      Are you sure you want to create this account with the following details?
    </p>

    <div id="confirmDetails" class="bg-cream rounded-lg p-4 mb-6 text-sm">
      <p><strong>Email:</strong> <span id="confirmEmail"></span></p>
      <p><strong>Role:</strong> <span id="confirmRole"></span></p>
      <p id="confirmStoreContainer" class="hidden">
        <strong>Store Name:</strong> <span id="confirmStore"></span>
      </p>
    </div>

    <div class="flex justify-end gap-2">
      <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
        Cancel
      </button>
      <button type="button" onclick="confirmCreateUser()" class="bg-gold text-white px-4 py-2 rounded-lg hover:bg-gold/90">
        Confirm & Create
      </button>
    </div>
  </div>
</div>
