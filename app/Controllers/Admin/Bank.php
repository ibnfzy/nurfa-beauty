<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BankAccountModel;

class Bank extends BaseController
{
    protected $bankAccountModel;

    public function __construct()
    {
        $this->bankAccountModel = new BankAccountModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Rekening Bank',
            'banks'     => $this->bankAccountModel->orderBy('id', 'DESC')->findAll(),
        ];

        return view('admin/bank/index', $data);
    }

    public function store()
    {
        $rules = [
            'bank_name'      => 'required|max_length[50]',
            'account_number' => 'required|max_length[50]',
            'account_name'   => 'required|max_length[100]',
        ];

        $messages = [
            'bank_name' => [
                'required'    => 'Nama bank wajib diisi.',
                'max_length'  => 'Nama bank maksimal 50 karakter.',
            ],
            'account_number' => [
                'required'    => 'Nomor rekening wajib diisi.',
                'max_length'  => 'Nomor rekening maksimal 50 karakter.',
            ],
            'account_name' => [
                'required'    => 'Nama pemilik rekening wajib diisi.',
                'max_length'  => 'Nama pemilik rekening maksimal 100 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menambahkan rekening. Periksa kembali data Anda.']);
        }

        $this->bankAccountModel->save([
            'bank_name'      => $this->request->getPost('bank_name'),
            'account_number' => $this->request->getPost('account_number'),
            'account_name'   => $this->request->getPost('account_name'),
            'is_active'      => 1,
        ]);

        return redirect()->to('/admin/banks')
            ->with('toast', ['type' => 'success', 'message' => 'Rekening bank berhasil ditambahkan!']);
    }

    public function update($id)
    {
        $bank = $this->bankAccountModel->find($id);

        if (!$bank) {
            return redirect()->to('/admin/banks')
                ->with('toast', ['type' => 'error', 'message' => 'Rekening bank tidak ditemukan.']);
        }

        $rules = [
            'bank_name'      => 'required|max_length[50]',
            'account_number' => 'required|max_length[50]',
            'account_name'   => 'required|max_length[100]',
        ];

        $messages = [
            'bank_name' => [
                'required'    => 'Nama bank wajib diisi.',
                'max_length'  => 'Nama bank maksimal 50 karakter.',
            ],
            'account_number' => [
                'required'    => 'Nomor rekening wajib diisi.',
                'max_length'  => 'Nomor rekening maksimal 50 karakter.',
            ],
            'account_name' => [
                'required'    => 'Nama pemilik rekening wajib diisi.',
                'max_length'  => 'Nama pemilik rekening maksimal 100 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui rekening. Periksa kembali data Anda.']);
        }

        $this->bankAccountModel->update($id, [
            'bank_name'      => $this->request->getPost('bank_name'),
            'account_number' => $this->request->getPost('account_number'),
            'account_name'   => $this->request->getPost('account_name'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/banks')
            ->with('toast', ['type' => 'success', 'message' => 'Rekening bank berhasil diperbarui!']);
    }

    public function delete($id)
    {
        $bank = $this->bankAccountModel->find($id);

        if (!$bank) {
            return redirect()->to('/admin/banks')
                ->with('toast', ['type' => 'error', 'message' => 'Rekening bank tidak ditemukan.']);
        }

        $this->bankAccountModel->delete($id);

        return redirect()->to('/admin/banks')
            ->with('toast', ['type' => 'success', 'message' => 'Rekening bank berhasil dihapus!']);
    }
}
