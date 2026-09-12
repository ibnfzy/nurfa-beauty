<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ReviewModel;
use App\Models\CustomerModel;
use App\Models\WishlistModel;

class Product extends BaseController
{
    protected ProductModel $productModel;
    protected ReviewModel $reviewModel;
    protected CustomerModel $customerModel;
    protected WishlistModel $wishlistModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->reviewModel   = new ReviewModel();
        $this->customerModel = new CustomerModel();
        $this->wishlistModel = new WishlistModel();
    }

    public function detail(int $id)
    {
        $product = $this->productModel->withCategory()
            ->where('products.id', $id)
            ->first();

        if (!$product) {
            return redirect()->to('/catalog')
                ->with('toast', ['type' => 'error', 'message' => 'Produk tidak ditemukan.']);
        }

        // Cek apakah produk ada di wishlist customer
        $isWishlisted = false;
        $userId = session()->get('user_id');
        if ($userId) {
            $customer = $this->customerModel->where('user_id', $userId)->first();
            if ($customer) {
                $isWishlisted = $this->wishlistModel
                    ->where('customer_id', $customer['id'])
                    ->where('product_id', $id)
                    ->countAllResults() > 0;
            }
        }

        // Get average rating and review count
        $reviewStats = $this->reviewModel
            ->select('AVG(rating) as avg_rating, COUNT(*) as review_count')
            ->where('product_id', $id)
            ->first();

        // Get recent reviews with customer->user join
        $reviews = $this->reviewModel->withCustomer()
            ->where('reviews.product_id', $id)
            ->orderBy('reviews.id', 'DESC')
            ->findAll(5);

        // Up-selling: Produk premium di kategori sama
        $premiumProducts = [];
        if (!($product['is_bundle'] ?? 0)) {
            $premiumProducts = $this->productModel->getPremiumInCategory(
                $product['category_id'],
                $product['price'],
                $product['id'],
                4
            );
        }

        // Cross-selling: Produk yang sering dibeli bersamaan
        $crossSellProducts = $this->productModel->getFrequentlyBoughtTogether($product['id'], 4);

        // Bundle components (jika produk ini bundle)
        $bundleComponents = [];
        if (!empty($product['is_bundle'] ?? 0) && !empty($product['bundle_products'])) {
            $bundleComponents = $this->productModel->getBundleComponents($product['bundle_products']);
        }

        $variants = !empty($product['variants']) ? json_decode($product['variants'], true) : null;

        $data = [
            'pageTitle'         => $product['name'] ?? 'Produk',
            'product'           => $product ?? [],
            'variants'          => $variants,
            'avgRating'         => $reviewStats ? round($reviewStats['avg_rating'] ?? 0, 1) : 0,
            'reviewCount'       => $reviewStats ? ($reviewStats['review_count'] ?? 0) : 0,
            'reviews'           => $reviews ?? [],
            'premiumProducts'   => $premiumProducts ?? [],
            'crossSellProducts' => $crossSellProducts ?? [],
            'bundleComponents'  => $bundleComponents ?? [],
            'isWishlisted'      => $isWishlisted,
        ];

        return view('customer/product/detail', $data);
    }
}
