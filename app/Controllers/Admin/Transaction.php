<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\BankAccountModel;

class Transaction extends BaseController
{
    protected $transactionModel;
    protected $transactionItemModel;
    protected $customerModel;
    protected $productModel;
    protected $bankAccountModel;

    public function __construct()
    {
        $this->transactionModel     = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
        $this->customerModel        = new CustomerModel();
        $this->productModel         = new ProductModel();
        $this->bankAccountModel     = new BankAccountModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status');

        $builder = $this->transactionModel->withCustomer();

        if ($search) {
            $builder->groupStart()
                ->like('transactions.transaction_code', $search)
                ->orLike('users.name', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('transactions.status', $status);
        }

        $data = [
            'pageTitle'    => 'Transaksi',
            'transactions' => $builder->orderBy('transactions.id', 'DESC')->paginate(15),
            'pager'        => $this->transactionModel->pager ?? null,
            'search'       => $search ?? '',
            'status'       => $status ?? null,
        ];

        return view('admin/transaction/index', $data);
    }

    public function create()
    {
        $customers = $this->customerModel
            ->select('customers.*, users.name, users.email')
            ->join('users', 'users.id = customers.user_id')
            ->orderBy('users.name', 'ASC')
            ->findAll();

        $products = $this->productModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $bankAccounts = $this->bankAccountModel
            ->where('is_active', 1)
            ->findAll();

        $data = [
            'pageTitle'    => 'Input Transaksi',
            'customers'    => $customers ?? [],
            'products'     => $products ?? [],
            'bankAccounts' => $bankAccounts ?? [],
        ];

        return view('admin/transaction/create', $data);
    }

    public function store()
    {
        $rules = [
            'customer_id'        => 'required|integer',
            'items'              => 'required',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity'   => 'required|integer|greater_than[0]',
            'notes'              => 'permit_empty',
        ];

        $messages = [
            'customer_id' => [
                'required' => 'Pelanggan wajib dipilih.',
                'integer'  => 'Pelanggan tidak valid.',
            ],
            'items' => [
                'required' => 'Minimal satu produk harus ditambahkan.',
            ],
            'items.*.product_id' => [
                'required' => 'Produk wajib dipilih.',
                'integer'  => 'Produk tidak valid.',
            ],
            'items.*.quantity' => [
                'required'      => 'Jumlah wajib diisi.',
                'integer'       => 'Jumlah harus berupa angka bulat.',
                'greater_than'  => 'Jumlah harus lebih dari 0.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menyimpan transaksi. Periksa kembali data Anda.']);
        }

        $items      = $this->request->getPost('items');
        $customerId = $this->request->getPost('customer_id');

        $totalAmount = 0;
        $transactionItems = [];

        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            $product = $this->productModel->find($item['product_id']);

            if (!$product) {
                return redirect()->back()
                    ->withInput()
                    ->with('toast', ['type' => 'error', 'message' => 'Produk tidak ditemukan.']);
            }

            if ($item['quantity'] > $product['stock']) {
                return redirect()->back()
                    ->withInput()
                    ->with('toast', ['type' => 'error', 'message' => 'Stok produk "' . $product['name'] . '" tidak mencukupi. Stok tersedia: ' . $product['stock'] . '.']);
            }

            $subtotal = $product['price'] * $item['quantity'];
            $totalAmount += $subtotal;

            $transactionItems[] = [
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $product['price'],
                'subtotal'   => $subtotal,
            ];
        }

        if (empty($transactionItems)) {
            return redirect()->back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Minimal satu produk harus ditambahkan.']);
        }

        $transactionCode = 'TRX-' . date('Ymd') . '-' . strtoupper(random_string('alnum', 6));

        // Create transaction
        $this->transactionModel->save([
            'customer_id'        => $customerId,
            'transaction_code'   => $transactionCode,
            'transaction_date'   => date('Y-m-d H:i:s'),
            'total_amount'       => $totalAmount,
            'shipping_cost'      => 0,
            'discount_amount'    => 0,
            'final_amount'       => $totalAmount,
            'payment_method'     => 'offline',
            'payment_status'     => 'verified',
            'payment_verified_at' => date('Y-m-d H:i:s'),
            'payment_verified_by' => session()->get('user_id'),
            'status'             => 'paid',
            'notes'              => $this->request->getPost('notes'),
        ]);

        $transactionId = $this->transactionModel->getInsertID();

        // Create transaction items & reduce stock
        foreach ($transactionItems as $tItem) {
            $this->transactionItemModel->save([
                'transaction_id' => $transactionId,
                'product_id'     => $tItem['product_id'],
                'quantity'       => $tItem['quantity'],
                'price'          => $tItem['price'],
                'subtotal'       => $tItem['subtotal'],
            ]);

            // Reduce stock
            $this->productModel->update($tItem['product_id'], [
                'stock' => $this->productModel->find($tItem['product_id'])['stock'] - $tItem['quantity'],
            ]);
        }

        // Update customer total_spending + loyalty_points
        $customer = $this->customerModel->find($customerId);
        if ($customer) {
            $this->customerModel->update($customerId, [
                'total_spending' => ($customer['total_spending'] ?? 0) + $totalAmount,
                'loyalty_points' => ($customer['loyalty_points'] ?? 0) + (int) floor($totalAmount / 10000),
            ]);
        }

        return redirect()->to('/admin/transactions')
            ->with('toast', ['type' => 'success', 'message' => 'Transaksi berhasil disimpan!']);
    }

    public function detail($id)
    {
        $transaction = $this->transactionModel
            ->select('transactions.*, customers.user_id, customers.loyalty_points, users.name as customer_name, users.email as customer_email, users.phone as customer_phone')
            ->join('customers', 'customers.id = transactions.customer_id', 'left')
            ->join('users', 'users.id = customers.user_id', 'left')
            ->find($id);

        if (!$transaction) {
            return redirect()->to('/admin/transactions')
                ->with('toast', ['type' => 'error', 'message' => 'Transaksi tidak ditemukan.']);
        }

        // Get transaction items with product info
        $items = $this->transactionItemModel
            ->withProduct()
            ->where('transaction_id', $id)
            ->findAll();

        // Get shipping address
        $shippingAddress = null;
        if ($transaction['shipping_address_id']) {
            $shippingAddress = $this->transactionModel
                ->select('shipping_addresses.*')
                ->join('shipping_addresses', 'shipping_addresses.id = transactions.shipping_address_id', 'left')
                ->where('transactions.id', $id)
                ->first();
        }

        // Get verified by user
        $verifiedBy = null;
        if ($transaction['payment_verified_by']) {
            $verifierModel = new \App\Models\UserModel();
            $verifiedBy = $verifierModel->find($transaction['payment_verified_by']);
        }

        $data = [
            'pageTitle'      => 'Detail Transaksi',
            'transaction'    => $transaction ?? [],
            'items'          => $items ?? [],
            'shippingAddress' => $shippingAddress ?? null,
            'verifiedBy'     => $verifiedBy ?? null,
        ];

        return view('admin/transaction/detail', $data);
    }
}
