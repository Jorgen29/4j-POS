<div
  id="editUserModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold text-green mb-4">Edit User</h3>

    <form id="editUserForm" onsubmit="submitEditUser(event)">
      <input
        type="hidden"
        id="editUserId"
      />

      <!-- Email (View Only) -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Email
        </label>
        <input
          id="editUserEmail"
          type="email"
          class="w-full px-4 py-2 border rounded bg-gray-100 text-gray-600 cursor-not-allowed"
          disabled
        />
      </div>

      <!-- Password -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Password
        </label>
        <div class="relative">
          <input
            id="editUserPassword"
            type="password"
            class="w-full px-4 py-2 pr-10 border rounded focus:ring-2 focus:ring-gold focus:outline-none"
            placeholder="Leave blank to keep current password"
          />
          <button
            type="button"
            onclick="toggleEditPasswordVisibility()"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <span class="material-icons text-sm" id="editPasswordIcon">visibility</span>
          </button>
        </div>
        <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current password</p>
      </div>

      <!-- Role -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Role
        </label>
        <select
          id="editUserRole"
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gold focus:outline-none"
          onchange="toggleEditStoreNameField()"
        >
          <option value="">Select Role</option>
          <option value="0">Admin</option>
          <option value="1">User</option>
        </select>
      </div>

      <!-- Store Name (Hidden by default) -->
      <div id="editStoreNameContainer" class="mb-4 hidden">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Store Name
        </label>
        <input
          id="editUserStoreName"
          type="text"
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gold focus:outline-none"
          placeholder="Store Name"
        />
      </div>

      <!-- Status -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Status
        </label>
        <select
          id="editUserStatus"
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gold focus:outline-none"
        >
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
          Cancel
        </button>
        <button type="submit" class="bg-gold text-white px-4 py-2 rounded-lg hover:bg-gold/90">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
