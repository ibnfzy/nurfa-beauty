<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table            = 'reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'customer_id',
        'product_id',
        'rating',
        'comment',
    ];

    // Join dengan customers
    public function withCustomer()
    {
        $this->join('customers', 'customers.id = reviews.customer_id');
        $this->join('users', 'users.id = customers.user_id');
        $this->select('reviews.*, users.name as customer_name');
        return $this;
    }

    // Join dengan products
    public function withProduct()
    {
        $this->join('products', 'products.id = reviews.product_id');
        $this->select('reviews.*, products.name as product_name, products.image as product_image');
        return $this;
    }
}
