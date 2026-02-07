<div id="renewSubscriptionModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 modal-overlay" style="transition: none;">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 p-8 modal-content" onclick="event.stopPropagation()" style="transition: none; display: flex; flex-direction: column; max-height: 90vh;">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-icons text-green-600 text-3xl">refresh</span>
            <h3 class="text-2xl font-bold text-gray-800">Renew Subscription</h3>
        </div>

        <div class="mb-4">
            <p class="text-sm text-gray-600">User: <span id="renewUserEmail" class="font-semibold text-gray-800"></span></p>
            <input type="hidden" id="renewUserId">
        </div>

        <form id="renewSubscriptionForm" class="flex-1 overflow-y-auto pr-2">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Subscription</label>
                <select id="renewSubscriptionSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="">-- Loading --</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
                <input type="text" id="renewDurationDisplay" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                <input type="text" id="renewPriceDisplay" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
            </div>

            <div id="renewSubscriptionError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                <span id="renewSubscriptionErrorText"></span>
            </div>
        </form>

        <div class="flex gap-3 pt-4 mt-auto flex-shrink-0">
            <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="toggleModal('renewSubscriptionModal')">
                Cancel
            </button>
            <button type="button" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold" style="background-color: #16a34a !important;" onclick="submitRenewSubscription()">
                Renew
            </button>
        </div>
    </div>
</div>
