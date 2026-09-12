<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var string $startDate */
/** @var string $endDate */
/** @var string $period */
/** @var int $totalRevenue */
/** @var int $totalTransactions */
/** @var int $avgTransaction */
/** @var array $breakdown */
/** @var string[] $chartLabels */
/** @var int[] $chartRevenue */
/** @var int[] $chartCounts */
/** @var array $topProducts */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Laporan', 'url' => base_url('admin/report')],
        ['label' => 'Penjualan'],
    ]
]) ?>

<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Laporan Penjualan</h2>
            <p class="text-sm text-gray-500 mt-1">Analisis pendapatan dan tren penjualan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/report/export/sales-pdf') ?>?start_date=<?= esc($startDate) ?>&end_date=<?= esc($endDate) ?>&period=<?= esc($period) ?>"
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 inline-flex items-center gap-2 text-sm font-medium transition-colors">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                PDF
            </a>
            <a href="<?= base_url('admin/report/export/sales-excel') ?>?start_date=<?= esc($startDate) ?>&end_date=<?= esc($endDate) ?>&period=<?= esc($period) ?>"
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 inline-flex items-center gap-2 text-sm font-medium transition-colors">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Excel
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 mb-6">
        <form action="<?= base_url('admin/report/sales') ?>" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= esc($startDate) ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= esc($endDate) ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode</label>
                <select name="period"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm appearance-none cursor-pointer">
                    <option value="daily" <?= ($period ?? 'daily') === 'daily' ? 'selected' : '' ?>>Harian</option>
                    <option value="weekly" <?= ($period ?? '') === 'weekly' ? 'selected' : '' ?>>Mingguan</option>
                    <option value="monthly" <?= ($period ?? '') === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                </select>
            </div>
            <button type="submit" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="search" class="w-4 h-4"></i>
                Filter
            </button>
        </form>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <?= view('components/stat-card', [
            'title'   => 'Total Pendapatan',
            'value'   => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'icon'    => 'dollar-sign',
            'color'   => 'success',
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Total Transaksi',
            'value'   => number_format($totalTransactions, 0, ',', '.') . ' Transaksi',
            'icon'    => 'receipt',
            'color'   => 'info',
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Beli Online (Web)',
            'value'   => number_format($onlineTransactions ?? 0, 0, ',', '.') . ' Transaksi',
            'icon'    => 'globe',
            'color'   => 'primary',
            'footer'  => 'Omset: Rp ' . number_format($onlineRevenue ?? 0, 0, ',', '.'),
        ]) ?>
        <?= view('components/stat-card', [
            'title'   => 'Beli Offline (Toko)',
            'value'   => number_format($offlineTransactions ?? 0, 0, ',', '.') . ' Transaksi',
            'icon'    => 'store',
            'color'   => 'warning',
            'footer'  => 'Omset: Rp ' . number_format($offlineRevenue ?? 0, 0, ',', '.'),
        ]) ?>
    </div>

    <!-- Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pendapatan</h3>
        <div class="relative" style="height: 350px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Breakdown Table -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Breakdown Penjualan (Online &amp; Offline)</h3>
                <p class="text-xs text-gray-500 mt-0.5">Satu output laporan dengan rincian transaksi online dan offline</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-green-100 text-green-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Beli Online
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-orange-100 text-orange-700 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Beli Offline
                </span>
            </div>
        </div>
        <?php if (empty($breakdown)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'bar-chart',
                'title'       => 'Belum ada data',
                'description' => 'Tidak ada transaksi pada periode yang dipilih.',
            ]) ?>
        <?php else: ?>
            <?php
                $periodLabel = match ($period ?? 'daily') {
                    'weekly'  => 'Minggu',
                    'monthly' => 'Bulan',
                    default   => 'Tanggal',
                };
            ?>
            <?= view('components/table', [
                'headers' => [$periodLabel, 'Total Transaksi', 'Beli Online', 'Beli Offline', 'Total Pendapatan', 'Rata-rata'],
                'slot'    => (function () use ($breakdown, $period) {
                    $output = '';
                    foreach ($breakdown as $row) {
                        $label = $row['period_label'] ?? '';
                        if (($period ?? 'daily') === 'daily' && $label) {
                            $label = date('d M Y', strtotime($label));
                        }
                        $onlineCount  = (int) ($row['online_count'] ?? 0);
                        $onlineRev    = (int) ($row['online_revenue'] ?? 0);
                        $offlineCount = (int) ($row['offline_count'] ?? 0);
                        $offlineRev   = (int) ($row['offline_revenue'] ?? 0);

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($label) . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-700 font-semibold">' . number_format((int) ($row['transaction_count'] ?? 0), 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">';
                        $output .= '<i data-lucide="globe" class="w-3 h-3"></i> ' . $onlineCount . ' transaksi';
                        $output .= '</span>';
                        $output .= '<span class="block text-[11px] text-gray-500 mt-0.5">Rp ' . number_format($onlineRev, 0, ',', '.') . '</span>';
                        $output .= '</td>';

                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700">';
                        $output .= '<i data-lucide="store" class="w-3 h-3"></i> ' . $offlineCount . ' transaksi';
                        $output .= '</span>';
                        $output .= '<span class="block text-[11px] text-gray-500 mt-0.5">Rp ' . number_format($offlineRev, 0, ',', '.') . '</span>';
                        $output .= '</td>';

                        $output .= '<td class="py-3 px-4 font-bold text-gray-900">Rp ' . number_format((int) ($row['period_revenue'] ?? 0), 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">Rp ' . number_format((int) ($row['period_avg'] ?? 0), 0, ',', '.') . '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Detail Transaksi Penjualan Terakhir dengan Penanda Online vs Offline -->
    <?php if (!empty($recentTransactions)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Transaksi (Status Beli Online / Offline)</h3>
            <span class="text-xs text-gray-500"><?= count($recentTransactions) ?> Transaksi ditampilkan</span>
        </div>
        <?= view('components/table', [
            'headers' => ['No', 'Kode Transaksi', 'Tanggal', 'Pelanggan', 'Tipe Penjualan', 'Nominal', 'Status'],
            'slot'    => (function () use ($recentTransactions) {
                $output = '';
                $no = 1;
                foreach ($recentTransactions as $tx) {
                    $isOffline = ($tx['payment_method'] ?? '') === 'offline';
                    $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                    $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                    $output .= '<td class="py-3 px-4 font-mono font-medium text-primary text-xs">' . esc($tx['transaction_code']) . '</td>';
                    $output .= '<td class="py-3 px-4 text-gray-600 text-xs">' . date('d M Y H:i', strtotime($tx['transaction_date'])) . '</td>';
                    $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($tx['customer_name'] ?? 'Pelanggan Offline') . '</td>';
                    $output .= '<td class="py-3 px-4">';
                    if ($isOffline) {
                        $output .= '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200">';
                        $output .= '<i data-lucide="store" class="w-3 h-3"></i> Beli Offline';
                        $output .= '</span>';
                    } else {
                        $output .= '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">';
                        $output .= '<i data-lucide="globe" class="w-3 h-3"></i> Beli Online';
                        $output .= '</span>';
                    }
                    $output .= '</td>';
                    $output .= '<td class="py-3 px-4 font-bold text-gray-900">Rp ' . number_format((int) $tx['final_amount'], 0, ',', '.') . '</td>';
                    $output .= '<td class="py-3 px-4"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 uppercase tracking-wider">' . esc($tx['status']) . '</span></td>';
                    $output .= '</tr>';
                }
                return $output;
            })(),
        ]) ?>
    </div>
    <?php endif; ?>

    <!-- Top Selling Products -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Produk Terlaris</h3>
        </div>
        <?php if (empty($topProducts)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'package',
                'title'       => 'Belum ada data',
                'description' => 'Tidak ada produk terjual pada periode yang dipilih.',
            ]) ?>
        <?php else: ?>
            <?= view('components/table', [
                'headers' => ['No', 'Produk', 'Qty Terjual', 'Total Pendapatan'],
                'slot'    => (function () use ($topProducts) {
                    $output = '';
                    $no = 1;
                    foreach ($topProducts as $prod) {
                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($prod['product_name'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . number_format((int) ($prod['total_qty'] ?? 0), 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">Rp ' . number_format((int) ($prod['total_revenue'] ?? 0), 0, ',', '.') . '</td>';
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
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels ?? []) ?>,
            datasets: [
                {
                    label: 'Pendapatan',
                    data: <?= json_encode($chartRevenue ?? []) ?>,
                    backgroundColor: 'rgba(232, 160, 191, 0.6)',
                    borderColor: '#E8A0BF',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y',
                },
                {
                    label: 'Jumlah Transaksi',
                    data: <?= json_encode($chartCounts ?? []) ?>,
                    backgroundColor: 'rgba(125, 184, 143, 0.6)',
                    borderColor: '#7DB88F',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { family: 'Poppins' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Pendapatan') {
                                return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        font: { family: 'Poppins', size: 11 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    ticks: {
                        font: { family: 'Poppins', size: 11 }
                    }
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
