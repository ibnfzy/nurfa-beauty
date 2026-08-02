<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'           => ['type' => 'INT', 'unsigned' => true],
            'loyalty_points'    => ['type' => 'INT', 'default' => 0],
            'membership_level'  => ['type' => 'ENUM', 'constraint' => ['bronze', 'silver', 'gold', 'platinum'], 'default' => 'bronze'],
            'total_spending'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'first_purchase_date' => ['type' => 'DATE', 'null' => true],
            'last_purchase_date'  => ['type' => 'DATE', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('customers');
    }

    public function down()
    {
        $this->forge->dropTable('customers');
    }
}
