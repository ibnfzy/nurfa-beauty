<?php

namespace App\Models;

use CodeIgniter\Model;

class VoucherModel extends Model
{
    protected $table            = 'vouchers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'promotion_id',
        'customer_id',
        'code',
        'is_used',
        'used_at',
        'expires_at',
    ];

    // Join dengan promotions
    public function withPromotion()
    {
        $this->join('promotions', 'promotions.id = vouchers.promotion_id');
        $this->select('vouchers.*, promotions.name as promotion_name, promotions.type as promotion_type, promotions.discount_type, promotions.discount_value, promotions.min_purchase');
        return $this;
    }

    // Join dengan customers
    public function withCustomer()
    {
        $this->join('customers', 'customers.id = vouchers.customer_id', 'left');
        $this->join('users', 'users.id = customers.user_id', 'left');
        $this->select('vouchers.*, users.name as customer_name, users.email as customer_email');
        return $this;
    }
}
