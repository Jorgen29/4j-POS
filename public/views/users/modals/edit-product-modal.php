<!-- Edit Product Modal -->
<div id="editProductModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full">
        <div class="border-b p-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-green">Edit Product</h2>
            <button onclick="closeEditProductModal()" class="text-gray-500 hover:text-gray-700">
                <span class="material-icons">close</span>
            </button>
        </div>

        <form id="editProductForm" class="p-6 space-y-4" onsubmit="submitEditProduct(event)">
            <input type="hidden" id="editProductId" name="product_id" />

            <div class="grid grid-cols-2 gap-4">
                <!-- Product Name -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                    <input 
                        type="text" 
                        id="editProductName" 
                        name="product_name" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                    />
                </div>

                <!-- Barcode (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barcode (Cannot be edited)</label>
                    <input 
                        type="text" 
                        id="editProductBarcode" 
                        name="barcode" 
                        readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                    />
                </div>

                <!-- SKU (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU (Auto-generated)</label>
                    <input 
                        type="text" 
                        id="editProductSku" 
                        name="sku" 
                        readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                    />
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (₱) *</label>
                    <input 
                        type="number" 
                        id="editProductPrice" 
                        name="price" 
                        step="0.01" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                    />
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input 
                        type="number" 
                        id="editProductQuantity" 
                        name="quantity" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeEditProductModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditProductModal(productId) {
        // Find product in allUserProducts
        const product = allUserProducts.find(p => p.product_id == productId);
        
        if (product) {
            populateEditProductForm(product);
            document.getElementById('editProductModal').classList.remove('hidden');
        } else {
            // Fallback: Fetch from server if not found in array
            fetch('../../php/handlers/getProductByIdHandler.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        populateEditProductForm(data.data);
                        document.getElementById('editProductModal').classList.remove('hidden');
                    }
                });
        }
    }

    function populateEditProductForm(product) {
        document.getElementById('editProductId').value = product.product_id;
        document.getElementById('editProductName').value = product.product_name;
        document.getElementById('editProductBarcode').value = product.barcode;
        document.getElementById('editProductSku').value = product.sku;
        document.getElementById('editProductPrice').value = product.price;
        document.getElementById('editProductQuantity').value = product.quantity;
    }

    function closeEditProductModal() {
        document.getElementById('editProductModal').classList.add('hidden');
    }

    function submitEditProduct(e) {
        e.preventDefault();

        const productId = document.getElementById('editProductId').value;
        const productName = document.getElementById('editProductName').value;
        const barcode = document.getElementById('editProductBarcode').value;
        const price = document.getElementById('editProductPrice').value;
        const quantity = document.getElementById('editProductQuantity').value;

        if (!productId || !productName || !price || !quantity || !barcode) {
            alert('Error: All fields are required');
            return;
        }

        fetch('../../php/handlers/updateProductHandler.php', {
            method: 'POST',
            body: new URLSearchParams({
                product_id: productId,
                product_name: productName,
                barcode: barcode,
                price: price,
                quantity: quantity,
                store_id: window.userStoreId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success modal with updated details
                document.getElementById('successHeader').textContent = 'Product Updated Successfully';
                document.getElementById('successMessage').textContent = `Product "${productName}" has been updated`;
                document.getElementById('successProductName').textContent = productName;
                document.getElementById('successProductBarcode').textContent = barcode;
                document.getElementById('successProductPrice').textContent = '₱' + parseFloat(price).toFixed(2);
                document.getElementById('successProductQuantity').textContent = quantity;

                closeEditProductModal();
                document.getElementById('successModal').classList.remove('hidden');

                // Reload page after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + (data.message || 'Failed to update product'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the product');
        });
    }
</script>
