<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $customers */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
/** @var string|null $statusFilter */
/** @var int $totalCustomers */
/** @var int $newThisMonth */
/** @var int $activeCount */
/** @var int $inactiveCount */
/** @var int $neverBoughtCount */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Pelanggan & CRM (Tahap GET)'],
    ]
]) ?>

<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Pelanggan & CRM (Tahap GET)</h2>
            <p class="text-sm text-gray-500 mt-1">Fokus akuisisi pelanggan baru (GET), pantau keaktifan, dan perilaku belanja bulanan</p>
        </div>
    </div>

    <!-- Metrik CRM Tahap GET & Keaktifan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Pelanggan -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-primary-light/30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pelanggan</span>
                <span class="p-2 bg-primary-light/40 text-primary-dark rounded-lg">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-gray-800"><?= number_format($totalCustomers ?? 0, 0, ',', '.') ?></div>
            <p class="text-xs text-gray-400 mt-1">Basis data seluruh pelanggan</p>
        </div>

        <!-- Pelanggan Baru Bulan Ini (Tahap GET) -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-amber-200 bg-amber-50/30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Baru Bulan Ini (GET)</span>
                <span class="p-2 bg-amber-100 text-amber-700 rounded-lg">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-900"><?= number_format($newThisMonth ?? 0, 0, ',', '.') ?></div>
            <p class="text-xs text-amber-700 mt-1">Akuisisi pelanggan baru</p>
        </div>

        <!-- Pelanggan Aktif -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-green-200 bg-green-50/20">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-green-700 uppercase tracking-wider">Pelanggan Aktif</span>
                <span class="p-2 bg-green-100 text-green-700 rounded-lg">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-green-800"><?= number_format($activeCount ?? 0, 0, ',', '.') ?></div>
            <p class="text-xs text-green-600 mt-1">Belanja dalam 60 hari</p>
        </div>

        <!-- Belum Belanja (Target GET) -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-blue-200 bg-blue-50/20">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-blue-700 uppercase tracking-wider">Belum Belanja</span>
                <span class="p-2 bg-blue-100 text-blue-700 rounded-lg">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-blue-800"><?= number_format($neverBoughtCount ?? 0, 0, ',', '.') ?></div>
            <p class="text-xs text-blue-600 mt-1">Perlu didorong order 1st</p>
        </div>

        <!-- Tidak Aktif -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 bg-gray-50/50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tidak Aktif</span>
                <span class="p-2 bg-gray-100 text-gray-600 rounded-lg">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-gray-700"><?= number_format($inactiveCount ?? 0, 0, ',', '.') ?></div>
            <p class="text-xs text-gray-400 mt-1">> 60 hari tanpa transaksi</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <!-- Filter Tabs Keaktifan -->
        <div class="flex flex-wrap items-center gap-1.5 bg-gray-100/80 p-1 rounded-xl border border-gray-200">
            <?php
                $activeFilter = $statusFilter ?: 'all';
                $tabs = [
                    'all'      => ['label' => 'Semua Pelanggan', 'count' => $totalCustomers],
                    'active'   => ['label' => 'Aktif', 'count' => $activeCount],
                    'new'      => ['label' => 'Belum Belanja', 'count' => $neverBoughtCount],
                    'inactive' => ['label' => 'Tidak Aktif', 'count' => $inactiveCount],
                ];
            ?>
            <?php foreach ($tabs as $key => $tab): ?>
                <a href="<?= base_url('admin/customers' . ($key !== 'all' ? '?status=' . $key : '') . ($search ? ($key !== 'all' ? '&q=' : '?q=') . urlencode($search) : '')) ?>" 
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all <?= $activeFilter === $key ? 'bg-white text-primary shadow-xs font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-white/50' ?>">
                    <?= $tab['label'] ?>
                    <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] <?= $activeFilter === $key ? 'bg-primary-light text-primary-dark' : 'bg-gray-200 text-gray-600' ?>">
                        <?= $tab['count'] ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Search -->
        <div class="w-full md:w-72">
            <form action="<?= base_url('admin/customers') ?>" method="GET" class="relative">
                <?php if ($statusFilter): ?>
                    <input type="hidden" name="status" value="<?= esc($statusFilter) ?>">
                <?php endif; ?>
                <input type="text" name="q" value="<?= esc($search ?? '') ?>" placeholder="Cari nama, email, hp..." 
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($customers)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'users',
                'title'       => 'Belum ada data pelanggan',
                'description' => 'Tidak ditemukan data pelanggan yang cocok dengan filter atau pencarian ini.',
            ]) ?>
        <?php else: ?>
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
            ?>
            <?= view('components/table', [
                'headers' => ['No', 'Nama & Kontak', 'Status Keaktifan', 'Beli Bulan Ini', 'Level & Poin', 'Total Belanja', 'Aksi'],
                'slot'    => (function() use ($customers, $pager, $membershipColors, $membershipLabels) {
                    $output = '';
                    $no = ($pager->getCurrentPage() - 1) * 10 + 1;
                    $colorMap = [
                        'success'   => 'bg-green-100 text-green-700 border-green-200',
                        'warning'   => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'danger'    => 'bg-red-100 text-red-700 border-red-200',
                        'info'      => 'bg-blue-100 text-blue-700 border-blue-200',
                        'primary'   => 'bg-primary-light text-primary-dark border-primary/20',
                        'secondary' => 'bg-secondary-light text-secondary-dark border-secondary/20',
                        'gray'      => 'bg-gray-100 text-gray-600 border-gray-200',
                    ];

                    foreach ($customers as $customer) {
                        $level = $customer['membership_level'] ?? 'bronze';
                        $color = $membershipColors[$level] ?? 'gray';
                        $label = $membershipLabels[$level] ?? ucfirst($level);
                        $colorClass = $colorMap[$color] ?? $colorMap['gray'];

                        // Status Keaktifan
                        $actColor = $customer['activity_color'] ?? 'gray';
                        $actColorClass = $colorMap[$actColor] ?? $colorMap['gray'];
                        $actLabel = $customer['activity_label'] ?? 'Tidak Diketahui';

                        // Promo Perilaku Bulanan
                        $monthlyQty = (int) ($customer['monthly_qty'] ?? 0);
                        $reward = $customer['behavior_reward'] ?? null;
                        $hasReward = !empty($reward['is_qualified']);

                        $output .= '<tr class="hover:bg-cream/40 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500 text-xs">' . $no++ . '</td>';
                        
                        // Nama & Kontak
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<div class="font-semibold text-gray-800 text-sm">' . esc($customer['name'] ?? '-') . '</div>';
                        $output .= '<div class="text-xs text-gray-400 mt-0.5">' . esc($customer['email'] ?? '-') . ' &bull; ' . esc($customer['phone'] ?? '-') . '</div>';
                        $output .= '</td>';

                        // Status Keaktifan
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ' . $actColorClass . '">';
                        $output .= $actLabel;
                        $output .= '</span>';
                        if (!empty($customer['last_purchase_date'])) {
                            $output .= '<div class="text-[11px] text-gray-400 mt-1">Terakhir: ' . date('d/m/Y', strtotime($customer['last_purchase_date'])) . '</div>';
                        } else {
                            $output .= '<div class="text-[11px] text-blue-500 font-medium mt-1">Belum pernah order</div>';
                        }
                        $output .= '</td>';

                        // Beli Bulan Ini (Behavioral Promo)
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<div class="text-xs font-semibold text-gray-800">' . $monthlyQty . ' produk</div>';
                        if ($hasReward) {
                            $rewardTier = $reward['active_reward']['tier_name'] ?? 'Promo';
                            $output .= '<span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded mt-0.5">';
                            $output .= '<i data-lucide="sparkles" class="w-3 h-3"></i> ' . esc($rewardTier);
                            $output .= '</span>';
                        } else {
                            $needed = $reward['next_tier']['products_needed'] ?? 3;
                            $output .= '<div class="text-[10px] text-gray-400 mt-0.5">' . $needed . ' lagi dpt promo</div>';
                        }
                        $output .= '</td>';

                        // Level & Poin
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium ' . $colorClass . '">' . $label . '</span>';
                        $output .= '<div class="text-xs text-gray-500 mt-0.5">' . number_format($customer['loyalty_points'] ?? 0, 0, ',', '.') . ' poin</div>';
                        $output .= '</td>';

                        // Total Belanja
                        $output .= '<td class="py-3 px-4 text-xs font-semibold text-gray-800">Rp ' . number_format($customer['total_spending'] ?? 0, 0, ',', '.') . '</td>';

                        // Aksi
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<a href="' . base_url('admin/customers/' . $customer['id']) . '" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-light text-primary-dark rounded-lg hover:bg-primary/20 transition-colors text-xs font-medium">';
                        $output .= '<i data-lucide="eye" class="w-3.5 h-3.5"></i>';
                        $output .= 'Detail';
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
        <?= view('components/pagination', ['pager' => $pager]) ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
