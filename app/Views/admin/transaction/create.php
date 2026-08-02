<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $customers */
/** @var array $products */
/** @var array $bankAccounts */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Transaksi', 'url' => base_url('admin/transactions')],
        ['label' => 'Input Transaksi'],
    ]
]) ?>

<div x-data="{
    items: [{product_id: '', quantity: 1}],
    products: <?= json_encode($products ?? []) ?>,
    addRow() {
        this.items.push({product_id: '', quantity: 1});
    },
    removeRow(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },
    getSubtotal(index) {
        const product = this.products.find(p => p.id == this.items[index].product_id);
        return product ? product.price * this.items[index].quantity : 0;
    },
    getTotal() {
        return this.items.reduce((sum, _, i) => sum + this.getSubtotal(i), 0);
    },
    getStock(productId) {
        const product = this.products.find(p => p.id == productId);
        return product ? product.stock : 0;
    },
    getPrice(productId) {
        const product = this.products.find(p => p.id == productId);
        return product ? product.price : 0;
    },
    formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Input Transaksi</h2>
            <p class="text-sm text-gray-500 mt-1">Buat transaksi manual (offline)</p>
        </div>
        <a href="<?= base_url('admin/transactions') ?>" class="btn-secondary inline-flex items-center gap-2">
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
    <form action="<?= base_url('admin/transactions/store') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-primary"></i>
                        Pelanggan
                    </h3>
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Pilih Pelanggan <span class="text-danger">*</span>
                        </label>
                        <select id="customer_id" name="customer_id" required class="input-field">
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php foreach ($customers ?? [] as $customer): ?>
                                <option value="<?= $customer['id'] ?>" <?= old('customer_id') == $customer['id'] ? 'selected' : '' ?>>
                                    <?= esc($customer['name']) ?> (<?= esc($customer['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="package" class="w-5 h-5 text-primary"></i>
                        Produk
                    </h3>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex flex-col sm:flex-row gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <!-- Product Select -->
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Produk</label>
                                    <select
                                        :name="'items[' + index + '][product_id]'"
                                        x-model="item.product_id"
                                        required
                                        class="input-field">
                                        <option value="">-- Pilih Produk --</option>
                                        <template x-for="product in products" :key="product.id">
                                            <option :value="product.id" x-text="product.name + ' - Rp ' + new Intl.NumberFormat('id-ID').format(product.price) + ' (Stok: ' + product.stock + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div class="w-full sm:w-28">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah</label>
                                    <input
                                        type="number"
                                        :name="'items[' + index + '][quantity]'"
                                        x-model.number="item.quantity"
                                        min="1"
                                        required
                                        class="input-field"
                                        placeholder="0">
                                </div>

                                <!-- Subtotal Display -->
                                <div class="w-full sm:w-40">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Subtotal</label>
                                    <div class="input-field bg-gray-100 text-gray-700 font-medium" x-text="formatRupiah(getSubtotal(index))">
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <div class="flex items-end">
                                    <button
                                        type="button"
                                        @click="removeRow(index)"
                                        class="p-2 rounded-lg text-danger hover:bg-red-50 transition-colors"
                                        :class="items.length <= 1 ? 'opacity-50 cursor-not-allowed' : ''"
                                        :disabled="items.length <= 1"
                                        title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button
                        type="button"
                        @click="addRow()"
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 border-2 border-dashed border-primary/40 text-primary rounded-lg hover:bg-primary-light/20 transition-colors text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah Produk
                    </button>
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
                        Catatan
                    </h3>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Transaksi</label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="input-field"
                            placeholder="Catatan transaksi (opsional)"><?= old('notes') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="space-y-6">
                <!-- Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 sticky top-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="calculator" class="w-5 h-5 text-primary"></i>
                        Ringkasan
                    </h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Jumlah Item</span>
                            <span class="text-sm font-medium text-gray-800" x-text="items.length + ' item'"></span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm font-medium text-gray-700">Total</span>
                            <span class="text-lg font-bold text-gray-800" x-text="formatRupiah(getTotal())"></span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="submit" class="btn-primary w-full inline-flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Transaksi
                        </button>
                        <a href="<?= base_url('admin/transactions') ?>" class="btn-secondary w-full text-center block">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>