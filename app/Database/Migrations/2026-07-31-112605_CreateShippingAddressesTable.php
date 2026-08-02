<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShippingAddressesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'customer_id'   => ['type' => 'INT', 'unsigned' => true],
            'label'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'recipient_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'         => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'       => ['type' => 'TEXT'],
            'province'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'city'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'district'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'postal_code'   => ['type' => 'VARCHAR', 'constraint' => 10],
            'is_default'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shipping_addresses');
    }

    public function down()
    {
        $this->forge->dropTable('shipping_addresses');
    }
}
