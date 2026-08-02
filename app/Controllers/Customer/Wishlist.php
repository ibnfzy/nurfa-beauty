<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\WishlistModel;
use App\Models\ProductModel;

class Wishlist extends BaseController
{
    protected CustomerModel $customerModel;
    protected WishlistModel $wishlistModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->wishlistModel = new WishlistModel();
        $this->productModel  = new ProductModel();
    }

    protected function getCustomerId()
    {
        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();
        return $customer ? $customer['id'] : null;
    }

    public function index()
    {
        $customerId = $this->getCustomerId();

        $wishlist = $this->wishlistModel
            ->withProduct()
            ->where('wishlists.customer_id', $customerId)
            ->findAll();

        $data = [
            'pageTitle' => 'Wishlist Saya',
            'wishlist'  => $wishlist ?? [],
        ];

        return view('customer/wishlist/index', $data);
    }

    public function toggle(int $productId)
    {
        $customerId = $this->getCustomerId();

        // Cek apakah produk ada
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ]);
        }

        // Cek apakah sudah ada di wishlist
        $existing = $this->wishlistModel
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            // Hapus dari wishlist
            $this->wishlistModel->delete($existing['id']);
            return $this->response->setJSON([
                'success' => true,
                'action'  => 'removed',
                'message' => 'Produk dihapus dari wishlist.',
            ]);
        }

        // Tambahkan ke wishlist
        $this->wishlistModel->insert([
            'customer_id' => $customerId,
            'product_id'  => $productId,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'action'  => 'added',
            'message' => 'Produk ditambahkan ke wishlist.',
        ]);
    }

    public function remove(int $id)
    {
        $customerId = $this->getCustomerId();

        $wishlist = $this->wishlistModel->find($id);

        if (!$wishlist || $wishlist['customer_id'] != $customerId) {
            return redirect()->to('/wishlist')
                ->with('toast', ['type' => 'error', 'message' => 'Item wishlist tidak ditemukan.']);
        }

        $this->wishlistModel->delete($id);

        return redirect()->to('/wishlist')
            ->with('toast', ['type' => 'success', 'message' => 'Item berhasil dihapus dari wishlist.']);
    }
}
