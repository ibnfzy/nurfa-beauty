<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to($this->getRedirectUrl());
        }

        return view('auth/login');
    }

    public function process()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        $messages = [
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
            ],
            'password' => [
                'required'    => 'Password wajib diisi.',
                'min_length'  => 'Password minimal 6 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Email atau password salah.']);
        }

        // Set session
        session()->set([
            'user_id'   => $user['id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to($this->getRedirectUrl())
            ->with('toast', ['type' => 'success', 'message' => 'Selamat datang, ' . $user['name'] . '!']);
    }

    private function getRedirectUrl(): string
    {
        if (session()->get('role') === 'admin') {
            return '/admin';
        }
        return '/';
    }
}
