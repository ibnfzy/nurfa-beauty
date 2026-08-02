<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table            = 'wishlists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'customer_id',
        'product_id',
    ];

    // Join dengan customers
    public function withCustomer()
    {
        $this->join('customers', 'customers.id = wishlists.customer_id');
        $this->join('users', 'users.id = customers.user_id');
        $this->select('wishlists.*, users.name as customer_name');
        return $this;
    }

    // Join dengan products
    public function withProduct()
    {
        $this->join('products', 'products.id = wishlists.product_id');
        $this->select('wishlists.*, products.name as product_name, products.price as product_price, products.image as product_image, products.stock as product_stock');
        return $this;
    }
}
