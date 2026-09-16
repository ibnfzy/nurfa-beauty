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
        $monthStart = date('Y-m-01') . ' 00:00:00';

        // Subquery akumulasi belanja bulan berjalan (channel online + offline yang lunas)
        $monthlySpendSub = "SELECT customer_id, SUM(final_amount) as month_total
                            FROM transactions
                            WHERE status IN ('completed','paid')
                            AND transaction_date >= '{$monthStart}'
                            GROUP BY customer_id";

        // Metrik CRM Tahap GET (Akuisisi & Keaktifan Pelanggan)
        $totalCustomers = $this->customerModel->countAllResults();
        $newThisMonth = $this->customerModel
            ->where('MONTH(created_at)', (int) date('m'))
            ->where('YEAR(created_at)', (int) date('Y'))
            ->countAllResults();

        $neverBoughtCount = $this->customerModel
            ->where('last_purchase_date IS NULL')
            ->countAllResults();

        // AKTIF = akumulasi belanja bulan berjalan minimal Rp 300.000
        $activeRow = $db->query(
            "SELECT COUNT(DISTINCT c.id) as cnt
             FROM customers c
             INNER JOIN ({$monthlySpendSub}) ms ON ms.customer_id = c.id
             WHERE ms.month_total >= 300000"
        )->getRowArray();
        $activeCount = (int) ($activeRow['cnt'] ?? 0);

        // BELUM AKTIF = pernah belanja namun akumulasi bulan ini belum mencapai Rp 300.000
        $inactiveRow = $db->query(
            "SELECT COUNT(DISTINCT c.id) as cnt
             FROM customers c
             LEFT JOIN ({$monthlySpendSub}) ms ON ms.customer_id = c.id
             WHERE c.last_purchase_date IS NOT NULL
             AND COALESCE(ms.month_total, 0) < 300000"
        )->getRowArray();
        $inactiveCount = (int) ($inactiveRow['cnt'] ?? 0);

        $builder = $this->customerModel->withUser();

        // Join akumulasi belanja bulan berjalan ke daftar pelanggan
        $builder->select('COALESCE(ms.month_total, 0) as monthly_total_spend');
        $builder->join('(' . $monthlySpendSub . ') ms', 'ms.customer_id = customers.id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.phone', $search)
                ->groupEnd();
        }

        if ($statusFilter === 'active') {
            // AKTIF = akumulasi belanja bulan berjalan >= 300000
            $builder->where('COALESCE(ms.month_total, 0) >=', 300000);
        } elseif ($statusFilter === 'inactive') {
            // BELUM AKTIF = pernah belanja tapi akumulasi bulan ini < 300000
            $builder->where('customers.last_purchase_date IS NOT NULL')
                    ->where('COALESCE(ms.month_total, 0) <', 300000);
        } elseif ($statusFilter === 'new') {
            $builder->where('customers.last_purchase_date IS NULL');
        }

        $customers = $builder->orderBy('customers.id', 'DESC')->paginate(10);
        $pager     = $this->customerModel->pager;

        // Tambahkan status & label berdasarkan akumulasi belanja bulanan
        helper('loyalty');
        $currentMonth = (int) date('m');
        $currentYear  = (int) date('Y');

        foreach ($customers as &$c) {
            // Pastikan monthly_total_spend ada (dari join, fallback 0)
            $c['monthly_total_spend'] = (int) ($c['monthly_total_spend'] ?? 0);
            // Status berdasarkan akumulasi bulanan (>= 300k = AKTIF)
            if ($c['monthly_total_spend'] >= 300000) {
                $c['monthly_status']   = 'AKTIF';
                $c['activity_status']  = 'active';
                $c['activity_label']   = 'Aktif';
                $c['activity_color']   = 'success';
            } elseif (!empty($c['last_purchase_date'])) {
                $c['monthly_status']   = 'BELUM AKTIF';
                $c['activity_status']  = 'inactive';
                $c['activity_label']   = 'Belum Aktif';
                $c['activity_color']   = 'gray';
            } else {
                $c['monthly_status']   = 'BELUM AKTIF';
                $c['activity_status']  = 'new';
                $c['activity_label']   = 'Belum Belanja';
                $c['activity_color']   = 'info';
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

        // Hitung total belanja bulan berjalan untuk pelanggan ini
        $monthStart = date('Y-m-01') . ' 00:00:00';
        $monthlySpendRow = $db->table('transactions')
            ->where('customer_id', $id)
            ->whereIn('status', ['completed', 'paid'])
            ->where('transaction_date >=', $monthStart)
            ->selectSum('final_amount', 'total_spend')
            ->get()
            ->getRow();
        $customer['monthly_total_spend'] = (int) ($monthlySpendRow->total_spend ?? 0);

        // Status berdasarkan akumulasi belanja bulanan (>= 300k = AKTIF)
        if ($customer['monthly_total_spend'] >= 300000) {
            $customer['monthly_status']  = 'AKTIF';
            $customer['activity_status'] = 'active';
            $customer['activity_label']  = 'Aktif';
            $customer['activity_color']  = 'success';
        } elseif (!empty($customer['last_purchase_date'])) {
            $customer['monthly_status']  = 'BELUM AKTIF';
            $customer['activity_status'] = 'inactive';
            $customer['activity_label']  = 'Belum Aktif';
            $customer['activity_color']  = 'gray';
        } else {
            $customer['monthly_status']  = 'BELUM AKTIF';
            $customer['activity_status'] = 'new';
            $customer['activity_label']  = 'Belum Belanja';
            $customer['activity_color']  = 'info';
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
