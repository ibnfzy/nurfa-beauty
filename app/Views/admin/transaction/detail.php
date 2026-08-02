<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $transaction */
/** @var array $items */
/** @var array|null $shippingAddress */
/** @var array|null $verifiedBy */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Transaksi', 'url' => base_url('admin/transactions')],
        ['label' => 'Detail'],
    ]
]) ?>

<div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Detail Transaksi</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap transaksi</p>
        </div>
        <a href="<?= base_url('admin/transactions') ?>" class="btn-secondary inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    <?php
    $badgeColorMap = [
        'success'  => 'bg-green-100 text-green-700',
        'warning'  => 'bg-yellow-100 text-yellow-700',
        'danger'   => 'bg-red-100 text-red-700',
        'info'     => 'bg-blue-100 text-blue-700',
        'primary'  => 'bg-primary-light text-primary-dark',
        'secondary' => 'bg-secondary-light text-secondary-dark',
        'gray'     => 'bg-gray-100 text-gray-600',
    ];

    $statusColors = [
        'pending_payment' => 'warning',
        'paid'            => 'info',
        'processing'      => 'primary',
        'shipped'         => 'info',
        'completed'       => 'success',
        'cancelled'       => 'danger',
    ];
    $statusLabels = [
        'pending_payment' => 'Menunggu Bayar',
        'paid'            => 'Sudah Bayar',
        'processing'      => 'Diproses',
        'shipped'         => 'Dikirim',
        'completed'       => 'Selesai',
        'cancelled'       => 'Dibatalkan',
    ];
    $paymentColors = [
        'pending'  => 'warning',
        'verified' => 'success',
        'rejected' => 'danger',
    ];
    $paymentLabels = [
        'pending'  => 'Menunggu',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ];
    ?>

    <!-- Transaction Info & Customer Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Transaction Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="receipt" class="w-5 h-5 text-primary"></i>
                    Informasi Transaksi
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-32 shrink-0">Kode Transaksi</span>
                    <span class="text-sm font-medium text-gray-800"><?= esc($transaction['transaction_code'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-32 shrink-0">Tanggal</span>
                    <span class="text-sm text-gray-800"><?= ($transaction['transaction_date'] ?? '') ? date('d M Y H:i', strtotime($transaction['transaction_date'] ?? '')) : '-' ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-32 shrink-0">Status</span>
                    <?php
                    $s = $transaction['status'] ?? '';
                    $sc = $statusColors[$s] ?? 'gray';
                    $sl = $statusLabels[$s] ?? ucfirst(str_replace('_', ' ', $s));
                    $scc = $badgeColorMap[$sc] ?? $badgeColorMap['gray'];
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $scc ?>"><?= $sl ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-32 shrink-0">Pembayaran</span>
                    <?php
                    $ps = $transaction['payment_status'] ?? '';
                    $pc = $paymentColors[$ps] ?? 'gray';
                    $pl = $paymentLabels[$ps] ?? ucfirst($ps);
                    $pcc = $badgeColorMap[$pc] ?? $badgeColorMap['gray'];
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $pcc ?>"><?= $pl ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-32 shrink-0">Metode Bayar</span>
                    <span class="text-sm text-gray-800"><?= esc(ucfirst($transaction['payment_method'] ?? '-')) ?></span>
                </div>
            </div>
        </div>

        <!-- Customer Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-primary"></i>
                    Pelanggan
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Nama</span>
                    <span class="text-sm font-medium text-gray-800"><?= esc($transaction['customer_name'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Email</span>
                    <span class="text-sm text-gray-800"><?= esc($transaction['customer_email'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Telepon</span>
                    <span class="text-sm text-gray-800"><?= esc($transaction['customer_phone'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($shippingAddress) && !empty($shippingAddress['address_label'])): ?>
        <!-- Shipping Address Card -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5 text-primary"></i>
                    Alamat Pengiriman
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Label</span>
                    <span class="text-sm font-medium text-gray-800"><?= esc($shippingAddress['address_label'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Penerima</span>
                    <span class="text-sm text-gray-800"><?= esc($shippingAddress['recipient_name'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Telepon</span>
                    <span class="text-sm text-gray-800"><?= esc($shippingAddress['phone'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Alamat</span>
                    <span class="text-sm text-gray-800">
                        <?= esc($shippingAddress['shipping_address'] ?? '-') ?>
                        <?php if (!empty($shippingAddress['district'])): ?>, <?= esc($shippingAddress['district']) ?><?php endif; ?>
                        <?php if (!empty($shippingAddress['city'])): ?>, <?= esc($shippingAddress['city']) ?><?php endif; ?>
                        <?php if (!empty($shippingAddress['province'])): ?>, <?= esc($shippingAddress['province']) ?><?php endif; ?>
                        <?php if (!empty($shippingAddress['postal_code'])): ?>, <?= esc($shippingAddress['postal_code']) ?><?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i data-lucide="package" class="w-5 h-5 text-primary"></i>
                Item Pesanan
            </h3>
        </div>
        <?php if (empty($items)): ?>
            <?= $this->include('components/empty-state', [
                'icon'        => 'package',
                'title'       => 'Tidak ada item',
                'description' => 'Transaksi ini tidak memiliki item.',
            ]) ?>
        <?php else: ?>
            <?= $this->include('components/table', [
                'headers' => ['No', 'Produk', 'Harga', 'Jumlah', 'Subtotal'],
                'slot'    => (function () use ($items) {
                    $output = '';
                    $no = 1;
                    foreach ($items as $item) {
                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<div class="flex items-center gap-3">';
                        if (!empty($item['product_image'])) {
                            $output .= '<img src="' . base_url('writable/uploads/products/' . $item['product_image']) . '" alt="' . esc($item['product_name'] ?? '') . '" class="w-10 h-10 rounded-lg object-cover border border-gray-100">';
                        } else {
                            $output .= '<div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">';
                            $output .= '<i data-lucide="image" class="w-4 h-4 text-gray-400"></i>';
                            $output .= '</div>';
                        }
                        $output .= '<span class="font-medium text-gray-800">' . esc($item['product_name'] ?? '-') . '</span>';
                        $output .= '</div>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">Rp ' . number_format($item['item_price'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . ($item['quantity'] ?? 0) . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">Rp ' . number_format($item['subtotal'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Payment Summary & Payment Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Payment Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="calculator" class="w-5 h-5 text-primary"></i>
                    Ringkasan Pembayaran
                </h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Subtotal</span>
                    <span class="text-sm text-gray-800">Rp <?= number_format($transaction['total_amount'] ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Ongkos Kirim</span>
                    <span class="text-sm text-gray-800">Rp <?= number_format($transaction['shipping_cost'] ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Diskon</span>
                    <span class="text-sm text-gray-800">- Rp <?= number_format($transaction['discount_amount'] ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <span class="text-sm font-bold text-gray-800">Total</span>
                    <span class="text-lg font-bold text-gray-800">Rp <?= number_format($transaction['final_amount'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="credit-card" class="w-5 h-5 text-primary"></i>
                    Informasi Pembayaran
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-32 shrink-0">Metode</span>
                    <span class="text-sm text-gray-800"><?= esc(ucfirst($transaction['payment_method'] ?? '-')) ?></span>
                </div>
                <?php if (!empty($transaction['payment_proof'])): ?>
                    <div class="flex items-start gap-3">
                        <span class="text-sm text-gray-500 w-32 shrink-0">Bukti Transfer</span>
                        <div>
                            <img src="<?= base_url('writable/uploads/payment_proofs/' . $transaction['payment_proof']) ?>" alt="Bukti Transfer" class="max-w-[200px] rounded-lg border border-gray-100">
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($transaction['payment_verified_at'])): ?>
                    <div class="flex items-start gap-3">
                        <span class="text-sm text-gray-500 w-32 shrink-0">Diverifikasi</span>
                        <span class="text-sm text-gray-800"><?= date('d M Y H:i', strtotime($transaction['payment_verified_at'])) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($verifiedBy)): ?>
                    <div class="flex items-start gap-3">
                        <span class="text-sm text-gray-500 w-32 shrink-0">Diverifikasi Oleh</span>
                        <span class="text-sm text-gray-800"><?= esc($verifiedBy['name'] ?? '-') ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($transaction['payment_rejection_reason'])): ?>
                    <div class="flex items-start gap-3">
                        <span class="text-sm text-gray-500 w-32 shrink-0">Alasan Ditolak</span>
                        <span class="text-sm text-red-600"><?= esc($transaction['payment_rejection_reason']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($transaction['notes'])): ?>
        <!-- Notes -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
                    Catatan
                </h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-700"><?= nl2br(esc($transaction['notes'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="flex justify-end">
        <a href="<?= base_url('admin/transactions') ?>" class="btn-secondary inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar Transaksi
        </a>
    </div>

</div>

<?= $this->endSection() ?>