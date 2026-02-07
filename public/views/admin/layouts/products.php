<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div
      class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6"
    >
      <h2 class="text-xl font-semibold text-green">All Products</h2>

      <div class="flex gap-3">
        <!-- Search -->
        <input
          id="productSearch"
          type="text"
          placeholder="Search product..."
          class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-gold focus:outline-none"
          onkeyup="filterProducts()"
        />

        <!-- Store Filter -->
        <select
          id="storeFilter"
          onchange="filterProducts()"
          class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-gold focus:outline-none"
        >
          <option value="">All Stores</option>
          <option value="Juan Store">Juan Store</option>
          <option value="Maria Mart">Maria Mart</option>
        </select>

        <!-- Add Product Button -->
        <button
          onclick="openProductModal()"
          class="bg-gold text-white px-4 py-2 rounded-lg hover:bg-gold/90 transition"
        >
          + Add Product
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b text-gray-500 bg-gray-50">
          <tr>
            <th class="py-3 px-4 text-left font-semibold">Product ID</th>
            <th class="py-3 px-4 text-left font-semibold">Barcode</th>
            <th class="py-3 px-4 text-left font-semibold">SKU</th>
            <th class="py-3 px-4 text-left font-semibold">Product Name</th>
            <th class="py-3 px-4 text-left font-semibold">Store Name</th>
            <th class="py-3 px-4 text-left font-semibold">Quantity</th>
            <th class="py-3 px-4 text-left font-semibold">Price</th>
            <th class="py-3 px-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody id="productTable">
          <tr class="border-b">
            <td colspan="8" class="py-8 px-4 text-center text-gray-500">
              Loading products...
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

