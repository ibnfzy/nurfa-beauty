<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var int $totalIssued */
/** @var int $totalRedeemed */
/** @var int $activePoints */
/** @var array $membershipStats */
/** @var string[] $trendLabels */
/** @var int[] $trendEarned */
/** @var int[] $trendRedeemed */
/** @var array $recentTransactions */
/** @var int $totalVouchers */
/** @var int $usedVouchers */
/** @var float $usageRate */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Laporan', 'url' => base_url('admin/report')],
        ['label' => 'Loyalitas'],
    ]
]) ?>

<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Laporan Loyalitas</h2>
            <p class="text-sm text-gray-500 mt-1">Analisis poin loyalitas dan penggunaan voucher</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/report/export/loyalty-pdf') ?>"
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 inline-flex items-center gap-2 text-sm font-medium transition-colors">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                PDF
            </a>
            <a href="<?= base_url('admin/report/export/loyalty-excel') ?>"
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 inline-flex items-center gap-2 text-sm font-medium transition-colors">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Excel
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <?= view('components/stat-card', [
            'title'   => 'Total Poin Dikeluarkan',
            'value'   => number_format($totalIssued, 0, ',', '.'),
            'icon'    => 'plus-circle',
            'color'   => 'success',
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Total Poin Ditebus',
            'value'   => number_format($totalRedeemed, 0, ',', '.'),
            'icon'    => 'minus-circle',
            'color'   => 'warning',
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Poin Aktif',
            'value'   => number_format($activePoints, 0, ',', '.'),
            'icon'    => 'award',
            'color'   => 'primary',
        ]) ?>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Points Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Poin (6 Bulan Terakhir)</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="pointsTrendChart"></canvas>
            </div>
        </div>

        <!-- Voucher Usage -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Voucher</h3>
            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <p class="text-2xl font-bold text-blue-600"><?= number_format($totalVouchers, 0, ',', '.') ?></p>
                        <p class="text-xs text-gray-500 mt-1">Total Voucher</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <p class="text-2xl font-bold text-green-600"><?= number_format($usedVouchers, 0, ',', '.') ?></p>
                        <p class="text-xs text-gray-500 mt-1">Terpakai</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-xl">
                        <p class="text-2xl font-bold text-yellow-600"><?= $usageRate ?>%</p>
                        <p class="text-xs text-gray-500 mt-1">Tingkat Penggunaan</p>
                    </div>
                </div>

                <!-- Voucher usage bar -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Tingkat Penggunaan Voucher</span>
                        <span class="text-sm font-semibold text-gray-800"><?= $usageRate ?>%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-100 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-primary-dark h-full rounded-full transition-all" style="width: <?= $usageRate ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Membership Distribution Table -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Distribusi per Membership</h3>
        </div>
        <?php if (empty($membershipStats)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'award',
                'title'       => 'Belum ada data',
                'description' => 'Belum ada data membership.',
            ]) ?>
        <?php else: ?>
            <?php
                $levelLabels = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
            ?>
            <?= view('components/table', [
                'headers' => ['Level', 'Jumlah Pelanggan', 'Rata-rata Poin', 'Total Spending'],
                'slot'    => (function () use ($membershipStats, $levelLabels) {
                    $output = '';
                    $badgeColorMap = [
                        'bronze'   => 'bg-secondary-light text-secondary-dark',
                        'silver'   => 'bg-gray-100 text-gray-600',
                        'gold'     => 'bg-yellow-100 text-yellow-700',
                        'platinum' => 'bg-primary-light text-primary-dark',
                    ];
                    foreach ($membershipStats as $ms) {
                        $level = $ms['membership_level'] ?? '';
                        $colorClass = $badgeColorMap[$level] ?? 'bg-gray-100 text-gray-600';

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . '">' . ($levelLabels[$level] ?? ucfirst($level)) . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . number_format((int) ($ms['customer_count'] ?? 0), 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . number_format((int) ($ms['avg_points'] ?? 0), 0, ',', '.') . ' poin</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">Rp ' . number_format((int) ($ms['total_spending'] ?? 0), 0, ',', '.') . '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Recent Loyalty Transactions -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Transaksi Loyalitas Terakhir</h3>
        </div>
        <?php if (empty($recentTransactions)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'clock',
                'title'       => 'Belum ada transaksi',
                'description' => 'Belum ada transaksi loyalitas.',
            ]) ?>
        <?php else: ?>
            <?= view('components/table', [
                'headers' => ['No', 'Pelanggan', 'Tipe', 'Poin', 'Keterangan', 'Tanggal'],
                'slot'    => (function () use ($recentTransactions) {
                    $output = '';
                    $no = 1;
                    $typeColors = [
                        'earn'   => 'bg-green-100 text-green-700',
                        'bonus'  => 'bg-blue-100 text-blue-700',
                        'redeem' => 'bg-red-100 text-red-700',
                    ];
                    $typeLabels = [
                        'earn'   => 'Earn',
                        'bonus'  => 'Bonus',
                        'redeem' => 'Redeem',
                    ];
                    foreach ($recentTransactions as $trx) {
                        $type = $trx['type'] ?? '';
                        $colorClass = $typeColors[$type] ?? 'bg-gray-100 text-gray-600';
                        $label = $typeLabels[$type] ?? ucfirst($type);
                        $points = (int) ($trx['points'] ?? 0);
                        $pointsPrefix = $type === 'redeem' ? '-' : '+';

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($trx['customer_name'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . '">' . $label . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 font-medium ' . ($type === 'redeem' ? 'text-red-600' : 'text-green-600') . '">' . $pointsPrefix . number_format(abs($points), 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600 text-sm">' . esc($trx['description'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600 text-sm">' . ($trx['created_at'] ? date('d M Y', strtotime($trx['created_at'])) : '-') . '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('pointsTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($trendLabels ?? []) ?>,
            datasets: [
                {
                    label: 'Poin Dikeluarkan',
                    data: <?= json_encode($trendEarned ?? []) ?>,
                    backgroundColor: 'rgba(125, 184, 143, 0.6)',
                    borderColor: '#7DB88F',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Poin Ditebus',
                    data: <?= json_encode($trendRedeemed ?? []) ?>,
                    backgroundColor: 'rgba(232, 196, 125, 0.6)',
                    borderColor: '#E8C47D',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { family: 'Poppins' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Poppins', size: 11 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: {
                        font: { family: 'Poppins', size: 11 }
                    },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
