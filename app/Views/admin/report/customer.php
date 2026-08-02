<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var int $totalCustomers */
/** @var int $newThisMonth */
/** @var int $activeCount */
/** @var int $churnCount */
/** @var string[] $trendLabels */
/** @var int[] $trendData */
/** @var array $membershipDistribution */
/** @var array $topCustomers */
/** @var int $retentionCount */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Laporan', 'url' => base_url('admin/report')],
        ['label' => 'Pelanggan'],
    ]
]) ?>

<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Laporan Pelanggan</h2>
            <p class="text-sm text-gray-500 mt-1">Analisis pertumbuhan dan retensi pelanggan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/report/export/customer-pdf') ?>"
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 inline-flex items-center gap-2 text-sm font-medium transition-colors">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                PDF
            </a>
            <a href="<?= base_url('admin/report/export/customer-excel') ?>"
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 inline-flex items-center gap-2 text-sm font-medium transition-colors">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Excel
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <?= view('components/stat-card', [
            'title'   => 'Total Pelanggan',
            'value'   => number_format($totalCustomers, 0, ',', '.'),
            'icon'    => 'users',
            'color'   => 'primary',
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Pelanggan Baru (Bulan Ini)',
            'value'   => number_format($newThisMonth, 0, ',', '.'),
            'icon'    => 'user-plus',
            'color'   => 'success',
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Pelanggan Aktif (30 Hari)',
            'value'   => number_format($activeCount, 0, ',', '.'),
            'icon'    => 'activity',
            'color'   => 'info',
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Churn (Tidak Aktif 90 Hari)',
            'value'   => number_format($churnCount, 0, ',', '.'),
            'icon'    => 'user-x',
            'color'   => 'danger',
        ]) ?>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- New Customers Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Pelanggan Baru (6 Bulan Terakhir)</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Membership Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Membership</h3>
            <?php
                $levelLabels = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
                $levelColors = [
                    'bronze'   => 'bg-secondary-light',
                    'silver'   => 'bg-gray-300',
                    'gold'     => 'bg-yellow-400',
                    'platinum' => 'bg-primary',
                ];
                $totalAll = 0;
                foreach ($membershipDistribution as $md) {
                    $totalAll += (int) ($md['count'] ?? 0);
                }
            ?>
            <?php if ($totalAll > 0): ?>
                <div class="space-y-5">
                    <?php foreach ($membershipDistribution as $md): ?>
                        <?php
                            $level = $md['membership_level'] ?? '';
                            $count = (int) ($md['count'] ?? 0);
                            $pct   = $totalAll > 0 ? round(($count / $totalAll) * 100, 1) : 0;
                            $barColor = $levelColors[$level] ?? 'bg-gray-300';
                        ?>
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-medium text-gray-700"><?= $levelLabels[$level] ?? ucfirst($level) ?></span>
                                <span class="text-sm text-gray-500"><?= $count ?> pelanggan (<?= $pct ?>%)</span>
                            </div>
                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="<?= $barColor ?> h-full rounded-full transition-all" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-400 text-center py-8">Belum ada data pelanggan</p>
            <?php endif; ?>

            <!-- Retention Info -->
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Pelanggan Repeat (Beli > 1x)</span>
                    <span class="text-sm font-semibold text-gray-800"><?= number_format($retentionCount, 0, ',', '.') ?> pelanggan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Top 10 Pelanggan</h3>
        </div>
        <?php if (empty($topCustomers)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'users',
                'title'       => 'Belum ada data',
                'description' => 'Belum ada data pelanggan.',
            ]) ?>
        <?php else: ?>
            <?= view('components/table', [
                'headers' => ['No', 'Nama', 'Email', 'Level', 'Total Belanja', 'Jumlah Transaksi'],
                'slot'    => (function () use ($topCustomers, $levelLabels) {
                    $output = '';
                    $no = 1;
                    $badgeColorMap = [
                        'bronze'   => 'bg-secondary-light text-secondary-dark',
                        'silver'   => 'bg-gray-100 text-gray-600',
                        'gold'     => 'bg-yellow-100 text-yellow-700',
                        'platinum' => 'bg-primary-light text-primary-dark',
                    ];
                    foreach ($topCustomers as $cust) {
                        $level = $cust['membership_level'] ?? 'bronze';
                        $colorClass = $badgeColorMap[$level] ?? 'bg-gray-100 text-gray-600';

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($cust['name'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($cust['email'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . '">' . ($levelLabels[$level] ?? ucfirst($level)) . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">Rp ' . number_format((int) ($cust['total_spending'] ?? 0), 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . number_format((int) ($cust['transaction_count'] ?? 0), 0, ',', '.') . '</td>';
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
    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($trendLabels ?? []) ?>,
            datasets: [{
                label: 'Pelanggan Baru',
                data: <?= json_encode($trendData ?? []) ?>,
                backgroundColor: 'rgba(232, 160, 191, 0.6)',
                borderColor: '#E8A0BF',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' pelanggan';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
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
