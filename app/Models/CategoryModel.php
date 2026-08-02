<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'name',
        'description',
    ];

    // Join dengan products
    public function withProducts()
    {
        $this->join('products', 'products.category_id = categories.id', 'left');
        return $this;
    }
}
