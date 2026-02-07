<div id="assignSubscriptionModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 modal-overlay" style="transition: none;">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 p-8 modal-content" onclick="event.stopPropagation()" style="transition: none; display: flex; flex-direction: column; max-height: 90vh;">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-icons text-blue-600 text-3xl">add_circle</span>
            <h3 class="text-2xl font-bold text-gray-800">Assign Subscription</h3>
        </div>

        <div class="mb-4">
            <p class="text-sm text-gray-600">User: <span id="assignUserEmail" class="font-semibold text-gray-800"></span></p>
            <input type="hidden" id="assignUserId">
        </div>

        <form id="assignSubscriptionForm" class="flex-1 overflow-y-auto pr-2">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Subscription</label>
                <select id="assignSubscriptionSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Choose Subscription --</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
                <input type="text" id="assignDurationDisplay" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                <input type="text" id="assignPriceDisplay" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
            </div>

            <div id="assignSubscriptionError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                <span id="assignSubscriptionErrorText"></span>
            </div>
        </form>

        <div class="flex gap-3 pt-4 mt-auto flex-shrink-0">
            <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="toggleModal('assignSubscriptionModal')">
                Cancel
            </button>
            <button type="button" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold" onclick="submitAssignSubscription()">
                Assign
            </button>
        </div>
    </div>
</div>
