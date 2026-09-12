<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
        'is_bundle',
        'bundle_products',
        'bundle_discount',
        'variants',
    ];

    // Join dengan categories
    public function withCategory()
    {
        $this->join('categories', 'categories.id = products.category_id');
        $this->select('products.*, categories.name as category_name');
        return $this;
    }

    // Join dengan reviews
    public function withReviews()
    {
        $this->join('reviews', 'reviews.product_id = products.id', 'left');
        return $this;
    }

    /**
     * Produk rekomendasi berdasarkan kategori riwayat pembelian customer
     */
    public function getRecommendedForCustomer(int $customerId, int $limit = 8): array
    {
        // Ambil kategori dari riwayat pembelian
        $purchasedCategories = $this->db->table('transaction_items')
            ->select('products.category_id')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('products', 'products.id = transaction_items.product_id')
            ->where('transactions.customer_id', $customerId)
            ->where('transactions.status', 'completed')
            ->groupBy('products.category_id')
            ->get()
            ->getResultArray();

        $categoryIds = array_column($purchasedCategories, 'category_id');

        if (empty($categoryIds)) {
            // Jika belum ada pembelian, return produk terbaru
            return $this->withCategory()
                ->where('products.is_active', 1)
                ->where('products.is_bundle', 0)
                ->orderBy('products.id', 'DESC')
                ->findAll($limit);
        }

        // Ambil produk yang belum dibeli di kategori yang sama
        $purchasedProductIds = $this->db->table('transaction_items')
            ->select('product_id')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->where('transactions.customer_id', $customerId)
            ->get()
            ->getResultArray();

        $excludeIds = array_column($purchasedProductIds, 'product_id');

        $builder = $this->withCategory()
            ->where('products.is_active', 1)
            ->whereIn('products.category_id', $categoryIds);

        if (!empty($excludeIds)) {
            $builder->whereNotIn('products.id', $excludeIds);
        }

        return $builder
            ->orderBy('products.id', 'DESC')
            ->findAll($limit);
    }

    /**
     * Produk premium di kategori sama (harga lebih tinggi) - untuk Up-selling
     */
    public function getPremiumInCategory(int $categoryId, float $currentPrice, int $excludeId, int $limit = 4): array
    {
        return $this->withCategory()
            ->where('products.is_active', 1)
            ->where('products.category_id', $categoryId)
            ->where('products.price >', $currentPrice)
            ->where('products.id !=', $excludeId)
            ->where('products.is_bundle', 0)
            ->orderBy('products.price', 'ASC')
            ->findAll($limit);
    }

    /**
     * Produk yang sering dibeli bersamaan - untuk Cross-selling
     */
    public function getFrequentlyBoughtTogether(int $productId, int $limit = 4): array
    {
        // Cari transaksi yang mengandung produk ini
        $transactionIds = $this->db->table('transaction_items')
            ->select('transaction_id')
            ->where('product_id', $productId)
            ->get()
            ->getResultArray();

        $trxIds = array_column($transactionIds, 'transaction_id');

        if (empty($trxIds)) {
            return [];
        }

        // Cari produk lain yang paling sering muncul di transaksi yang sama
        $frequentProducts = $this->db->table('transaction_items')
            ->select('product_id, COUNT(*) as frequency')
            ->whereIn('transaction_id', $trxIds)
            ->where('product_id !=', $productId)
            ->groupBy('product_id')
            ->orderBy('frequency', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $productIds = array_column($frequentProducts, 'product_id');

        if (empty($productIds)) {
            return [];
        }

        return $this->withCategory()
            ->where('products.is_active', 1)
            ->whereIn('products.id', $productIds)
            ->findAll();
    }

    /**
     * Ambil semua produk bundle aktif
     */
    public function getActiveBundles(int $limit = 8): array
    {
        return $this->withCategory()
            ->where('products.is_active', 1)
            ->where('products.is_bundle', 1)
            ->orderBy('products.id', 'DESC')
            ->findAll($limit);
    }

    /**
     * Ambil detail komponen bundle
     */
    public function getBundleComponents(string $bundleProductsJson): array
    {
        $components = json_decode($bundleProductsJson, true);
        if (empty($components) || !is_array($components)) {
            return [];
        }

        $result = [];
        foreach ($components as $comp) {
            $product = $this->withCategory()->find($comp['product_id'] ?? 0);
            if ($product) {
                $product['bundle_quantity'] = $comp['quantity'] ?? 1;
                $result[] = $product;
            }
        }
        return $result;
    }
}
