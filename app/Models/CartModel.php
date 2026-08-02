<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table            = 'carts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'customer_id',
        'product_id',
        'quantity',
    ];

    public function withProduct()
    {
        $this->join('products', 'products.id = carts.product_id');
        $this->select('carts.*, products.name as product_name, products.price as product_price, products.image as product_image, products.stock as product_stock, products.is_active as product_is_active');
        return $this;
    }

    public function getCartCount($customerId)
    {
        return $this->where('customer_id', $customerId)->countAllResults();
    }

    public function getCartWithProducts($customerId)
    {
        return $this->withProduct()->where('carts.customer_id', $customerId)->findAll();
    }
}
