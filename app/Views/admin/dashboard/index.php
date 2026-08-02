<?= $this->extend('layouts/admin') ?>
<?php
/**
 * @var string $pageTitle
 * @var float|int $totalPenjualanBulanIni
 * @var int $pelangganBaruBulanIni
 * @var int $stokMenipis
 * @var int $transaksi7Hari
 * @var array $chartLabels
 * @var array $chartData
 * @var array $transaksiTerakhir
 * @var array $produkTerlaris
 * @var array $distribusiMembership
 */
?>

<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?= view('components/stat-card', [
            'title'     => 'Total Penjualan Bulan Ini',
            'value'     => 'Rp ' . number_format($totalPenjualanBulanIni ?? 0, 0, ',', '.'),
            'icon'      => 'wallet',
            'color'     => 'primary',
            'subtitle'  => date('F Y'),
        ]) ?>

        <?= view('components/stat-card', [
            'title'     => 'Pelanggan Baru',
            'value'     => $pelangganBaruBulanIni ?? 0,
            'icon'      => 'user-plus',
            'color'     => 'success',
            'subtitle'  => 'Bulan ini',
        ]) ?>

        <?= view('components/stat-card', [
            'title'     => 'Stok Menipis',
            'value'     => $stokMenipis ?? 0,
            'icon'      => 'alert-triangle',
            'color'     => 'warning',
            'subtitle'  => 'Produk stok < 10',
        ]) ?>

        <?= view('components/stat-card', [
            'title'     => 'Transaksi 7 Hari',
            'value'     => $transaksi7Hari ?? 0,
            'icon'      => 'shopping-bag',
            'color'     => 'info',
            'subtitle'  => 'Terakhir',
        ]) ?>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Penjualan Harian (30 Hari Terakhir)</h3>
        <div x-data="{
            chart: null,
            labels: <?= json_encode($chartLabels ?? []) ?>,
            data: <?= json_encode($chartData ?? []) ?>,
            init() {
                this.$nextTick(() => {
                    const ctx = this.$refs.salesChart.getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: 'Penjualan (Rp)',
                                data: this.data,
                                borderColor: '#E8A0BF',
                                backgroundColor: 'rgba(232, 160, 191, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#E8A0BF',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                                    },
                                    grid: { color: 'rgba(0,0,0,0.05)' }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                });
            }
        }" class="relative" style="height: 300px;">
            <canvas x-ref="salesChart"></canvas>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Transaksi Terakhir -->
        <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Transaksi Terakhir</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-2 text-gray-500 font-medium">No</th>
                            <th class="text-left py-3 px-2 text-gray-500 font-medium">Kode</th>
                            <th class="text-left py-3 px-2 text-gray-500 font-medium">Pelanggan</th>
                            <th class="text-left py-3 px-2 text-gray-500 font-medium">Tanggal</th>
                            <th class="text-left py-3 px-2 text-gray-500 font-medium">Total</th>
                            <th class="text-left py-3 px-2 text-gray-500 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transaksiTerakhir)): ?>
                            <?php foreach ($transaksiTerakhir as $i => $trx): ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                    <td class="py-3 px-2 text-gray-600"><?= $i + 1 ?></td>
                                    <td class="py-3 px-2 font-medium text-gray-800"><?= esc($trx['transaction_code'] ?? '') ?></td>
                                    <td class="py-3 px-2 text-gray-600"><?= esc($trx['customer_name'] ?? '-') ?></td>
                                    <td class="py-3 px-2 text-gray-500"><?= date('d M Y', strtotime($trx['transaction_date'] ?? 'now')) ?></td>
                                    <td class="py-3 px-2 font-medium text-gray-800">Rp <?= number_format($trx['final_amount'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="py-3 px-2">
                                        <?php
                                        $status = $trx['status'] ?? '';
                                        $statusColors = [
                                            'completed'              => 'bg-green-100 text-green-700',
                                            'processing'             => 'bg-blue-100 text-blue-700',
                                            'shipped'                => 'bg-blue-100 text-blue-700',
                                            'pending_payment'        => 'bg-yellow-100 text-yellow-700',
                                            'pending_verification'   => 'bg-yellow-100 text-yellow-700',
                                            'cancelled'              => 'bg-red-100 text-red-700',
                                            'payment_rejected'       => 'bg-red-100 text-red-700',
                                        ];
                                        $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-700';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colorClass ?>">
                                            <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">Belum ada transaksi</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Produk Terlaris + Distribusi Membership -->
        <div class="space-y-6">

            <!-- Produk Terlaris -->
            <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Produk Terlaris</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-2 text-gray-500 font-medium">No</th>
                                <th class="text-left py-3 px-2 text-gray-500 font-medium">Produk</th>
                                <th class="text-left py-3 px-2 text-gray-500 font-medium">Qty Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($produkTerlaris)): ?>
                                <?php foreach ($produkTerlaris as $i => $prod): ?>
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                        <td class="py-3 px-2 text-gray-600"><?= $i + 1 ?></td>
                                        <td class="py-3 px-2 font-medium text-gray-800"><?= esc($prod['product_name'] ?? '') ?></td>
                                        <td class="py-3 px-2 text-gray-600"><?= $prod['total_qty'] ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-gray-400">Belum ada data penjualan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Distribusi Membership -->
            <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Distribusi Membership</h3>
                <div class="space-y-3">
                    <?php
                    $membershipColors = [
                        'bronze'   => 'bg-amber-100 text-amber-700',
                        'silver'   => 'bg-gray-200 text-gray-700',
                        'gold'     => 'bg-yellow-100 text-yellow-700',
                        'platinum' => 'bg-purple-100 text-purple-700',
                    ];
                    $totalMembers = 0;
                    foreach (($distribusiMembership ?? []) as $m) {
                        $totalMembers += (int) ($m['total'] ?? 0);
                    }
                    ?>
                    <?php if (!empty($distribusiMembership)): ?>
                        <?php foreach ($distribusiMembership as $m): ?>
                            <?php
                            $level     = $m['membership_level'] ?? 'unknown';
                            $count     = (int) ($m['total'] ?? 0);
                            $pct       = $totalMembers > 0 ? round(($count / $totalMembers) * 100) : 0;
                            $colorCls  = $membershipColors[$level] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colorCls ?>">
                                        <?= ucfirst($level) ?>
                                    </span>
                                    <span class="text-sm text-gray-600"><?= $count ?> pelanggan</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full transition-all" style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada data membership</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?= $this->endSection() ?>