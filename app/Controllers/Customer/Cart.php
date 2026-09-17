<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;

class Cart extends BaseController
{
    protected $cartModel;
    protected $customerModel;
    protected $productModel;

    public function __construct()
    {
        $this->cartModel     = new CartModel();
        $this->customerModel = new CustomerModel();
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
        $cartItems = $this->cartModel->getCartWithProducts($customerId) ?? [];

        $total      = 0;
        $stockWarning = false;

        foreach ($cartItems as &$item) {
            $item['subtotal'] = $item['product_price'] * $item['quantity'];
            $total += $item['subtotal'];

            if ($item['quantity'] > $item['product_stock']) {
                $stockWarning = true;
            }
        }

        $data = [
            'pageTitle'    => 'Keranjang Belanja',
            'cartItems'    => $cartItems,
            'total'        => $total,
            'stockWarning' => $stockWarning,
        ];

        return view('customer/cart/index', $data);
    }

    public function add()
    {
        $rules = [
            'product_id'       => 'required|integer',
            'quantity'         => 'required|integer|greater_than[0]',
            'variant_selection' => 'permit_empty',
        ];

        $messages = [
            'product_id' => [
                'required' => 'Produk wajib dipilih.',
                'integer'  => 'Produk tidak valid.',
            ],
            'quantity' => [
                'required'     => 'Jumlah wajib diisi.',
                'integer'      => 'Jumlah harus berupa angka bulat.',
                'greater_than' => 'Jumlah harus lebih dari 0.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menambahkan ke keranjang. Periksa kembali data Anda.']);
        }

        $productId     = $this->request->getPost('product_id');
        $quantity      = (int) $this->request->getPost('quantity');
        $variantSelection = $this->request->getPost('variant_selection') ?: null;
        $customerId    = $this->getCustomerId();

        // Cek produk ada dan aktif
        $product = $this->productModel->where('is_active', 1)->find($productId);
        if (!$product) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Produk tidak ditemukan atau tidak aktif.']);
        }

        // Validasi varian jika produk punya varian
        $variants = !empty($product['variants']) ? json_decode((string) $product['variants'], true) : null;
        if (!empty($variants) && empty($variantSelection)) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Silakan pilih minimal satu varian produk sebelum menambahkan ke keranjang.']);
        }

        // Validasi stok berdasarkan varian jika ada
        if (!empty($variants) && !empty($variantSelection)) {
            $selectedVariant = json_decode($variantSelection, true);
            $available = [];
            foreach ($variants as $attributeName => $attributeValues) {
                if (is_array($attributeValues)) {
                    $available[$attributeName] = $attributeValues;
                }
            }

            if (!is_array($selectedVariant) || count($selectedVariant) < 1) {
                return redirect()->back()
                    ->with('toast', ['type' => 'error', 'message' => 'Silakan pilih minimal satu varian produk.']);
            }

            foreach ($selectedVariant as $name => $value) {
                if (!isset($available[$name]) || !in_array($value, $available[$name], true)) {
                    return redirect()->back()
                        ->with('toast', ['type' => 'error', 'message' => 'Varian yang dipilih tidak valid.']);
                }
            }
        }

        // Cek stok
        $existingCartQuery = $this->cartModel
            ->where('customer_id', $customerId)
            ->where('product_id', $productId);

        if ($variantSelection === null) {
            $existingCartQuery->where('variant_selection', null);
        } else {
            $existingCartQuery->where('variant_selection', $variantSelection);
        }

        $existingCart = $existingCartQuery->first();

        $totalQuantity = $quantity + ($existingCart['quantity'] ?? 0);

        if ($totalQuantity > $product['stock']) {
            return redirect()->back()
                ->with('toast', ['type' => 'warning', 'message' => 'Stok produk tidak mencukupi. Stok tersedia: ' . $product['stock'] . '.']);
        }

        // Jika sudah ada di keranjang, tambah quantity
        if ($existingCart) {
            $this->cartModel->update($existingCart['id'], [
                'quantity'        => $existingCart['quantity'] + $quantity,
                'variant_selection' => $variantSelection,
            ]);
        } else {
            $this->cartModel->save([
                'customer_id'       => $customerId,
                'product_id'        => $productId,
                'quantity'          => $quantity,
                'variant_selection' => $variantSelection,
            ]);
        }

        return redirect()->back()
            ->with('toast', ['type' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
    }

    public function update()
    {
        $rules = [
            'cart_id'  => 'required|integer',
            'quantity' => 'required|integer|greater_than[0]',
        ];

        $messages = [
            'cart_id' => [
                'required' => 'Item keranjang tidak valid.',
                'integer'  => 'Item keranjang tidak valid.',
            ],
            'quantity' => [
                'required'     => 'Jumlah wajib diisi.',
                'integer'      => 'Jumlah harus berupa angka bulat.',
                'greater_than' => 'Jumlah harus lebih dari 0.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui keranjang.']);
        }

        $cartId    = $this->request->getPost('cart_id');
        $quantity  = (int) $this->request->getPost('quantity');
        $customerId = $this->getCustomerId();

        $cartItem = $this->cartModel->find($cartId);
        if (!$cartItem || $cartItem['customer_id'] != $customerId) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Item keranjang tidak ditemukan.']);
        }

        // Cek stok
        $product = $this->productModel->find($cartItem['product_id']);
        if ($quantity > $product['stock']) {
            return redirect()->back()
                ->with('toast', ['type' => 'warning', 'message' => 'Stok produk tidak mencukupi. Stok tersedia: ' . $product['stock'] . '.']);
        }

        $this->cartModel->update($cartId, ['quantity' => $quantity]);

        return redirect()->back()
            ->with('toast', ['type' => 'success', 'message' => 'Jumlah produk berhasil diperbarui!']);
    }

    public function remove()
    {
        $rules = [
            'cart_id' => 'required|integer',
        ];

        $messages = [
            'cart_id' => [
                'required' => 'Item keranjang tidak valid.',
                'integer'  => 'Item keranjang tidak valid.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menghapus item dari keranjang.']);
        }

        $cartId     = $this->request->getPost('cart_id');
        $customerId = $this->getCustomerId();

        $cartItem = $this->cartModel->find($cartId);
        if (!$cartItem || $cartItem['customer_id'] != $customerId) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Item keranjang tidak ditemukan.']);
        }

        $this->cartModel->delete($cartId);

        return redirect()->back()
            ->with('toast', ['type' => 'success', 'message' => 'Produk berhasil dihapus dari keranjang!']);
    }
}
