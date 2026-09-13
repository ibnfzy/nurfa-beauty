<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $cartItems */
/** @var int|float $total */
/** @var bool $stockWarning */
?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <?= view('components/breadcrumb', [
        'items' => [
            ['label' => 'Beranda', 'url' => base_url()],
            ['label' => 'Keranjang Belanja'],
        ]
    ]) ?>

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Keranjang Belanja</h1>

    <!-- Panduan Belanja -->
    <section class="rounded-2xl border border-primary/10 bg-white p-5 sm:p-6 mb-6 shadow-sm" aria-labelledby="panduan-belanja-title">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-primary mb-2">Panduan Belanja</p>
            <h2 id="panduan-belanja-title" class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">Cara menyelesaikan pesanan</h2>
            <p class="text-sm text-gray-600 leading-relaxed">Ikuti langkah berikut agar proses pemesanan berjalan lancar sampai pembayaran selesai.</p>
        </div>

        <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <li class="rounded-xl bg-cream/60 border border-primary/10 p-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mb-3">1</span>
                <h3 class="font-semibold text-gray-900 mb-1">Periksa keranjang</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Pastikan produk, varian, dan jumlah yang dipilih sudah sesuai.</p>
            </li>
            <li class="rounded-xl bg-cream/60 border border-primary/10 p-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mb-3">2</span>
                <h3 class="font-semibold text-gray-900 mb-1">Lanjutkan checkout</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Klik tombol checkout, lalu pilih alamat dan metode pengiriman.</p>
            </li>
            <li class="rounded-xl bg-cream/60 border border-primary/10 p-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mb-3">3</span>
                <h3 class="font-semibold text-gray-900 mb-1">Tinjau pesanan</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Periksa kembali detail pesanan dan total pembayaran sebelum melanjutkan.</p>
            </li>
            <li class="rounded-xl bg-cream/60 border border-primary/10 p-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mb-3">4</span>
                <h3 class="font-semibold text-gray-900 mb-1">Selesaikan pembayaran</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Pilih metode pembayaran dan selesaikan pembayaran agar pesanan diproses.</p>
            </li>
        </ol>

        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <p class="font-semibold text-sm sm:text-base mb-2">Peringatan ketersediaan stok</p>
            <p class="text-sm sm:text-base leading-relaxed">Ketika Anda sudah memasukkan produk ke dalam keranjang dan tidak segera melakukan checkout, kami tidak bertanggung jawab jika stok produk tersebut habis dan Anda tidak dapat membelinya karena ada pembeli lain yang melakukan checkout lebih dahulu. Produk yang hanya dimasukkan ke dalam keranjang tetapi belum selesai checkout tidak akan menyimpan stok barang secara permanen. Jika ada pembeli lain yang menyelesaikan proses checkout lebih awal, Anda tidak akan dapat membeli produk tersebut meskipun produk itu masih ada di dalam keranjang Anda.</p>
        </div>
    </section>

    <?php if (empty($cartItems ?? [])): ?>
        <!-- Empty State -->
        <?= view('components/empty-state', [
            'icon'        => 'shopping-cart',
            'title'       => 'Keranjang kosong',
            'description' => 'Mulai belanja dan tambahkan produk ke keranjang Anda.',
            'actionText'  => 'Mulai Belanja',
            'actionUrl'   => base_url('/'),
        ]) ?>
    <?php else: ?>

        <?php if ($stockWarning ?? false): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-warning flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-yellow-800">Perhatian Stok</p>
                    <p class="text-sm text-yellow-700 mt-1">Beberapa produk di keranjang Anda melebihi stok yang tersedia. Silakan sesuaikan jumlah sebelum melanjutkan ke checkout.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <?php foreach ($cartItems ?? [] as $item): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <?php if (!empty($item['product_image'] ?? '')): ?>
                                    <img src="<?= base_url('uploads/products/' . ($item['product_image'] ?? '')) ?>"
                                        alt="<?= esc($item['product_name'] ?? '') ?>"
                                        class="w-24 h-24 rounded-lg object-cover border border-gray-100">
                                <?php else: ?>
                                    <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i data-lucide="image" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-800 mb-1"><?= esc($item['product_name'] ?? '') ?></h3>
                                <p class="text-primary font-bold text-sm mb-3">
                                    Rp <?= number_format($item['product_price'] ?? 0, 0, ',', '.') ?>
                                </p>
                                <?php $variantSelection = $item['variant_selection'] ?? null; ?>
                                <?php if (!empty($variantSelection)): ?>
                                    <?php $variantSelection = is_string($variantSelection) ? json_decode($variantSelection, true) : $variantSelection; ?>
                                    <?php if (is_array($variantSelection)): ?>
                                        <p class="text-xs text-gray-500 mb-3">
                                            Varian:
                                            <?= esc(implode(', ', array_map(static fn ($key, $value) => $key . ': ' . $value, array_keys($variantSelection), $variantSelection))) ?>
                                        </p>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Stock Warning -->
                                <?php if (($item['quantity'] ?? 0) > ($item['product_stock'] ?? 0)): ?>
                                    <p class="text-xs text-danger mb-2">
                                        <i data-lucide="alert-circle" class="w-3 h-3 inline"></i>
                                        Stok tersisa: <?= $item['product_stock'] ?? 0 ?>
                                    </p>
                                <?php endif; ?>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <!-- Quantity Controls -->
                                    <form action="<?= base_url('cart/update') ?>" method="POST" class="flex items-center gap-2">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="cart_id" value="<?= $item['id'] ?? 0 ?>">

                                        <button type="submit" name="quantity" value="<?= max(1, ($item['quantity'] ?? 1) - 1) ?>"
                                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-cream transition-colors text-gray-600"
                                            title="Kurangi">
                                            <i data-lucide="minus" class="w-4 h-4"></i>
                                        </button>

                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?? 1 ?>"
                                            min="1" max="<?= $item['product_stock'] ?? 1 ?>"
                                            class="w-16 h-8 text-center border border-gray-200 rounded-lg text-sm font-medium focus:border-primary focus:ring-1 focus:ring-primary">

                                        <button type="submit" class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-cream transition-colors text-gray-600"
                                            title="Tambah">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                        </button>
                                    </form>

                                    <!-- Subtotal & Remove -->
                                    <div class="flex items-center justify-between sm:gap-4">
                                        <p class="text-sm font-semibold text-gray-800">
                                            Subtotal: Rp <?= number_format($item['subtotal'] ?? 0, 0, ',', '.') ?>
                                        </p>

                                        <form action="<?= base_url('cart/remove') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="cart_id" value="<?= $item['id'] ?? 0 ?>">
                                            <button type="submit"
                                                class="p-2 rounded-lg text-danger hover:bg-red-50 transition-colors"
                                                title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Belanja</h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Barang (<?= count($cartItems ?? []) ?> item)</span>
                            <span class="font-medium text-gray-800">Rp <?= number_format($total ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">Total</span>
                            <span class="text-lg font-bold text-primary">Rp <?= number_format($total ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <?php if ($stockWarning ?? false): ?>
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs text-yellow-700 text-center">Sesuaikan jumlah produk yang melebihi stok terlebih dahulu.</p>
                        </div>
                        <button disabled class="w-full py-3 rounded-lg bg-gray-300 text-gray-500 font-medium cursor-not-allowed text-sm">
                            Lanjut ke Checkout
                        </button>
                    <?php else: ?>
                        <a href="<?= base_url('checkout') ?>"
                            class="w-full py-3 rounded-lg bg-gradient-to-r from-primary to-primary-dark text-white font-medium hover:shadow-md transition-all text-center block text-sm">
                            Lanjut ke Checkout
                        </a>
                    <?php endif; ?>

                    <a href="<?= base_url('/') ?>"
                        class="w-full mt-3 py-2.5 rounded-lg border border-primary text-primary font-medium hover:bg-primary-light transition-colors text-center block text-sm">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>