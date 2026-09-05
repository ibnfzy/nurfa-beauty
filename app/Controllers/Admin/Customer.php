<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\UserModel;
use App\Models\TransactionModel;
use App\Models\LoyaltyPointsLogModel;

class Customer extends BaseController
{
    protected $customerModel;
    protected $userModel;
    protected $transactionModel;
    protected $loyaltyPointsLogModel;

    public function __construct()
    {
        $this->customerModel        = new CustomerModel();
        $this->userModel            = new UserModel();
        $this->transactionModel     = new TransactionModel();
        $this->loyaltyPointsLogModel = new LoyaltyPointsLogModel();
    }

    public function index()
    {
        $search       = $this->request->getGet('q');
        $statusFilter = $this->request->getGet('status'); // 'all', 'active', 'inactive', 'new'

        $db = \Config\Database::connect();
        $sixtyDaysAgo = date('Y-m-d H:i:s', strtotime('-60 days'));

        // Metrik CRM Tahap GET (Akuisisi & Keaktifan Pelanggan)
        $totalCustomers = $this->customerModel->countAllResults();
        $newThisMonth = $this->customerModel
            ->where('MONTH(created_at)', (int) date('m'))
            ->where('YEAR(created_at)', (int) date('Y'))
            ->countAllResults();

        $activeCount = $this->customerModel
            ->where('last_purchase_date >=', $sixtyDaysAgo)
            ->countAllResults();

        $neverBoughtCount = $this->customerModel
            ->where('last_purchase_date IS NULL')
            ->countAllResults();

        $inactiveCount = $this->customerModel
            ->where('last_purchase_date IS NOT NULL')
            ->where('last_purchase_date <', $sixtyDaysAgo)
            ->countAllResults();

        $builder = $this->customerModel->withUser();

        if ($search) {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.phone', $search)
                ->groupEnd();
        }

        if ($statusFilter === 'active') {
            $builder->where('customers.last_purchase_date >=', $sixtyDaysAgo);
        } elseif ($statusFilter === 'inactive') {
            $builder->where('customers.last_purchase_date IS NOT NULL')
                    ->where('customers.last_purchase_date <', $sixtyDaysAgo);
        } elseif ($statusFilter === 'new') {
            $builder->where('customers.last_purchase_date IS NULL');
        }

        $customers = $builder->orderBy('customers.id', 'DESC')->paginate(10);
        $pager     = $this->customerModel->pager;

        // Tambahkan status keaktifan dan akumulasi pembelian bulanan untuk tiap customer
        helper('loyalty');
        $currentMonth = (int) date('m');
        $currentYear  = (int) date('Y');

        foreach ($customers as &$c) {
            // Status keaktifan
            if (empty($c['last_purchase_date'])) {
                $c['activity_status'] = 'new';
                $c['activity_label']  = 'Belum Belanja';
                $c['activity_color']  = 'info';
            } elseif (strtotime($c['last_purchase_date']) >= strtotime('-60 days')) {
                $c['activity_status'] = 'active';
                $c['activity_label']  = 'Aktif';
                $c['activity_color']  = 'success';
            } else {
                $c['activity_status'] = 'inactive';
                $c['activity_label']  = 'Tidak Aktif';
                $c['activity_color']  = 'gray';
            }

            // Hitung kuantiti produk dibeli bulan berjalan (Behavioral promo monitoring)
            $monthlyQtyRow = $db->table('transaction_items')
                ->join('transactions', 'transactions.id = transaction_items.transaction_id')
                ->where('transactions.customer_id', $c['id'])
                ->whereIn('transactions.status', ['completed', 'paid'])
                ->where('MONTH(transactions.transaction_date)', $currentMonth)
                ->where('YEAR(transactions.transaction_date)', $currentYear)
                ->selectSum('transaction_items.quantity', 'total_qty')
                ->get()
                ->getRow();

            $c['monthly_qty']       = (int) ($monthlyQtyRow->total_qty ?? 0);
            $c['behavior_reward']   = get_monthly_behavior_reward($c['monthly_qty']);
        }
        unset($c);

        $data = [
            'pageTitle'        => 'Pelanggan & CRM (Tahap GET)',
            'customers'        => $customers,
            'pager'            => $pager,
            'search'           => $search,
            'statusFilter'     => $statusFilter,
            'totalCustomers'   => $totalCustomers,
            'newThisMonth'     => $newThisMonth,
            'activeCount'      => $activeCount,
            'inactiveCount'    => $inactiveCount,
            'neverBoughtCount' => $neverBoughtCount,
        ];

        return view('admin/customer/index', $data);
    }

    public function detail($id)
    {
        $customer = $this->customerModel->withUser()->find($id);

        if (!$customer) {
            return redirect()->to('/admin/customers')
                ->with('toast', ['type' => 'error', 'message' => 'Pelanggan tidak ditemukan.']);
        }

        $db = \Config\Database::connect();
        helper('loyalty');

        // Status keaktifan
        if (empty($customer['last_purchase_date'])) {
            $customer['activity_status'] = 'new';
            $customer['activity_label']  = 'Belum Belanja';
            $customer['activity_color']  = 'info';
        } elseif (strtotime($customer['last_purchase_date']) >= strtotime('-60 days')) {
            $customer['activity_status'] = 'active';
            $customer['activity_label']  = 'Aktif';
            $customer['activity_color']  = 'success';
        } else {
            $customer['activity_status'] = 'inactive';
            $customer['activity_label']  = 'Tidak Aktif';
            $customer['activity_color']  = 'gray';
        }

        // Kuantitas produk dibeli bulan berjalan
        $monthlyQtyRow = $db->table('transaction_items')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->where('transactions.customer_id', $id)
            ->whereIn('transactions.status', ['completed', 'paid'])
            ->where('MONTH(transactions.transaction_date)', (int) date('m'))
            ->where('YEAR(transactions.transaction_date)', (int) date('Y'))
            ->selectSum('transaction_items.quantity', 'total_qty')
            ->get()
            ->getRow();

        $monthlyProductCount   = (int) ($monthlyQtyRow->total_qty ?? 0);
        $monthlyBehaviorReward = get_monthly_behavior_reward($monthlyProductCount);

        $transactions = $this->transactionModel
            ->where('customer_id', $id)
            ->orderBy('transaction_date', 'DESC')
            ->findAll();

        $pointsLog = $this->loyaltyPointsLogModel
            ->where('customer_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'pageTitle'              => 'Detail Pelanggan',
            'customer'               => $customer,
            'transactions'           => $transactions,
            'pointsLog'              => $pointsLog,
            'monthlyProductCount'    => $monthlyProductCount,
            'monthlyBehaviorReward'  => $monthlyBehaviorReward,
        ];

        return view('admin/customer/detail', $data);
    }

    public function update($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return redirect()->to('/admin/customers')
                ->with('toast', ['type' => 'error', 'message' => 'Pelanggan tidak ditemukan.']);
        }

        $rules = [
            'membership_level' => 'required|in_list[bronze,silver,gold,platinum]',
            'loyalty_points'   => 'required|integer|greater_than_equal_to[0]',
        ];

        $messages = [
            'membership_level' => [
                'required'  => 'Level keanggotaan wajib dipilih.',
                'in_list'   => 'Level keanggotaan tidak valid.',
            ],
            'loyalty_points' => [
                'required'             => 'Poin loyalitas wajib diisi.',
                'integer'              => 'Poin loyalitas harus berupa angka.',
                'greater_than_equal_to' => 'Poin loyalitas tidak boleh negatif.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui data pelanggan. Periksa kembali data Anda.']);
        }

        $this->customerModel->update($id, [
            'membership_level' => $this->request->getPost('membership_level'),
            'loyalty_points'   => $this->request->getPost('loyalty_points'),
        ]);

        return redirect()->to('/admin/customers/' . $id)
            ->with('toast', ['type' => 'success', 'message' => 'Data pelanggan berhasil diperbarui!']);
    }
}
