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
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b text-gray-500">
          <tr>
            <th class="py-3 text-left">Product ID</th>
            <th>Product Name</th>
            <th>Store Name</th>
            <th>Quantity</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody id="productTable">
          <tr class="border-b hover:bg-cream">
            <td class="py-3 font-medium">P-001</td>
            <td>Coffee Beans</td>
            <td>Juan Store</td>
            <td>120</td>
            <td>₱250</td>
          </tr>

          <tr class="border-b hover:bg-cream">
            <td class="py-3 font-medium">P-002</td>
            <td>Sugar Pack</td>
            <td>Maria Mart</td>
            <td>85</td>
            <td>₱90</td>
          </tr>

          <tr class="border-b hover:bg-cream">
            <td class="py-3 font-medium">P-003</td>
            <td>Milk Carton</td>
            <td>Juan Store</td>
            <td>60</td>
            <td>₱120</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

