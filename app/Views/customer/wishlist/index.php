<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $wishlist */
?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    showDeleteModal: false,
    deleteId: null,
    deleteName: ''
}">
    <!-- Breadcrumb -->
    <?= view('components/breadcrumb', [
        'items' => [
            ['label' => 'Beranda', 'url' => base_url()],
            ['label' => 'Wishlist Saya'],
        ]
    ]) ?>

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Wishlist Saya</h1>

    <?php if (empty($wishlist)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="heart" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Wishlist Kosong</h3>
            <p class="text-gray-500 text-sm mb-6">Anda belum menambahkan produk ke wishlist.</p>
            <a href="<?= base_url('catalog') ?>" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <!-- Wishlist Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <?php foreach ($wishlist as $item): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    <!-- Product Image -->
                    <a href="<?= base_url('product/' . ($item['product_id'] ?? 0)) ?>" class="block relative">
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            <?php if (!empty($item['product_image'])): ?>
                                <img src="<?= base_url('uploads/products/' . $item['product_image']) ?>"
                                    alt="<?= esc($item['product_name'] ?? '') ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i data-lucide="image" class="w-12 h-12 text-gray-300"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>

                    <!-- Product Info -->
                    <div class="p-4">
                        <a href="<?= base_url('product/' . ($item['product_id'] ?? 0)) ?>" class="block">
                            <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 hover:text-primary transition-colors">
                                <?= esc($item['product_name'] ?? '') ?>
                            </h3>
                        </a>
                        <p class="text-primary font-bold mt-2">
                            Rp <?= number_format($item['product_price'] ?? 0, 0, ',', '.') ?>
                        </p>

                        <!-- Stock Status -->
                        <div class="mt-2">
                            <?php if (($item['product_stock'] ?? 0) > 0): ?>
                                <span class="text-xs text-success font-medium">Stok: <?= $item['product_stock'] ?></span>
                            <?php else: ?>
                                <span class="text-xs text-danger font-medium">Stok Habis</span>
                            <?php endif; ?>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 mt-3">
                            <?php if (($item['product_stock'] ?? 0) > 0): ?>
                                <form action="<?= base_url('cart/add') ?>" method="POST" class="flex-1">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?? 0 ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-xs font-medium">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                        Keranjang
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-400 rounded-lg text-xs font-medium cursor-not-allowed">
                                    Stok Habis
                                </button>
                            <?php endif; ?>

                            <button type="button"
                                @click="deleteId = <?= $item['id'] ?? 0 ?>; deleteName = '<?= esc($item['product_name'] ?? '') ?>'; showDeleteModal = true"
                                class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-danger hover:bg-red-50 rounded-lg transition-colors"
                                title="Hapus dari wishlist">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Delete Modal -->
    <?= view('components/modal', [
        'showVar'   => 'showDeleteModal',
        'title'     => 'Hapus dari Wishlist',
        'maxWidth'  => 'max-w-md',
        'slot'      => (function () {
            $output = '<div class="text-center">';
            $output .= '<div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">';
            $output .= '<i data-lucide="alert-triangle" class="w-8 h-8 text-danger"></i>';
            $output .= '</div>';
            $output .= '<h3 class="text-lg font-semibold text-gray-800 mb-2">Yakin ingin menghapus?</h3>';
            $output .= '<p class="text-sm text-gray-500 mb-6">Produk <span class="font-semibold text-gray-700" x-text="deleteName"></span> akan dihapus dari wishlist.</p>';
            $output .= '<div class="flex items-center justify-center gap-3">';
            $output .= '<button type="button" @click="showDeleteModal = false" class="btn-secondary">Batal</button>';
            $output .= '<form :action="\'/wishlist/remove/\' + deleteId" method="POST" class="inline">';
            $output .= csrf_field();
            $output .= '<button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">Hapus</button>';
            $output .= '</form>';
            $output .= '</div>';
            $output .= '</div>';
            return $output;
        })(),
    ]) ?>
</div>

<?= $this->endSection() ?>
