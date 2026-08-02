<?php
/**
 * Product Card component
 * Usage: view('components/product-card', ['product' => $product])
 */
$product = $product ?? null;
if ($product):
    $imageUrl = !empty($product['image'])
        ? base_url('uploads/products/' . $product['image'])
        : 'https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=beauty+product+cosmetics+cream+bottle+clean+white+background+professional+product+photography&image_size=square';
?>
<div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden group hover:shadow-md transition-shadow">
    <a href="<?= base_url('product/' . ($product['slug'] ?? $product['id'])) ?>">
        <div class="relative aspect-square overflow-hidden">
            <img src="<?= $imageUrl ?>"
                 alt="<?= esc($product['name'] ?? '') ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <?php if (!empty($product['promo_badge'])): ?>
                <span class="absolute top-2 left-2 bg-danger text-white text-xs px-2 py-0.5 rounded-full font-medium">
                    <?= $product['promo_badge'] ?>
                </span>
            <?php endif; ?>
            <button class="absolute top-2 right-2 w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors wishlist-btn"
                    data-product-id="<?= $product['id'] ?>" title="Tambah ke Wishlist">
                <i data-lucide="heart" class="w-4 h-4 text-gray-400 hover:text-danger"></i>
            </button>
        </div>
    </a>
    <div class="p-4">
        <?php if (!empty($product['category_name'])): ?>
            <p class="text-xs text-secondary font-medium mb-1"><?= esc($product['category_name']) ?></p>
        <?php endif; ?>
        <a href="<?= base_url('product/' . ($product['slug'] ?? $product['id'])) ?>">
            <h3 class="font-medium text-gray-800 text-sm line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                <?= esc($product['name'] ?? '') ?>
            </h3>
        </a>
        <div class="flex items-center justify-between">
            <p class="text-primary font-bold text-sm">
                Rp <?= number_format($product['price'] ?? 0, 0, ',', '.') ?>
            </p>
            <?php if (!empty($product['rating'])): ?>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <i data-lucide="star" class="w-3 h-3 text-yellow-400 fill-yellow-400"></i>
                    <?= number_format($product['rating'], 1) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (isset($product['stock']) && $product['stock'] <= 5 && $product['stock'] > 0): ?>
            <p class="text-xs text-warning mt-1">Stok terbatas (<?= $product['stock'] ?>)</p>
        <?php elseif (isset($product['stock']) && $product['stock'] == 0): ?>
            <p class="text-xs text-danger mt-1">Habis</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
