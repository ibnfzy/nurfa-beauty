<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class Logout extends BaseController
{
    public function index()
    {
        session()->destroy();
        return redirect()->to('/auth/login')
            ->with('toast', ['type' => 'success', 'message' => 'Anda telah berhasil logout.']);
    }
}
