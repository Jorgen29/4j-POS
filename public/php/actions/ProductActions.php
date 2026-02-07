<?php

class ProductActions
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Get all products
    public function getAllProducts()
    {
        try {
            $query = $this->mysqli->prepare("
                SELECT 
                    p.product_id,
                    p.barcode,
                    p.sku,
                    p.product_name,
                    p.price,
                    p.quantity,
                    p.store_id,
                    p.created_at,
                    p.updated_at,
                    s.store_name
                FROM products p
                LEFT JOIN stores s ON p.store_id = s.store_id
                ORDER BY p.created_at DESC
            ");
            $query->execute();
            $result = $query->get_result();
            $products = [];

            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }

            return [
                'status' => 'success',
                'data' => $products
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Get product by ID
    public function getProductById($productId)
    {
        try {
            $query = $this->mysqli->prepare("
                SELECT 
                    p.product_id,
                    p.barcode,
                    p.sku,
                    p.product_name,
                    p.price,
                    p.quantity,
                    p.store_id,
                    p.created_at,
                    p.updated_at,
                    s.store_name
                FROM products p
                LEFT JOIN stores s ON p.store_id = s.store_id
                WHERE p.product_id = ?
            ");
            $query->bind_param("i", $productId);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                return [
                    'status' => 'success',
                    'data' => $result->fetch_assoc()
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Product not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Create product
    public function createProduct($barcode, $sku, $productName, $price, $quantity, $storeId)
    {
        try {
            // Check if product already exists by barcode
            $checkQuery = $this->mysqli->prepare("SELECT product_id FROM products WHERE barcode = ?");
            $checkQuery->bind_param("s", $barcode);
            $checkQuery->execute();
            $checkResult = $checkQuery->get_result();

            if ($checkResult->num_rows > 0) {
                return [
                    'status' => 'error',
                    'message' => 'Product with this barcode already exists'
                ];
            }

            $query = $this->mysqli->prepare("
                INSERT INTO products (barcode, sku, product_name, price, quantity, store_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $query->bind_param("sssdii", $barcode, $sku, $productName, $price, $quantity, $storeId);

            if ($query->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Product created successfully',
                    'data' => [
                        'product_id' => $query->insert_id,
                        'barcode' => $barcode,
                        'sku' => $sku,
                        'product_name' => $productName,
                        'price' => $price,
                        'quantity' => $quantity,
                        'store_id' => $storeId
                    ]
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to create product: ' . $query->error
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Update product (SKU should not be updated after creation)
    public function updateProduct($productId, $barcode, $sku, $productName, $price, $quantity, $storeId)
    {
        try {
            // Note: SKU is intentionally NOT updated - it's auto-generated on creation and should not change
            $query = $this->mysqli->prepare("
                UPDATE products 
                SET barcode = ?, product_name = ?, price = ?, quantity = ?, store_id = ?, updated_at = NOW()
                WHERE product_id = ?
            ");
            $query->bind_param("ssdiii", $barcode, $productName, $price, $quantity, $storeId, $productId);

            if ($query->execute()) {
                // Return the updated product data
                return $this->getProductById($productId);
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update product'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Delete product
    public function deleteProduct($productId)
    {
        try {
            $query = $this->mysqli->prepare("DELETE FROM products WHERE product_id = ?");
            $query->bind_param("i", $productId);

            if ($query->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Product deleted successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete product'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Generate and update SKU for a product
    public function generateAndUpdateSKU($productId, $storeId)
    {
        try {
            // Fetch store owner ID from stores table
            $storeQuery = $this->mysqli->prepare("
                SELECT store_owner FROM stores WHERE store_id = ?
            ");
            $storeQuery->bind_param("i", $storeId);
            $storeQuery->execute();
            $storeResult = $storeQuery->get_result();

            if ($storeResult->num_rows === 0) {
                return [
                    'status' => 'error',
                    'message' => 'Store not found'
                ];
            }

            $storeData = $storeResult->fetch_assoc();
            $storeOwnerId = $storeData['store_owner'];

            // Format: store_id-store_owner_id-product_id-MMDDYYYY
            $dateFormat = date('mdY'); // MMDDYYYY format
            $sku = "{$storeId}-{$storeOwnerId}-{$productId}-{$dateFormat}";

            $query = $this->mysqli->prepare("
                UPDATE products 
                SET sku = ?, updated_at = NOW()
                WHERE product_id = ?
            ");
            $query->bind_param("si", $sku, $productId);

            if ($query->execute()) {
                // Return the updated product
                return $this->getProductById($productId);
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update SKU: ' . $query->error
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
?>
