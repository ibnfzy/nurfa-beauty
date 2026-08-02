<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $transactions */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
/** @var string|null $status */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Transaksi'],
    ]
]) ?>

<div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Daftar Transaksi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua transaksi di toko Anda</p>
        </div>
        <a href="<?= base_url('admin/transactions/create') ?>" class="btn-primary inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Input Transaksi
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1 max-w-md">
            <?= $this->include('components/search', [
                'placeholder' => 'Cari kode transaksi atau nama pelanggan...',
                'action'      => base_url('admin/transactions'),
            ]) ?>
        </div>
        <div class="relative">
            <select
                onchange="window.location.href = '<?= base_url('admin/transactions') ?>?q=<?= esc($search ?? '') ?>&status=' + this.value"
                class="input-field pr-10 appearance-none cursor-pointer min-w-[200px]">
                <option value="">Semua Status</option>
                <option value="pending_payment" <?= ($status ?? '') === 'pending_payment' ? 'selected' : '' ?>>Menunggu Bayar</option>
                <option value="paid" <?= ($status ?? '') === 'paid' ? 'selected' : '' ?>>Sudah Bayar</option>
                <option value="processing" <?= ($status ?? '') === 'processing' ? 'selected' : '' ?>>Diproses</option>
                <option value="shipped" <?= ($status ?? '') === 'shipped' ? 'selected' : '' ?>>Dikirim</option>
                <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($transactions ?? [])): ?>
            <?= $this->include('components/empty-state', [
                'icon'        => 'receipt',
                'title'       => 'Belum ada transaksi',
                'description' => 'Transaksi akan muncul di sini setelah pelanggan melakukan pemesanan atau Anda menginput transaksi manual.',
                'actionText'  => 'Input Transaksi',
                'actionUrl'   => base_url('admin/transactions/create'),
            ]) ?>
        <?php else: ?>
            <?php
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
            $badgeColorMap = [
                'success'  => 'bg-green-100 text-green-700',
                'warning'  => 'bg-yellow-100 text-yellow-700',
                'danger'   => 'bg-red-100 text-red-700',
                'info'     => 'bg-blue-100 text-blue-700',
                'primary'  => 'bg-primary-light text-primary-dark',
                'secondary' => 'bg-secondary-light text-secondary-dark',
                'gray'     => 'bg-gray-100 text-gray-600',
            ];
            ?>

            <?= $this->include('components/table', [
                'headers' => ['No', 'Kode Transaksi', 'Pelanggan', 'Tanggal', 'Total', 'Status', 'Pembayaran', 'Aksi'],
                'slot'    => (function () use ($transactions, $pager, $statusColors, $statusLabels, $paymentColors, $paymentLabels, $badgeColorMap) {
                    $output = '';
                    $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1;
                    foreach ($transactions as $trx) {
                        $status = $trx['status'] ?? '';
                        $sColor = $statusColors[$status] ?? 'gray';
                        $sLabel = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                        $sColorClass = $badgeColorMap[$sColor] ?? $badgeColorMap['gray'];

                        $payStatus = $trx['payment_status'] ?? '';
                        $pColor = $paymentColors[$payStatus] ?? 'gray';
                        $pLabel = $paymentLabels[$payStatus] ?? ucfirst($payStatus);
                        $pColorClass = $badgeColorMap[$pColor] ?? $badgeColorMap['gray'];

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($trx['transaction_code'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($trx['customer_name'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . ($trx['transaction_date'] ? date('d M Y H:i', strtotime($trx['transaction_date'])) : '-') . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">Rp ' . number_format($trx['final_amount'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $sColorClass . '">' . $sLabel . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $pColorClass . '">' . $pLabel . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<a href="' . base_url('admin/transactions/detail/' . $trx['id']) . '" class="p-1.5 rounded-lg text-info hover:bg-blue-50 transition-colors" title="Detail">';
                        $output .= '<i data-lucide="eye" class="w-4 h-4"></i>';
                        $output .= '</a>';
                        $output .= '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <?= $this->include('components/pagination', ['pager' => $pager]) ?>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>