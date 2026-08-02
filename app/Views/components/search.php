<?php
/**
 * Search component
 * Usage: view('components/search', ['placeholder' => 'Cari produk...', 'action' => '/products'])
 */
?>
<form action="<?= $action ?? '' ?>" method="GET" class="relative">
    <div class="relative">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
        <input type="text" name="q" value="<?= esc($_GET['q'] ?? '') ?>"
               placeholder="<?= $placeholder ?? 'Cari...' ?>"
               class="input-field pl-10 pr-10">
        <?php if (!empty($_GET['q'])): ?>
            <a href="<?= $action ?? current_url() ?>" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </a>
        <?php endif; ?>
    </div>
</form>
