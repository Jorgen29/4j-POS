<div
  id="successModal"
  class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>
  <div class="bg-white rounded-xl p-6 w-full max-w-md text-center">
    <div class="mb-4">
      <svg class="w-16 h-16 mx-auto text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
    </div>

    <h3 id="successHeader" class="text-lg font-semibold text-green mb-2">Account Created Successfully</h3>
    <p id="successMessage" class="text-gray-700 mb-6">
      The new account has been created successfully!
    </p>

    <div id="successDetails" class="bg-cream rounded-lg p-4 mb-6 text-sm text-left">
      <p><strong>Email:</strong> <span id="successEmail"></span></p>
      <p><strong>Role:</strong> <span id="successRole"></span></p>
      <p id="successStoreContainer" class="hidden">
        <strong>Store Name:</strong> <span id="successStore"></span>
      </p>
      <p><strong>User ID:</strong> <span id="successUserId"></span></p>
    </div>

    <button type="button" onclick="closeSuccessModal()" class="w-full bg-gold text-white px-4 py-2 rounded-lg hover:bg-gold/90">
      Close
    </button>
  </div>
</div>
