<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'customer_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    // Join dengan customers
    public function withCustomer()
    {
        $this->join('customers', 'customers.id = notifications.customer_id');
        $this->join('users', 'users.id = customers.user_id');
        $this->select('notifications.*, users.name as customer_name, users.email as customer_email');
        return $this;
    }

    /**
     * Hitung notifikasi belum dibaca untuk customer
     */
    public function getUnreadCount(int $customerId): int
    {
        return $this->where(['customer_id' => $customerId, 'is_read' => 0])
                    ->countAllResults();
    }
}
