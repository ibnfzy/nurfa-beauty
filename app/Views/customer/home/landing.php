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

<!-- Promo Strip: voucher selamat datang -->
<?php if (!empty($welcomeVoucher)): ?>
<div x-data="{ copied: false }" class="w-full bg-primary-dark py-2 px-4 flex items-center justify-center gap-3 text-white text-sm">
    <i data-lucide="ticket" class="w-4 h-4 flex-shrink-0"></i>
    <span class="font-medium">Voucher pertamamu:</span>
    <code class="font-bold tracking-widest bg-white/20 px-2 py-0.5 rounded select-all">
        <?= esc($welcomeVoucher['code'] ?? $welcomeVoucher) ?>
    </code>
    <button @click="
        navigator.clipboard.writeText('<?= esc($welcomeVoucher['code'] ?? $welcomeVoucher) ?>');
        copied = true; setTimeout(() => copied = false, 2000);
    " class="flex items-center gap-1 text-xs border border-white/40 rounded px-2 py-0.5 hover:bg-white/10 transition-colors">
        <i data-lucide="copy" class="w-3 h-3" x-show="!copied"></i>
        <i data-lucide="check" class="w-3 h-3" x-show="copied" x-cloak></i>
        <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
    </button>
</div>
<?php endif; ?>

<!-- Hero Split: headline kiri + ilustrasi kanan -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <!-- Kiri: headline + CTA -->
        <div class="fade-up order-2 md:order-1">
            <span class="text-xs font-poppins font-semibold uppercase tracking-widest text-primary-dark mb-4 block">
                Nurfa Beauty
            </span>
            <h1 class="text-4xl sm:text-5xl font-playfair font-bold text-gray-900 leading-tight mb-5">
                Cantik dari<br>dalam &amp; luar
            </h1>
            <p class="text-gray-500 text-sm sm:text-base leading-relaxed mb-8 max-w-md">
                Produk kecantikan berkualitas untuk perawatan kulit, rambut, dan tubuh. Kualitas terjamin, harga terjangkau.
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?= base_url('catalog') ?>"
                   class="inline-flex items-center gap-2 bg-primary text-white font-semibold px-6 py-3 rounded-xl shadow-sm hover:bg-primary-dark transition-colors text-sm">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    Mulai Belanja
                </a>
                <a href="<?= base_url('catalog') ?>"
                   class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-xl hover:border-primary hover:text-primary transition-colors text-sm">
                    Lihat Katalog
                </a>
            </div>
        </div>
        <!-- Hallmark · component: hero-product-slideshow · genre: playful · tone: soft
             states: default · hover · focus · active -->
        <!-- Kanan: slideshow produk hero -->
        <?php
        // Ambil maks. 5 produk untuk slideshow (latestProducts sudah tersedia)
        $heroSlides = array_values(array_filter(
            array_slice(!empty($latestProducts) ? $latestProducts : [], 0, 5),
            fn($p) => !empty($p['image'])
        ));
        // Fallback: gunakan foto produk default jika tidak ada data
        if (empty($heroSlides)) {
            $heroSlides = [[
                'id'            => null,
                'name'          => 'Produk Nurfa Beauty',
                'category_name' => '',
                'price'         => null,
                'image'         => '1785649634_ee7adeaf1d8789615eea.jpeg',
            ]];
        }
        ?>
        <div
            class="fade-up order-1 md:order-2"
            style="animation-delay:0.1s"
            x-data="{
                current: 0,
                total: <?= count($heroSlides) ?>,
                timer: null,
                start() {
                    this.timer = setInterval(() => {
                        this.current = (this.current + 1) % this.total;
                    }, 3000);
                },
                stop()  { clearInterval(this.timer); },
                goto(i) { this.current = i; }
            }"
            x-init="start()"
            @mouseenter="stop()"
            @mouseleave="start()"
        >
            <!-- Wrapper: relative + aspect ratio agar slide absolute bisa berfungsi -->
            <div class="relative w-full aspect-square sm:aspect-[4/3] rounded-2xl overflow-hidden bg-primary-light/20">
            <?php foreach ($heroSlides as $idx => $slide): ?>
            <?php $href = !empty($slide['id']) ? base_url('product/' . $slide['id']) : base_url('catalog'); ?>
            <a
                href="<?= $href ?>"
                :class="current === <?= $idx ?>
                    ? 'opacity-100 z-10 pointer-events-auto'
                    : 'opacity-0 z-0 pointer-events-none'"
                class="absolute inset-0 rounded-2xl overflow-hidden
                       transition-opacity duration-500 ease-in-out
                       group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2"
                aria-label="Lihat produk <?= esc($slide['name']) ?>"
            >
                <!-- Gambar -->
                <img
                    src="<?= base_url('uploads/products/' . $slide['image']) ?>"
                    alt="<?= esc($slide['name']) ?>"
                    class="w-full h-full object-cover object-center
                           group-hover:scale-[1.03] transition-transform duration-500"
                    loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>"
                >

                <!-- Info overlay: selalu tampil, background gelap solid -->
                <div class="absolute bottom-0 inset-x-0
                            bg-gradient-to-t from-black/75 via-black/40 to-transparent
                            px-4 pt-10 pb-4">
                    <?php if (!empty($slide['category_name'])): ?>
                    <span class="text-xs font-poppins font-semibold uppercase tracking-widest text-white/70 block mb-1">
                        <?= esc($slide['category_name']) ?>
                    </span>
                    <?php endif; ?>
                    <p class="font-playfair font-bold text-white text-base leading-snug line-clamp-2 drop-shadow-sm">
                        <?= esc($slide['name']) ?>
                    </p>
                    <?php if (!empty($slide['price'])): ?>
                    <p class="text-sm font-poppins font-semibold text-white mt-1 drop-shadow-sm">
                        Rp <?= number_format($slide['price'], 0, ',', '.') ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Hover CTA pill -->
                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100
                            transition-opacity duration-200">
                    <span class="inline-flex items-center gap-1 bg-white/90 text-gray-800 text-xs font-semibold
                                 px-3 py-1.5 rounded-full shadow-sm">
                        Lihat Detail
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
            </div><!-- /wrapper -->

            <!-- Dot indicators -->
            <?php if (count($heroSlides) > 1): ?>
            <div class="flex items-center justify-center gap-1.5 mt-3" role="tablist" aria-label="Pilih produk">
                <?php foreach ($heroSlides as $idx => $slide): ?>
                <button
                    @click="goto(<?= $idx ?>)"
                    :class="current === <?= $idx ?>
                        ? 'bg-primary w-5'
                        : 'bg-gray-300 hover:bg-gray-400 w-2'"
                    class="h-2 rounded-full transition-all duration-300
                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50"
                    role="tab"
                    :aria-selected="current === <?= $idx ?>"
                    aria-label="Produk <?= $idx + 1 ?>"
                ></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Kategori Chip: scroll horizontal -->
<?php if (!empty($categories)): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 mb-12">
    <h2 class="text-xl font-playfair font-semibold text-gray-800 mb-4">Kategori</h2>
    <?php
    $categoryIcons = [
        'Perawatan Rambut' => 'scissors',
        'Wajah'            => 'sparkles',
        'Badan'            => 'heart',
        'Rambut'           => 'scissors',
        'Kulit'            => 'sparkles',
    ];
    ?>
    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
        <a href="<?= base_url('catalog') ?>"
           class="flex-shrink-0 flex items-center gap-2 border border-gray-200 bg-white text-gray-700 text-sm font-medium px-4 py-2 rounded-full hover:border-primary hover:text-primary transition-colors">
            <i data-lucide="grid" class="w-4 h-4"></i>
            Semua
        </a>
        <?php foreach ($categories as $cat): ?>
            <?php $icon = $categoryIcons[$cat['name']] ?? 'tag'; ?>
            <a href="<?= base_url('catalog?category=' . $cat['id']) ?>"
               class="flex-shrink-0 flex items-center gap-2 border border-gray-200 bg-white text-gray-700 text-sm font-medium px-4 py-2 rounded-full hover:border-primary hover:text-primary transition-colors">
                <i data-lucide="<?= $icon ?>" class="w-4 h-4"></i>
                <?= esc($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Flash Deal / Promo Spesial -->
<?php if (!empty($promotions)): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 mb-12">
    <!-- Strip header -->
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <span class="bg-warning/20 text-warning-700 text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full border border-warning/30">
                Flash Deal
            </span>
            <h2 class="text-xl font-playfair font-semibold text-gray-800">Promo Spesial</h2>
        </div>
        <i data-lucide="sparkles" class="w-5 h-5 text-primary"></i>
    </div>

    <div class="flex gap-4 overflow-x-auto pb-3 scrollbar-hide">
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
        ?>
        <?php foreach ($promotions as $promo): ?>
            <?php $type = $promo['type'] ?? 'discount'; ?>
            <div class="flex-shrink-0 w-64 bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <span class="font-semibold text-gray-800 text-sm line-clamp-1 flex-1 mr-2"><?= esc($promo['name'] ?? '') ?></span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0 <?= $typeColors[$type] ?? 'bg-gray-100 text-gray-700' ?>">
                        <?= $typeLabels[$type] ?? ucfirst($type) ?>
                    </span>
                </div>
                <div class="px-4 py-3">
                    <div class="flex items-baseline gap-1 mb-1">
                        <?php if (($promo['discount_type'] ?? '') === 'percentage'): ?>
                            <span class="text-2xl font-bold text-primary"><?= esc($promo['discount_value'] ?? 0) ?>%</span>
                        <?php else: ?>
                            <span class="text-xl font-bold text-primary">Rp <?= number_format($promo['discount_value'] ?? 0, 0, ',', '.') ?></span>
                        <?php endif; ?>
                        <span class="text-xs text-gray-400 font-medium">OFF</span>
                    </div>
                    <?php if (!empty($promo['min_purchase'])): ?>
                        <p class="text-xs text-gray-400">Min. Rp <?= number_format($promo['min_purchase'], 0, ',', '.') ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-400 mt-2">
                        s/d <?= date('d M Y', strtotime($promo['end_date'])) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Produk Terbaru -->
<?php if (!empty($latestProducts)): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 mb-12">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-playfair font-semibold text-gray-800">Produk Terbaru</h2>
        <a href="<?= base_url('catalog') ?>" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
            Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <?php foreach ($latestProducts as $product): ?>
            <a href="<?= base_url('product/' . ($product['id'] ?? '')) ?>"
               class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow fade-up">
                <div class="aspect-square bg-gray-50 overflow-hidden relative">
                    <?php if (!empty($product['image'] ?? '')): ?>
                        <img src="<?= base_url('uploads/products/' . ($product['image'] ?? '')) ?>"
                             alt="<?= esc($product['name']) ?>"
                             class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i data-lucide="package" class="w-10 h-10 text-gray-200"></i>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($product['stock']) && $product['stock'] <= 5 && $product['stock'] > 0): ?>
                        <span class="absolute top-2 left-2 bg-orange-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">Terbatas</span>
                    <?php endif; ?>
                </div>
                <div class="p-3">
                    <?php if (!empty($product['category_name'])): ?>
                        <span class="text-xs text-primary font-medium uppercase tracking-wide"><?= esc($product['category_name']) ?></span>
                    <?php endif; ?>
                    <h3 class="text-gray-800 text-sm font-semibold mt-1 line-clamp-2 leading-snug"><?= esc($product['name'] ?? '') ?></h3>
                    <p class="text-primary font-bold mt-2 text-sm">Rp <?= number_format($product['price'] ?? 0, 0, ',', '.') ?></p>
                    <?php if (isset($product['stock']) && $product['stock'] == 0): ?>
                        <p class="text-xs text-danger mt-1">Habis</p>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Rekomendasi untuk Anda -->
<?php if (!empty($recommendedProducts ?? [])): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 mb-12">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-playfair font-semibold text-gray-800">Rekomendasi untuk Anda</h2>
        <a href="<?= base_url('catalog') ?>" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
            Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <?php foreach ($recommendedProducts as $product): ?>
            <a href="<?= base_url('product/' . ($product['id'] ?? '')) ?>"
               class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="aspect-square bg-gray-50 overflow-hidden relative">
                    <?php if (!empty($product['image'] ?? '')): ?>
                        <img src="<?= base_url('uploads/products/' . ($product['image'] ?? '')) ?>"
                             alt="<?= esc($product['name']) ?>"
                             class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i data-lucide="package" class="w-10 h-10 text-gray-200"></i>
                        </div>
                    <?php endif; ?>
                    <span class="absolute top-2 left-2 bg-primary text-white text-xs font-semibold px-2 py-0.5 rounded-full">Pilihan</span>
                </div>
                <div class="p-3">
                    <?php if (!empty($product['category_name'])): ?>
                        <span class="text-xs text-primary font-medium uppercase tracking-wide"><?= esc($product['category_name']) ?></span>
                    <?php endif; ?>
                    <h3 class="text-gray-800 text-sm font-semibold mt-1 line-clamp-2 leading-snug"><?= esc($product['name'] ?? '') ?></h3>
                    <p class="text-primary font-bold mt-2 text-sm">Rp <?= number_format($product['price'] ?? 0, 0, ',', '.') ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Paket Hemat -->
<?php if (!empty($bundleProducts ?? [])): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 mb-12">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-playfair font-semibold text-gray-800">Paket Hemat</h2>
        <i data-lucide="package-open" class="w-5 h-5 text-primary"></i>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <?php foreach ($bundleProducts as $bundle): ?>
            <a href="<?= base_url('product/' . ($bundle['id'] ?? '')) ?>"
               class="group bg-white rounded-xl border border-primary-light/40 overflow-hidden hover:shadow-md transition-shadow">
                <div class="aspect-square bg-gray-50 overflow-hidden relative">
                    <?php if (!empty($bundle['image'] ?? '')): ?>
                        <img src="<?= base_url('uploads/products/' . ($bundle['image'] ?? '')) ?>"
                             alt="<?= esc($bundle['name']) ?>"
                             class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i data-lucide="package" class="w-10 h-10 text-gray-200"></i>
                        </div>
                    <?php endif; ?>
                    <span class="absolute top-2 left-2 bg-secondary text-white text-xs font-semibold px-2 py-0.5 rounded-full">Bundling</span>
                    <?php if (($bundle['bundle_discount'] ?? 0) > 0): ?>
                        <span class="absolute top-2 right-2 bg-danger text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            -<?= $bundle['bundle_discount'] ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <div class="p-3">
                    <?php if (!empty($bundle['category_name'])): ?>
                        <span class="text-xs text-primary font-medium uppercase tracking-wide"><?= esc($bundle['category_name']) ?></span>
                    <?php endif; ?>
                    <h3 class="text-gray-800 text-sm font-semibold mt-1 line-clamp-2 leading-snug"><?= esc($bundle['name'] ?? '') ?></h3>
                    <p class="text-primary font-bold mt-2 text-sm">Rp <?= number_format($bundle['price'] ?? 0, 0, ',', '.') ?></p>
                    <?php if (($bundle['bundle_discount'] ?? 0) > 0): ?>
                        <p class="text-xs text-success mt-1 font-medium">Hemat <?= $bundle['bundle_discount'] ?>%</p>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- CTA Band: full-bleed solid primary -->
<section class="bg-primary py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl font-playfair font-bold text-white mb-3">
            Temukan produk terbaik untukmu
        </h2>
        <p class="text-white/80 text-sm sm:text-base max-w-lg mx-auto mb-7">
            Jelajahi koleksi lengkap produk kecantikan kami dan dapatkan yang terbaik.
        </p>
        <a href="<?= base_url('catalog') ?>"
           class="inline-flex items-center gap-2 border-2 border-white text-white font-semibold px-8 py-3 mt-4 rounded-xl hover:bg-white hover:text-primary transition-colors text-sm sm:text-base">
            <i data-lucide="store" class="w-4 h-4"></i>
            Lihat Katalog
        </a>
    </div>
</section>

<?php if (!session()->get('logged_in')): ?>
<!-- Promo popup: di-inject langsung ke body via JS agar bebas dari stacking context -->
<script>
(function() {
    if (localStorage.getItem('nurfa_promo_popup')) return;

    var registerUrl = '<?= base_url('auth/register') ?>';
    var loginUrl    = '<?= base_url('auth/login') ?>';

    function dismiss() {
        localStorage.setItem('nurfa_promo_popup', '1');
        var el = document.getElementById('nurfa-promo-modal');
        if (el) {
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 200);
        }
    }

    function buildModal() {
        var wrap = document.createElement('div');
        wrap.id = 'nurfa-promo-modal';
        wrap.style.cssText = 'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;transition:opacity .2s ease;';
        wrap.innerHTML = [
            '<div id="nurfa-promo-backdrop" style="position:absolute;inset:0;background:rgba(0,0,0,.45);" onclick="window.__nurfa_dismiss()"></div>',
            '<div style="position:relative;width:calc(100% - 2rem);max-width:28rem;background:#fff;border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;',
                 'transform:scale(.95);transition:transform .3s ease,opacity .3s ease;opacity:0;" id="nurfa-promo-panel">',
                '<div style="height:6px;background:linear-gradient(90deg,var(--color-primary,#e8a0bf),var(--color-secondary,#b76e9b),var(--color-primary,#e8a0bf));"></div>',
                '<div style="padding:1.5rem 1.5rem 1.75rem;position:relative;">',
                    '<button onclick="window.__nurfa_dismiss()" aria-label="Tutup" ',
                        'style="position:absolute;top:1rem;right:1rem;border:none;background:none;cursor:pointer;color:#9ca3af;font-size:1.25rem;line-height:1;">&#x2715;</button>',
                    '<div style="width:2.75rem;height:2.75rem;border-radius:50%;background:rgba(232,160,191,.15);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">',
                        '<i data-lucide="gift" style="width:1.25rem;height:1.25rem;color:var(--color-primary,#e8a0bf);"></i>',
                    '</div>',
                    '<h2 style="font-family:Playfair Display,serif;font-size:1.25rem;font-weight:700;color:#111827;margin:0 0 .5rem;">Promo khusus akun baru</h2>',
                    '<p style="font-size:.875rem;color:#6b7280;line-height:1.6;margin:0 0 1.25rem;">',
                        'Daftar sekarang dan dapatkan <strong style="color:var(--color-primary,#e8a0bf);">diskon 10%</strong> untuk pembelian pertamamu. Berlaku 7 hari setelah registrasi.',
                    '</p>',
                    '<div style="display:flex;gap:.5rem;flex-wrap:wrap;">',
                        '<a href="'+registerUrl+'" style="flex:1;min-width:8rem;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;',
                            'background:var(--color-primary,#e8a0bf);color:#fff;font-weight:600;font-size:.875rem;',
                            'padding:.625rem 1.25rem;border-radius:.75rem;text-decoration:none;border:none;">',
                            '<i data-lucide="user-plus" style="width:1rem;height:1rem;"></i> Daftar Sekarang',
                        '</a>',
                        '<button onclick="window.__nurfa_dismiss()" style="flex:1;min-width:8rem;font-size:.875rem;color:#6b7280;',
                            'padding:.625rem 1.25rem;border-radius:.75rem;border:1px solid #e5e7eb;background:none;cursor:pointer;">',
                            'Nanti saja',
                        '</button>',
                    '</div>',
                    '<p style="font-size:.75rem;color:#9ca3af;text-align:center;margin:.75rem 0 0;">',
                        'Sudah punya akun? <a href="'+loginUrl+'" style="color:var(--color-primary,#e8a0bf);font-weight:500;">Masuk di sini</a>',
                    '</p>',
                '</div>',
            '</div>',
        ].join('');
        return wrap;
    }

    window.__nurfa_dismiss = dismiss;

    setTimeout(function() {
        var modal = buildModal();
        document.body.appendChild(modal);
        // Init lucide icons pada elemen baru
        if (window.lucide) lucide.createIcons({ nodes: [modal] });
        // Trigger enter animation
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                modal.style.opacity = '1';
                var panel = document.getElementById('nurfa-promo-panel');
                if (panel) { panel.style.transform = 'scale(1)'; panel.style.opacity = '1'; }
            });
        });
    }, 3000);
})();
</script>
<?php endif; ?>

<?= $this->endSection() ?>
