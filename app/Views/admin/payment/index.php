<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $transactions */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
/** @var string|null $filter */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Verifikasi Pembayaran'],
    ]
]) ?>

<div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Verifikasi Pembayaran</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola verifikasi pembayaran pelanggan</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-6">
        <a href="<?= base_url('admin/payments') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= empty($filter) ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="clock" class="w-4 h-4"></i>
            Menunggu Verifikasi
        </a>
        <a href="<?= base_url('admin/payments?filter=verified') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($filter ?? '') === 'verified' ? 'bg-success text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            Sudah Diverifikasi
        </a>
        <a href="<?= base_url('admin/payments?filter=rejected') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($filter ?? '') === 'rejected' ? 'bg-danger text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="x-circle" class="w-4 h-4"></i>
            Ditolak
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6 max-w-md">
        <?= $this->include('components/search', [
            'placeholder' => 'Cari kode transaksi atau nama pelanggan...',
            'action'      => base_url('admin/payments') . (($filter ?? '') ? '?filter=' . ($filter ?? '') : ''),
        ]) ?>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($transactions)): ?>
            <?= $this->include('components/empty-state', [
                'icon'        => 'credit-card',
                'title'       => 'Tidak ada pembayaran',
                'description' => ($filter ?? '') === 'verified' ? 'Belum ada pembayaran yang diverifikasi.' : (($filter ?? '') === 'rejected' ? 'Belum ada pembayaran yang ditolak.' : 'Tidak ada pembayaran yang menunggu verifikasi.'),
            ]) ?>
        <?php else: ?>
            <?php
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
                'headers' => ['No', 'Kode Transaksi', 'Pelanggan', 'Tanggal', 'Total', 'Bukti Transfer', 'Status', 'Aksi'],
                'slot'    => (function () use ($transactions, $pager, $paymentColors, $paymentLabels, $badgeColorMap) {
                    $output = '';
                    $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1;
                    foreach ($transactions as $trx) {
                        $ps = $trx['payment_status'] ?? '';
                        $pc = $paymentColors[$ps] ?? 'gray';
                        $pl = $paymentLabels[$ps] ?? ucfirst($ps);
                        $pcc = $badgeColorMap[$pc] ?? $badgeColorMap['gray'];

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($trx['transaction_code'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($trx['customer_name'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . ($trx['transaction_date'] ? date('d M Y H:i', strtotime($trx['transaction_date'])) : '-') . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">Rp ' . number_format($trx['final_amount'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        if (!empty($trx['payment_proof'])) {
                            $output .= '<img src="' . base_url('writable/uploads/payment_proofs/' . $trx['payment_proof']) . '" alt="Bukti" class="w-12 h-12 rounded-lg object-cover border border-gray-100">';
                        } else {
                            $output .= '<span class="text-gray-400 italic text-sm">-</span>';
                        }
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $pcc . '">' . $pl . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<a href="' . base_url('admin/payments/detail/' . $trx['id']) . '" class="p-1.5 rounded-lg text-info hover:bg-blue-50 transition-colors" title="Detail">';
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