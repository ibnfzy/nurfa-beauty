<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\ShippingAddressModel;
use App\Models\CustomerModel;

class Address extends BaseController
{
    protected $addressModel;
    protected $customerModel;

    public function __construct()
    {
        $this->addressModel  = new ShippingAddressModel();
        $this->customerModel = new CustomerModel();
    }

    protected function getCustomerId()
    {
        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();
        return $customer ? $customer['id'] : null;
    }

    public function index()
    {
        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $addresses = $this->addressModel
            ->where('customer_id', $customerId)
            ->orderBy('is_default', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'pageTitle' => 'Alamat Pengiriman',
            'addresses' => $addresses ?? [],
        ];

        return view('customer/address/index', $data);
    }

    public function store()
    {
        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $rules = [
            'label'          => 'required|max_length[50]',
            'recipient_name' => 'required|max_length[100]',
            'phone'          => 'required|max_length[20]',
            'address'        => 'required',
            'province'       => 'required|max_length[100]',
            'city'           => 'required|max_length[100]',
            'postal_code'    => 'required|max_length[10]',
        ];

        $messages = [
            'label' => [
                'required'   => 'Label alamat wajib diisi.',
                'max_length' => 'Label alamat maksimal 50 karakter.',
            ],
            'recipient_name' => [
                'required'   => 'Nama penerima wajib diisi.',
                'max_length' => 'Nama penerima maksimal 100 karakter.',
            ],
            'phone' => [
                'required'   => 'Nomor telepon wajib diisi.',
                'max_length' => 'Nomor telepon maksimal 20 karakter.',
            ],
            'address' => [
                'required' => 'Alamat lengkap wajib diisi.',
            ],
            'province' => [
                'required'   => 'Provinsi wajib diisi.',
                'max_length' => 'Provinsi maksimal 100 karakter.',
            ],
            'city' => [
                'required'   => 'Kota wajib diisi.',
                'max_length' => 'Kota maksimal 100 karakter.',
            ],
            'postal_code' => [
                'required'   => 'Kode pos wajib diisi.',
                'max_length' => 'Kode pos maksimal 10 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal menambahkan alamat. Periksa kembali data Anda.']);
        }

        $isDefault = $this->request->getPost('is_default') ? 1 : 0;

        // If this is the first address, make it default
        $addressCount = $this->addressModel->where('customer_id', $customerId)->countAllResults();
        if ($addressCount === 0) {
            $isDefault = 1;
        }

        // If setting as default, unset other defaults
        if ($isDefault) {
            $this->addressModel->where('customer_id', $customerId)->set(['is_default' => 0])->update();
        }

        $this->addressModel->save([
            'customer_id'    => $customerId,
            'label'          => $this->request->getPost('label'),
            'recipient_name' => $this->request->getPost('recipient_name'),
            'phone'          => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address'),
            'province'       => $this->request->getPost('province'),
            'city'           => $this->request->getPost('city'),
            'district'       => $this->request->getPost('district'),
            'postal_code'    => $this->request->getPost('postal_code'),
            'is_default'     => $isDefault,
        ]);

        return redirect()->to('/addresses')
            ->with('toast', ['type' => 'success', 'message' => 'Alamat berhasil ditambahkan!']);
    }

    public function update($id)
    {
        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $address = $this->addressModel->find($id);

        if (!$address || $address['customer_id'] != $customerId) {
            return redirect()->to('/addresses')
                ->with('toast', ['type' => 'error', 'message' => 'Alamat tidak ditemukan.']);
        }

        $rules = [
            'label'          => 'required|max_length[50]',
            'recipient_name' => 'required|max_length[100]',
            'phone'          => 'required|max_length[20]',
            'address'        => 'required',
            'province'       => 'required|max_length[100]',
            'city'           => 'required|max_length[100]',
            'postal_code'    => 'required|max_length[10]',
        ];

        $messages = [
            'label' => [
                'required'   => 'Label alamat wajib diisi.',
                'max_length' => 'Label alamat maksimal 50 karakter.',
            ],
            'recipient_name' => [
                'required'   => 'Nama penerima wajib diisi.',
                'max_length' => 'Nama penerima maksimal 100 karakter.',
            ],
            'phone' => [
                'required'   => 'Nomor telepon wajib diisi.',
                'max_length' => 'Nomor telepon maksimal 20 karakter.',
            ],
            'address' => [
                'required' => 'Alamat lengkap wajib diisi.',
            ],
            'province' => [
                'required'   => 'Provinsi wajib diisi.',
                'max_length' => 'Provinsi maksimal 100 karakter.',
            ],
            'city' => [
                'required'   => 'Kota wajib diisi.',
                'max_length' => 'Kota maksimal 100 karakter.',
            ],
            'postal_code' => [
                'required'   => 'Kode pos wajib diisi.',
                'max_length' => 'Kode pos maksimal 10 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memperbarui alamat. Periksa kembali data Anda.']);
        }

        $isDefault = $this->request->getPost('is_default') ? 1 : 0;

        // If setting as default, unset other defaults
        if ($isDefault) {
            $this->addressModel->where('customer_id', $customerId)->where('id !=', $id)->set(['is_default' => 0])->update();
        }

        $this->addressModel->update($id, [
            'label'          => $this->request->getPost('label'),
            'recipient_name' => $this->request->getPost('recipient_name'),
            'phone'          => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address'),
            'province'       => $this->request->getPost('province'),
            'city'           => $this->request->getPost('city'),
            'district'       => $this->request->getPost('district'),
            'postal_code'    => $this->request->getPost('postal_code'),
            'is_default'     => $isDefault,
        ]);

        return redirect()->to('/addresses')
            ->with('toast', ['type' => 'success', 'message' => 'Alamat berhasil diperbarui!']);
    }

    public function delete($id)
    {
        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $address = $this->addressModel->find($id);

        if (!$address || $address['customer_id'] != $customerId) {
            return redirect()->to('/addresses')
                ->with('toast', ['type' => 'error', 'message' => 'Alamat tidak ditemukan.']);
        }

        $this->addressModel->delete($id);

        return redirect()->to('/addresses')
            ->with('toast', ['type' => 'success', 'message' => 'Alamat berhasil dihapus!']);
    }

    public function setDefault($id)
    {
        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return redirect()->to('/auth/login')
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
        }

        $address = $this->addressModel->find($id);

        if (!$address || $address['customer_id'] != $customerId) {
            return redirect()->to('/addresses')
                ->with('toast', ['type' => 'error', 'message' => 'Alamat tidak ditemukan.']);
        }

        // Unset all defaults for this customer
        $this->addressModel->where('customer_id', $customerId)->set(['is_default' => 0])->update();

        // Set this address as default
        $this->addressModel->update($id, ['is_default' => 1]);

        return redirect()->to('/addresses')
            ->with('toast', ['type' => 'success', 'message' => 'Alamat utama berhasil diubah!']);
    }
}
