<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\UserModel;

class Profile extends BaseController
{
    protected CustomerModel $customerModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->userModel     = new UserModel();
    }

    protected function getCustomerId()
    {
        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();
        return $customer ? $customer['id'] : null;
    }

    public function index()
    {
        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();

        if (!$customer) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $user = $this->userModel->find($userId);

        $data = [
            'pageTitle' => 'Profil Saya',
            'customer'  => $customer,
            'user'      => $user,
        ];

        return view('customer/profile/index', $data);
    }

    public function update()
    {
        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();

        if (!$customer) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $rules = [
            'name'  => 'required',
            'email' => 'required|valid_email',
        ];

        $messages = [
            'name' => [
                'required' => 'Nama lengkap wajib diisi.',
            ],
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui profil. Periksa kembali data Anda.']);
        }

        // Cek apakah email sudah digunakan oleh user lain
        $email = $this->request->getPost('email');
        $existingUser = $this->userModel->where('email', $email)->where('id !=', $userId)->first();
        if ($existingUser) {
            return redirect()->back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Email sudah digunakan oleh pengguna lain.']);
        }

        $userData = [
            'name'       => $this->request->getPost('name'),
            'email'      => $email,
            'phone'      => $this->request->getPost('phone'),
            'address'    => $this->request->getPost('address'),
            'birth_date' => $this->request->getPost('birth_date'),
        ];

        // Jika password diisi, validasi dan update
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $passwordRules = [
                'password' => 'min_length[6]',
            ];

            $passwordMessages = [
                'password' => [
                    'min_length' => 'Password minimal 6 karakter.',
                ],
            ];

            if (!$this->validate($passwordRules, $passwordMessages)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors())
                    ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui profil. Periksa kembali data Anda.']);
            }

            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $userData);

        // Update session name agar navbar ter-update
        session()->set('name', $userData['name']);

        return redirect()->to('/profile')
            ->with('toast', ['type' => 'success', 'message' => 'Profil berhasil diperbarui!']);
    }
}
