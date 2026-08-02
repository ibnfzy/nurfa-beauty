<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CustomerModel;
use App\Models\LoyaltyPointsLogModel;
use App\Models\NotificationModel;

class Payment extends BaseController
{
    protected TransactionModel $transactionModel;
    protected TransactionItemModel $transactionItemModel;
    protected CustomerModel $customerModel;
    protected LoyaltyPointsLogModel $loyaltyPointsLogModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->transactionModel     = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
        $this->customerModel          = new CustomerModel();
        $this->loyaltyPointsLogModel  = new LoyaltyPointsLogModel();
        $this->notificationModel      = new NotificationModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $filter = $this->request->getGet('filter');

        $builder = $this->transactionModel->withCustomer()
            ->where('transactions.payment_proof IS NOT NULL', null, false);

        if ($filter === 'verified') {
            $builder->where('transactions.payment_status', 'verified');
        } elseif ($filter === 'rejected') {
            $builder->where('transactions.payment_status', 'rejected');
        } else {
            $builder->where('transactions.payment_status', 'pending');
        }

        if ($search) {
            $builder->groupStart()
                ->like('transactions.transaction_code', $search)
                ->orLike('users.name', $search)
                ->groupEnd();
        }

        $data = [
            'pageTitle'     => 'Verifikasi Pembayaran',
            'transactions'  => $builder->orderBy('transactions.id', 'DESC')->paginate(15),
            'pager'         => $this->transactionModel->pager ?? null,
            'search'        => $search ?? '',
            'filter'        => $filter ?? 'pending',
        ];

        return view('admin/payment/index', $data);
    }

    public function detail(int $id)
    {
        $transaction = $this->transactionModel
            ->select('transactions.*, customers.user_id, customers.loyalty_points, users.name as customer_name, users.email as customer_email, users.phone as customer_phone')
            ->join('customers', 'customers.id = transactions.customer_id', 'left')
            ->join('users', 'users.id = customers.user_id', 'left')
            ->find($id);

        if (!$transaction) {
            return redirect()->to('/admin/payments')
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
            'pageTitle'      => 'Detail Pembayaran',
            'transaction'    => $transaction ?? [],
            'items'          => $items ?? [],
            'shippingAddress' => $shippingAddress ?? null,
            'verifiedBy'     => $verifiedBy ?? null,
        ];

        return view('admin/payment/detail', $data);
    }

    public function verify(int $id)
    {
        $transaction = $this->transactionModel->find($id);

        if (!$transaction) {
            return redirect()->to('/admin/payments')
                ->with('toast', ['type' => 'error', 'message' => 'Transaksi tidak ditemukan.']);
        }

        if ($transaction['payment_status'] !== 'pending') {
            return redirect()->to('/admin/payments/detail/' . $id)
                ->with('toast', ['type' => 'warning', 'message' => 'Pembayaran sudah diverifikasi atau ditolak sebelumnya.']);
        }

        $this->transactionModel->update($id, [
            'payment_status'      => 'verified',
            'status'              => 'processing',
            'payment_verified_at' => date('Y-m-d H:i:s'),
            'payment_verified_by' => session()->get('user_id'),
        ]);

        // Tambah poin loyalitas setelah pembayaran diverifikasi
        helper('loyalty');
        $customerId = $transaction['customer_id'];
        $customer   = $this->customerModel->find($customerId);

        if ($customer) {
            $level  = $customer['membership_level'] ?? 'bronze';
            $amount = $transaction['final_amount'] ?? 0;
            $points      = calculate_loyalty_points($amount, $level);
            $bonusPoints = calculate_bonus_points($amount);
            $points     += $bonusPoints;

            if ($points > 0) {
                $this->loyaltyPointsLogModel->insert([
                    'customer_id'    => $customerId,
                    'points'         => $points,
                    'type'           => 'earn',
                    'description'    => 'Poin dari transaksi ' . ($transaction['transaction_code'] ?? ''),
                    'transaction_id' => $id,
                ]);

                $newSpending = ($customer['total_spending'] ?? 0) + $amount;
                $updateData = [
                    'loyalty_points'     => ($customer['loyalty_points'] ?? 0) + $points,
                    'total_spending'     => $newSpending,
                    'last_purchase_date' => date('Y-m-d H:i:s'),
                ];

                // Cek & update membership level
                $newLevel = get_membership_level($newSpending);
                if ($newLevel !== $level) {
                    $updateData['membership_level'] = $newLevel;

                    $benefits = get_membership_benefits($newLevel);
                    $this->notificationModel->insert([
                        'customer_id' => $customerId,
                        'title'       => 'Selamat! Level Membership Naik!',
                        'message'     => 'Anda telah naik ke level ' . $benefits['label'] . '! Nikmati benefit: ' . implode(', ', $benefits['perks']) . '.',
                        'type'        => 'loyalty',
                        'is_read'     => 0,
                    ]);
                }

                $this->customerModel->update($customerId, $updateData);

                $this->notificationModel->insert([
                    'customer_id' => $customerId,
                    'title'       => 'Poin Loyalitas Bertambah!',
                    'message'     => 'Anda mendapatkan ' . $points . ' poin dari transaksi ' . ($transaction['transaction_code'] ?? '') . ($bonusPoints > 0 ? ' (termasuk bonus ' . $bonusPoints . ' poin)' : '') . '. Total poin: ' . (($customer['loyalty_points'] ?? 0) + $points) . '.',
                    'type'        => 'loyalty',
                    'is_read'     => 0,
                ]);
            }
        }

        return redirect()->to('/admin/payments/detail/' . $id)
            ->with('toast', ['type' => 'success', 'message' => 'Pembayaran berhasil diverifikasi!']);
    }

    public function reject(int $id)
    {
        $transaction = $this->transactionModel->find($id);

        if (!$transaction) {
            return redirect()->to('/admin/payments')
                ->with('toast', ['type' => 'error', 'message' => 'Transaksi tidak ditemukan.']);
        }

        $rules = [
            'payment_rejection_reason' => 'required',
        ];

        $messages = [
            'payment_rejection_reason' => [
                'required' => 'Alasan penolakan wajib diisi.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menolak pembayaran. Alasan penolakan wajib diisi.']);
        }

        $this->transactionModel->update($id, [
            'payment_status'          => 'rejected',
            'status'                  => 'pending_payment',
            'payment_rejection_reason' => $this->request->getPost('payment_rejection_reason'),
        ]);

        return redirect()->to('/admin/payments/detail/' . $id)
            ->with('toast', ['type' => 'error', 'message' => 'Pembayaran ditolak.']);
    }
}
