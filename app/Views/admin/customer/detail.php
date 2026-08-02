<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array|null $customer */
/** @var array $transactions */
/** @var array $loyaltyLogs */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Pelanggan', 'url' => base_url('admin/customers')],
        ['label' => 'Detail'],
    ]
]) ?>

<div x-data="{ showEditModal: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Detail Pelanggan</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap pelanggan</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showEditModal = true" class="btn-primary inline-flex items-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Edit Pelanggan
            </button>
            <a href="<?= base_url('admin/customers') ?>" class="btn-secondary inline-flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    <?php
        $membershipColors = [
            'bronze'   => 'secondary',
            'silver'   => 'gray',
            'gold'     => 'warning',
            'platinum' => 'primary',
        ];
        $membershipLabels = [
            'bronze'   => 'Bronze',
            'silver'   => 'Silver',
            'gold'     => 'Gold',
            'platinum' => 'Platinum',
        ];
        $level = $customer['membership_level'] ?? 'bronze';
        $levelColor = $membershipColors[$level] ?? 'gray';
        $levelLabel = $membershipLabels[$level] ?? ucfirst($level);

        $badgeColorMap = [
            'success'  => 'bg-green-100 text-green-700',
            'warning'  => 'bg-yellow-100 text-yellow-700',
            'danger'   => 'bg-red-100 text-red-700',
            'info'     => 'bg-blue-100 text-blue-700',
            'primary'  => 'bg-primary-light text-primary-dark',
            'secondary' => 'bg-secondary-light text-secondary-dark',
            'gray'     => 'bg-gray-100 text-gray-600',
        ];

        $levelColorClass = $badgeColorMap[$levelColor] ?? $badgeColorMap['gray'];
    ?>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Card 1: Profile Info -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-primary"></i>
                    Informasi Profil
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Nama</span>
                    <span class="text-sm font-medium text-gray-800"><?= esc($customer['name'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Email</span>
                    <span class="text-sm text-gray-800"><?= esc($customer['email'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Telepon</span>
                    <span class="text-sm text-gray-800"><?= esc($customer['phone'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Alamat</span>
                    <span class="text-sm text-gray-800"><?= esc($customer['address'] ?? '-') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-28 shrink-0">Tanggal Lahir</span>
                    <span class="text-sm text-gray-800"><?= $customer['birth_date'] ? date('d/m/Y', strtotime($customer['birth_date'])) : '-' ?></span>
                </div>
            </div>
        </div>

        <!-- Card 2: Membership & Loyalty -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="award" class="w-5 h-5 text-primary"></i>
                    Keanggotaan & Loyalitas
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-36 shrink-0">Level</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $levelColorClass ?>"><?= $levelLabel ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-36 shrink-0">Poin Loyalitas</span>
                    <span class="text-sm font-medium text-gray-800"><?= number_format($customer['loyalty_points'] ?? 0, 0, ',', '.') ?> poin</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-36 shrink-0">Total Belanja</span>
                    <span class="text-sm font-medium text-gray-800">Rp <?= number_format($customer['total_spending'] ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-36 shrink-0">Pertama Belanja</span>
                    <span class="text-sm text-gray-800"><?= $customer['first_purchase_date'] ? date('d/m/Y', strtotime($customer['first_purchase_date'])) : '-' ?></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-sm text-gray-500 w-36 shrink-0">Terakhir Belanja</span>
                    <span class="text-sm text-gray-800"><?= $customer['last_purchase_date'] ? date('d/m/Y', strtotime($customer['last_purchase_date'])) : '-' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Transaction History -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-primary"></i>
                Riwayat Transaksi
            </h3>
        </div>
        <?php if (empty($transactions)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'receipt',
                'title'       => 'Belum ada transaksi',
                'description' => 'Pelanggan ini belum memiliki riwayat transaksi.',
            ]) ?>
        <?php else: ?>
            <?php
                $statusColors = [
                    'pending_payment' => 'warning',
                    'paid'            => 'info',
                    'processing'      => 'primary',
                    'shipped'         => 'secondary',
                    'completed'       => 'success',
                    'cancelled'       => 'danger',
                ];
                $statusLabels = [
                    'pending_payment' => 'Menunggu Pembayaran',
                    'paid'            => 'Sudah Dibayar',
                    'processing'      => 'Diproses',
                    'shipped'         => 'Dikirim',
                    'completed'       => 'Selesai',
                    'cancelled'       => 'Dibatalkan',
                ];
            ?>
            <?= view('components/table', [
                'headers' => ['Tanggal', 'Kode Transaksi', 'Total', 'Status'],
                'slot'    => (function() use ($transactions, $statusColors, $statusLabels, $badgeColorMap) {
                    $output = '';
                    foreach ($transactions as $trx) {
                        $status = $trx['status'] ?? '';
                        $color = $statusColors[$status] ?? 'gray';
                        $label = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                        $colorClass = $badgeColorMap[$color] ?? $badgeColorMap['gray'];

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . ($trx['transaction_date'] ? date('d/m/Y', strtotime($trx['transaction_date'])) : '-') . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($trx['transaction_code'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">Rp ' . number_format($trx['final_amount'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . '">' . $label . '</span>';
                        $output .= '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Card 4: Points History -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i data-lucide="star" class="w-5 h-5 text-primary"></i>
                Riwayat Poin
            </h3>
        </div>
        <?php if (empty($pointsLog)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'star',
                'title'       => 'Belum ada riwayat poin',
                'description' => 'Pelanggan ini belum memiliki riwayat poin loyalitas.',
            ]) ?>
        <?php else: ?>
            <?php
                $pointTypeColors = [
                    'earn'    => 'success',
                    'redeem'  => 'warning',
                    'expired' => 'danger',
                ];
                $pointTypeLabels = [
                    'earn'    => 'Earn',
                    'redeem'  => 'Redeem',
                    'expired' => 'Expired',
                ];
            ?>
            <?= view('components/table', [
                'headers' => ['Tanggal', 'Tipe', 'Poin', 'Keterangan'],
                'slot'    => (function() use ($pointsLog, $pointTypeColors, $pointTypeLabels, $badgeColorMap) {
                    $output = '';
                    foreach ($pointsLog as $log) {
                        $type = $log['type'] ?? '';
                        $color = $pointTypeColors[$type] ?? 'gray';
                        $label = $pointTypeLabels[$type] ?? ucfirst($type);
                        $colorClass = $badgeColorMap[$color] ?? $badgeColorMap['gray'];

                        $points = (int) ($log['points'] ?? 0);
                        $pointsDisplay = ($type === 'earn' ? '+' : '-') . number_format(abs($points), 0, ',', '.');
                        $pointsColor = ($type === 'earn') ? 'text-green-600' : (($type === 'redeem' || $type === 'expired') ? 'text-red-600' : 'text-gray-600');

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . (isset($log['created_at']) ? date('d/m/Y', strtotime($log['created_at'])) : '-') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . '">' . $label . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 font-medium ' . $pointsColor . '">' . $pointsDisplay . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($log['description'] ?? '-') . '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <?= view('components/modal', [
        'showVar' => 'showEditModal',
        'title'   => 'Edit Pelanggan',
        'slot'    => (function() use ($customer, $membershipLabels) {
            $output = '<form action="' . base_url('admin/customers/update/' . $customer['id']) . '" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div>';
            $output .= '<label for="edit_membership_level" class="block text-sm font-medium text-gray-700 mb-1">Level Keanggotaan <span class="text-danger">*</span></label>';
            $output .= '<select id="edit_membership_level" name="membership_level" required class="input-field">';
            foreach ($membershipLabels as $value => $label) {
                $selected = ($customer['membership_level'] ?? '') === $value ? 'selected' : '';
                $output .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
            }
            $output .= '</select>';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_loyalty_points" class="block text-sm font-medium text-gray-700 mb-1">Poin Loyalitas <span class="text-danger">*</span></label>';
            $output .= '<input type="number" id="edit_loyalty_points" name="loyalty_points" value="' . ($customer['loyalty_points'] ?? 0) . '" required min="0" class="input-field" placeholder="Masukkan poin loyalitas">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="flex items-center justify-end gap-3 mt-6">';
            $output .= '<button type="button" @click="showEditModal = false" class="btn-secondary">Batal</button>';
            $output .= '<button type="submit" class="btn-primary">Simpan Perubahan</button>';
            $output .= '</div>';
            $output .= '</form>';
            return $output;
        })(),
    ]) ?>

</div>

<?= $this->endSection() ?>
