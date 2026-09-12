<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantsToProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'variants' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'bundle_discount',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['variants']);
    }
}
