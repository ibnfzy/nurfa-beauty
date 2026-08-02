<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Models\CustomerModel;

class Notification extends BaseController
{
    protected NotificationModel $notificationModel;
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->customerModel     = new CustomerModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $type   = $this->request->getGet('type');

        $builder = $this->notificationModel->withCustomer();

        if ($search) {
            $builder->groupStart()
                ->like('notifications.title', $search)
                ->orLike('users.name', $search)
                ->groupEnd();
        }

        if ($type) {
            $builder->where('notifications.type', $type);
        }

        $data = [
            'pageTitle'     => 'Notifikasi',
            'notifications' => $builder->orderBy('notifications.id', 'DESC')->paginate(15),
            'pager'         => $this->notificationModel->pager ?? null,
            'search'        => $search ?? '',
            'type'          => $type ?? '',
            'customers'     => $this->customerModel
                ->select('customers.*, users.name, users.email')
                ->join('users', 'users.id = customers.user_id')
                ->orderBy('users.name', 'ASC')
                ->findAll(),
        ];

        return view('admin/notification/index', $data);
    }

    public function send()
    {
        $rules = [
            'customer_id' => 'required|integer',
            'title'       => 'required|max_length[255]',
            'message'     => 'required|max_length[1000]',
            'type'        => 'required|in_list[promotion,birthday,loyalty,general]',
        ];

        $messages = [
            'customer_id' => [
                'required' => 'Pelanggan wajib dipilih.',
            ],
            'title' => [
                'required'   => 'Judul notifikasi wajib diisi.',
                'max_length' => 'Judul maksimal 255 karakter.',
            ],
            'message' => [
                'required'   => 'Pesan notifikasi wajib diisi.',
                'max_length' => 'Pesan maksimal 1000 karakter.',
            ],
            'type' => [
                'required' => 'Tipe notifikasi wajib dipilih.',
                'in_list'  => 'Tipe notifikasi tidak valid.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal mengirim notifikasi. Periksa kembali data Anda.']);
        }

        $this->notificationModel->insert([
            'customer_id' => $this->request->getPost('customer_id'),
            'title'       => $this->request->getPost('title'),
            'message'     => $this->request->getPost('message'),
            'type'        => $this->request->getPost('type'),
            'is_read'     => 0,
        ]);

        return redirect()->to('/admin/notifications')
            ->with('toast', ['type' => 'success', 'message' => 'Notifikasi berhasil dikirim!']);
    }

    public function broadcast()
    {
        $rules = [
            'title'   => 'required|max_length[255]',
            'message' => 'required|max_length[1000]',
            'type'    => 'required|in_list[promotion,birthday,loyalty,general]',
            'target'  => 'required|in_list[all,bronze,silver,gold,platinum]',
        ];

        $messages = [
            'title' => [
                'required'   => 'Judul notifikasi wajib diisi.',
                'max_length' => 'Judul maksimal 255 karakter.',
            ],
            'message' => [
                'required'   => 'Pesan notifikasi wajib diisi.',
                'max_length' => 'Pesan maksimal 1000 karakter.',
            ],
            'type' => [
                'required' => 'Tipe notifikasi wajib dipilih.',
                'in_list'  => 'Tipe notifikasi tidak valid.',
            ],
            'target' => [
                'required' => 'Target penerima wajib dipilih.',
                'in_list'  => 'Target penerima tidak valid.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal mengirim broadcast. Periksa kembali data Anda.']);
        }

        $target = $this->request->getPost('target');

        $builder = $this->customerModel;
        if ($target !== 'all') {
            $builder = $builder->where('membership_level', $target);
        }

        $customers = $builder->findAll();

        $count = 0;
        foreach ($customers as $customer) {
            $this->notificationModel->insert([
                'customer_id' => $customer['id'],
                'title'       => $this->request->getPost('title'),
                'message'     => $this->request->getPost('message'),
                'type'        => $this->request->getPost('type'),
                'is_read'     => 0,
            ]);
            $count++;
        }

        return redirect()->to('/admin/notifications')
            ->with('toast', ['type' => 'success', 'message' => 'Broadcast berhasil dikirim ke ' . $count . ' pelanggan!']);
    }
}
