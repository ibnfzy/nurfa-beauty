<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $products */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var array $categories */
/** @var string|null $search */
/** @var string|null $categoryId */
?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary/10 to-secondary/10 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl sm:text-4xl font-playfair font-bold text-gray-800 mb-3">
            Nurfa Beauty
        </h1>
        <p class="text-gray-600 text-sm sm:text-base max-w-lg mx-auto mb-8">
            Temukan produk kecantikan terbaik untuk perawatan kulit dan kecantikanmu. Kualitas terjamin, harga terjangkau.
        </p>

        <!-- Search Bar -->
        <div class="max-w-xl mx-auto mb-6">
            <form action="<?= base_url('catalog') ?>" method="GET" class="relative">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    <input type="text" name="q" value="<?= esc($search ?? '') ?>"
                        placeholder="Cari produk kecantikan..."
                        class="w-full pl-12 pr-12 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm">
                    <?php if (!empty($search)): ?>
                        <a href="<?= base_url('catalog') ?>" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide justify-center flex-wrap">
            <a href="<?= base_url('catalog') ?>"
                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors <?= empty($categoryId) ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary/10 border border-gray-200' ?>">
                Semua
            </a>
            <?php foreach ($categories ?? [] as $cat): ?>
                <a href="<?= base_url('catalog?category=' . $cat['id'] . ($search ? '&q=' . urlencode($search) : '')) ?>"
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors <?= (isset($categoryId) && $categoryId == $cat['id']) ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary/10 border border-gray-200' ?>">
                    <?= esc($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold mb-1">Informasi penting proses belanja</p>
        <p>Setelah produk dimasukkan ke keranjang, segera lakukan checkout. Kami tidak menjamin stok produk yang hanya tersimpan di keranjang. Stok diberikan kepada pelanggan yang lebih dahulu menyelesaikan checkout.</p>
    </div>
</section>

<!-- Products Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if (!empty($search)): ?>
        <p class="text-sm text-gray-500 mb-4">
            Hasil pencarian untuk "<span class="font-medium text-gray-700"><?= esc($search) ?></span>"
        </p>
    <?php endif; ?>

    <?php if (empty($products ?? [])): ?>
        <?= view('components/empty-state', [
            'icon'        => 'package-open',
            'title'       => 'Produk tidak ditemukan',
            'description' => 'Belum ada produk yang sesuai dengan pencarian atau filter Anda. Coba kata kunci lain.',
        ]) ?>
    <?php else: ?>
        <!-- Product Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php foreach ($products ?? [] as $product): ?>
                <a href="<?= base_url('product/' . ($product['id'] ?? '')) ?>" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Image -->
                    <div class="aspect-square bg-gray-100 overflow-hidden relative">
                        <?php if (!empty($product['image'] ?? '')): ?>
                            <img src="<?= base_url('uploads/products/' . ($product['image'] ?? '')) ?>"
                                alt="<?= esc($product['name']) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                                <i data-lucide="package" class="w-12 h-12 text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="text-xs text-primary font-medium"><?= esc($product['category_name']) ?></span>
                        <?php endif; ?>
                        <h3 class="font-semibold text-gray-800 mt-1 text-sm line-clamp-2"><?= esc($product['name'] ?? '') ?></h3>
                        <p class="text-primary font-bold mt-2">Rp <?= number_format($product['price'] ?? 0, 0, ',', '.') ?></p>
                        <?php if (isset($product['stock']) && $product['stock'] <= 5 && $product['stock'] > 0): ?>
                            <p class="text-xs text-warning mt-1">Stok terbatas (<?= $product['stock'] ?>)</p>
                        <?php elseif (isset($product['stock']) && $product['stock'] == 0): ?>
                            <p class="text-xs text-danger mt-1">Habis</p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <?= view('components/pagination', ['pager' => $pager]) ?>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>