<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $transaction */
/** @var array $items */
/** @var array|null $shippingAddress */
?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <?= $this->include('components/breadcrumb', [
        'items' => [
            ['label' => 'Beranda', 'url' => base_url()],
            ['label' => 'Pesanan', 'url' => base_url('transactions')],
            ['label' => $transaction['transaction_code'] ?? '-'],
        ]
    ]) ?>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Detail Pesanan</h1>
        <a href="<?= base_url('transactions') ?>"
            class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-primary transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    <?php
    $statusConfig = match ($transaction['status'] ?? '') {
        'pending_payment' => ['color' => 'warning', 'text' => 'Menunggu Bayar'],
        'paid'            => ['color' => 'info', 'text' => 'Sudah Bayar'],
        'processing'      => ['color' => 'primary', 'text' => 'Diproses'],
        'shipped'         => ['color' => 'info', 'text' => 'Dikirim'],
        'completed'       => ['color' => 'success', 'text' => 'Selesai'],
        'cancelled'       => ['color' => 'danger', 'text' => 'Dibatalkan'],
        default           => ['color' => 'gray', 'text' => ucfirst($transaction['status'] ?? '')],
    };

    // Timeline steps
    $steps = [
        ['key' => 'created',     'label' => 'Dibuat',     'icon' => 'file-text'],
        ['key' => 'paid',        'label' => 'Dibayar',    'icon' => 'credit-card'],
        ['key' => 'verified',    'label' => 'Diverifikasi', 'icon' => 'check-circle'],
        ['key' => 'processing',  'label' => 'Diproses',   'icon' => 'package'],
        ['key' => 'shipped',     'label' => 'Dikirim',    'icon' => 'truck'],
        ['key' => 'completed',   'label' => 'Selesai',    'icon' => 'check-circle-2'],
    ];

    // Tentukan step aktif
    $activeStepIndex = 0;
    switch ($transaction['status'] ?? '') {
        case 'pending_payment':
            $activeStepIndex = 0;
            break;
        case 'paid':
            $activeStepIndex = (($transaction['payment_status'] ?? '') === 'verified') ? 2 : 1;
            break;
        case 'processing':
            $activeStepIndex = 3;
            break;
        case 'shipped':
            $activeStepIndex = 4;
            break;
        case 'completed':
            $activeStepIndex = 5;
            break;
        case 'cancelled':
            $activeStepIndex = -1;
            break;
    }
    ?>

    <!-- Status Timeline -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 mb-6">
        <div class="flex items-center justify-between overflow-x-auto pb-2">
            <?php foreach ($steps as $i => $step): ?>
                <div class="flex items-center <?= $i > 0 ? 'flex-1' : '' ?>">
                    <?php if ($i > 0): ?>
                        <div class="flex-1 h-0.5 <?= $i <= $activeStepIndex ? 'bg-primary' : 'bg-gray-200' ?> min-w-[20px]"></div>
                    <?php endif; ?>
                    <div class="flex flex-col items-center <?= $i > 0 ? 'ml-2' : '' ?>">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            <?= $i <= $activeStepIndex ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400' ?>
                            <?= (($transaction['status'] ?? '') === 'cancelled' && $i === 0) ? 'bg-danger text-white' : '' ?>">
                            <i data-lucide="<?= $step['icon'] ?>" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs mt-1 font-medium whitespace-nowrap
                            <?= $i <= $activeStepIndex ? 'text-primary' : 'text-gray-400' ?>">
                            <?= $step['label'] ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Transaction Info -->
            <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pesanan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Kode Pesanan</p>
                        <p class="font-semibold text-gray-800"><?= esc($transaction['transaction_code'] ?? '') ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-medium text-gray-800"><?= date('d M Y H:i', strtotime($transaction['transaction_date'] ?? 'now')) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <?= $this->include('components/badge', [
                            'text'  => $statusConfig['text'],
                            'color' => $statusConfig['color'],
                        ]) ?>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Pembayaran</p>
                        <?php
                        $payStatusConfig = match (($transaction['payment_status'] ?? '')) {
                            'pending'  => ['color' => 'warning', 'text' => 'Menunggu Verifikasi'],
                            'verified' => ['color' => 'success', 'text' => 'Terverifikasi'],
                            'rejected' => ['color' => 'danger', 'text' => 'Ditolak'],
                            default    => ['color' => 'gray', 'text' => ucfirst($transaction['payment_status'] ?? '')],
                        };
                        ?>
                        <?= $this->include('components/badge', [
                            'text'  => $payStatusConfig['text'],
                            'color' => $payStatusConfig['color'],
                        ]) ?>
                    </div>
                </div>
                <?php if (!empty($transaction['notes'] ?? '')): ?>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500">Catatan</p>
                        <p class="text-sm text-gray-700"><?= esc($transaction['notes'] ?? '') ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Shipping Address -->
            <?php if ($shippingAddress ?? []): ?>
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Alamat Pengiriman</h2>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-light flex items-center justify-center flex-shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5 text-primary"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-gray-800"><?= esc($shippingAddress['recipient_name'] ?? '') ?></span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><?= esc($shippingAddress['label'] ?? '') ?></span>
                            </div>
                            <p class="text-sm text-gray-600"><?= esc($shippingAddress['phone'] ?? '') ?></p>
                            <p class="text-sm text-gray-500 mt-1">
                                <?= esc($shippingAddress['address'] ?? '') ?><?= !empty($shippingAddress['district'] ?? '') ? ', ' . esc($shippingAddress['district'] ?? '') : '' ?>,
                                <?= esc($shippingAddress['city'] ?? '') ?>, <?= esc($shippingAddress['province'] ?? '') ?> <?= esc($shippingAddress['postal_code'] ?? '') ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Produk Dipesan</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-2 text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="text-center py-3 px-2 text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <?php if (!empty($items ?? [])): ?>
                            <tbody>
                                <?php foreach ($items ?? [] as $item): ?>
                                    <tr class="border-b border-gray-50 last:border-0">
                                        <td class="py-3 px-2">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($item['product_image'] ?? '')): ?>
                                                    <img src="<?= base_url('writable/uploads/products/' . ($item['product_image'] ?? '')) ?>"
                                                        alt="<?= esc($item['product_name'] ?? '') ?>"
                                                        class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                                                <?php else: ?>
                                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                        <i data-lucide="image" class="w-5 h-5 text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="text-sm font-medium text-gray-800"><?= esc($item['product_name'] ?? '') ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2 text-right text-sm text-gray-700">
                                            Rp <?= number_format($item['item_price'] ?? 0, 0, ',', '.') ?>
                                        </td>
                                        <td class="py-3 px-2 text-center text-sm text-gray-700">
                                            <?= $item['quantity'] ?? 0 ?>
                                        </td>
                                        <td class="py-3 px-2 text-right text-sm font-semibold text-gray-800">
                                            Rp <?= number_format($item['subtotal'] ?? 0, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        <?php else: ?>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-gray-400">Tidak ada item</td>
                                </tr>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Review Section (hanya untuk transaksi selesai) -->
            <?php if (($transaction['status'] ?? '') === 'completed'): ?>
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="star" class="w-5 h-5 text-primary"></i>
                        Beri Ulasan
                    </h2>
                    <div class="space-y-6">
                        <?php foreach ($items ?? [] as $item): ?>
                            <div class="border border-gray-100 rounded-lg p-4" x-data="{ 
                                rating: <?= $item['existing_review']['rating'] ?? 0 ?>, 
                                hover: 0,
                                reviewed: <?= !empty($item['existing_review'] ?? null) ? 'true' : 'false' ?>
                            }">
                                <div class="flex items-center gap-3 mb-4">
                                    <?php if (!empty($item['product_image'] ?? '')): ?>
                                        <img src="<?= base_url('writable/uploads/products/' . ($item['product_image'] ?? '')) ?>"
                                            alt="<?= esc($item['product_name'] ?? '') ?>"
                                            class="w-10 h-10 rounded-lg object-cover border border-gray-100">
                                    <?php endif; ?>
                                    <span class="text-sm font-medium text-gray-800"><?= esc($item['product_name'] ?? '') ?></span>
                                    <template x-if="reviewed">
                                        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success/10 text-success">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                            Sudah Diulas
                                        </span>
                                    </template>
                                </div>

                                <form action="<?= base_url('review/add') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?? 0 ?>">

                                    <!-- Star Rating -->
                                    <div class="flex items-center gap-1 mb-3">
                                        <span class="text-sm text-gray-600 mr-2">Rating:</span>
                                        <template x-for="i in 5" :key="i">
                                            <button type="button" 
                                                @click="rating = i; reviewed = false"
                                                @mouseenter="hover = i"
                                                @mouseleave="hover = 0"
                                                class="focus:outline-none">
                                                <i data-lucide="star" class="w-6 h-6 transition-colors"
                                                    :class="(hover >= i || rating >= i) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'"></i>
                                            </button>
                                        </template>
                                        <input type="hidden" name="rating" :value="rating" required>
                                    </div>

                                    <!-- Comment -->
                                    <textarea name="comment" rows="2" 
                                        placeholder="Tulis ulasan Anda (opsional)..."
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none"
                                        maxlength="1000"><?= esc($item['existing_review']['comment'] ?? '') ?></textarea>

                                    <div class="flex justify-end mt-2">
                                        <button type="submit" x-show="rating > 0"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
                                            <i data-lucide="send" class="w-4 h-4"></i>
                                            <span x-text="reviewed ? 'Perbarui Ulasan' : 'Kirim Ulasan'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Payment Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pembayaran</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-800">Rp <?= number_format($transaction['total_amount'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Ongkos Kirim</span>
                        <span class="font-medium text-gray-800">Rp <?= number_format($transaction['shipping_cost'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <?php if (($transaction['discount_amount'] ?? 0) > 0): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Diskon</span>
                            <span class="font-medium text-success">-Rp <?= number_format($transaction['discount_amount'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                    <hr class="border-gray-100">
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-800">Total</span>
                        <span class="text-lg font-bold text-primary">Rp <?= number_format($transaction['final_amount'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Pembayaran</h2>

                <?php if (($transaction['status'] ?? '') === 'pending_payment'): ?>
                    <!-- Upload bukti transfer -->
                    <?php if (!empty($transaction['payment_proof'] ?? '')): ?>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Bukti Transfer</p>
                            <img src="<?= base_url('writable/uploads/payment_proofs/' . ($transaction['payment_proof'] ?? '')) ?>"
                                alt="Bukti Transfer"
                                class="w-full rounded-lg border border-gray-100">
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('checkout/upload-proof/' . ($transaction['id'] ?? 0)) ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer</label>
                            <input type="file" name="payment_proof" accept="image/*" required
                                class="input-field text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-light file:text-primary hover:file:bg-primary hover:file:text-white file:cursor-pointer">
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 rounded-lg bg-gradient-to-r from-primary to-primary-dark text-white font-medium hover:shadow-md transition-all text-sm">
                            Upload Bukti Transfer
                        </button>
                    </form>

                <?php elseif (($transaction['payment_status'] ?? '') === 'rejected'): ?>
                    <!-- Payment rejected - show reason & re-upload -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-2">
                            <i data-lucide="x-circle" class="w-5 h-5 text-danger flex-shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-danger">Bukti Transfer Ditolak</p>
                                <?php if (!empty($transaction['payment_rejection_reason'] ?? '')): ?>
                                    <p class="text-sm text-red-700 mt-1">Alasan: <?= esc($transaction['payment_rejection_reason'] ?? '') ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($transaction['payment_proof'] ?? '')): ?>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Bukti Transfer Sebelumnya</p>
                            <img src="<?= base_url('writable/uploads/payment_proofs/' . ($transaction['payment_proof'] ?? '')) ?>"
                                alt="Bukti Transfer"
                                class="w-full rounded-lg border border-gray-100">
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('checkout/upload-proof/' . ($transaction['id'] ?? 0)) ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Ulang Bukti Transfer</label>
                            <input type="file" name="payment_proof" accept="image/*" required
                                class="input-field text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-light file:text-primary hover:file:bg-primary hover:file:text-white file:cursor-pointer">
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 rounded-lg bg-gradient-to-r from-primary to-primary-dark text-white font-medium hover:shadow-md transition-all text-sm">
                            Upload Ulang Bukti Transfer
                        </button>
                    </form>

                <?php elseif (($transaction['status'] ?? '') === 'paid'): ?>
                    <!-- Waiting for verification -->
                    <div class="text-center py-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="clock" class="w-6 h-6 text-info"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-800 mb-1">Menunggu Verifikasi Admin</p>
                        <p class="text-xs text-gray-500">Bukti transfer Anda sedang diverifikasi oleh admin.</p>
                    </div>

                    <?php if (!empty($transaction['payment_proof'] ?? '')): ?>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-2">Bukti Transfer</p>
                            <img src="<?= base_url('writable/uploads/payment_proofs/' . ($transaction['payment_proof'] ?? '')) ?>"
                                alt="Bukti Transfer"
                                class="w-full rounded-lg border border-gray-100">
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Other statuses - show proof if exists -->
                    <?php if (!empty($transaction['payment_proof'] ?? '')): ?>
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Bukti Transfer</p>
                            <img src="<?= base_url('writable/uploads/payment_proofs/' . ($transaction['payment_proof'] ?? '')) ?>"
                                alt="Bukti Transfer"
                                class="w-full rounded-lg border border-gray-100">
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>