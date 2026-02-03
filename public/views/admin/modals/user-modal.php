<div
  id="userModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold text-green mb-4">Create New User</h3>

    <form id="createUserForm" onsubmit="createUser(event)">
      <!-- Email -->
      <input
        id="userEmail"
        type="email"
        class="w-full mb-3 px-4 py-2 border rounded"
        placeholder="Email"
        required
      />

      <!-- Password -->
      <div class="relative mb-3">
        <input
          id="userPassword"
          type="password"
          class="w-full px-4 py-2 pr-10 border rounded"
          placeholder="Password"
          required
        />
        <button
          type="button"
          onclick="toggleCreatePasswordVisibility()"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
        >
          <span class="material-icons text-sm" id="createPasswordIcon">visibility</span>
        </button>
      </div>

      <!-- Role Dropdown -->
      <select
        id="userRole"
        onchange="toggleStoreNameField()"
        class="w-full mb-3 px-4 py-2 border rounded"
        required
      >
        <option value="">Select Role</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>

      <!-- Store Name (Hidden by default) -->
      <div id="storeNameContainer" class="hidden">
        <input
          id="userStoreName"
          type="text"
          class="w-full mb-3 px-4 py-2 border rounded"
          placeholder="Store Name"
        />
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" onclick="toggleModal('userModal')" class="px-4 py-2 border rounded">
          Cancel
        </button>
        <button type="submit" class="bg-gold text-white px-4 py-2 rounded">Create</button>
      </div>
    </form>
  </div>
</div>
