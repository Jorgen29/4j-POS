<!-- Delete Product Modal -->
<div id="deleteProductModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
        <div class="border-b p-6 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-red-600">Delete Product</h2>
            <button onclick="closeDeleteProductModal()" class="text-gray-500 hover:text-gray-700">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <p class="text-gray-700">Are you sure you want to delete this product?</p>
            <div id="deleteProductInfo" class="bg-gray-50 p-4 rounded-lg space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Product Name:</span>
                    <span id="deleteProductName" class="font-semibold"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Product ID:</span>
                    <span id="deleteProductId" class="font-semibold"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Barcode:</span>
                    <span id="deleteProductBarcode" class="font-semibold"></span>
                </div>
            </div>
            <p class="text-sm text-red-600">⚠️ This action cannot be undone.</p>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button onclick="closeDeleteProductModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button onclick="confirmDeleteProduct()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Delete Product
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let deleteProductId = null;

    function openDeleteProductModal(productId) {
        deleteProductId = productId;
        
        // Find product in userStoreProducts (from products layout)
        let product = null;
        if (typeof userStoreProducts !== 'undefined') {
            product = userStoreProducts.find(p => p.product_id == productId);
        }
        
        if (product) {
            populateDeleteProductModal(product);
            document.getElementById('deleteProductModal').classList.remove('hidden');
        } else {
            // Fallback: Fetch from server if not found in array
            fetch('../../php/handlers/getProductByIdHandler.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        populateDeleteProductModal(data.data);
                        document.getElementById('deleteProductModal').classList.remove('hidden');
                    }
                });
        }
    }

    function populateDeleteProductModal(product) {
        document.getElementById('deleteProductName').textContent = product.product_name;
        document.getElementById('deleteProductId').textContent = product.product_id;
        document.getElementById('deleteProductBarcode').textContent = product.barcode || 'N/A';
    }

    function closeDeleteProductModal() {
        document.getElementById('deleteProductModal').classList.add('hidden');
        deleteProductId = null;
    }

    function confirmDeleteProduct() {
        if (!deleteProductId) return;

        fetch('../../php/handlers/deleteProductHandler.php', {
            method: 'POST',
            body: new URLSearchParams({
                product_id: deleteProductId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success modal
                document.getElementById('successHeader').textContent = 'Product Deleted Successfully';
                document.getElementById('successMessage').textContent = 'The product has been removed from your inventory';
                document.getElementById('successProductName').textContent = document.getElementById('deleteProductName').textContent;
                document.getElementById('successProductBarcode').textContent = document.getElementById('deleteProductBarcode').textContent;
                
                closeDeleteProductModal();
                document.getElementById('successModal').classList.remove('hidden');

                // Reload page after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + (data.message || 'Failed to delete product'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the product');
        });
    }
</script>
