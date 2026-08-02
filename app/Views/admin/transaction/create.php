<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $customers */
/** @var array $products */
/** @var array $bankAccounts */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Transaksi', 'url' => base_url('admin/transactions')],
        ['label' => 'Input Transaksi'],
    ]
]) ?>

<script>
    window.productsData = <?= json_encode($products ?? []) ?>;
</script>

<div x-data="{
    items: [{id: 1, product_id: '', quantity: 1}],
    nextId: 2,
    products: window.productsData,
    addRow() {
        this.items.push({id: this.nextId++, product_id: '', quantity: 1});
    },
    removeRow(id) {
        if (this.items.length > 1) {
            this.items = this.items.filter(item => item.id !== id);
        }
    },
    getAvailableProducts(currentIndex) {
        const selectedIds = this.items
            .filter((_, i) => i !== currentIndex)
            .map(item => item.product_id)
            .filter(id => id !== '');
        return this.products.filter(p => !selectedIds.includes(p.id.toString()));
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
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6" x-data="{
                    customerType: '<?= old('customer_type', 'existing') ?>',
                    guestName: '<?= old('guest_name', '') ?>',
                    guestEmail: '<?= old('guest_email', '') ?>'
                }">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-primary"></i>
                        Pelanggan
                    </h3>

                    <!-- Toggle Tipe Pelanggan -->
                    <div class="flex gap-2 mb-4">
                        <button type="button"
                            @click="customerType = 'existing'"
                            :class="customerType === 'existing' ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            Pelanggan Terdaftar
                        </button>
                        <button type="button"
                            @click="customerType = 'guest'"
                            :class="customerType === 'guest' ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            Pelanggan Guest
                        </button>
                    </div>

                    <input type="hidden" name="customer_type" :value="customerType">

                    <!-- Existing Customer -->
                    <div x-show="customerType === 'existing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Pelanggan <span class="text-danger">*</span>
                            </label>
                            <select id="customer_id" name="customer_id" class="input-field" :required="customerType === 'existing'">
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($customers ?? [] as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= old('customer_id') == $customer['id'] ? 'selected' : '' ?>>
                                        <?= esc($customer['name']) ?> (<?= esc($customer['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Guest Customer -->
                    <div x-show="customerType === 'guest'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                            <div class="flex items-start gap-2">
                                <i data-lucide="info" class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0"></i>
                                <p class="text-xs text-amber-700">Akun pelanggan akan otomatis dibuat dengan password 6 digit angka. Simpan password untuk diberikan ke pelanggan.</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Pelanggan <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="guest_name" name="guest_name"
                                    x-model="guestName"
                                    :required="customerType === 'guest'"
                                    class="input-field"
                                    placeholder="Masukkan nama pelanggan"
                                    maxlength="100">
                            </div>
                            <div>
                                <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email Pelanggan <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="guest_email" name="guest_email"
                                    x-model="guestEmail"
                                    :required="customerType === 'guest'"
                                    class="input-field"
                                    placeholder="contoh@email.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="package" class="w-5 h-5 text-primary"></i>
                        Produk
                    </h3>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="item.id">
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
                                        <template x-for="product in getAvailableProducts(index)" :key="product.id">
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
                                        @click="removeRow(item.id)"
                                        class="p-2 rounded-lg text-danger hover:bg-red-50 transition-colors"
                                        :class="items.length <= 1 ? 'opacity-50 cursor-not-allowed' : ''"
                                        :disabled="items.length <= 1"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
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