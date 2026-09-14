<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class ClearDatabase extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:clear-except-users-products';
    protected $description = 'Menghapus seluruh data database kecuali users dan products.';

    public function run(array $params)
    {
        CLI::error('PERINGATAN: seluruh data selain users dan products akan dihapus.');

        if (strtoupper(trim(CLI::prompt('Ketik CLEAR untuk melanjutkan'))) !== 'CLEAR') {
            CLI::write('Dibatalkan.', 'yellow');
            return;
        }

        $tables = [
            'wishlists',
            'transaction_items',
            'transactions',
        ];

        $db = Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        try {
            foreach ($tables as $table) {
                $db->table($table)->truncate();
                CLI::write("Dikosongkan: {$table}", 'green');
            }
        } finally {
            $db->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        CLI::write('Selesai. Data users dan products tetap dipertahankan.', 'green');
    }
}
