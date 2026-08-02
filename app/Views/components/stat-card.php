<?php
/**
 * Stat Card component
 * Usage: view('components/stat-card', ['title' => 'Total Penjualan', 'value' => 'Rp 1.000.000', 'icon' => 'dollar-sign', 'color' => 'primary'])
 */
$colorMap = [
    'primary'   => 'bg-primary-light text-primary-dark',
    'secondary' => 'bg-secondary-light text-secondary-dark',
    'success'   => 'bg-green-100 text-green-600',
    'warning'   => 'bg-yellow-100 text-yellow-600',
    'danger'    => 'bg-red-100 text-red-600',
    'info'      => 'bg-blue-100 text-blue-600',
];
$iconBg = $colorMap[$color ?? 'primary'] ?? $colorMap['primary'];
?>
<div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 mb-1"><?= $title ?? '' ?></p>
            <p class="text-2xl font-bold text-gray-800"><?= $value ?? '0' ?></p>
            <?php if (isset($subtitle)): ?>
                <p class="text-xs text-gray-400 mt-1"><?= $subtitle ?></p>
            <?php endif; ?>
        </div>
        <div class="w-12 h-12 rounded-xl flex items-center justify-center <?= $iconBg ?>">
            <i data-lucide="<?= $icon ?? 'bar-chart' ?>" class="w-6 h-6"></i>
        </div>
    </div>
</div>
