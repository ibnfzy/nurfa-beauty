<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Perawatan Rambut',
                'description' => 'Produk perawatan rambut seperti shampoo, conditioner, hair mask, dan serum rambut.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Perawatan Wajah',
                'description' => 'Produk perawatan wajah seperti cleanser, toner, moisturizer, serum, dan sunscreen.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Perawatan Badan',
                'description' => 'Produk perawatan badan seperti body lotion, body scrub, dan sabun mandi.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Makeup',
                'description' => 'Produk makeup seperti foundation, bedak, lipstik, dan eye shadow.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Parfum',
                'description' => 'Parfum dan fragrance untuk wanita dan pria.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}
