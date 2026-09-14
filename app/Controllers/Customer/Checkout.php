<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\ShippingAddressModel;
use App\Models\BankAccountModel;
use App\Models\VoucherModel;
use App\Models\LoyaltyPointsLogModel;
use App\Models\NotificationModel;

class Checkout extends BaseController
{
    protected CartModel $cartModel;
    protected CustomerModel $customerModel;
    protected ProductModel $productModel;
    protected TransactionModel $transactionModel;
    protected TransactionItemModel $transactionItemModel;
    protected ShippingAddressModel $shippingAddressModel;
    protected BankAccountModel $bankAccountModel;
    protected VoucherModel $voucherModel;
    protected LoyaltyPointsLogModel $loyaltyPointsLogModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        helper('text');

        $this->cartModel            = new CartModel();
        $this->customerModel        = new CustomerModel();
        $this->productModel         = new ProductModel();
        $this->transactionModel     = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
        $this->shippingAddressModel = new ShippingAddressModel();
        $this->bankAccountModel     = new BankAccountModel();
        $this->voucherModel         = new VoucherModel();
        $this->loyaltyPointsLogModel = new LoyaltyPointsLogModel();
        $this->notificationModel     = new NotificationModel();
    }

    protected function getCustomerId()
    {
        $userId   = session()->get('user_id');
        $customer = $this->customerModel->where('user_id', $userId)->first();
        return $customer ? $customer['id'] : null;
    }

    public function index()
    {
        $customerId = $this->getCustomerId();
        $voucherCodeInput = $this->request->getGet('voucher_code') ?? session()->get('applied_voucher');

        // Cek keranjang tidak kosong
        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        if (empty($cartItems)) {
            return redirect()->to('/cart')
                ->with('toast', ['type' => 'warning', 'message' => 'Keranjang Anda kosong.']);
        }

        // Ambil alamat pengiriman
        $addresses = $this->shippingAddressModel
            ->where('customer_id', $customerId)
            ->orderBy('is_default', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        if (empty($addresses)) {
            return redirect()->to('/address')
                ->with('toast', ['type' => 'warning', 'message' => 'Silakan tambahkan alamat pengiriman terlebih dahulu.']);
        }

        // Alamat default atau pertama
        $defaultAddress = null;
        foreach ($addresses as $addr) {
            if ($addr['is_default']) {
                $defaultAddress = $addr;
                break;
            }
        }
        if (!$defaultAddress) {
            $defaultAddress = $addresses[0];
        }

        // Ongkos kirim belum digunakan.
        $shippingCost = 0;

        // Hitung total
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['product_price'] * $item['quantity'];
        }

        // Cek voucher first purchase
        $discountAmount = 0;
        $voucherCode    = null;
        $voucher        = null;

        $firstPurchaseVoucher = $this->voucherModel
            ->join('promotions', 'promotions.id = vouchers.promotion_id')
            ->where('vouchers.customer_id', $customerId)
            ->where('vouchers.is_used', 0)
            ->where('vouchers.expires_at >', date('Y-m-d H:i:s'))
            ->where('promotions.type', 'discount')
            ->where('promotions.target_segment', 'new')
            ->where('promotions.is_active', 1)
            ->first();

        if ($firstPurchaseVoucher) {
            if ($firstPurchaseVoucher['discount_type'] === 'percentage') {
                $discountAmount = $totalAmount * $firstPurchaseVoucher['discount_value'] / 100;
            } else {
                $discountAmount = $firstPurchaseVoucher['discount_value'];
            }
            $voucherCode = $firstPurchaseVoucher['code'];
            $voucher     = $firstPurchaseVoucher;
        }

        // Cek promo perilaku bulanan (Behavioral Reward)
        helper('loyalty');
        $db = \Config\Database::connect();
        $monthlyRow = $db->table('transaction_items')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->where('transactions.customer_id', $customerId)
            ->whereIn('transactions.status', ['completed', 'paid'])
            ->where('MONTH(transactions.transaction_date)', (int) date('m'))
            ->where('YEAR(transactions.transaction_date)', (int) date('Y'))
            ->selectSum('transaction_items.quantity', 'total_qty')
            ->get()
            ->getRow();
        $monthlyProductCount = (int) ($monthlyRow->total_qty ?? 0);
        $behaviorReward = get_monthly_behavior_reward($monthlyProductCount);

        // Manual voucher code apply
        $appliedVoucher = null;
        if ($voucherCodeInput && !$firstPurchaseVoucher) {
            if ($voucherCodeInput === 'BEHAVIOR_REWARD' && $behaviorReward['is_qualified']) {
                $pct = $behaviorReward['active_reward']['discount_pct'];
                $maxDisc = $behaviorReward['active_reward']['max_discount'];
                $discountAmount = min(($totalAmount * $pct) / 100, $maxDisc);
                $discountAmount = min($discountAmount, $totalAmount);
                $voucherCode = 'BEHAVIOR_REWARD';
                $appliedVoucher = [
                    'code'           => 'BEHAVIOR_REWARD',
                    'promotion_name' => $behaviorReward['active_reward']['title'] . ' (' . $behaviorReward['active_reward']['badge'] . ')',
                    'discount_type'  => 'percentage',
                    'discount_value' => $pct,
                    'is_behavior'    => true,
                ];
                $voucher = $appliedVoucher;
            } else {
                $appliedVoucher = $this->voucherModel
                    ->join('promotions', 'promotions.id = vouchers.promotion_id')
                    ->where('vouchers.code', $voucherCodeInput)
                    ->where('vouchers.is_used', 0)
                    ->where('vouchers.expires_at >', date('Y-m-d H:i:s'))
                    ->where('promotions.is_active', 1)
                    ->where('promotions.start_date <=', date('Y-m-d'))
                    ->where('promotions.end_date >=', date('Y-m-d'))
                    ->first();

                if ($appliedVoucher) {
                    // Check if customer-specific voucher belongs to this customer
                    if ($appliedVoucher['customer_id'] && $appliedVoucher['customer_id'] != $customerId) {
                        $appliedVoucher = null;
                    }
                    // Check min purchase
                    if ($appliedVoucher && $appliedVoucher['min_purchase'] > 0 && $totalAmount < $appliedVoucher['min_purchase']) {
                        $appliedVoucher = null;
                    }
                }

                if ($appliedVoucher) {
                    if ($appliedVoucher['discount_type'] === 'percentage') {
                        $discountAmount = $totalAmount * $appliedVoucher['discount_value'] / 100;
                    } else {
                        $discountAmount = min($appliedVoucher['discount_value'], $totalAmount);
                    }
                    $voucherCode = $appliedVoucher['code'];
                    $voucher = $appliedVoucher;
                }
            }
        }

        $finalAmount  = $totalAmount + $shippingCost - $discountAmount;

        // Ambil rekening bank aktif
        $bankAccounts = $this->bankAccountModel
            ->where('is_active', 1)
            ->findAll();

        // Hitung bonus poin
        helper('loyalty');
        $customer = $this->customerModel->find($customerId);
        $level = $customer['membership_level'] ?? 'bronze';
        $basePoints = calculate_loyalty_points($finalAmount, $level);
        $bonusPoints = calculate_bonus_points($finalAmount);

        // Cross-selling: produk yang sering dibeli bersamaan dengan item di keranjang
        $crossSellProducts = [];
        foreach ($cartItems as $item) {
            $related = $this->productModel->getFrequentlyBoughtTogether($item['product_id'], 2);
            foreach ($related as $rp) {
                $alreadyInCart = false;
                foreach ($cartItems as $ci) {
                    if ($ci['product_id'] == $rp['id']) {
                        $alreadyInCart = true;
                        break;
                    }
                }
                if (!$alreadyInCart) {
                    $exists = false;
                    foreach ($crossSellProducts as $cs) {
                        if ($cs['id'] == $rp['id']) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $crossSellProducts[] = $rp;
                    }
                }
            }
            if (count($crossSellProducts) >= 4) break;
        }

        $data = [
            'pageTitle'           => 'Checkout',
            'cartItems'           => $cartItems ?? [],
            'addresses'           => $addresses ?? [],
            'defaultAddress'      => $defaultAddress ?? [],
            'bankAccounts'        => $bankAccounts ?? [],
            'totalAmount'         => $totalAmount ?? 0,
            'shippingCost'        => $shippingCost ?? 0,
            'discountAmount'      => $discountAmount,
            'voucherCode'         => $voucherCode,
            'firstPurchaseVoucher' => $voucher,
            'finalAmount'         => $finalAmount ?? 0,
            'basePoints'          => $basePoints ?? 0,
            'bonusPoints'         => $bonusPoints ?? 0,
            'crossSellProducts'   => $crossSellProducts ?? [],
            'appliedVoucher'      => $appliedVoucher ?? null,
            'voucherCodeInput'    => $voucherCodeInput ?? null,
            'monthlyProductCount' => $monthlyProductCount ?? 0,
            'behaviorReward'      => $behaviorReward ?? null,
        ];

        return view('customer/checkout/index', $data);
    }

    public function process()
    {
        $rules = [
            'shipping_address_id' => 'required|integer',
            'notes'               => 'permit_empty|max_length[500]',
        ];

        $messages = [
            'shipping_address_id' => [
                'required' => 'Alamat pengiriman wajib dipilih.',
                'integer'  => 'Alamat pengiriman tidak valid.',
            ],
            'notes' => [
                'max_length' => 'Catatan maksimal 500 karakter.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => 'Gagal memproses pesanan. Periksa kembali data Anda.']);
        }

        $customerId = $this->getCustomerId();
        $addressId  = $this->request->getPost('shipping_address_id');
        $voucherCodeInput = $this->request->getPost('voucher_code');

        // Verifikasi alamat milik customer
        $address = $this->shippingAddressModel
            ->where('id', $addressId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Alamat pengiriman tidak valid.']);
        }

        // Ambil keranjang
        $cartItems = $this->cartModel->getCartWithProducts($customerId);
        if (empty($cartItems)) {
            return redirect()->to('/cart')
                ->with('toast', ['type' => 'warning', 'message' => 'Keranjang Anda kosong.']);
        }

        // Verifikasi varian dan stok untuk setiap item
        foreach ($cartItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            if (!$product || !$product['is_active']) {
                return redirect()->back()
                    ->with('toast', ['type' => 'error', 'message' => 'Produk "' . ($item['product_name'] ?? 'ini') . '" tidak tersedia.']);
            }

            $variants = !empty($product['variants']) ? json_decode((string) $product['variants'], true) : [];
            $selection = !empty($item['variant_selection']) ? json_decode((string) $item['variant_selection'], true) : [];
            if (!empty($variants) && (!is_array($selection) || count($selection) !== count($variants))) {
                return redirect()->to('/cart')
                    ->with('toast', ['type' => 'error', 'message' => 'Silakan pilih semua varian produk sebelum checkout.']);
            }

            if ($item['quantity'] > $product['stock']) {
                return redirect()->back()
                    ->with('toast', ['type' => 'warning', 'message' => 'Stok produk "' . $item['product_name'] . '" tidak mencukupi.']);
            }
        }

        // Hitung ongkos kirim (flat rate untuk sekarang)
        $shippingCost = 0;

        // Hitung total
        $totalAmount   = 0;
        $discountAmount = 0;
        $firstPurchaseVoucher = null;

        foreach ($cartItems as $item) {
            $totalAmount += $item['product_price'] * $item['quantity'];
        }

        // Cek voucher first purchase
        $firstPurchaseVoucher = $this->voucherModel
            ->join('promotions', 'promotions.id = vouchers.promotion_id')
            ->where('vouchers.customer_id', $customerId)
            ->where('vouchers.is_used', 0)
            ->where('vouchers.expires_at >', date('Y-m-d H:i:s'))
            ->where('promotions.type', 'discount')
            ->where('promotions.target_segment', 'new')
            ->where('promotions.is_active', 1)
            ->first();

        if ($firstPurchaseVoucher) {
            if ($firstPurchaseVoucher['discount_type'] === 'percentage') {
                $discountAmount = $totalAmount * $firstPurchaseVoucher['discount_value'] / 100;
            } else {
                $discountAmount = $firstPurchaseVoucher['discount_value'];
            }
        }

        // Manual voucher code apply or Behavior Reward apply
        $appliedVoucher = null;
        if ($voucherCodeInput && !$firstPurchaseVoucher) {
            if ($voucherCodeInput === 'BEHAVIOR_REWARD') {
                helper('loyalty');
                $db = \Config\Database::connect();
                $monthlyRow = $db->table('transaction_items')
                    ->join('transactions', 'transactions.id = transaction_items.transaction_id')
                    ->where('transactions.customer_id', $customerId)
                    ->whereIn('transactions.status', ['completed', 'paid'])
                    ->where('MONTH(transactions.transaction_date)', (int) date('m'))
                    ->where('YEAR(transactions.transaction_date)', (int) date('Y'))
                    ->selectSum('transaction_items.quantity', 'total_qty')
                    ->get()
                    ->getRow();
                $monthlyProductCount = (int) ($monthlyRow->total_qty ?? 0);
                $bReward = get_monthly_behavior_reward($monthlyProductCount);

                if ($bReward['is_qualified']) {
                    $pct = $bReward['active_reward']['discount_pct'];
                    $maxDisc = $bReward['active_reward']['max_discount'];
                    $discountAmount = min(($totalAmount * $pct) / 100, $maxDisc);
                    $discountAmount = min($discountAmount, $totalAmount);
                }
            } else {
                $appliedVoucher = $this->voucherModel
                    ->join('promotions', 'promotions.id = vouchers.promotion_id')
                    ->where('vouchers.code', $voucherCodeInput)
                    ->where('vouchers.is_used', 0)
                    ->where('vouchers.expires_at >', date('Y-m-d H:i:s'))
                    ->where('promotions.is_active', 1)
                    ->where('promotions.start_date <=', date('Y-m-d'))
                    ->where('promotions.end_date >=', date('Y-m-d'))
                    ->first();

                if ($appliedVoucher) {
                    // Check if customer-specific voucher belongs to this customer
                    if ($appliedVoucher['customer_id'] && $appliedVoucher['customer_id'] != $customerId) {
                        $appliedVoucher = null;
                    }
                    // Check min purchase
                    if ($appliedVoucher && $appliedVoucher['min_purchase'] > 0 && $totalAmount < $appliedVoucher['min_purchase']) {
                        $appliedVoucher = null;
                    }
                }

                if ($appliedVoucher) {
                    if ($appliedVoucher['discount_type'] === 'percentage') {
                        $discountAmount = $totalAmount * $appliedVoucher['discount_value'] / 100;
                    } else {
                        $discountAmount = min($appliedVoucher['discount_value'], $totalAmount);
                    }
                }
            }
        }

        $finalAmount = $totalAmount + $shippingCost - $discountAmount;

        // Generate kode transaksi
        $transactionCode = 'TRX-' . date('Ymd') . '-' . strtoupper(random_string('alnum', 6));

        // Buat transaksi
        $transactionId = $this->transactionModel->insert([
            'customer_id'         => $customerId,
            'shipping_address_id' => $addressId,
            'transaction_code'    => $transactionCode,
            'transaction_date'    => date('Y-m-d H:i:s'),
            'total_amount'        => $totalAmount,
            'shipping_cost'       => $shippingCost,
            'discount_amount'     => $discountAmount,
            'final_amount'        => $finalAmount,
            'payment_method'      => 'bank_transfer',
            'status'              => 'pending_payment',
            'payment_status'      => 'pending',
            'notes'               => $this->request->getPost('notes'),
        ]);

        // Buat item transaksi & kurangi stok
        foreach ($cartItems as $item) {
            $subtotal = $item['product_price'] * $item['quantity'];

            $this->transactionItemModel->insert([
                'transaction_id' => $transactionId,
                'product_id'     => $item['product_id'],
                'quantity'       => $item['quantity'],
                'price'             => $item['product_price'],
                'subtotal'          => $subtotal,
                'variant_selection' => $item['variant_selection'] ?? null,
            ]);

            // Kurangi stok
            $product = $this->productModel->find($item['product_id']);
            $this->productModel->update($item['product_id'], [
                'stock' => $product['stock'] - $item['quantity'],
            ]);
        }

        // Kosongkan keranjang
        $this->cartModel->where('customer_id', $customerId)->delete();

        // Tandai voucher first purchase sebagai terpakai
        if ($firstPurchaseVoucher) {
            $this->voucherModel->update($firstPurchaseVoucher['id'], [
                'is_used' => 1,
                'used_at' => date('Y-m-d H:i:s'),
            ]);

            // Update first_purchase_date customer
            $customer = $this->customerModel->find($customerId);
            if (empty($customer['first_purchase_date'])) {
                $this->customerModel->update($customerId, [
                    'first_purchase_date' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Tandai voucher manual sebagai terpakai
        if ($appliedVoucher) {
            $this->voucherModel->update($appliedVoucher['id'], [
                'is_used' => 1,
                'used_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Tambah poin loyalitas
        helper('loyalty');
        $customer = $this->customerModel->find($customerId);
        $level    = $customer['membership_level'] ?? 'bronze';
        $points      = calculate_loyalty_points($finalAmount, $level);
        $bonusPoints = calculate_bonus_points($finalAmount);
        $points     += $bonusPoints;

        if ($points > 0) {
            $this->loyaltyPointsLogModel->insert([
                'customer_id'    => $customerId,
                'points'         => $points,
                'type'           => 'earn',
                'description'    => 'Poin dari transaksi ' . $transactionCode,
                'transaction_id' => $transactionId,
            ]);

            // Update loyalty_points customer
            $this->customerModel->update($customerId, [
                'loyalty_points'  => ($customer['loyalty_points'] ?? 0) + $points,
                'total_spending'  => ($customer['total_spending'] ?? 0) + $finalAmount,
                'last_purchase_date' => date('Y-m-d H:i:s'),
            ]);

            // Cek & update membership level
            $newLevel = get_membership_level(($customer['total_spending'] ?? 0) + $finalAmount);
            if ($newLevel !== $level) {
                $this->customerModel->update($customerId, [
                    'membership_level' => $newLevel,
                ]);

                // Kirim notifikasi level up
                $benefits = get_membership_benefits($newLevel);
                $this->notificationModel->insert([
                    'customer_id' => $customerId,
                    'title'       => 'Selamat! Level Membership Naik!',
                    'message'     => 'Anda telah naik ke level ' . $benefits['label'] . '! Nikmati benefit: ' . implode(', ', $benefits['perks']) . '.',
                    'type'        => 'loyalty',
                    'is_read'     => 0,
                ]);
            }

            // Kirim notifikasi poin
            $this->notificationModel->insert([
                'customer_id' => $customerId,
                'title'       => 'Poin Loyalitas Bertambah!',
                'message'     => 'Anda mendapatkan ' . $points . ' poin dari transaksi ' . $transactionCode . ($bonusPoints > 0 ? ' (termasuk bonus ' . $bonusPoints . ' poin)' : '') . '. Total poin: ' . (($customer['loyalty_points'] ?? 0) + $points) . '.',
                'type'        => 'loyalty',
                'is_read'     => 0,
            ]);
        }

        return redirect()->to('/transactions/' . $transactionId)
            ->with('toast', ['type' => 'success', 'message' => 'Pesanan berhasil dibuat! Silakan upload bukti transfer.']);
    }

    public function uploadProof(int $id)
    {
        $customerId  = $this->getCustomerId();
        $transaction = $this->transactionModel->find($id);

        if (!$transaction || $transaction['customer_id'] != $customerId) {
            return redirect()->to('/transactions')
                ->with('toast', ['type' => 'error', 'message' => 'Transaksi tidak ditemukan.']);
        }

        if ($transaction['status'] !== 'pending_payment') {
            return redirect()->back()
                ->with('toast', ['type' => 'warning', 'message' => 'Transaksi ini tidak memerlukan upload bukti transfer.']);
        }

        $file = $this->request->getFile('payment_proof');

        // Validasi manual lebih detail
        if (!$file || $file->getError() === 4) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Bukti transfer wajib diunggah.']);
        }

        if (!$file->isValid()) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'File tidak valid: ' . $file->getErrorString()]);
        }

        if ($file->getSize() > 2048 * 1024) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Ukuran file maksimal 2MB. Ukuran file Anda: ' . round($file->getSize() / 1024) . 'KB.']);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'File harus berupa gambar (JPG, PNG, GIF, WebP). Tipe file Anda: ' . $file->getMimeType()]);
        }

        // Pastikan direktori ada
        $uploadPath = FCPATH . 'uploads/payment_proofs';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $fileName = $file->getRandomName();
        $file->move($uploadPath, $fileName);

        // Update transaksi
        $this->transactionModel->update($id, [
            'payment_proof' => $fileName,
            'status'        => 'paid',
            'payment_status' => 'pending',
        ]);

        return redirect()->to('/transactions/' . $id)
            ->with('toast', ['type' => 'success', 'message' => 'Bukti transfer berhasil diupload! Menunggu verifikasi admin.']);
    }

}
