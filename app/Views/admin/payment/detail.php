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
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Verifikasi Pembayaran', 'url' => base_url('admin/payments')],
        ['label' => 'Detail'],
    ]
]) ?>

<div x-data="{ showRejectModal: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Detail Pembayaran</h2>
            <p class="text-sm text-gray-500 mt-1">Verifikasi pembayaran transaksi</p>
        </div>
        <a href="<?= base_url('admin/payments') ?>" class="btn-secondary inline-flex items-center gap-2">
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
    $paymentColors = [
        'pending'  => 'warning',
        'verified' => 'success',
        'rejected' => 'danger',
    ];
    $paymentLabels = [
        'pending'  => 'Menunggu Verifikasi',
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
                    <span class="text-sm text-gray-500 w-32 shrink-0">Status Pembayaran</span>
                    <?php
                    $ps = $transaction['payment_status'] ?? '';
                    $pc = $paymentColors[$ps] ?? 'gray';
                    $pl = $paymentLabels[$ps] ?? ucfirst($ps);
                    $pcc = $badgeColorMap[$pc] ?? $badgeColorMap['gray'];
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $pcc ?>"><?= $pl ?></span>
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

    <!-- Payment Proof -->
    <?php if (!empty($transaction['payment_proof'])): ?>
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="image" class="w-5 h-5 text-primary"></i>
                    Bukti Transfer
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="inline-block rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <img src="<?= base_url('uploads/payment_proofs/' . $transaction['payment_proof']) ?>" alt="Bukti Transfer" class="max-w-sm max-h-96 object-contain">
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
            <?= view('components/empty-state', [
                'icon'        => 'package',
                'title'       => 'Tidak ada item',
                'description' => 'Transaksi ini tidak memiliki item.',
            ]) ?>
        <?php else: ?>
            <?= view('components/table', [
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
                            $output .= '<img src="' . base_url('uploads/products/' . $item['product_image']) . '" alt="' . esc($item['product_name'] ?? '') . '" class="w-10 h-10 rounded-lg object-cover border border-gray-100">';
                        } else {
                            $output .= '<div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">';
                            $output .= '<i data-lucide="image" class="w-4 h-4 text-gray-400"></i>';
                            $output .= '</div>';
                        }
                        $output .= '<span class="font-medium text-gray-800">' . esc($item['product_name'] ?? '-') . '</span>';
                        $output .= '</div>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">Rp ' . number_format($item['price'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . ($item['quantity'] ?? 0) . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">Rp ' . number_format($item['subtotal'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Payment Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
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

    <!-- Verification Actions -->
    <?php if (($transaction['payment_status'] ?? '') === 'pending'): ?>
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-primary"></i>
                    Aksi Verifikasi
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Verify Button -->
                    <form action="<?= base_url('admin/payments/verify/' . ($transaction['id'] ?? '')) ?>" method="POST" class="flex-1">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-success text-white rounded-lg hover:opacity-90 transition-colors text-sm font-medium">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Verifikasi Pembayaran
                        </button>
                    </form>

                    <!-- Reject Button -->
                    <button @click="showRejectModal = true" type="button" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-danger text-white rounded-lg hover:opacity-90 transition-colors text-sm font-medium">
                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                        Tolak Pembayaran
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (($transaction['payment_status'] ?? '') === 'verified'): ?>
        <!-- Verified Info -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-6 h-6 text-success mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="text-sm font-semibold text-green-800 mb-1">Pembayaran Terverifikasi</h4>
                    <div class="text-sm text-green-700 space-y-1">
                        <p>Diverifikasi pada: <?= ($transaction['payment_verified_at'] ?? '') ? date('d M Y H:i', strtotime($transaction['payment_verified_at'] ?? '')) : '-' ?></p>
                        <?php if (!empty($verifiedBy)): ?>
                            <p>Diverifikasi oleh: <?= esc($verifiedBy['name'] ?? '-') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (($transaction['payment_status'] ?? '') === 'rejected'): ?>
        <!-- Rejected Info -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="x-circle" class="w-6 h-6 text-danger mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="text-sm font-semibold text-red-800 mb-1">Pembayaran Ditolak</h4>
                    <div class="text-sm text-red-700">
                        <p>Alasan: <?= esc($transaction['payment_rejection_reason'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php $transaction = $transaction ?? [] ?>

    <!-- Rejection Modal -->
    <?= view('components/modal', [
        'showVar' => 'showRejectModal',
        'title'   => 'Tolak Pembayaran',
        'slot'    => (function () use ($transaction) {
            $output = '<form action="' . base_url('admin/payments/reject/' . ($transaction['id'] ?? '')) . '" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">';
            $output .= '<div class="flex items-start gap-2">';
            $output .= '<i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0"></i>';
            $output .= '<p class="text-sm text-yellow-700">Menolak pembayaran akan mengembalikan status transaksi ke "Menunggu Bayar" agar pelanggan dapat mengunggah bukti transfer ulang.</p>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="payment_rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-danger">*</span></label>';
            $output .= '<textarea id="payment_rejection_reason" name="payment_rejection_reason" rows="4" required class="input-field" placeholder="Masukkan alasan penolakan pembayaran"></textarea>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="flex items-center justify-end gap-3 mt-6">';
            $output .= '<button type="button" @click="showRejectModal = false" class="btn-secondary">Batal</button>';
            $output .= '<button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">Tolak Pembayaran</button>';
            $output .= '</div>';
            $output .= '</form>';
            return $output;
        })(),
    ]) ?>

    <!-- Back Button -->
    <div class="flex justify-end">
        <a href="<?= base_url('admin/payments') ?>" class="btn-secondary inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Verifikasi Pembayaran
        </a>
    </div>

</div>

<?= $this->endSection() ?>