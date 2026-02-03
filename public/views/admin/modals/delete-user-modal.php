<div
  id="deleteUserModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <div class="mb-4">
      <svg class="w-12 h-12 mx-auto text-redsoft" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
    </div>

    <h3 class="text-lg font-semibold text-green mb-2 text-center">Delete User</h3>
    <p class="text-gray-700 mb-6 text-center">
      Are you sure you want to delete this user?
    </p>

    <div class="bg-cream rounded-lg p-4 mb-6 text-sm">
      <p><strong>Email:</strong> <span id="deleteUserEmail"></span></p>
      <p><strong>User ID:</strong> <span id="deleteUserId"></span></p>
    </div>

    <p class="text-xs text-redsoft mb-4 text-center">
      This action cannot be undone.
    </p>

    <div class="flex justify-end gap-2">
      <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
        Cancel
      </button>
      <button type="button" onclick="confirmDeleteUser()" class="bg-redsoft text-white px-4 py-2 rounded-lg hover:bg-redsoft/90">
        Delete User
      </button>
    </div>
  </div>
</div>
