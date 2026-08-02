<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePromotionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'type'           => ['type' => 'ENUM', 'constraint' => ['discount', 'voucher', 'flash_sale', 'birthday']],
            'discount_type'  => ['type' => 'ENUM', 'constraint' => ['percentage', 'fixed']],
            'discount_value' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'min_purchase'   => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'start_date'     => ['type' => 'DATE'],
            'end_date'       => ['type' => 'DATE'],
            'target_segment' => ['type' => 'ENUM', 'constraint' => ['all', 'new', 'loyal', 'vip'], 'default' => 'all'],
            'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('promotions');
    }

    public function down()
    {
        $this->forge->dropTable('promotions');
    }
}
