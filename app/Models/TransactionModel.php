<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'customer_id',
        'shipping_address_id',
        'transaction_code',
        'transaction_date',
        'total_amount',
        'shipping_cost',
        'discount_amount',
        'final_amount',
        'payment_method',
        'payment_proof',
        'payment_status',
        'payment_verified_at',
        'payment_verified_by',
        'payment_rejection_reason',
        'status',
        'notes',
    ];

    // Join dengan customers + users
    public function withCustomer()
    {
        $this->join('customers', 'customers.id = transactions.customer_id');
        $this->join('users', 'users.id = customers.user_id');
        $this->select('transactions.*, customers.user_id, customers.loyalty_points, customers.membership_level, users.name as customer_name, users.email as customer_email, users.phone as customer_phone');
        return $this;
    }

    // Join dengan shipping_addresses
    public function withShippingAddress()
    {
        $this->join('shipping_addresses', 'shipping_addresses.id = transactions.shipping_address_id', 'left');
        $this->select('transactions.*, shipping_addresses.label as address_label, shipping_addresses.recipient_name, shipping_addresses.address as shipping_address, shipping_addresses.city, shipping_addresses.province, shipping_addresses.postal_code');
        return $this;
    }

    // Join dengan transaction_items + products
    public function withItems()
    {
        $this->join('transaction_items', 'transaction_items.transaction_id = transactions.id', 'left');
        $this->join('products', 'products.id = transaction_items.product_id', 'left');
        $this->select('transactions.*, transaction_items.quantity, transaction_items.price as item_price, transaction_items.subtotal, products.name as product_name, products.image as product_image');
        return $this;
    }

    // Join dengan admin yang verifikasi
    public function withVerifiedBy()
    {
        $this->join('users as verifier', 'verifier.id = transactions.payment_verified_by', 'left');
        $this->select('transactions.*, verifier.name as verified_by_name');
        return $this;
    }
}
