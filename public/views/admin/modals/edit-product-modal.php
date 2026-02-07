<?php
// Edit Product Modal
?>
<div id="editProductModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 modal-overlay" style="transition: none;">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 p-8 modal-content" onclick="event.stopPropagation()" style="transition: none; display: flex; flex-direction: column; max-height: 90vh;">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-icons text-blue-600 text-3xl">edit</span>
            <h3 class="text-2xl font-bold text-gray-800">Edit Product</h3>
        </div>

        <form id="editProductForm" class="flex-1 overflow-y-auto pr-2">
            <input type="hidden" id="editProductId">

            <div class="mb-4 hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Barcode</label>
                <div class="flex gap-2">
                    <input type="text" id="editProductBarcode" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter barcode" required>
                    <button type="button" class="px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition font-semibold text-sm" style="background-color: #f97316 !important;" onclick="openBarcodeScanner()" title="Scan Barcode">
                        <span class="material-icons text-base">camera_alt</span>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name</label>
                <input type="text" id="editProductName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter product name" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                <input type="number" id="editProductPrice" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter price" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                <input type="number" id="editProductQuantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter quantity" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Store</label>
                <select id="editProductStore" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Select Store --</option>
                </select>
            </div>

            <div id="editProductError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                <span id="editProductErrorText"></span>
            </div>
        </form>

        <div class="flex gap-3 pt-4 mt-auto flex-shrink-0">
            <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="toggleModal('editProductModal')">
                Cancel
            </button>
            <button type="button" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold" style="background-color: #2563eb !important;" onclick="submitEditProduct()">
                Update Product
            </button>
        </div>
    </div>
</div>
