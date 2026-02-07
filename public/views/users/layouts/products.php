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

<div class="max-w-12xl">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-green">My Products</h2>
                <p class="text-sm text-gray-600 mt-1">Store: <?php echo htmlspecialchars($storeName); ?></p>
            </div>
            <button onclick="openProductModal()" class="bg-gold text-white px-4 py-2 rounded-lg hover:bg-gold/90 transition flex items-center gap-2">
                <span class="material-icons text-sm">add</span>
                Add Product
            </button>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6 flex gap-4">
            <input 
                type="text" 
                id="productSearch" 
                placeholder="Search by product name, barcode, or ID..." 
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold"
                onkeyup="filterProducts()"
            />
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b text-gray-500 bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Product ID</th>
                        <th class="py-3 px-4 text-left font-semibold">Barcode</th>
                        <th class="py-3 px-4 text-left font-semibold">SKU</th>
                        <th class="py-3 px-4 text-left font-semibold">Product Name</th>
                        <th class="py-3 px-4 text-left font-semibold">Quantity</th>
                        <th class="py-3 px-4 text-left font-semibold">Price</th>
                        <th class="py-3 px-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <?php if (!empty($storeProducts)): ?>
                        <?php foreach ($storeProducts as $product): ?>
                            <tr class="border-b hover:bg-cream transition product-row" data-product='<?php echo json_encode($product); ?>'>
                                <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($product['product_id']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($product['barcode']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td class="py-3 px-4"><?php echo $product['quantity']; ?></td>
                                <td class="py-3 px-4">₱<?php echo number_format($product['price'], 2); ?></td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button onclick="openEditProductModal(<?php echo $product['product_id']; ?>)" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Edit">
                                            <span class="material-icons text-sm">edit</span>
                                        </button>
                                        <button onclick="openDeleteProductModal(<?php echo $product['product_id']; ?>)" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Delete">
                                            <span class="material-icons text-sm">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                No products found. <a href="#" onclick="openProductModal(); return false;" class="text-blue-500 hover:underline">Add one now</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Store products data for filtering - use unique var name to avoid conflicts
    let userStoreProducts = <?php echo json_encode($storeProducts); ?>;
    window.userStoreId = <?php echo $storeId; ?>;

    function filterProducts() {
        const searchInput = document.getElementById('productSearch').value.toLowerCase();
        const tableBody = document.getElementById('productsTableBody');
        
        if (!searchInput) {
            displayUserProducts(userStoreProducts);
            return;
        }

        const filtered = userStoreProducts.filter(product => 
            product.product_name.toLowerCase().includes(searchInput) ||
            product.barcode.toLowerCase().includes(searchInput) ||
            product.product_id.toString().includes(searchInput) ||
            product.sku.toLowerCase().includes(searchInput)
        );

        displayUserProducts(filtered);
    }

    function displayUserProducts(products) {
        const tableBody = document.getElementById('productsTableBody');
        
        if (!products || products.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="py-8 px-4 text-center text-gray-500">No products found</td></tr>';
            return;
        }

        tableBody.innerHTML = products.map(product => `
            <tr class="border-b hover:bg-cream transition">
                <td class="py-3 px-4 font-semibold">${product.product_id}</td>
                <td class="py-3 px-4">${product.barcode}</td>
                <td class="py-3 px-4">${product.sku}</td>
                <td class="py-3 px-4">${product.product_name}</td>
                <td class="py-3 px-4">${product.quantity}</td>
                <td class="py-3 px-4">₱${parseFloat(product.price).toFixed(2)}</td>
                <td class="py-3 px-4 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="openEditProductModal(${product.product_id})" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Edit">
                            <span class="material-icons text-sm">edit</span>
                        </button>
                        <button onclick="openDeleteProductModal(${product.product_id})" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Delete">
                            <span class="material-icons text-sm">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
</script>
