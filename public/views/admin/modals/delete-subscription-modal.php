<div
  id="deleteSubscriptionModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md text-center">
    <div class="mb-4">
      <span class="material-icons text-5xl text-redsoft">warning</span>
    </div>

    <h3 class="text-lg font-semibold text-gray-800 mb-2">
      Delete Subscription
    </h3>

    <p class="text-gray-600 mb-6">
      Are you sure you want to delete this subscription? This action cannot be undone.
    </p>

    <div class="bg-cream rounded-lg p-4 mb-6 text-sm text-left">
      <p class="mb-2">
        <strong>Subscription ID:</strong>
        <span id="deleteSubscriptionId"></span>
      </p>
      <p>
        <strong>Subscription Name:</strong>
        <span id="deleteSubscriptionName"></span>
      </p>
    </div>

    <div class="flex gap-3 justify-end">
      <button
        onclick="toggleModal('deleteSubscriptionModal')"
        class="px-4 py-2 border rounded hover:bg-gray-50"
      >
        Cancel
      </button>
      <button
        onclick="confirmDeleteSubscription()"
        class="px-4 py-2 bg-redsoft text-white rounded hover:bg-red-700"
      >
        Delete
      </button>
    </div>
  </div>
</div>
