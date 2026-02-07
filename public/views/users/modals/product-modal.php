<!-- Add Product Modal -->
<div id="productModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 modal-overlay" style="transition: none;">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4 p-8 modal-content" onclick="event.stopPropagation()" style="transition: none; display: flex; flex-direction: column; max-height: 90vh;">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-icons text-gold text-3xl">add_circle</span>
            <h3 class="text-2xl font-bold text-gray-800">Add New Product</h3>
        </div>

        <form id="productForm" class="flex-1 overflow-y-auto pr-2 space-y-4" onsubmit="submitProduct(event)">
            <!-- Product Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                <input 
                    type="text" 
                    id="productName" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                    placeholder="Enter product name"
                    required
                />
            </div>

            <!-- Barcode with Scanner -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Barcode</label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        id="productBarcode" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                        placeholder="Enter or scan barcode"
                    />
                    <button type="button" class="px-4 py-2 bg-gold hover:bg-gold/90 text-white rounded-lg transition font-semibold" onclick="openBarcodeScannerModal()" title="Scan Barcode">
                        <span class="material-icons text-base">qr_code_2</span>
                    </button>
                </div>
            </div>

            <!-- Price and Quantity -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price (₱) *</label>
                    <input 
                        type="number" 
                        id="productPrice" 
                        step="0.01" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                        placeholder="0.00"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity *</label>
                    <input 
                        type="number" 
                        id="productQuantity" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                        placeholder="0"
                        required
                    />
                </div>
            </div>

            <div id="productError" class="hidden p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                <span id="productErrorText"></span>
            </div>

            <div class="flex gap-3 pt-6 mt-auto flex-shrink-0 border-t">
                <button type="button" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold" onclick="closeProductModal()">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green text-white rounded-lg hover:bg-green/90 transition font-semibold">
                    Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openProductModal() {
        document.getElementById('productForm').reset();
        document.getElementById('productModal').classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
        document.getElementById('productForm').reset();
    }

    function submitProduct(e) {
        e.preventDefault();

        const productName = document.getElementById('productName').value;
        const barcode = document.getElementById('productBarcode').value;
        const price = document.getElementById('productPrice').value;
        const quantity = document.getElementById('productQuantity').value;
        const errorDiv = document.getElementById('productError');
        const errorText = document.getElementById('productErrorText');

        // Clear previous errors
        if (errorDiv) {
            errorDiv.classList.add('hidden');
        }

        if (!productName || !price || !quantity) {
            if (errorDiv && errorText) {
                errorText.textContent = 'Please fill in all required fields';
                errorDiv.classList.remove('hidden');
            }
            return;
        }

        fetch('../../php/handlers/createProductHandler.php', {
            method: 'POST',
            body: new URLSearchParams({
                product_name: productName,
                barcode: barcode || '',
                price: price,
                quantity: quantity,
                store_id: window.userStoreId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success modal with product details
                document.getElementById('successHeader').textContent = 'Product Added Successfully';
                document.getElementById('successMessage').textContent = `Product "${productName}" created successfully`;
                document.getElementById('successProductName').textContent = data.data.product_name;
                document.getElementById('successProductBarcode').textContent = data.data.barcode || 'N/A';
                document.getElementById('successProductSku').textContent = data.data.sku;
                document.getElementById('successProductPrice').textContent = '₱' + parseFloat(data.data.price).toFixed(2);
                document.getElementById('successProductQuantity').textContent = data.data.quantity;

                closeProductModal();
                document.getElementById('successModal').classList.remove('hidden');

                // Reload page after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                if (errorDiv && errorText) {
                    errorText.textContent = data.message || 'Failed to add product';
                    errorDiv.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (errorDiv && errorText) {
                errorText.textContent = 'An error occurred while adding the product';
                errorDiv.classList.remove('hidden');
            }
        });
    }
</script>
