<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CustomerModel;
use App\Models\PromotionModel;
use App\Models\VoucherModel;
use App\Models\NotificationModel;

class Register extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }

        return view('auth/register');
    }

    public function process()
    {
        helper('text');

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'name' => [
                'required'    => 'Nama lengkap wajib diisi.',
                'min_length'  => 'Nama minimal 3 karakter.',
                'max_length'  => 'Nama maksimal 100 karakter.',
            ],
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Email sudah terdaftar.',
            ],
            'password' => [
                'required'    => 'Password wajib diisi.',
                'min_length'  => 'Password minimal 6 karakter.',
            ],
            'pass_confirm' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak cocok.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $customerModel = new CustomerModel();

        // Create user
        $userData = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'customer',
            'phone'    => $this->request->getPost('phone') ?: null,
        ];

        $userId = $userModel->insert($userData);

        if (!$userId) {
            return redirect()->back()->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal membuat akun. Silakan coba lagi.']);
        }

        // Create customer record
        $customerModel->insert([
            'user_id'          => $userId,
            'loyalty_points'   => 0,
            'membership_level' => 'bronze',
            'total_spending'   => 0,
        ]);

        $customerId = $customerModel->getInsertID();

        // Create "Diskon Pembelian Pertama" promotion for new customer
        $promotionModel = new PromotionModel();
        $today = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));

        $promotionId = $promotionModel->insert([
            'name'            => 'Diskon Pembelian Pertama',
            'type'            => 'discount',
            'discount_type'   => 'percentage',
            'discount_value'  => 20,
            'min_purchase'    => 0,
            'target_segment'  => 'new',
            'is_active'       => 1,
            'start_date'      => $today,
            'end_date'        => $endDate,
        ]);

        // Generate unique voucher code for the customer
        $voucherModel = new VoucherModel();
        $voucherCode = 'WELCOME-' . strtoupper(random_string('alnum', 8));

        $voucherModel->insert([
            'promotion_id' => $promotionId,
            'customer_id'  => $customerId,
            'code'         => $voucherCode,
            'is_used'      => 0,
            'expires_at'   => $endDate,
        ]);

        // Send welcome notification with voucher info
        $notificationModel = new NotificationModel();
        $notificationModel->insert([
            'customer_id' => $customerId,
            'title'       => 'Selamat Datang di Nurfa Beauty!',
            'message'     => 'Terima kasih telah bergabung! Anda mendapatkan voucher diskon 20% untuk pembelian pertama. Gunakan kode: ' . $voucherCode . '. Berlaku hingga ' . date('d F Y', strtotime($endDate)) . '.',
            'type'        => 'promotion',
            'is_read'     => 0,
        ]);

        // Auto login after register
        session()->set([
            'user_id'         => $userId,
            'name'            => $userData['name'],
            'email'           => $userData['email'],
            'role'            => 'customer',
            'logged_in'       => true,
            'welcome_voucher' => $voucherCode,
        ]);

        return redirect()->to('/')
            ->with('toast', ['type' => 'success', 'message' => 'Registrasi berhasil! Selamat datang di Nurfa Beauty. Anda mendapatkan voucher diskon 20% untuk pembelian pertama dengan kode: ' . $voucherCode]);
    }
}
