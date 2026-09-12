<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantSelectionToCartsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('carts', [
            'variant_selection' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'quantity',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('carts', ['variant_selection']);
    }
}