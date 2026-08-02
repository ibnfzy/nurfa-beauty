<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\CustomerModel;
use App\Models\PromotionModel;
use App\Models\VoucherModel;
use App\Models\NotificationModel;

class BirthdayVoucher extends BaseCommand
{
    protected $group       = 'CRM';
    protected $name        = 'crm:birthday-voucher';
    protected $description = 'Generate voucher ulang tahun untuk pelanggan yang berulang tahun dalam 7 hari (H-7).';

    public function run(array $params)
    {
        $userModel       = new UserModel();
        $customerModel   = new CustomerModel();
        $promotionModel  = new PromotionModel();
        $voucherModel    = new VoucherModel();
        $notificationModel = new NotificationModel();

        // Cari pelanggan yang ulang tahun dalam 7 hari ke depan
        $today     = date('m-d');
        $nextWeek  = date('m-d', strtotime('+7 days'));

        // Handle pergantian tahun (Des -> Jan)
        if ($today > $nextWeek) {
            // Misal: today = 12-28, nextWeek = 01-04
            $customers = $userModel
                ->select('users.id as user_id, users.name, users.birth_date, customers.id as customer_id')
                ->join('customers', 'customers.user_id = users.id')
                ->where('users.role', 'customer')
                ->where('users.birth_date IS NOT NULL', null, false)
                ->groupStart()
                    ->where("DATE_FORMAT(users.birth_date, '%m-%d') >= ", $today)
                    ->orWhere("DATE_FORMAT(users.birth_date, '%m-%d') <= ", $nextWeek)
                ->groupEnd()
                ->findAll();
        } else {
            $customers = $userModel
                ->select('users.id as user_id, users.name, users.birth_date, customers.id as customer_id')
                ->join('customers', 'customers.user_id = users.id')
                ->where('users.role', 'customer')
                ->where('users.birth_date IS NOT NULL', null, false)
                ->where("DATE_FORMAT(users.birth_date, '%m-%d') >= ", $today)
                ->where("DATE_FORMAT(users.birth_date, '%m-%d') <= ", $nextWeek)
                ->findAll();
        }

        if (empty($customers)) {
            CLI::write('Tidak ada pelanggan yang berulang tahun dalam 7 hari ke depan.', 'yellow');
            return;
        }

        $count = 0;
        foreach ($customers as $customer) {
            // Cek apakah sudah ada voucher birthday tahun ini untuk customer ini
            $existingVoucher = $voucherModel
                ->join('promotions', 'promotions.id = vouchers.promotion_id')
                ->where('vouchers.customer_id', $customer['customer_id'])
                ->where('promotions.type', 'birthday')
                ->where('YEAR(vouchers.created_at)', date('Y'))
                ->first();

            if ($existingVoucher) {
                CLI::write("  - {$customer['name']} sudah mendapatkan voucher birthday tahun ini.", 'yellow');
                continue;
            }

            // Buat promosi birthday jika belum ada untuk tahun ini
            $birthdayPromo = $promotionModel
                ->where('type', 'birthday')
                ->where('name', 'Diskon Ulang Tahun ' . date('Y'))
                ->first();

            if (!$birthdayPromo) {
                $promotionModel->insert([
                    'name'            => 'Diskon Ulang Tahun ' . date('Y'),
                    'type'            => 'birthday',
                    'discount_type'   => 'percentage',
                    'discount_value'  => 15,
                    'min_purchase'    => 0,
                    'start_date'      => date('Y-01-01'),
                    'end_date'        => date('Y-12-31'),
                    'target_segment'  => 'all',
                    'is_active'       => 1,
                ]);
                $promoId = $promotionModel->getInsertID();
            } else {
                $promoId = $birthdayPromo['id'];
            }

            // Generate voucher
            $voucherCode = 'BDAY-' . strtoupper(random_string('alnum', 8));
            $expiresAt   = date('Y-m-d H:i:s', strtotime('+30 days'));

            $voucherModel->insert([
                'promotion_id' => $promoId,
                'customer_id'  => $customer['customer_id'],
                'code'         => $voucherCode,
                'is_used'      => 0,
                'expires_at'   => $expiresAt,
            ]);

            // Kirim notifikasi
            $notificationModel->insert([
                'customer_id' => $customer['customer_id'],
                'title'       => 'Selamat Ulang Tahun!',
                'message'     => 'Selamat ulang tahun, ' . $customer['name'] . '! Kami hadiahkan voucher diskon 15% untuk Anda. Gunakan kode: ' . $voucherCode . '. Berlaku hingga ' . date('d M Y', strtotime($expiresAt)) . '.',
                'type'        => 'birthday',
                'is_read'     => 0,
            ]);

            $count++;
            CLI::write("  + Voucher {$voucherCode} dikirim ke {$customer['name']}", 'green');
        }

        CLI::write("\nSelesai! {$count} voucher ulang tahun berhasil dibuat.", 'green');
    }
}
