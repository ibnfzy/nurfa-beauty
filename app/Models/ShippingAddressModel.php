<?php

namespace App\Models;

use CodeIgniter\Model;

class ShippingAddressModel extends Model
{
    protected $table            = 'shipping_addresses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'customer_id',
        'label',
        'recipient_name',
        'phone',
        'address',
        'province',
        'city',
        'district',
        'postal_code',
        'is_default',
    ];

    // Join dengan customers
    public function withCustomer()
    {
        $this->join('customers', 'customers.id = shipping_addresses.customer_id');
        return $this;
    }
}
