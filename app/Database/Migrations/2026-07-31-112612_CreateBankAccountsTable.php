<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankAccountsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'bank_name'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'account_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'account_name'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('bank_accounts');
    }

    public function down()
    {
        $this->forge->dropTable('bank_accounts');
    }
}
