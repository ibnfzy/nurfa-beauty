<?php
/**
 * Breadcrumb component
 * Usage: $this->include('components/breadcrumb', ['items' => [['label' => 'Home', 'url' => '/'], ['label' => 'Produk']])
 */
?>
<nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="<?= base_url() ?>" class="hover:text-primary transition-colors">
        <i data-lucide="home" class="w-4 h-4"></i>
    </a>
    <?php foreach (($items ?? []) as $i => $item): ?>
        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
        <?php if (isset($item['url'])): ?>
            <a href="<?= $item['url'] ?>" class="hover:text-primary transition-colors"><?= $item['label'] ?></a>
        <?php else: ?>
            <span class="text-gray-800 font-medium"><?= $item['label'] ?></span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
