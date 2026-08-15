<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $image = '1785649634_ee7adeaf1d8789615eea.jpeg';
        $now   = date('Y-m-d H:i:s');

        $data = [
            // Makeup (category_id = 4)
            [
                'category_id'      => 4,
                'name'             => 'Pensil Alis Natural Brown',
                'description'      => 'Pensil alis dengan formula tahan lama, warna coklat natural yang cocok untuk semua jenis kulit. Dilengkapi sikat penghalus di ujungnya.',
                'price'            => 35000.00,
                'stock'            => 50,
                'image'            => $image,
                'is_active'        => 1,
                'is_bundle'        => 0,
                'bundle_products'  => null,
                'bundle_discount'  => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'category_id'      => 4,
                'name'             => 'Pensil Alis Dark Brown',
                'description'      => 'Pensil alis warna coklat gelap untuk tampilan lebih tegas dan bold. Tekstur lembut, mudah diaplikasikan.',
                'price'            => 35000.00,
                'stock'            => 40,
                'image'            => $image,
                'is_active'        => 1,
                'is_bundle'        => 0,
                'bundle_products'  => null,
                'bundle_discount'  => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'category_id'      => 4,
                'name'             => 'Pensil Alis Hitam',
                'description'      => 'Pensil alis warna hitam pekat untuk riasan mata yang dramatis. Tahan air hingga 12 jam.',
                'price'            => 38000.00,
                'stock'            => 35,
                'image'            => $image,
                'is_active'        => 1,
                'is_bundle'        => 0,
                'bundle_products'  => null,
                'bundle_discount'  => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'category_id'      => 4,
                'name'             => 'Pensil Alis Abu-Abu',
                'description'      => 'Pensil alis warna abu-abu elegan, cocok untuk tampilan soft makeup. Formula smudge-proof seharian.',
                'price'            => 38000.00,
                'stock'            => 30,
                'image'            => $image,
                'is_active'        => 1,
                'is_bundle'        => 0,
                'bundle_products'  => null,
                'bundle_discount'  => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'category_id'      => 4,
                'name'             => 'Paket Pensil Alis 3 Warna',
                'description'      => 'Bundling 3 pensil alis: Natural Brown, Dark Brown, dan Hitam. Hemat lebih banyak dengan paket ini.',
                'price'            => 90000.00,
                'stock'            => 20,
                'image'            => $image,
                'is_active'        => 1,
                'is_bundle'        => 1,
                'bundle_products'  => null,
                'bundle_discount'  => 15.00,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ];

        $this->db->table('products')->insertBatch($data);
    }
}
