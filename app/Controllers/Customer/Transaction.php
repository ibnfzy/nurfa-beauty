<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\ShippingAddressModel;
use App\Models\ReviewModel;

class Transaction extends BaseController
{
    protected CustomerModel $customerModel;
    protected TransactionModel $transactionModel;
    protected TransactionItemModel $transactionItemModel;
    protected ShippingAddressModel $shippingAddressModel;
    protected ReviewModel $reviewModel;

    public function __construct()
    {
        $this->customerModel        = new CustomerModel();
        $this->transactionModel     = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
        $this->shippingAddressModel = new ShippingAddressModel();
        $this->reviewModel          = new ReviewModel();
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
        $status     = $this->request->getGet('status');

        $builder = $this->transactionModel
            ->where('customer_id', $customerId);

        if ($status) {
            $builder->where('status', $status);
        }

        $transactions = $builder
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $pager = $this->transactionModel->pager;

        // Hitung jumlah item per transaksi
        $transactionIds = array_column($transactions, 'id');
        $itemCounts = [];

        if (!empty($transactionIds)) {
            $items = $this->transactionItemModel
                ->select('transaction_id, COUNT(*) as item_count')
                ->whereIn('transaction_id', $transactionIds)
                ->groupBy('transaction_id')
                ->findAll();

            foreach ($items as $item) {
                $itemCounts[$item['transaction_id']] = $item['item_count'];
            }
        }

        $data = [
            'pageTitle'    => 'Pesanan Saya',
            'transactions' => $transactions ?? [],
            'pager'        => $pager ?? null,
            'status'       => $status ?? null,
            'itemCounts'   => $itemCounts ?? [],
        ];

        return view('customer/transaction/index', $data);
    }

    public function detail(int $id)
    {
        $customerId = $this->getCustomerId();

        $transaction = $this->transactionModel->find($id);

        if (!$transaction || $transaction['customer_id'] != $customerId) {
            return redirect()->to('/transactions')
                ->with('toast', ['type' => 'error', 'message' => 'Transaksi tidak ditemukan.']);
        }

        // Ambil item transaksi dengan detail produk
        $items = $this->transactionItemModel
            ->withProduct()
            ->where('transaction_id', $id)
            ->findAll();

        // Ambil review yang sudah ada untuk setiap produk
        $customerId = $this->getCustomerId();
        foreach ($items as &$item) {
            $item['existing_review'] = $this->reviewModel
                ->where('customer_id', $customerId)
                ->where('product_id', $item['product_id'])
                ->first();
        }
        unset($item);

        // Ambil alamat pengiriman
        $shippingAddress = null;
        if ($transaction['shipping_address_id']) {
            $shippingAddress = $this->shippingAddressModel->find($transaction['shipping_address_id']);
        }

        $data = [
            'pageTitle'       => 'Detail Pesanan - ' . $transaction['transaction_code'],
            'transaction'     => $transaction,
            'items'           => $items,
            'shippingAddress' => $shippingAddress,
        ];

        return view('customer/transaction/detail', $data);
    }
}
