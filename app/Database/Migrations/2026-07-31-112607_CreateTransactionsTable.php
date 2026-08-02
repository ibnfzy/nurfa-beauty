<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'customer_id'             => ['type' => 'INT', 'unsigned' => true],
            'shipping_address_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'transaction_code'        => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'transaction_date'        => ['type' => 'DATETIME'],
            'total_amount'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'shipping_cost'           => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'discount_amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'final_amount'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'payment_method'          => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'transfer'],
            'payment_proof'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_status'          => ['type' => 'ENUM', 'constraint' => ['pending', 'verified', 'rejected'], 'default' => 'pending'],
            'payment_verified_at'     => ['type' => 'DATETIME', 'null' => true],
            'payment_verified_by'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'payment_rejection_reason' => ['type' => 'TEXT', 'null' => true],
            'status'                  => ['type' => 'ENUM', 'constraint' => ['pending_payment', 'paid', 'processing', 'shipped', 'completed', 'cancelled'], 'default' => 'pending_payment'],
            'notes'                   => ['type' => 'TEXT', 'null' => true],
            'created_at'              => ['type' => 'DATETIME', 'null' => true],
            'updated_at'              => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('shipping_address_id', 'shipping_addresses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('payment_verified_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
