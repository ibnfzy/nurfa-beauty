<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model
{
    protected $table            = 'promotions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'name',
        'type',
        'discount_type',
        'discount_value',
        'min_purchase',
        'start_date',
        'end_date',
        'target_segment',
        'is_active',
    ];

    // Join dengan vouchers
    public function withVouchers()
    {
        $this->join('vouchers', 'vouchers.promotion_id = promotions.id', 'left');
        return $this;
    }
}
