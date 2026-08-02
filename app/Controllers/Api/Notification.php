<?php

namespace App\Controllers\Api;

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
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(401);
        }

        $notifications = $this->notificationModel
            ->where('customer_id', $customerId)
            ->orderBy('id', 'DESC')
            ->findAll(20);

        $unreadCount = $this->notificationModel
            ->where('customer_id', $customerId)
            ->where('is_read', 0)
            ->countAllResults();

        return $this->response->setJSON([
            'success'      => true,
            'notifications' => $notifications ?? [],
            'unread_count'  => $unreadCount ?? 0,
        ]);
    }

    public function read($id = null)
    {
        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(401);
        }

        $notification = $this->notificationModel
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$notification) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ])->setStatusCode(404);
        }

        $this->notificationModel->update($id, ['is_read' => 1]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca.',
        ]);
    }

    public function readAll()
    {
        $customerId = $this->getCustomerId();

        if (!$customerId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(401);
        }

        $this->notificationModel
            ->where('customer_id', $customerId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai dibaca.',
        ]);
    }
}
