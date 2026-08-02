<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
  protected $table            = 'users';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $useTimestamps    = true;

  protected $allowedFields = [
    'name',
    'email',
    'password',
    'role',
    'phone',
    'address',
    'birth_date',
  ];

  // Join dengan customers
  public function withCustomer()
  {
    $this->join('customers', 'customers.user_id = users.id', 'left');
    $this->select('users.*, customers.id as customer_id, customers.loyalty_points, customers.membership_level, customers.total_spending');
    return $this;
  }
}
