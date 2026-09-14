<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionItemModel extends Model
{
    protected $table            = 'transaction_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'variant_selection',
    ];

    // Join dengan transactions
    public function withTransaction()
    {
        $this->join('transactions', 'transactions.id = transaction_items.transaction_id');
        return $this;
    }

    // Join dengan products
    public function withProduct()
    {
        $this->join('products', 'products.id = transaction_items.product_id');
        $this->select('transaction_items.*, products.name as product_name, products.image as product_image, products.description as product_description');
        return $this;
    }
}
