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
        $search = $this->request->getGet('q');

        $builder = $this->customerModel->withUser();

        if ($search) {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
        }

        $data = [
            'pageTitle'  => 'Pelanggan',
            'customers'  => $builder->orderBy('customers.id', 'DESC')->paginate(10),
            'pager'      => $this->customerModel->pager,
            'search'     => $search,
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

        $transactions = $this->transactionModel
            ->where('customer_id', $id)
            ->orderBy('transaction_date', 'DESC')
            ->findAll();

        $pointsLog = $this->loyaltyPointsLogModel
            ->where('customer_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'pageTitle'   => 'Detail Pelanggan',
            'customer'    => $customer,
            'transactions' => $transactions,
            'pointsLog'   => $pointsLog,
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
