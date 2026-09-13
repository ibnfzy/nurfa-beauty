<?php
// pendingPaymentCount passed from BaseController
$pendingPaymentCount = $pendingPaymentCount ?? 0;
?>

<!-- Sidebar Admin -->
<aside class="w-64 bg-white shadow-sm min-h-screen border-r border-gray-100" x-data="{ collapsed: false }">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-100">
        <a href="<?= base_url('admin') ?>" class="flex items-center gap-2">
            <span class="text-xl font-playfair font-bold text-primary">Nurfa Beauty</span>
            <span class="text-xs bg-primary-light text-primary-dark px-2 py-0.5 rounded-full">Admin</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1">
        <a href="<?= base_url('admin') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= uri_string() === 'admin' ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            Dashboard
        </a>
        <a href="<?= base_url('admin/categories') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/categories') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="tags" class="w-5 h-5"></i>
            Kategori
        </a>
        <a href="<?= base_url('admin/products') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/products') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="package" class="w-5 h-5"></i>
            Produk
        </a>
        <a href="<?= base_url('admin/customers') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/customers') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="users" class="w-5 h-5"></i>
            Pelanggan
        </a>
        <a href="<?= base_url('admin/transactions') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/transactions') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="receipt" class="w-5 h-5"></i>
            Transaksi
        </a>
        <a href="<?= base_url('admin/payments') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/payments') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="credit-card" class="w-5 h-5"></i>
            <span class="flex-1">Verifikasi Bayar</span>
            <?php if ($pendingPaymentCount == 0): ?>
                <span class="bg-danger text-white text-xs rounded-full min-w-5 h-5 px-1 flex items-center justify-center font-semibold">
                    0
                </span>
            <?php endif; ?>
            <?php if ($pendingPaymentCount > 0): ?>
                <span class="bg-danger text-white text-xs rounded-full min-w-5 h-5 px-1 flex items-center justify-center font-semibold">
                    <?= $pendingPaymentCount > 99 ? '99+' : $pendingPaymentCount ?>
                </span>
            <?php endif; ?>
        </a>
        <a href="<?= base_url('admin/promotions') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/promotions') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="megaphone" class="w-5 h-5"></i>
            Promosi
        </a>
        <a href="<?= base_url('admin/banks') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/banks') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="landmark" class="w-5 h-5"></i>
            Rekening Bank
        </a>
        <a href="<?= base_url('admin/notifications') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/notifications') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="bell" class="w-5 h-5"></i>
            Notifikasi
            <?php if ($unreadNotificationCount == 0): ?>
                <span class="bg-danger text-white text-xs rounded-full min-w-5 h-5 px-1 flex items-center justify-center font-semibold">
                    0
                </span>
            <?php endif; ?>
            <?php if ($unreadNotificationCount > 0): ?>
                <span class="bg-danger text-white text-xs rounded-full min-w-5 h-5 px-1 flex items-center justify-center font-semibold">
                    <?= $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount ?>
                </span>
            <?php endif; ?>
        </a>
        <a href="<?= base_url('admin/report') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                  <?= strpos(uri_string(), 'admin/report') === 0 ? 'bg-primary-light text-primary-dark' : 'text-gray-600 hover:bg-cream hover:text-primary' ?>">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            Laporan
        </a>

        <hr class="my-3 border-gray-100">

        <a href="<?= base_url('auth/logout') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-danger hover:bg-red-50">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            Logout
        </a>
    </nav>
</aside>
