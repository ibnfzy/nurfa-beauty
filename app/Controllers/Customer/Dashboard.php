<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\PromotionModel;
use App\Models\VoucherModel;

class Dashboard extends BaseController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;
    protected CustomerModel $customerModel;
    protected PromotionModel $promotionModel;
    protected VoucherModel $voucherModel;

    public function __construct()
    {
        $this->productModel   = new ProductModel();
        $this->categoryModel  = new CategoryModel();
        $this->customerModel  = new CustomerModel();
        $this->promotionModel = new PromotionModel();
        $this->voucherModel   = new VoucherModel();
    }

    public function index()
    {
        $today = date('Y-m-d');

        // Active promotions
        $promotions = $this->promotionModel
            ->where('is_active', 1)
            ->where('start_date <=', $today)
            ->where('end_date >=', $today)
            ->orderBy('id', 'DESC')
            ->findAll();

        // Latest products (limit 8)
        $latestProducts = $this->productModel->withCategory()
            ->where('products.is_active', 1)
            ->where('products.is_bundle', 0)
            ->orderBy('products.id', 'DESC')
            ->findAll(8);

        // Categories
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();

        // Welcome voucher from session (if logged in)
        $welcomeVoucher = null;
        $session = session();
        if ($session->get('welcome_voucher')) {
            $welcomeVoucher = $session->get('welcome_voucher');
        }

        // Rekomendasi produk (jika login)
        $recommendedProducts = [];
        $bundleProducts = [];
        $userId = $session->get('user_id');
        if ($userId) {
            $customer = $this->customerModel->where('user_id', $userId)->first();
            if ($customer) {
                $recommendedProducts = $this->productModel->getRecommendedForCustomer($customer['id'], 8);
            }
        }

        // Produk bundle
        $bundleProducts = $this->productModel->getActiveBundles(4);

        $data = [
            'pageTitle'           => 'Nurfa Beauty - Kecantikan Terpercaya',
            'promotions'          => $promotions ?? [],
            'latestProducts'      => $latestProducts ?? [],
            'categories'          => $categories ?? [],
            'welcomeVoucher'      => $welcomeVoucher,
            'recommendedProducts' => $recommendedProducts ?? [],
            'bundleProducts'      => $bundleProducts ?? [],
        ];

        return view('customer/home/landing', $data);
    }

    public function catalog()
    {
        $search     = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');

        $products = $this->productModel->withCategory()
            ->where('products.is_active', 1);

        if ($search) {
            $products->like('products.name', $search);
        }

        if ($categoryId) {
            $products->where('products.category_id', $categoryId);
        }

        $products = $products->orderBy('products.id', 'DESC')->paginate(12);

        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();

        // Produk bundle
        $bundleProducts = $this->productModel->getActiveBundles(4);

        $data = [
            'pageTitle'    => 'Katalog Produk',
            'products'     => $products ?? [],
            'pager'        => $this->productModel->pager ?? null,
            'categories'   => $categories ?? [],
            'search'       => $search ?? '',
            'categoryId'   => $categoryId ?? null,
            'bundleProducts' => $bundleProducts ?? [],
        ];

        return view('customer/home/index', $data);
    }
}
