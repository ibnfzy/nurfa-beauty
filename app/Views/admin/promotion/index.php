<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $promotions */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
/** @var string|null $typeFilter */
/** @var string|null $segmentFilter */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Promosi'],
    ]
]) ?>

<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Promosi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola promosi dan diskon untuk pelanggan</p>
        </div>
        <a href="<?= base_url('admin/promotions/create') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Promosi
        </a>
    </div>

    <!-- Filter Tabs - Type -->
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="<?= base_url('admin/promotions' . (($segmentFilter ?? '') ? '?target_segment=' . $segmentFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= empty($typeFilter) ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="layers" class="w-4 h-4"></i>
            Semua
        </a>
        <a href="<?= base_url('admin/promotions?type=discount' . (($segmentFilter ?? '') ? '&target_segment=' . $segmentFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($typeFilter ?? '') === 'discount' ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="percent" class="w-4 h-4"></i>
            Diskon
        </a>
        <a href="<?= base_url('admin/promotions?type=voucher' . (($segmentFilter ?? '') ? '&target_segment=' . $segmentFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($typeFilter ?? '') === 'voucher' ? 'bg-info text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="ticket" class="w-4 h-4"></i>
            Voucher
        </a>
        <a href="<?= base_url('admin/promotions?type=flash_sale' . (($segmentFilter ?? '') ? '&target_segment=' . $segmentFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($typeFilter ?? '') === 'flash_sale' ? 'bg-danger text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="zap" class="w-4 h-4"></i>
            Flash Sale
        </a>
        <a href="<?= base_url('admin/promotions?type=birthday' . (($segmentFilter ?? '') ? '&target_segment=' . $segmentFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($typeFilter ?? '') === 'birthday' ? 'bg-warning text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="cake" class="w-4 h-4"></i>
            Birthday
        </a>
    </div>

    <!-- Filter Tabs - Segment -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="<?= base_url('admin/promotions' . (($typeFilter ?? '') ? '?type=' . $typeFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors <?= empty($segmentFilter) ? 'bg-primary-light text-primary-dark border border-primary/20' : 'bg-white text-gray-500 border border-gray-200 hover:bg-cream' ?>">
            Semua Segment
        </a>
        <a href="<?= base_url('admin/promotions?target_segment=new' . (($typeFilter ?? '') ? '&type=' . $typeFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($segmentFilter ?? '') === 'new' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-white text-gray-500 border border-gray-200 hover:bg-cream' ?>">
            Pelanggan Baru
        </a>
        <a href="<?= base_url('admin/promotions?target_segment=loyal' . (($typeFilter ?? '') ? '&type=' . $typeFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($segmentFilter ?? '') === 'loyal' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-white text-gray-500 border border-gray-200 hover:bg-cream' ?>">
            Loyal
        </a>
        <a href="<?= base_url('admin/promotions?target_segment=vip' . (($typeFilter ?? '') ? '&type=' . $typeFilter : '')) ?>"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($segmentFilter ?? '') === 'vip' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-white text-gray-500 border border-gray-200 hover:bg-cream' ?>">
            VIP
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6 max-w-md">
        <?= $this->include('components/search', [
            'placeholder' => 'Cari nama promosi...',
            'action'      => base_url('admin/promotions') . '?' . (($typeFilter ?? '') ? 'type=' . $typeFilter . '&' : '') . (($segmentFilter ?? '') ? 'target_segment=' . $segmentFilter . '&' : ''),
        ]) ?>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($promotions)): ?>
            <?= $this->include('components/empty-state', [
                'icon'        => 'megaphone',
                'title'       => 'Belum ada promosi',
                'description' => 'Mulai buat promosi untuk menarik pelanggan Anda.',
                'actionText'  => 'Tambah Promosi',
                'actionUrl'   => base_url('admin/promotions/create'),
            ]) ?>
        <?php else: ?>
            <?php
            $typeColors = [
                'discount'   => 'primary',
                'voucher'    => 'info',
                'flash_sale' => 'danger',
                'birthday'   => 'warning',
            ];
            $typeLabels = [
                'discount'   => 'Diskon',
                'voucher'    => 'Voucher',
                'flash_sale' => 'Flash Sale',
                'birthday'   => 'Birthday',
            ];
            $segmentColors = [
                'all'   => 'gray',
                'new'   => 'info',
                'loyal' => 'success',
                'vip'   => 'warning',
            ];
            $segmentLabels = [
                'all'   => 'Semua',
                'new'   => 'Pelanggan Baru',
                'loyal' => 'Loyal',
                'vip'   => 'VIP',
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
                'headers' => ['No', 'Nama', 'Tipe', 'Diskon', 'Segment', 'Periode', 'Status', 'Jumlah Voucher', 'Aksi'],
                'slot'    => (function () use ($promotions, $pager, $typeColors, $typeLabels, $segmentColors, $segmentLabels, $badgeColorMap) {
                    $output = '';
                    $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1;
                    foreach ($promotions as $promo) {
                        $tc = $typeColors[$promo['type'] ?? ''] ?? 'gray';
                        $tl = $typeLabels[$promo['type'] ?? ''] ?? ucfirst($promo['type'] ?? '');
                        $tcc = $badgeColorMap[$tc] ?? $badgeColorMap['gray'];

                        $sc = $segmentColors[$promo['target_segment'] ?? ''] ?? 'gray';
                        $sl = $segmentLabels[$promo['target_segment'] ?? ''] ?? ucfirst($promo['target_segment'] ?? '');
                        $scc = $badgeColorMap[$sc] ?? $badgeColorMap['gray'];

                        $isActive = ($promo['is_active'] ?? 0) == 1;

                        // Format diskon
                        if (($promo['discount_type'] ?? '') === 'percentage') {
                            $diskonText = ($promo['discount_value'] ?? 0) . '%';
                        } else {
                            $diskonText = 'Rp ' . number_format($promo['discount_value'] ?? 0, 0, ',', '.');
                        }

                        // Format periode
                        $startDate = ($promo['start_date'] ?? '') ? date('d M Y', strtotime($promo['start_date'])) : '-';
                        $endDate = ($promo['end_date'] ?? '') ? date('d M Y', strtotime($promo['end_date'])) : '-';
                        $periode = $startDate . ' - ' . $endDate;

                        // Jumlah voucher
                        $voucherCount = $promo['voucher_count'] ?? 0;

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';

                        // No
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';

                        // Nama
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($promo['name'] ?? '-') . '</td>';

                        // Tipe
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $tcc . '">' . $tl . '</span>';
                        $output .= '</td>';

                        // Diskon
                        $output .= '<td class="py-3 px-4 font-medium text-gray-700">' . $diskonText . '</td>';

                        // Segment
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $scc . '">' . $sl . '</span>';
                        $output .= '</td>';

                        // Periode
                        $output .= '<td class="py-3 px-4 text-gray-600 text-sm">' . $periode . '</td>';

                        // Status
                        $output .= '<td class="py-3 px-4">';
                        if ($isActive) {
                            $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>';
                        } else {
                            $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>';
                        }
                        $output .= '</td>';

                        // Jumlah Voucher
                        $output .= '<td class="py-3 px-4 text-center text-gray-600">' . $voucherCount . '</td>';

                        // Aksi
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<div class="flex items-center gap-2">';
                        $output .= '<a href="' . base_url('admin/promotions/edit/' . $promo['id']) . '" class="p-1.5 rounded-lg text-info hover:bg-blue-50 transition-colors" title="Edit">';
                        $output .= '<i data-lucide="pencil" class="w-4 h-4"></i>';
                        $output .= '</a>';
                        $output .= '</div>';
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
