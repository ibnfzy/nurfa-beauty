<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantSelectionToTransactionItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction_items', [
            'variant_selection' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'quantity',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction_items', ['variant_selection']);
    }
}
