<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'user_id',
        'loyalty_points',
        'membership_level',
        'total_spending',
        'first_purchase_date',
        'last_purchase_date',
    ];

    // Join dengan users
    public function withUser()
    {
        $this->join('users', 'users.id = customers.user_id');
        $this->select('customers.*, users.name, users.email, users.phone, users.address, users.birth_date');
        return $this;
    }

    // Join dengan shipping_addresses
    public function withShippingAddresses()
    {
        $this->join('shipping_addresses', 'shipping_addresses.customer_id = customers.id', 'left');
        return $this;
    }

    // Join dengan transactions
    public function withTransactions()
    {
        $this->join('transactions', 'transactions.customer_id = customers.id', 'left');
        return $this;
    }
}
