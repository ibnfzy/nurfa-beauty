<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\LoyaltyPointsLogModel;
use App\Models\VoucherModel;

class Loyalty extends BaseController
{
    protected CustomerModel $customerModel;
    protected LoyaltyPointsLogModel $loyaltyPointsLogModel;
    protected VoucherModel $voucherModel;

    public function __construct()
    {
        $this->customerModel        = new CustomerModel();
        $this->loyaltyPointsLogModel = new LoyaltyPointsLogModel();
        $this->voucherModel         = new VoucherModel();
    }

    public function index()
    {
        helper('loyalty');

        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();

        if (!$customer) {
            return redirect()->to('/')
                ->with('toast', ['type' => 'error', 'message' => 'Data pelanggan tidak ditemukan.']);
        }

        $level    = $customer['membership_level'] ?? 'bronze';
        $benefits = get_membership_benefits($level);
        $nextLevel = get_next_membership_level($customer['total_spending'] ?? 0);

        // Riwayat poin
        $pointHistory = $this->loyaltyPointsLogModel
            ->where('customer_id', $customer['id'])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $pager = $this->loyaltyPointsLogModel->pager;

        // Voucher aktif milik customer
        $vouchers = $this->voucherModel
            ->join('promotions', 'promotions.id = vouchers.promotion_id')
            ->where('vouchers.customer_id', $customer['id'])
            ->where('vouchers.is_used', 0)
            ->where('vouchers.expires_at >', date('Y-m-d H:i:s'))
            ->select('vouchers.*, promotions.name as promotion_name, promotions.discount_type, promotions.discount_value')
            ->orderBy('vouchers.id', 'DESC')
            ->findAll();

        $data = [
            'pageTitle'    => 'Loyalitas Saya',
            'customer'     => $customer ?? [],
            'level'        => $level,
            'benefits'     => $benefits,
            'nextLevel'    => $nextLevel,
            'pointHistory' => $pointHistory ?? [],
            'pager'        => $pager ?? null,
            'vouchers'     => $vouchers ?? [],
        ];

        return view('customer/loyalty/index', $data);
    }
}
