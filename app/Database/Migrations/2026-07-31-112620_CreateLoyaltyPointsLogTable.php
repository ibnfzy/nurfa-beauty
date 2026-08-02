<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoyaltyPointsLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'customer_id'    => ['type' => 'INT', 'unsigned' => true],
            'points'         => ['type' => 'INT'],
            'type'           => ['type' => 'ENUM', 'constraint' => ['earn', 'redeem', 'expired']],
            'description'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'transaction_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('loyalty_points_log');
    }

    public function down()
    {
        $this->forge->dropTable('loyalty_points_log');
    }
}
