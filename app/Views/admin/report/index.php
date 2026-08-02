<?= $this->extend('layouts/admin') ?>
<?php /** @var string $pageTitle */ ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Laporan'],
    ]
]) ?>

<div>
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800">Laporan</h2>
        <p class="text-sm text-gray-500 mt-1">Pilih jenis laporan yang ingin Anda lihat</p>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Laporan Penjualan -->
        <a href="<?= base_url('admin/report/sales') ?>" class="group block bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 hover:shadow-md hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-300 group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Laporan Penjualan</h3>
            <p class="text-sm text-gray-500 mb-4">Analisis pendapatan, tren penjualan, dan produk terlaris dalam periode tertentu.</p>
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary-light text-primary-dark rounded-lg text-sm font-medium group-hover:bg-primary group-hover:text-white transition-colors">
                <i data-lucide="eye" class="w-4 h-4"></i>
                Lihat Laporan
            </span>
        </a>

        <!-- Laporan Pelanggan -->
        <a href="<?= base_url('admin/report/customer') ?>" class="group block bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 hover:shadow-md hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-300 group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Laporan Pelanggan</h3>
            <p class="text-sm text-gray-500 mb-4">Pantau pertumbuhan pelanggan, distribusi membership, dan retensi pelanggan.</p>
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary-light text-primary-dark rounded-lg text-sm font-medium group-hover:bg-primary group-hover:text-white transition-colors">
                <i data-lucide="eye" class="w-4 h-4"></i>
                Lihat Laporan
            </span>
        </a>

        <!-- Laporan Loyalitas -->
        <a href="<?= base_url('admin/report/loyalty') ?>" class="group block bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 hover:shadow-md hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                    <i data-lucide="award" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-300 group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Laporan Loyalitas</h3>
            <p class="text-sm text-gray-500 mb-4">Analisis poin loyalitas, distribusi membership, dan penggunaan voucher.</p>
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary-light text-primary-dark rounded-lg text-sm font-medium group-hover:bg-primary group-hover:text-white transition-colors">
                <i data-lucide="eye" class="w-4 h-4"></i>
                Lihat Laporan
            </span>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
