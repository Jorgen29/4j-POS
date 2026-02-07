<?php
// Help page - Instructions for users
?>

<div class="space-y-6">
    <h2 class="text-xl sm:text-2xl font-bold text-green mb-6">Help & Instructions</h2>

    <!-- Account Management Section -->
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-gold/10 p-3 rounded-lg">
                <span class="material-icons text-gold text-2xl">account_circle</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-green">Account Management</h3>
        </div>
        
        <div class="space-y-4 ml-12">
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">View Your Profile</h4>
                <p class="text-gray-600 text-sm">Your account information is displayed on the Dashboard page. Here you can see your email, user ID, and store information at a glance.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Check Subscription Status</h4>
                <p class="text-gray-600 text-sm">Monitor your subscription plan, current status (Active/Inactive), and renewal date from your Dashboard. Keep track of upcoming renewals to avoid service interruptions.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Logout</h4>
                <p class="text-gray-600 text-sm">Click the user icon in the top right corner and select "Logout" to securely exit your account. Always remember to logout from public devices.</p>
            </div>
        </div>
    </div>

    <!-- Product Management Section -->
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-gold/10 p-3 rounded-lg">
                <span class="material-icons text-gold text-2xl">inventory_2</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-green">Product Management</h3>
        </div>
        
        <div class="space-y-4 ml-12">
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">View Products</h4>
                <p class="text-gray-600 text-sm">Navigate to the "Products" tab to see all items in your store. View product names, prices, stock levels, and barcodes for easy reference.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Check Inventory</h4>
                <p class="text-gray-600 text-sm">The Dashboard shows your total inventory value and quantity. Use the Products page to check individual stock levels for each item.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Monitor Stock</h4>
                <p class="text-gray-600 text-sm">Keep an eye on low-stock items displayed in the Products page. Stock level is shown for each product to help you manage reordering.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Product Details</h4>
                <p class="text-gray-600 text-sm">Each product displays: product name, price, stock quantity, and unique barcode for quick scanning at checkout.</p>
            </div>
        </div>
    </div>

    <!-- Point of Sale Section -->
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-gold/10 p-3 rounded-lg">
                <span class="material-icons text-gold text-2xl">point_of_sale</span>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-green">Point of Sale (POS)</h3>
        </div>
        
        <div class="space-y-4 ml-12">
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Adding Items to Cart</h4>
                <p class="text-gray-600 text-sm">Navigate to "POS / Sales" tab. Browse your products and click the "Add" button on any item to add it to your cart. The item will appear in the Checkout section on the right.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Scanning Barcodes</h4>
                <p class="text-gray-600 text-sm">Click the barcode scanner icon (📱) in the Checkout section. You can either use your device's camera to scan barcodes or manually type the barcode number. The scanner will remain open for continuous scanning of multiple items.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Audio Feedback - Beep Sounds</h4>
                <p class="text-gray-600 text-sm">When you scan a barcode:</p>
                <ul class="text-gray-600 text-sm ml-4 mt-1 space-y-1">
                    <li>🔔 <strong>Success Beep (High tone):</strong> Item was found and added to cart</li>
                    <li>⚠️ <strong>Error Beep (Low tone):</strong> Item not found - check the barcode or use Product ID</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Continuous Scanning</h4>
                <p class="text-gray-600 text-sm">The barcode scanner stays open after each scan, allowing you to quickly scan multiple items without reopening the modal. Simply scan the next item after hearing the beep.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Manage Your Cart</h4>
                <p class="text-gray-600 text-sm">View all items you've added in the cart section. You can adjust quantities or remove items before checkout.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Calculate Totals</h4>
                <p class="text-gray-600 text-sm">The system automatically calculates your subtotal and total. See "Total Sales" on your Dashboard to track daily and overall sales.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Process Payment</h4>
                <p class="text-gray-600 text-sm"><strong>Customer Pays:</strong> Enter the amount the customer is paying in the "Customer Pays" field.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Calculate Change</h4>
                <p class="text-gray-600 text-sm">The system automatically calculates the change. The change amount appears in the green box below the customer payment field.</p>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Complete Transaction</h4>
                <p class="text-gray-600 text-sm">Click the "Checkout" button to complete the sale. The transaction will be recorded, and your inventory will be updated automatically. Your sales will reflect in the Dashboard totals.</p>
            </div>

            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Clear Cart</h4>
                <p class="text-gray-600 text-sm">To start a new transaction, click "Clear Cart" to remove all items and begin fresh. This is useful when you need to cancel a sale.</p>
            </div>
        </div>
    </div>

    <!-- Quick Tips Section -->
    <div class="bg-green/5 border-l-4 border-green rounded-lg p-4 sm:p-6">
        <div class="flex items-start gap-3">
            <span class="material-icons text-green text-2xl flex-shrink-0">lightbulb</span>
            <div>
                <h3 class="font-bold text-green mb-3">Quick Tips</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li>✓ Check your Dashboard daily to monitor sales and inventory</li>
                    <li>✓ Use barcode scanning for faster transactions - scanner stays open for batch scanning</li>
                    <li>✓ Listen for beep sounds during scanning: high beep = success, low beep = item not found</li>
                    <li>✓ Scan multiple items quickly without closing the barcode scanner modal</li>
                    <li>✓ If barcode not found, try typing the Product ID instead</li>
                    <li>✓ Ensure you have sufficient payment before processing checkout</li>
                    <li>✓ Review your subscription status to avoid service interruptions</li>
                    <li>✓ Monitor stock levels to prevent running out of popular items</li>
                    <li>✓ Keep your account information up to date</li>
                </ul>
            </div>
        </div>
    </div>
</div>
