<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $customers */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Pelanggan'],
    ]
]) ?>

<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Daftar Pelanggan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data pelanggan Anda</p>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 max-w-md">
        <?= $this->include('components/search', [
            'placeholder' => 'Cari nama atau email pelanggan...',
            'action'      => base_url('admin/customers'),
        ]) ?>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($customers)): ?>
            <?= $this->include('components/empty-state', [
                'icon'        => 'users',
                'title'       => 'Belum ada pelanggan',
                'description' => 'Data pelanggan akan muncul di sini setelah ada yang mendaftar.',
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
            <?= $this->include('components/table', [
                'headers' => ['No', 'Nama', 'Email', 'Telepon', 'Level', 'Poin', 'Total Belanja', 'Aksi'],
                'slot'    => (function() use ($customers, $pager, $membershipColors, $membershipLabels) {
                    $output = '';
                    $no = ($pager->getCurrentPage() - 1) * 10 + 1;
                    foreach ($customers as $customer) {
                        $level = $customer['membership_level'] ?? 'bronze';
                        $color = $membershipColors[$level] ?? 'gray';
                        $label = $membershipLabels[$level] ?? ucfirst($level);

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($customer['name'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($customer['email'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($customer['phone'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $colorMap = [
                            'success'  => 'bg-green-100 text-green-700',
                            'warning'  => 'bg-yellow-100 text-yellow-700',
                            'danger'   => 'bg-red-100 text-red-700',
                            'info'     => 'bg-blue-100 text-blue-700',
                            'primary'  => 'bg-primary-light text-primary-dark',
                            'secondary' => 'bg-secondary-light text-secondary-dark',
                            'gray'     => 'bg-gray-100 text-gray-600',
                        ];
                        $colorClass = $colorMap[$color] ?? $colorMap['gray'];
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass . '">' . $label . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . number_format($customer['loyalty_points'] ?? 0, 0, ',', '.') . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">Rp ' . number_format($customer['total_spending'] ?? 0, 0, ',', '.') . '</td>';
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
        <?= $this->include('components/pagination', ['pager' => $pager]) ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
