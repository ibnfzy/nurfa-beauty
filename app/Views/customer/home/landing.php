<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $promotions */
/** @var array $latestProducts */
/** @var array $categories */
/** @var array|null $welcomeVoucher */
/** @var array|null $loyaltyProgram */
/** @var array|null $loyaltyProgramDetails */
/** @var array|null $recommendedProducts */
/** @var array|null $bundleProducts */
?>

<?= $this->section('content') ?>

<!-- Welcome Banner -->
<section class="relative overflow-hidden bg-gradient-to-br from-primary via-primary/90 to-secondary py-16 sm:py-24">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-white rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h1 class="text-3xl sm:text-5xl font-playfair font-bold text-white mb-4">
            Selamat Datang di Nurfa Beauty
        </h1>
        <p class="text-white/90 text-sm sm:text-lg max-w-2xl mx-auto mb-8">
            Temukan produk kecantikan terbaik untuk perawatan kulit, rambut, dan tubuhmu. Kualitas terjamin, harga terjangkau.
        </p>
        <a href="<?= base_url('catalog') ?>"
            class="inline-flex items-center gap-2 bg-white text-primary font-semibold px-8 py-3 rounded-xl shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all text-sm sm:text-base">
            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            Mulai Belanja
        </a>
    </div>
</section>

<!-- Voucher Banner -->
<?php if (!empty($welcomeVoucher)): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-20 mb-8">
        <div x-data="{ copied: false }" class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row items-center gap-4">
            <div class="flex-shrink-0">
                <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i data-lucide="ticket" class="w-7 h-7 text-yellow-600"></i>
                </div>
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h3 class="font-playfair font-bold text-gray-800 text-lg">Selamat Datang! Ini Voucher Untukmu</h3>
                <p class="text-gray-600 text-sm mt-1">
                    Gunakan kode voucher di bawah ini untuk mendapatkan diskon spesial di pembelian pertamamu.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <code class="bg-white border-2 border-dashed border-yellow-400 text-yellow-700 font-bold text-lg px-5 py-2 rounded-lg tracking-wider select-all">
                    <?= esc($welcomeVoucher['code'] ?? $welcomeVoucher) ?>
                </code>
                <button @click="
                    navigator.clipboard.writeText('<?= esc($welcomeVoucher['code'] ?? $welcomeVoucher) ?>');
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                "
                    class="p-2 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition-colors"
                    :title="copied ? 'Tersalin!' : 'Salin kode'">
                    <i data-lucide="copy" class="w-5 h-5 text-yellow-600" x-show="!copied"></i>
                    <i data-lucide="check" class="w-5 h-5 text-green-600" x-show="copied" x-cloak></i>
                </button>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Active Promotions Section -->
<?php if (!empty($promotions)): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-playfair font-bold text-gray-800">
                    Promo Spesial
                </h2>
                <p class="text-gray-500 text-sm mt-1">Jangan lewatkan penawaran menarik berikut</p>
            </div>
            <i data-lucide="sparkles" class="w-6 h-6 text-primary"></i>
        </div>

        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
            <?php foreach ($promotions as $promo): ?>
                <div class="flex-shrink-0 w-72 sm:w-80 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="bg-gradient-to-r from-primary/10 to-secondary/10 px-5 py-3 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-800 text-sm line-clamp-1"><?= esc($promo['name'] ?? '') ?></h3>
                            <?php
                            $typeLabels = [
                                'flash_sale' => 'Flash Sale',
                                'buy_get'    => 'Beli Gratis',
                                'voucher'    => 'Voucher',
                                'discount'   => 'Diskon',
                            ];
                            $typeColors = [
                                'flash_sale' => 'bg-red-100 text-red-700',
                                'buy_get'    => 'bg-green-100 text-green-700',
                                'voucher'    => 'bg-blue-100 text-blue-700',
                                'discount'   => 'bg-purple-100 text-purple-700',
                            ];
                            $type = $promo['type'] ?? 'discount';
                            ?>
                            <span class="text-xs font-medium px-2 py-1 rounded-full <?= $typeColors[$type] ?? 'bg-gray-100 text-gray-700' ?>">
                                <?= $typeLabels[$type] ?? ucfirst($type) ?>
                            </span>
                        </div>
                    </div>
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i data-lucide="percent" class="w-5 h-5 text-primary"></i>
                            <?php if (($promo['discount_type'] ?? '') === 'percentage'): ?>
                                <span class="text-2xl font-bold text-primary"><?= esc($promo['discount_value'] ?? 0) ?>%</span>
                                <span class="text-gray-500 text-sm">OFF</span>
                            <?php else: ?>
                                <span class="text-2xl font-bold text-primary">Rp <?= number_format($promo['discount_value'] ?? 0, 0, ',', '.') ?></span>
                                <span class="text-gray-500 text-sm">OFF</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($promo['min_purchase'])): ?>
                            <p class="text-xs text-gray-500">Min. pembelian Rp <?= number_format($promo['min_purchase'], 0, ',', '.') ?></p>
                        <?php endif; ?>
                        <div class="flex items-center gap-1 mt-3 text-xs text-gray-400">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <span>
                                <?= date('d M Y', strtotime($promo['start_date'])) ?> - <?= date('d M Y', strtotime($promo['end_date'])) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Latest Products Section -->
<?php if (!empty($latestProducts)): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-playfair font-bold text-gray-800">
                    Produk Terbaru
                </h2>
                <p class="text-gray-500 text-sm mt-1">Produk kecantikan terbaru dari koleksi kami</p>
            </div>
            <a href="<?= base_url('catalog') ?>" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
                Lihat Semua
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php foreach ($latestProducts as $product): ?>
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
                        <?php if (isset($product['stock']) && $product['stock'] <= 5 && $product['stock'] > 0): ?>
                            <span class="absolute top-2 left-2 bg-orange-500 text-white text-xs font-medium px-2 py-0.5 rounded-full">Terbatas</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="text-xs text-primary font-medium"><?= esc($product['category_name']) ?></span>
                        <?php endif; ?>
                        <h3 class="font-semibold text-gray-800 mt-1 text-sm line-clamp-2"><?= esc($product['name'] ?? '') ?></h3>
                        <p class="text-primary font-bold mt-2">Rp <?= number_format($product['price'] ?? 0, 0, ',', '.') ?></p>
                        <?php if (isset($product['stock']) && $product['stock'] == 0): ?>
                            <p class="text-xs text-danger mt-1">Habis</p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Rekomendasi untuk Anda Section -->
<?php if (!empty($recommendedProducts ?? [])): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-playfair font-bold text-gray-800">
                    Rekomendasi untuk Anda
                </h2>
                <p class="text-gray-500 text-sm mt-1">Berdasarkan produk yang pernah Anda beli</p>
            </div>
            <a href="<?= base_url('catalog') ?>" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
                Lihat Semua
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php foreach ($recommendedProducts as $product): ?>
                <a href="<?= base_url('product/' . ($product['id'] ?? '')) ?>" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
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
                        <span class="absolute top-2 left-2 bg-primary text-white text-xs font-medium px-2 py-0.5 rounded-full">Rekomendasi</span>
                    </div>
                    <div class="p-4">
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="text-xs text-primary font-medium"><?= esc($product['category_name']) ?></span>
                        <?php endif; ?>
                        <h3 class="font-semibold text-gray-800 mt-1 text-sm line-clamp-2"><?= esc($product['name'] ?? '') ?></h3>
                        <p class="text-primary font-bold mt-2">Rp <?= number_format($product['price'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Paket Hemat Section -->
<?php if (!empty($bundleProducts ?? [])): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-playfair font-bold text-gray-800">
                    Paket Hemat
                </h2>
                <p class="text-gray-500 text-sm mt-1">Beli paket bundling dan hemat lebih banyak</p>
            </div>
            <i data-lucide="package-open" class="w-6 h-6 text-primary"></i>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php foreach ($bundleProducts as $bundle): ?>
                <a href="<?= base_url('product/' . ($bundle['id'] ?? '')) ?>" class="group bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 overflow-hidden relative">
                        <?php if (!empty($bundle['image'] ?? '')): ?>
                            <img src="<?= base_url('uploads/products/' . ($bundle['image'] ?? '')) ?>"
                                alt="<?= esc($bundle['name']) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                                <i data-lucide="package" class="w-12 h-12 text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                        <span class="absolute top-2 left-2 bg-gradient-to-r from-primary to-secondary text-white text-xs font-medium px-2.5 py-1 rounded-full">
                            Paket Hemat
                        </span>
                        <?php if (($bundle['bundle_discount'] ?? 0) > 0): ?>
                            <span class="absolute top-2 right-2 bg-danger text-white text-xs font-bold px-2 py-1 rounded-full">
                                -<?= $bundle['bundle_discount'] ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <?php if (!empty($bundle['category_name'])): ?>
                            <span class="text-xs text-primary font-medium"><?= esc($bundle['category_name']) ?></span>
                        <?php endif; ?>
                        <h3 class="font-semibold text-gray-800 mt-1 text-sm line-clamp-2"><?= esc($bundle['name'] ?? '') ?></h3>
                        <p class="text-primary font-bold mt-2">Rp <?= number_format($bundle['price'] ?? 0, 0, ',', '.') ?></p>
                        <?php if (($bundle['bundle_discount'] ?? 0) > 0): ?>
                            <p class="text-xs text-success mt-1">Hemat <?= $bundle['bundle_discount'] ?>%</p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Categories Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="text-center mb-8">
        <h2 class="text-2xl sm:text-3xl font-playfair font-bold text-gray-800">
            Belanja Berdasarkan Kategori
        </h2>
        <p class="text-gray-500 text-sm mt-1">Temukan produk sesuai kebutuhanmu</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <?php
        $categoryIcons = [
            'Perawatan Rambut' => 'scissors',
            'Wajah'            => 'sparkles',
            'Badan'            => 'heart',
            'Rambut'           => 'scissors',
            'Kulit'            => 'sparkles',
        ];
        $categoryColors = [
            'Perawatan Rambut' => 'from-pink-50 to-rose-50 border-pink-200',
            'Wajah'            => 'from-purple-50 to-violet-50 border-purple-200',
            'Badan'            => 'from-blue-50 to-sky-50 border-blue-200',
            'Rambut'           => 'from-pink-50 to-rose-50 border-pink-200',
            'Kulit'            => 'from-purple-50 to-violet-50 border-purple-200',
        ];
        ?>
        <?php foreach (array_slice($categories ?? [], 0, 3) as $cat): ?>
            <?php
            $icon  = $categoryIcons[$cat['name']] ?? 'tag';
            $color = $categoryColors[$cat['name']] ?? 'from-gray-50 to-slate-50 border-gray-200';
            ?>
            <a href="<?= base_url('catalog?category=' . $cat['id']) ?>"
                class="group bg-gradient-to-br <?= $color ?> border rounded-xl p-6 text-center hover:shadow-md transition-all">
                <div class="w-16 h-16 mx-auto bg-white/80 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="<?= $icon ?>" class="w-8 h-8 text-primary"></i>
                </div>
                <h3 class="font-playfair font-bold text-gray-800 text-lg"><?= esc($cat['name']) ?></h3>
                <p class="text-gray-500 text-sm mt-1">Lihat semua produk</p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gradient-to-r from-primary/10 to-secondary/10 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl font-playfair font-bold text-gray-800 mb-3">
            Mulai Belanja Sekarang
        </h2>
        <p class="text-gray-600 text-sm sm:text-base max-w-lg mx-auto mb-6">
            Jelajahi koleksi lengkap produk kecantikan kami dan temukan yang terbaik untukmu.
        </p>
        <a href="<?= base_url('catalog') ?>"
            class="inline-flex items-center gap-2 bg-primary text-white font-semibold px-8 py-3 rounded-xl shadow-lg hover:shadow-xl hover:bg-primary/90 transition-all text-sm sm:text-base">
            <i data-lucide="store" class="w-5 h-5"></i>
            Lihat Katalog
        </a>
    </div>
</section>

<?= $this->endSection() ?>