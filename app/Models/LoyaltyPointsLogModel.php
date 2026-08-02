<?php

namespace App\Models;

use CodeIgniter\Model;

class LoyaltyPointsLogModel extends Model
{
    protected $table            = 'loyalty_points_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'customer_id',
        'points',
        'type',
        'description',
        'transaction_id',
    ];

    // Join dengan customers
    public function withCustomer()
    {
        $this->join('customers', 'customers.id = loyalty_points_log.customer_id');
        $this->join('users', 'users.id = customers.user_id');
        $this->select('loyalty_points_log.*, users.name as customer_name, users.email as customer_email');
        return $this;
    }

    // Join dengan transactions
    public function withTransaction()
    {
        $this->join('transactions', 'transactions.id = loyalty_points_log.transaction_id', 'left');
        $this->select('loyalty_points_log.*, transactions.transaction_code, transactions.final_amount');
        return $this;
    }
}
