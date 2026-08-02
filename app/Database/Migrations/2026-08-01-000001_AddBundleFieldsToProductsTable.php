<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBundleFieldsToProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'is_bundle' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'is_active',
            ],
            'bundle_products' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'is_bundle',
            ],
            'bundle_discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
                'after'      => 'bundle_products',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['is_bundle', 'bundle_products', 'bundle_discount']);
    }
}
