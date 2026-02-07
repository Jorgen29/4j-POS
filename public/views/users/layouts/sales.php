<?php
require_once '../../php/db/connection.php';
require_once '../../php/actions/ProductActions.php';

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo '<p class="text-red-600">User ID not found in session.</p>';
    exit;
}

// Get user's store information
$storeQuery = $mysqli->prepare("SELECT store_id, store_name FROM stores WHERE store_owner = ? LIMIT 1");
$storeQuery->bind_param("i", $userId);
$storeQuery->execute();
$storeResult = $storeQuery->get_result();
$storeRow = $storeResult->fetch_assoc();
$storeId = $storeRow['store_id'] ?? null;
$storeName = $storeRow['store_name'] ?? 'N/A';

if (!$storeId) {
    echo '<p class="text-red-600">No store found for your account.</p>';
    exit;
}

// Get all products for this store
$productActions = new ProductActions($mysqli);
$products = $productActions->getAllProducts();

// Filter products for this store only
$storeProducts = [];
if ($products['status'] === 'success' && !empty($products['data'])) {
    foreach ($products['data'] as $product) {
        if ($product['store_id'] == $storeId) {
            $storeProducts[] = $product;
        }
    }
}
?>

<div class="flex flex-col lg:flex-row gap-4 lg:gap-6 h-full">
    <!-- Products Grid -->
    <div class="flex-1 overflow-y-auto lg:pr-4">
        <h2 class="text-xl sm:text-2xl font-bold text-green mb-4 sm:mb-6">Point of Sale</h2>
        
        <div class="product-grid grid grid-cols-2 gap-1 sm:gap-2 md:gap-3 md:grid-cols-3 lg:gap-4 lg:grid-cols-4">
            <?php if (!empty($storeProducts)): ?>
                <?php foreach ($storeProducts as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition product-card" data-product-id="<?php echo $product['product_id']; ?>" data-product='<?php echo json_encode($product); ?>'>
                        <!-- Product Image Placeholder -->
                        <div class="bg-gradient-to-br from-gold to-gold/50 h-20 sm:h-32 flex items-center justify-center text-white">
                            <span class="material-icons text-4xl sm:text-6xl opacity-30">shopping_bag</span>
                        </div>

                        <!-- Product Info -->
                        <div class="p-2 sm:p-4">
                            <h3 class="font-semibold text-gray-800 text-xs sm:text-sm mb-1 sm:mb-2 line-clamp-2">
                                <?php echo htmlspecialchars($product['product_name']); ?>
                            </h3>
                            
                            <p class="text-gold font-bold text-sm sm:text-lg mb-1 sm:mb-2">
                                ₱<?php echo number_format($product['price'], 2); ?>
                            </p>

                            <p class="text-xs text-gray-500 mb-2 sm:mb-3">
                                Stock: <span class="font-semibold"><?php echo $product['quantity']; ?></span>
                            </p>

                            <button 
                                onclick="addToCart(<?php echo htmlspecialchars(json_encode($product)); ?>)" 
                                class="w-full bg-green text-white py-1.5 sm:py-2 rounded-lg hover:bg-green/90 transition font-semibold text-xs sm:text-sm flex items-center justify-center gap-1 sm:gap-2"
                                <?php if ($product['quantity'] <= 0) echo 'disabled style="opacity: 0.5; cursor: not-allowed;"'; ?>
                            >
                                <span class="material-icons text-sm sm:text-base">add</span>
                                Add
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-span-full text-center text-gray-500 py-12">No products available in your store</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Checkout Sidebar -->
    <div class="w-full lg:w-96 bg-white rounded-xl shadow-lg p-4 sm:p-6 flex flex-col h-auto lg:h-full lg:overflow-hidden lg:min-h-screen">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg sm:text-xl font-bold text-green">Checkout</h3>
            <button 
                onclick="document.getElementById('barcodeScannerModalPOS').classList.remove('hidden'); setTimeout(() => document.getElementById('barcodeInputPOS').focus(), 100);"
                class="bg-gold text-white p-2 rounded-lg hover:bg-gold/90 transition"
                title="Scan Barcode"
            >
                <span class="material-icons text-lg sm:text-xl">qr_code_scanner</span>
            </button>
        </div>

        <!-- Cart Items -->
        <div id="cartItems" class="flex-1 lg:flex-1 overflow-y-auto mb-3 sm:mb-4 bg-cream rounded-lg p-3 sm:p-4">
            <p id="emptyCart" class="text-center text-gray-500 py-4 sm:py-6 text-sm sm:text-base">No items added</p>
            <div id="cartContent" class="hidden space-y-2 sm:space-y-3"></div>
        </div>

        <!-- Subtotal -->
        <div class="border-t pt-3 sm:pt-4 mb-3 sm:mb-4">
            <div class="flex justify-between mb-2">
                <span class="text-sm sm:text-base text-gray-700">Subtotal:</span>
                <span class="font-semibold text-sm sm:text-base text-gray-800">₱<span id="subtotal">0.00</span></span>
            </div>
        </div>

        <!-- Total -->
        <div class="bg-gold/10 border-2 border-gold rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
            <div class="flex justify-between items-center">
                <span class="text-base sm:text-lg font-bold text-gray-800">Total:</span>
                <span class="text-xl sm:text-2xl font-bold text-gold">₱<span id="total">0.00</span></span>
            </div>
        </div>

        <!-- Customer Payment -->
        <div class="mb-3 sm:mb-4">
            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Customer Pays</label>
            <input 
                type="number" 
                id="customerPayment" 
                placeholder="₱0.00" 
                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold focus:border-transparent text-base sm:text-lg font-semibold"
                step="0.01"
                min="0"
                oninput="calculateChange()"
            >
        </div>

        <!-- Change -->
        <div class="bg-green/10 border-2 border-green rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
            <div class="flex justify-between items-center">
                <span class="text-base sm:text-lg font-bold text-gray-800">Change:</span>
                <span class="text-xl sm:text-2xl font-bold text-green">₱<span id="change">0.00</span></span>
            </div>
        </div>

        <!-- Checkout Button -->
        <button 
            onclick="processCheckout()" 
            id="checkoutBtn"
            class="w-full bg-green text-white py-2 sm:py-3 rounded-lg hover:bg-green/90 transition font-bold flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base"
            disabled
        >
            <span class="material-icons text-lg">payment</span>
            Checkout
        </button>

        <!-- Clear Cart Button -->
        <button 
            onclick="clearCart()" 
            id="clearBtn"
            class="w-full text-gray-700 py-2 rounded-lg hover:bg-gray-100 transition font-semibold mt-2 hidden text-sm sm:text-base"
        >
            Clear Cart
        </button>
    </div>
</div>

<script>
    let cartPOS = [];
    const storeIdPOS = <?php echo $storeId; ?>;
    const posProducts = <?php echo json_encode($storeProducts); ?>;

    function showWarningModal(message) {
        document.getElementById('warningMessage').textContent = message;
        document.getElementById('warningModal').classList.remove('hidden');
    }

    function closeWarningModal() {
        document.getElementById('warningModal').classList.add('hidden');
    }

    function calculateChange() {
        const total = parseFloat(document.getElementById('total').textContent) || 0;
        const customerPayment = parseFloat(document.getElementById('customerPayment').value) || 0;
        const change = Math.max(0, customerPayment - total);
        document.getElementById('change').textContent = change.toFixed(2);
    }

    function addToCart(product) {
        // Check if product already in cart
        const existingItem = cartPOS.find(item => item.product_id == product.product_id);

        if (existingItem) {
            // Check if adding more would exceed stock
            if (existingItem.quantity >= existingItem.stock_quantity) {
                showWarningModal(`Cannot add more. Stock available: ${existingItem.stock_quantity}`);
                return;
            }
            existingItem.quantity += 1;
        } else {
            // Preserve stock quantity separately
            const cartItem = { ...product, stock_quantity: product.quantity, quantity: 1 };
            cartPOS.push(cartItem);
        }

        updateCartDisplay();
    }

    function removeFromCart(productId) {
        cartPOS = cartPOS.filter(item => item.product_id != productId);
        updateCartDisplay();
    }

    function updateQuantity(productId, newQuantity) {
        const item = cartPOS.find(item => item.product_id == productId);
        if (item) {
            const parsedQuantity = parseInt(newQuantity) || 1;
            // Prevent quantity from exceeding stock
            if (parsedQuantity > item.stock_quantity) {
                showWarningModal(`Cannot exceed stock. Available: ${item.stock_quantity}`);
                return;
            }
            item.quantity = Math.max(1, parsedQuantity);
            updateCartDisplay();
        }
    }

    function updateCartDisplay() {
        const cartItemsContainer = document.getElementById('cartContent');
        const emptyCart = document.getElementById('emptyCart');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const clearBtn = document.getElementById('clearBtn');

        if (cartPOS.length === 0) {
            emptyCart.classList.remove('hidden');
            cartItemsContainer.classList.add('hidden');
            checkoutBtn.disabled = true;
            clearBtn.classList.add('hidden');
            document.getElementById('subtotal').textContent = '0.00';
            document.getElementById('total').textContent = '0.00';
            document.getElementById('customerPayment').value = '';
            document.getElementById('change').textContent = '0.00';
            return;
        }

        emptyCart.classList.add('hidden');
        cartItemsContainer.classList.remove('hidden');
        checkoutBtn.disabled = false;
        clearBtn.classList.remove('hidden');

        cartItemsContainer.innerHTML = cartPOS.map(item => `
            <div class="bg-white p-3 rounded-lg border border-gray-200 flex justify-between items-start gap-2">
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800 line-clamp-1">${item.product_name}</p>
                    <p class="text-gold font-semibold text-sm">₱${parseFloat(item.price).toFixed(2)}</p>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="updateQuantity(${item.product_id}, ${item.quantity - 1})" class="px-1 py-0 bg-gray-200 rounded text-xs hover:bg-gray-300">−</button>
                    <input type="number" value="${item.quantity}" onchange="updateQuantity(${item.product_id}, this.value)" class="w-8 text-center text-xs border border-gray-300 rounded py-0" min="1">
                    <button onclick="updateQuantity(${item.product_id}, ${item.quantity + 1})" class="px-1 py-0 bg-gray-200 rounded text-xs hover:bg-gray-300">+</button>
                </div>
                <button onclick="removeFromCart(${item.product_id})" class="text-redsoft hover:text-redsoft/80 ml-2">
                    <span class="material-icons text-base">close</span>
                </button>
            </div>
        `).join('');

        const subtotal = cartPOS.reduce((total, item) => total + (item.price * item.quantity), 0);
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('total').textContent = subtotal.toFixed(2);
        calculateChange();
    }



    function clearCart() {
        cartPOS = [];
        updateCartDisplay();
    }

    function processCheckout() {
        if (cartPOS.length === 0) {
            showWarningModal('Cart is empty');
            return;
        }

        const total = parseFloat(document.getElementById('total').textContent) || 0;
        const customerPayment = parseFloat(document.getElementById('customerPayment').value) || 0;

        if (customerPayment < total) {
            showWarningModal(`Insufficient payment. Customer pays ₱${customerPayment.toFixed(2)} but total is ₱${total.toFixed(2)}`);
            return;
        }

        const subtotal = cartPOS.reduce((total, item) => total + (item.price * item.quantity), 0);
        const change = parseFloat(document.getElementById('change').textContent);

        // Prepare transaction data
        const transactionData = {
            store_id: storeIdPOS,
            total_amount: total,
            items: cartPOS
        };

        console.log('Processing checkout:', transactionData);

        // Submit to server
        fetch('../../php/handlers/createTransactionHandler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(transactionData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success modal
                document.getElementById('successHeader').textContent = 'Transaction Success';
                document.getElementById('successMessage').textContent = `Transaction of ₱${total.toFixed(2)} completed successfully`;
                document.getElementById('successProductName').textContent = `Items: ${cartPOS.length}`;
                document.getElementById('successProductBarcode').textContent = `Subtotal: ₱${subtotal.toFixed(2)}`;
                document.getElementById('successProductPrice').textContent = `Total: ₱${total.toFixed(2)}`;
                document.getElementById('successProductQuantity').textContent = `Change: ₱${change.toFixed(2)}`;

                document.getElementById('successModal').classList.remove('hidden');

                // Clear cart
                clearCart();

                // Reload after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showWarningModal('Error: ' + (data.message || 'Failed to process transaction'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showWarningModal('An error occurred while processing the transaction');
        });
    }

    // Barcode Scanner Functions for POS
    let barcodeStream = null;

    function startBarcodeScanner() {
        const video = document.getElementById('barcodeVideoPOS');
        const loading = document.getElementById('barcodeLoadingPOS');

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                barcodeStream = stream;
                video.style.display = 'block';
                video.srcObject = stream;
                loading.style.display = 'none';
                initBarcodeScannerPOS();
            })
            .catch(err => {
                showWarningModal('Camera access denied or unavailable');
                console.error('Camera error:', err);
            });
    }

    function stopBarcodeScanner() {
        if (barcodeStream) {
            barcodeStream.getTracks().forEach(track => track.stop());
            barcodeStream = null;
        }
    }

    function initBarcodeScannerPOS() {
        const video = document.getElementById('barcodeVideoPOS');
        const canvas = document.getElementById('barcodeCanvasPOS');
        const ctx = canvas.getContext('2d');

        // Focus on input for more reliable scanning
        document.getElementById('barcodeInputPOS').focus();

        function decode() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                try {
                    if (window.Quagga) {
                        // Use Quagga if available
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        // Quagga barcode detection would go here
                    }
                } catch (error) {
                    // Continue scanning
                }
            }
            if (barcodeStream) {
                requestAnimationFrame(decode);
            }
        }
        decode();
    }

    function handleBarcodeInputPOS() {
        const input = document.getElementById('barcodeInputPOS');
        const barcode = input.value.trim();

        if (barcode) {
            handleBarcodeScanned(barcode);
            input.value = '';
            input.focus();
        }
    }

    function handleBarcodeScanned(barcode) {
        const product = posProducts.find(p => p.barcode === barcode);

        if (product) {
            addToCart(product);
            showWarningModal(`✓ Added: ${product.product_name}`);
        } else {
            showWarningModal(`Product not found for barcode: ${barcode}`);
        }
    }
</script>

<!-- Barcode Scanner Modal for POS -->
<div id="barcodeScannerModalPOS" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-40">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-2xl w-full max-h-96 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-green">Scan Barcode</h3>
            <button 
                onclick="document.getElementById('barcodeScannerModalPOS').classList.add('hidden'); stopBarcodeScanner();"
                class="text-gray-500 hover:text-gray-700"
            >
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <div id="barcodeVideoContainerPOS" class="flex-1 bg-black rounded-lg mb-4 flex items-center justify-center">
            <video id="barcodeVideoPOS" class="w-full h-full object-cover rounded-lg" style="display:none;"></video>
            <canvas id="barcodeCanvasPOS" class="hidden"></canvas>
            <p id="barcodeLoadingPOS" class="text-white text-center">Loading camera...</p>
        </div>
        
        <input 
            type="text" 
            id="barcodeInputPOS" 
            placeholder="Or type barcode here..." 
            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold mb-4"
            onkeypress="if(event.key==='Enter') handleBarcodeInputPOS();"
        >
        
        <div class="flex gap-2">
            <button 
                onclick="startBarcodeScanner()"
                class="flex-1 bg-gold text-white py-2 rounded-lg hover:bg-gold/90 transition font-semibold"
            >
                Start Scanner
            </button>
            <button 
                onclick="document.getElementById('barcodeScannerModalPOS').classList.add('hidden'); stopBarcodeScanner();"
                class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition font-semibold"
            >
                Close
            </button>
        </div>
    </div>
</div>

<!-- Warning Modal -->
<div id="warningModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full">
        <div class="flex items-center justify-center mb-4">
            <div class="bg-red-100 rounded-full p-4">
                <span class="material-icons text-red-600 text-4xl">warning</span>
            </div>
        </div>
        <h3 class="text-center text-xl font-bold text-gray-800 mb-4">Warning</h3>
        <p id="warningMessage" class="text-center text-gray-600 mb-6"></p>
        <button 
            onclick="closeWarningModal()" 
            class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition font-semibold"
        >
            OK
        </button>
    </div>
</div>
