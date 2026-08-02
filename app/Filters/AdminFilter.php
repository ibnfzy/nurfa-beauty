<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'warning', 'message' => 'Silakan login terlebih dahulu.']);
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')
                ->with('toast', ['type' => 'error', 'message' => 'Anda tidak memiliki akses ke halaman ini.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
