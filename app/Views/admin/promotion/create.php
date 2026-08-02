<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $customers */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Promosi', 'url' => base_url('admin/promotions')],
        ['label' => 'Tambah'],
    ]
]) ?>

<div x-data="{
    type: '<?= old('type', 'discount') ?>',
    discountType: '<?= old('discount_type', 'percentage') ?>',
    isActive: <?= old('is_active', 1) ? 'true' : 'false' ?>,
    sendNotification: <?= old('send_notification', 0) ? 'true' : 'false' ?>
}">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Tambah Promosi</h2>
            <p class="text-sm text-gray-500 mt-1">Buat promosi baru untuk pelanggan</p>
        </div>
        <a href="<?= base_url('admin/promotions') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    <!-- Validation Errors -->
    <?php if (session('errors')): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-danger mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="text-sm font-semibold text-red-800 mb-1">Terjadi kesalahan:</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form action="<?= base_url('admin/promotions/store') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Informasi Promosi</h3>

                    <div class="space-y-4">
                        <!-- Nama Promosi -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Promosi <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?= old('name') ?>"
                                required
                                maxlength="255"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"
                                placeholder="Masukkan nama promosi">
                        </div>

                        <!-- Tipe -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe <span class="text-danger">*</span>
                            </label>
                            <select
                                id="type"
                                name="type"
                                x-model="type"
                                required
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                                <option value="discount">Diskon</option>
                                <option value="voucher">Voucher</option>
                                <option value="flash_sale">Flash Sale</option>
                                <option value="birthday">Birthday</option>
                            </select>
                        </div>

                        <!-- Tipe Diskon & Nilai Diskon -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipe Diskon <span class="text-danger">*</span>
                                </label>
                                <select
                                    id="discount_type"
                                    name="discount_type"
                                    x-model="discountType"
                                    required
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed">Nominal Tetap (Rp)</option>
                                </select>
                            </div>
                            <div>
                                <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nilai Diskon <span class="text-danger">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium"
                                        x-text="discountType === 'percentage' ? '%' : 'Rp'"></span>
                                    <input
                                        type="number"
                                        id="discount_value"
                                        name="discount_value"
                                        value="<?= old('discount_value') ?>"
                                        required
                                        min="0"
                                        step="any"
                                        class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- Minimum Pembelian -->
                        <div>
                            <label for="min_purchase" class="block text-sm font-medium text-gray-700 mb-1">
                                Minimum Pembelian
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                                <input
                                    type="number"
                                    id="min_purchase"
                                    name="min_purchase"
                                    value="<?= old('min_purchase', 0) ?>"
                                    min="0"
                                    class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"
                                    placeholder="0">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Kosongkan atau 0 jika tidak ada minimum</p>
                        </div>

                        <!-- Tanggal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    value="<?= old('start_date') ?>"
                                    required
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Berakhir <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    value="<?= old('end_date') ?>"
                                    required
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            </div>
                        </div>

                        <!-- Voucher Fields (conditional) -->
                        <div x-show="type === 'voucher'" x-transition class="space-y-4 border-t border-gray-100 pt-4" style="display: none;">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="voucher_count" class="block text-sm font-medium text-gray-700 mb-1">
                                        Jumlah Voucher <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="voucher_count"
                                        name="voucher_count"
                                        value="<?= old('voucher_count') ?>"
                                        min="1"
                                        max="100"
                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"
                                        placeholder="1">
                                    <p class="text-xs text-gray-400 mt-1">Maksimal 100 voucher</p>
                                </div>
                                <div>
                                    <label for="voucher_validity_days" class="block text-sm font-medium text-gray-700 mb-1">
                                        Masa Berlaku Voucher (hari) <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="voucher_validity_days"
                                        name="voucher_validity_days"
                                        value="<?= old('voucher_validity_days') ?>"
                                        min="1"
                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"
                                        placeholder="30">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Settings -->
            <div class="space-y-6">
                <!-- Target Segmentasi -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Target Segmentasi</h3>

                    <div>
                        <label for="target_segment" class="block text-sm font-medium text-gray-700 mb-1">
                            Segment Pelanggan
                        </label>
                        <select
                            id="target_segment"
                            name="target_segment"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            <option value="all" <?= old('target_segment', 'all') === 'all' ? 'selected' : '' ?>>Semua Pelanggan</option>
                            <option value="new" <?= old('target_segment') === 'new' ? 'selected' : '' ?>>Pelanggan Baru</option>
                            <option value="loyal" <?= old('target_segment') === 'loyal' ? 'selected' : '' ?>>Pelanggan Loyal</option>
                            <option value="vip" <?= old('target_segment') === 'vip' ? 'selected' : '' ?>>VIP</option>
                        </select>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Pengaturan</h3>

                    <div class="space-y-4">
                        <!-- Status Aktif -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Status Aktif</p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="isActive ? 'Promosi akan aktif dan berlaku' : 'Promosi tidak aktif'"></p>
                            </div>
                            <button type="button" @click="isActive = !isActive" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="isActive ? 'bg-success' : 'bg-gray-300'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm" :class="isActive ? 'translate-x-6' : 'translate-x-1'"></span>
                            </button>
                            <input type="hidden" name="is_active" :value="isActive ? 1 : 0">
                        </div>

                        <!-- Kirim Notifikasi -->
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Kirim Notifikasi Promosi</p>
                                    <p class="text-xs text-gray-400 mt-0.5" x-text="sendNotification ? 'Notifikasi akan dikirim ke segment target' : 'Tidak mengirim notifikasi'"></p>
                                </div>
                                <button type="button" @click="sendNotification = !sendNotification" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="sendNotification ? 'bg-primary' : 'bg-gray-300'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm" :class="sendNotification ? 'translate-x-6' : 'translate-x-1'"></span>
                                </button>
                                <input type="hidden" name="send_notification" :value="sendNotification ? 1 : 0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('admin/promotions') ?>" class="inline-flex items-center justify-center gap-2 flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Promosi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
