<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'bank_name'      => 'BCA',
                'account_number' => '1234567890',
                'account_name'   => 'Nurfa Beauty Shop',
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'bank_name'      => 'Mandiri',
                'account_number' => '0987654321',
                'account_name'   => 'Nurfa Beauty Shop',
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'bank_name'      => 'BRI',
                'account_number' => '1122334455',
                'account_name'   => 'Nurfa Beauty Shop',
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'bank_name'      => 'BNI',
                'account_number' => '5566778899',
                'account_name'   => 'Nurfa Beauty Shop',
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('bank_accounts')->insertBatch($data);
    }
}
