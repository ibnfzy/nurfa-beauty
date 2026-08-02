<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $product */
/** @var array $categories */
/** @var array $allProducts */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Produk', 'url' => base_url('admin/products')],
        ['label' => 'Edit'],
    ]
]) ?>

<?php $product = $product ?? [] ?>

<div x-data="{
    imagePreview: null,
    currentImage: '<?= $product['image'] ? base_url('uploads/products/' . $product['image']) : '' ?>',
    isActive: <?= $product['is_active'] ? 'true' : 'false' ?>,
    isBundle: <?= ($product['is_bundle'] ?? 0) ? 'true' : 'false' ?>,
    bundleItems: <?= htmlspecialchars($product['bundle_products'] ?? '[]', ENT_QUOTES, 'UTF-8') ?>,
    showLightbox: false,
    lightboxImage: '',
    openLightbox(src) {
        this.lightboxImage = src;
        this.showLightbox = true;
    },
    handleImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.imagePreview = URL.createObjectURL(file);
        }
    },
    removeImage() {
        this.imagePreview = null;
        $refs.imageInput.value = '';
    },
    addBundleItem() {
        this.bundleItems.push({ product_id: '', quantity: 1 });
    },
    removeBundleItem(index) {
        this.bundleItems.splice(index, 1);
    },
    getBundleJson() {
        return JSON.stringify(this.bundleItems.filter(item => item.product_id));
    }
}">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Edit Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi produk</p>
        </div>
        <a href="<?= base_url('admin/products') ?>" class="btn-secondary inline-flex items-center gap-2">
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
    <form action="<?= base_url('admin/products/update/' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Informasi Produk</h3>

                    <div class="space-y-4">
                        <!-- Nama Produk -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?= old('name', $product['name']) ?>"
                                required
                                maxlength="200"
                                class="input-field"
                                placeholder="Masukkan nama produk">
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select id="category_id" name="category_id" required class="input-field">
                                <option value="">Pilih Kategori</option>
                                <?php foreach (($categories ?? []) as $cat): ?>
                                    <option value="<?= $cat['id'] ?? '' ?>" <?= old('category_id', $product['category_id']) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Harga & Stok -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga <span class="text-danger">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                                    <input
                                        type="number"
                                        id="price"
                                        name="price"
                                        value="<?= old('price', $product['price']) ?>"
                                        required
                                        min="0"
                                        step="any"
                                        class="input-field pl-10"
                                        placeholder="0">
                                </div>
                            </div>
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">
                                    Stok <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="stock"
                                    name="stock"
                                    value="<?= old('stock', $product['stock']) ?>"
                                    required
                                    min="0"
                                    class="input-field"
                                    placeholder="0">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Deskripsi
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="input-field"
                                placeholder="Masukkan deskripsi produk (opsional)"><?= old('description', $product['description']) ?></textarea>
                        </div>

                        <!-- Tipe Produk: Bundle -->
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Produk Bundling</p>
                                    <p class="text-xs text-gray-400 mt-0.5" x-text="isBundle ? 'Produk ini berisi beberapa produk' : 'Produk biasa'"></p>
                                </div>
                                <button type="button" @click="isBundle = !isBundle" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="isBundle ? 'bg-primary' : 'bg-gray-300'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm" :class="isBundle ? 'translate-x-6' : 'translate-x-1'"></span>
                                </button>
                                <input type="hidden" name="is_bundle" :value="isBundle ? 1 : 0">
                            </div>

                            <div x-show="isBundle" x-transition class="space-y-4" style="display: none;">
                                <!-- Diskon Bundle -->
                                <div>
                                    <label for="bundle_discount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Diskon Bundle (%)
                                    </label>
                                    <input type="number" id="bundle_discount" name="bundle_discount" value="<?= $product['bundle_discount'] ?? 0 ?>" min="0" max="100" step="0.01" class="input-field" placeholder="0">
                                </div>

                                <!-- Komponen Bundle -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-sm font-medium text-gray-700">Komponen Paket</label>
                                        <button type="button" @click="addBundleItem()" class="text-sm text-primary hover:text-primary-dark font-medium inline-flex items-center gap-1">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                            Tambah Item
                                        </button>
                                    </div>

                                    <template x-for="(item, index) in bundleItems" :key="index">
                                        <div class="flex items-center gap-3 mb-3">
                                            <select x-model="item.product_id" class="input-field flex-1">
                                                <option value="">Pilih Produk</option>
                                                <?php foreach (($allProducts ?? []) as $ap): ?>
                                                    <option value="<?= $ap['id'] ?>"><?= esc($ap['name']) ?> - Rp <?= number_format($ap['price'], 0, ',', '.') ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="number" x-model.number="item.quantity" min="1" class="input-field w-20" placeholder="Qty">
                                            <button type="button" @click="removeBundleItem(index)" class="p-2 text-danger hover:bg-red-50 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </div>
                                    </template>

                                    <div x-show="bundleItems.length === 0" class="text-center py-4 text-sm text-gray-400">
                                        Belum ada komponen paket
                                    </div>
                                </div>

                                <input type="hidden" name="bundle_products" :value="getBundleJson()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Image & Status -->
            <div class="space-y-6">
                <!-- Gambar Produk -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Gambar Produk</h3>

                    <!-- Current Image -->
                    <div x-show="currentImage && !imagePreview" class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                        <div class="relative">
                            <img :src="currentImage" @click="openLightbox(currentImage)" class="w-full h-48 object-cover rounded-xl border border-gray-100 cursor-pointer hover:opacity-90 transition-opacity" alt="Gambar produk">
                            <label for="image" class="absolute bottom-2 right-2 cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-black/50 rounded-lg text-white text-xs font-medium hover:bg-black/70 transition-colors">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                Ganti
                            </label>
                        </div>
                    </div>

                    <!-- New Image Preview -->
                    <div x-show="imagePreview" class="mb-4" style="display: none;">
                        <p class="text-xs text-gray-500 mb-2">Gambar baru:</p>
                        <div class="relative">
                            <img :src="imagePreview" class="w-full h-48 object-cover rounded-xl border border-gray-100" alt="Preview">
                            <button type="button" @click="removeImage()" class="absolute top-2 right-2 w-8 h-8 bg-black/50 rounded-full flex items-center justify-center text-white hover:bg-black/70 transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Upload (when no image) -->
                    <div x-show="!currentImage && !imagePreview" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary transition-colors">
                        <i data-lucide="image-plus" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-500 mb-2">Klik untuk memilih gambar</p>
                        <p class="text-xs text-gray-400 mb-3">JPG, JPEG, PNG, atau WEBP (Maks 2MB)</p>
                        <label for="image" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            Pilih Gambar
                        </label>
                    </div>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        x-ref="imageInput"
                        @change="handleImage($event)"
                        accept="image/jpg,image/jpeg,image/png,image/webp"
                        class="hidden">
                    <p class="text-xs text-gray-400 mt-2">Kosongkan jika tidak ingin mengganti gambar.</p>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Status</h3>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Status Produk</p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="isActive ? 'Produk akan ditampilkan di toko' : 'Produk tidak ditampilkan di toko'"></p>
                        </div>
                        <button type="button" @click="isActive = !isActive" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="isActive ? 'bg-success' : 'bg-gray-300'">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm" :class="isActive ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                        <input type="hidden" name="is_active" :value="isActive ? 1 : 0">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('admin/products') ?>" class="btn-secondary flex-1 text-center">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary flex-1 inline-flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Perbarui
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Lightbox Modal -->
    <div x-show="showLightbox" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" @click.self="showLightbox = false" style="display: none;">
        <button @click="showLightbox = false" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
        <img :src="lightboxImage" class="max-w-full max-h-[90vh] object-contain rounded-lg" alt="Gambar produk">
    </div>
</div>

<?= $this->endSection() ?>