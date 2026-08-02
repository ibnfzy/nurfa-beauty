<?php
/**
 * Empty State component
 * Usage: $this->include('components/empty-state', ['icon' => 'package', 'title' => 'Belum ada produk', 'description' => '...', 'actionText' => 'Tambah', 'actionUrl' => '/admin/products/create'])
 */
?>
<div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-16 h-16 rounded-full bg-primary-light flex items-center justify-center mb-4">
        <i data-lucide="<?= $icon ?? 'inbox' ?>" class="w-8 h-8 text-primary"></i>
    </div>
    <h3 class="text-lg font-semibold text-gray-800 mb-2"><?= $title ?? 'Belum ada data' ?></h3>
    <p class="text-sm text-gray-500 max-w-sm mb-6"><?= $description ?? '' ?></p>
    <?php if (isset($actionUrl)): ?>
        <a href="<?= $actionUrl ?>" class="btn-primary">
            <?= $actionText ?? 'Tambah' ?>
        </a>
    <?php endif; ?>
</div>
