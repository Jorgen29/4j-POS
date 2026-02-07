<?php
// Delete Product Confirmation Modal
?>
<div id="deleteProductModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 modal-overlay" style="transition: none;">
    <div class="bg-white rounded-lg shadow-2xl max-w-sm w-full mx-4 p-8 modal-content" onclick="event.stopPropagation()" style="transition: none;">
        <div class="flex items-center gap-3 mb-4">
            <span class="material-icons text-red-600 text-3xl">delete_outline</span>
            <h3 class="text-2xl font-bold text-gray-800">Delete Product</h3>
        </div>

        <p class="text-gray-600 mb-2">Are you sure you want to delete this product?</p>
        <p id="deleteProductName" class="font-semibold text-gray-800 mb-6 p-3 bg-gray-100 rounded-lg"></p>

        <div id="deleteProductError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
            <span id="deleteProductErrorText"></span>
        </div>

        <input type="hidden" id="deleteProductId">

        <div class="flex gap-3">
            <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="toggleModal('deleteProductModal')">
                Cancel
            </button>
            <button type="button" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold" onclick="confirmDeleteProduct()">
                Delete
            </button>
        </div>
    </div>
</div>
