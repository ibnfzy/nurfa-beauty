<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $product */
/** @var float $avgRating */
/** @var int $reviewCount */
/** @var array $reviews */
/** @var array $premiumProducts */
/** @var array $crossSellProducts */
/** @var array $bundleComponents */
/** @var bool $isWishlisted */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Beranda', 'url' => base_url()],
        ['label' => $product['category_name'] ?? 'Produk', 'url' => base_url('?category=' . ($product['category_id'] ?? ''))],
        ['label' => $product['name'] ?? 'Produk'],
    ]
]) ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        wishlisted: <?= ($isWishlisted ?? false) ? 'true' : 'false' ?>,
        loading: false,
        async toggleWishlist() {
            if (this.loading) return;
            this.loading = true;
            try {
                const res = await fetch('<?= base_url('wishlist/toggle/' . ($product['id'] ?? 0)) ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.wishlisted = data.action === 'added';
                }
            } catch (e) {}
            this.loading = false;
        }
    }">

    <!-- Product Detail -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Left: Product Image -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="aspect-square bg-gray-50 flex items-center justify-center">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?= base_url('uploads/products/' . $product['image']) ?>"
                        alt="<?= esc($product['name']) ?>"
                        class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center text-gray-300">
                        <i data-lucide="package" class="w-24 h-24 mb-2"></i>
                        <span class="text-sm">Tidak ada gambar</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Product Info -->
        <div class="flex flex-col">
            <?php if (!empty($product['category_name'])): ?>
                <?= view('components/badge', ['text' => $product['category_name'], 'color' => 'primary']) ?>
            <?php endif; ?>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 mb-4">
                <?= esc($product['name'] ?? '') ?>
            </h1>

            <!-- Wishlist Button -->
            <?php if (session()->get('logged_in')): ?>
                <button @click="toggleWishlist()" class="inline-flex items-center gap-1.5 text-sm mb-4 transition-colors"
                    :class="wishlisted ? 'text-danger' : 'text-gray-400 hover:text-danger'">
                    <i data-lucide="heart" class="w-5 h-5" :class="wishlisted ? 'fill-danger' : ''"></i>
                    <span x-text="wishlisted ? 'Di Wishlist' : 'Tambah ke Wishlist'"></span>
                </button>
            <?php endif; ?>

            <p class="text-3xl font-bold text-primary mb-4">
                Rp <?= number_format($product['price'] ?? 0, 0, ',', '.') ?>
            </p>

            <!-- Stock Status -->
            <div class="mb-4">
                <?php if (($product['stock'] ?? 0) > 5): ?>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-success"></span>
                        <span class="text-sm text-success font-medium">Stok tersedia (<?= $product['stock'] ?? 0 ?>)</span>
                    </div>
                <?php elseif (($product['stock'] ?? 0) > 0): ?>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-warning"></span>
                        <span class="text-sm text-warning font-medium">Stok terbatas (<?= $product['stock'] ?? 0 ?>)</span>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-danger"></span>
                        <span class="text-sm text-danger font-medium">Stok habis</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rating -->
            <?php if (($reviewCount ?? 0) > 0): ?>
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex items-center gap-0.5">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= round($avgRating)): ?>
                                <i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-yellow-400"></i>
                            <?php else: ?>
                                <i data-lucide="star" class="w-4 h-4 text-gray-300"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <span class="text-sm text-gray-500"><?= $avgRating ?? 0 ?> (<?= $reviewCount ?? 0 ?> ulasan)</span>
                </div>
            <?php endif; ?>

            <!-- Description -->
            <?php if (!empty($product['description'])): ?>
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi Produk</h3>
                    <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(esc($product['description'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Add to Cart -->
            <?php if (($product['stock'] ?? 0) > 0): ?>
                <form action="<?= base_url('cart/add') ?>" method="POST" class="mt-auto" x-data="{ 
                    quantity: 1,
                    selectedVariant: {},
                    variantOptions: <?= !empty($variants) ? json_encode($variants) : '[]' ?>
                }">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">

                    <!-- Variant Selector -->
                    <?php if (!empty($variants)): ?>
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Pilih Varian</h3>
                            <div class="space-y-3">
                                <?php
                                    // Format: {"attribute_name": ["val1","val2"]}
                                    $variantKeys = is_array($variants) ? $variants : [];
                                ?>
                                <?php foreach ($variantKeys as $attrName => $attrValues): ?>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 block mb-1.5">
                                            <?= ucfirst($attrName) ?>
                                        </label>
                                        <div class="flex flex-wrap gap-2">
                                            <?php foreach ($attrValues as $val): ?>
                                                <button type="button"
                                                    @click="selectedVariant['<?= $attrName ?>'] = '<?= $val ?>'"
                                                    :class="selectedVariant['<?= $attrName ?>'] === '<?= $val ?>' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300'"
                                                    class="px-3 py-2 border rounded-lg text-xs font-medium transition-colors">
                                                    <?= esc($val) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="variant_selection" :value="Object.keys(selectedVariant).length > 0 ? JSON.stringify(selectedVariant) : ''">
                        </div>
                    <?php endif; ?>

                    <!-- Quantity Selector -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-sm font-medium text-gray-700">Jumlah:</span>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                            <button type="button" @click="quantity = Math.max(1, quantity - 1)"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-50 transition-colors">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </button>
                            <input type="number" name="quantity" x-model.number="quantity" min="1" max="<?= $product['stock'] ?? 1 ?>"
                                class="w-16 text-center border-x border-gray-200 py-2 text-sm focus:outline-none">
                            <button type="button" @click="quantity = Math.min(<?= $product['stock'] ?? 1 ?>, quantity + 1)"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-50 transition-colors">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 flex items-center justify-center gap-2">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        Tambah ke Keranjang
                    </button>
                </form>
            <?php else: ?>
                <button disabled class="w-full py-3 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed text-sm font-medium mt-auto">
                    Stok Habis
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <i data-lucide="message-square" class="w-5 h-5 text-primary"></i>
            Ulasan Produk
            <?php if (($reviewCount ?? 0) > 0): ?>
                <span class="text-sm font-normal text-gray-400">(<?= $reviewCount ?? 0 ?>)</span>
            <?php endif; ?>
        </h2>

        <?php if (empty($reviews ?? [])): ?>
            <div class="text-center py-8">
                <i data-lucide="message-circle" class="w-12 h-12 text-gray-200 mx-auto mb-3"></i>
                <p class="text-sm text-gray-400">Belum ada ulasan</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($reviews ?? [] as $review): ?>
                    <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-medium text-gray-800 text-sm"><?= esc($review['customer_name'] ?? 'Pelanggan') ?></p>
                                <div class="flex items-center gap-0.5 mt-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $review['rating']): ?>
                                            <i data-lucide="star" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400"></i>
                                        <?php else: ?>
                                            <i data-lucide="star" class="w-3.5 h-3.5 text-gray-300"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php if (!empty($review['created_at'])): ?>
                                <span class="text-xs text-gray-400"><?= date('d M Y', strtotime($review['created_at'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($review['comment'])): ?>
                            <p class="text-sm text-gray-600"><?= nl2br(esc($review['comment'])) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bundle Components (jika produk bundle) -->
    <?php if (!empty($product['is_bundle'] ?? 0) && !empty($bundleComponents ?? [])): ?>
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i data-lucide="package-open" class="w-5 h-5 text-primary"></i>
            Isi Paket Bundling
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($bundleComponents as $comp): ?>
                <div class="flex items-center gap-4 p-3 bg-cream/50 rounded-lg border border-gray-100">
                    <div class="flex-shrink-0 w-16 h-16 rounded-lg bg-gray-100 overflow-hidden">
                        <?php if (!empty($comp['image'])): ?>
                            <img src="<?= base_url('uploads/products/' . $comp['image']) ?>"
                                alt="<?= esc($comp['name']) ?>"
                                class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i data-lucide="package" class="w-6 h-6 text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-800 line-clamp-1"><?= esc($comp['name']) ?></h4>
                        <p class="text-xs text-gray-500"><?= esc($comp['category_name'] ?? '') ?></p>
                        <p class="text-xs text-gray-600 mt-1">x<?= $comp['bundle_quantity'] ?? 1 ?> &middot; Rp <?= number_format($comp['price'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Up-selling: Produk Premium Serupa -->
    <?php if (!empty($premiumProducts ?? [])): ?>
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i data-lucide="trending-up" class="w-5 h-5 text-primary"></i>
            Produk Premium Serupa
        </h2>
        <p class="text-sm text-gray-500 mb-6">Upgrade ke produk premium untuk hasil yang lebih baik</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($premiumProducts as $pp): ?>
                <a href="<?= base_url('product/' . ($pp['id'] ?? '')) ?>" class="group bg-cream/50 rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 overflow-hidden relative">
                        <?php if (!empty($pp['image'] ?? '')): ?>
                            <img src="<?= base_url('uploads/products/' . ($pp['image'] ?? '')) ?>"
                                alt="<?= esc($pp['name']) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                                <i data-lucide="package" class="w-10 h-10 text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                        <span class="absolute top-2 left-2 bg-gradient-to-r from-yellow-500 to-amber-500 text-white text-xs font-medium px-2 py-0.5 rounded-full">Premium</span>
                    </div>
                    <div class="p-3">
                        <h4 class="font-medium text-gray-800 text-sm line-clamp-1"><?= esc($pp['name'] ?? '') ?></h4>
                        <p class="text-primary font-bold text-sm mt-1">Rp <?= number_format($pp['price'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cross-selling: Pelanggan Juga Membeli -->
    <?php if (!empty($crossSellProducts ?? [])): ?>
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-primary"></i>
            Pelanggan Juga Membeli
        </h2>
        <p class="text-sm text-gray-500 mb-6">Produk yang sering dibeli bersamaan dengan produk ini</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($crossSellProducts as $cp): ?>
                <a href="<?= base_url('product/' . ($cp['id'] ?? '')) ?>" class="group bg-cream/50 rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 overflow-hidden">
                        <?php if (!empty($cp['image'] ?? '')): ?>
                            <img src="<?= base_url('uploads/products/' . ($cp['image'] ?? '')) ?>"
                                alt="<?= esc($cp['name']) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                                <i data-lucide="package" class="w-10 h-10 text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-3">
                        <h4 class="font-medium text-gray-800 text-sm line-clamp-1"><?= esc($cp['name'] ?? '') ?></h4>
                        <p class="text-primary font-bold text-sm mt-1">Rp <?= number_format($cp['price'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>