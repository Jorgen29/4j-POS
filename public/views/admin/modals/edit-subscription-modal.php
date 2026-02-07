<div
  id="editSubscriptionModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md">
    <h3 class="text-lg font-semibold text-green mb-4">
      Edit Subscription
    </h3>

    <!-- Error Message -->
    <div id="editSubscriptionError" class="hidden bg-redsoft/10 text-redsoft text-sm px-4 py-2 rounded mb-4">
      <p id="editSubscriptionErrorText"></p>
    </div>

    <form id="editSubscriptionForm">
      <input id="editSubscriptionId" type="hidden" />

      <!-- Subscription Name -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Subscription Name <span class="text-red-500">*</span>
        </label>
        <input
          id="editSubscriptionName"
          type="text"
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gold focus:outline-none"
          placeholder="e.g., Basic, Standard, Premium"
          required
        />
      </div>

      <!-- Duration (in months) -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Duration (months) <span class="text-red-500">*</span>
        </label>
        <input
          id="editSubscriptionDuration"
          type="number"
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gold focus:outline-none"
          placeholder="e.g., 1, 3, 6, 12"
          min="1"
          required
        />
      </div>

      <!-- Price -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Price (PHP) <span class="text-red-500">*</span>
        </label>
        <input
          id="editSubscriptionPrice"
          type="number"
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gold focus:outline-none"
          placeholder="e.g., 499, 1299"
          min="1"
          required
        />
      </div>

      <div class="flex justify-end gap-2">
        <button
          type="button"
          onclick="toggleModal('editSubscriptionModal')"
          class="px-4 py-2 border rounded hover:bg-gray-50"
        >
          Cancel
        </button>
        <button type="button" onclick="submitEditSubscription()" class="bg-gold text-white px-4 py-2 rounded hover:bg-gold/90">
          Update Subscription
        </button>
      </div>
    </form>
  </div>
</div>
