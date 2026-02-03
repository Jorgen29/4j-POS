<div class="max-w-2xl">
  <div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-semibold text-green mb-6">Profile Settings</h2>
    
    <form class="space-y-6">
      <!-- Email (View Only) -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Email Address
        </label>
        <input
          type="email"
          value="admin@smartpos.com"
          disabled
          class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
        />
        <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
      </div>

      <!-- Current Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Current Password
        </label>
        <div class="relative">
          <input
            id="currentPassword"
            type="password"
            placeholder="Enter current password"
            class="w-full px-4 py-2 pr-10 border rounded-lg focus:ring-2 focus:ring-gold focus:outline-none"
          />
          <button
            type="button"
            onclick="toggleProfilePasswordVisibility('currentPassword')"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <span class="material-icons text-sm" id="currentPasswordIcon">visibility</span>
          </button>
        </div>
      </div>

      <!-- New Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          New Password
        </label>
        <div class="relative">
          <input
            id="newPassword"
            type="password"
            placeholder="Enter new password"
            class="w-full px-4 py-2 pr-10 border rounded-lg focus:ring-2 focus:ring-gold focus:outline-none"
          />
          <button
            type="button"
            onclick="toggleProfilePasswordVisibility('newPassword')"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <span class="material-icons text-sm" id="newPasswordIcon">visibility</span>
          </button>
        </div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Confirm Password
        </label>
        <div class="relative">
          <input
            id="confirmPassword"
            type="password"
            placeholder="Confirm new password"
            class="w-full px-4 py-2 pr-10 border rounded-lg focus:ring-2 focus:ring-gold focus:outline-none"
          />
          <button
            type="button"
            onclick="toggleProfilePasswordVisibility('confirmPassword')"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <span class="material-icons text-sm" id="confirmPasswordIcon">visibility</span>
          </button>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end gap-3 pt-4">
        <button
          type="button"
          class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="px-6 py-2 bg-gold text-white rounded-lg hover:bg-gold/90"
        >
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
