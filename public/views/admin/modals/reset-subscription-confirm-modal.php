<div id="resetSubscriptionConfirmModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 modal-overlay" style="transition: none;">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 p-8 modal-content" onclick="event.stopPropagation()" style="transition: none;">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-icons text-red-600 text-3xl">warning</span>
            <h3 class="text-2xl font-bold text-gray-800">Reset Subscription</h3>
        </div>

        <p class="text-gray-700 mb-4">
            Are you sure you want to reset the subscription for <strong><span id="resetConfirmEmail" class="text-gray-900"></span></strong>?
        </p>

        <div class="bg-red-50 rounded-lg p-4 mb-6 text-sm">
            <p class="text-red-800">
                <strong>Warning:</strong> This action will remove their current active subscription. They will need to add a new subscription to continue using the service.
            </p>
        </div>

        <input type="hidden" id="resetConfirmUserId">
        <input type="hidden" id="resetConfirmUserEmail">

        <div class="flex gap-3">
            <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="toggleModal('resetSubscriptionConfirmModal')">
                Cancel
            </button>
            <button type="button" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold" onclick="confirmResetSubscription()">
                Reset
            </button>
        </div>
    </div>
</div>
