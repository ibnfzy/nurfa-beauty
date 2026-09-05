<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $cartItems */
/** @var array $addresses */
/** @var array|null $defaultAddress */
/** @var array $bankAccounts */
/** @var int|float $totalAmount */
/** @var int|float $shippingCost */
/** @var int|float $discountAmount */
/** @var string|null $voucherCode */
/** @var array|null $firstPurchaseVoucher */
/** @var int|float $finalAmount */
/** @var int $basePoints */
/** @var int $bonusPoints */
/** @var array $crossSellProducts */
/** @var array|null $appliedVoucher */
/** @var string|null $voucherCodeInput */
/** @var int $monthlyProductCount */
/** @var array|null $behaviorReward */
?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <?= view('components/breadcrumb', [
        'items' => [
            ['label' => 'Beranda', 'url' => base_url()],
            ['label' => 'Keranjang', 'url' => base_url('cart')],
            ['label' => 'Checkout'],
        ]
    ]) ?>

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Checkout</h1>

    <!-- Step Indicators -->
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center gap-0">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-medium">1</div>
                <span class="ml-2 text-sm font-medium text-primary hidden sm:inline">Alamat Pengiriman</span>
            </div>
            <div class="w-12 h-px bg-primary mx-2"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-medium">2</div>
                <span class="ml-2 text-sm font-medium text-primary hidden sm:inline">Ringkasan Pesanan</span>
            </div>
            <div class="w-12 h-px bg-primary mx-2"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-medium">3</div>
                <span class="ml-2 text-sm font-medium text-primary hidden sm:inline">Pembayaran</span>
            </div>
        </div>
    </div>

    <form action="<?= base_url('checkout/process') ?>" method="POST">
        <?= csrf_field() ?>
        <?php if (!empty($voucherCodeInput ?? '')): ?>
            <input type="hidden" name="voucher_code" value="<?= esc($voucherCodeInput) ?>">
        <?php endif; ?>

        <div x-data="{
            selectedAddressId: <?= $defaultAddress['id'] ?? 0 ?>,
            showAddressList: false
        }">
            <input type="hidden" name="shipping_address_id" :value="selectedAddressId">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Step 1: Alamat Pengiriman -->
                    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-medium">1</div>
                            <h2 class="text-lg font-semibold text-gray-800">Alamat Pengiriman</h2>
                        </div>

                        <!-- Selected Address -->
                        <template x-for="addr in <?= esc(json_encode($addresses ?? []), 'attr') ?>" :key="addr.id">
                            <div x-show="selectedAddressId == addr.id"
                                class="border border-primary-light/50 rounded-lg p-4 bg-primary-light/10">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-medium text-gray-800" x-text="addr.recipient_name"></span>
                                            <span x-show="addr.is_default"
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-light text-primary-dark">
                                                Utama
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"
                                                x-text="addr.label"></span>
                                        </div>
                                        <p class="text-sm text-gray-600" x-text="addr.phone"></p>
                                        <p class="text-sm text-gray-500 mt-1"
                                            x-text="addr.address + ', ' + (addr.district || '') + ', ' + addr.city + ', ' + addr.province + ' ' + addr.postal_code"></p>
                                    </div>
                                    <button type="button" @click="showAddressList = !showAddressList"
                                        class="text-sm text-primary hover:text-primary-dark font-medium">
                                        Ganti
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Address List (dropdown) -->
                        <div x-show="showAddressList" x-transition class="mt-4 space-y-3">
                            <p class="text-sm font-medium text-gray-600 mb-2">Pilih alamat lain:</p>
                            <?php foreach ($addresses ?? [] as $addr): ?>
                                <div @click="selectedAddressId = <?= $addr['id'] ?? 0 ?>; showAddressList = false"
                                    class="border rounded-lg p-4 cursor-pointer transition-colors"
                                    :class="selectedAddressId == <?= $addr['id'] ?? 0 ?> ? 'border-primary bg-primary-light/10' : 'border-gray-200 hover:border-primary-light'">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-800"><?= esc($addr['recipient_name'] ?? '') ?></span>
                                        <?php if ($addr['is_default'] ?? false): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-light text-primary-dark">Utama</span>
                                        <?php endif; ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><?= esc($addr['label'] ?? '') ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600"><?= esc($addr['phone'] ?? '') ?></p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <?= esc($addr['address'] ?? '') ?><?= !empty($addr['district'] ?? '') ? ', ' . esc($addr['district']) : '' ?>,
                                        <?= esc($addr['city'] ?? '') ?>, <?= esc($addr['province'] ?? '') ?> <?= esc($addr['postal_code'] ?? '') ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 2: Ringkasan Pesanan -->
                    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-medium">2</div>
                            <h2 class="text-lg font-semibold text-gray-800">Ringkasan Pesanan</h2>
                        </div>

                        <div class="space-y-4">
                            <?php foreach ($cartItems ?? [] as $item): ?>
                                <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
                                    <div class="flex-shrink-0">
                                        <?php if (!empty($item['product_image'] ?? '')): ?>
                                            <img src="<?= base_url('uploads/products/' . ($item['product_image'] ?? '')) ?>"
                                                alt="<?= esc($item['product_name'] ?? '') ?>"
                                                class="w-16 h-16 rounded-lg object-cover border border-gray-100">
                                        <?php else: ?>
                                            <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <i data-lucide="image" class="w-6 h-6 text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-800"><?= esc($item['product_name'] ?? '') ?></h4>
                                        <p class="text-xs text-gray-500 mt-0.5"><?= $item['quantity'] ?? 0 ?> x Rp <?= number_format($item['product_price'] ?? 0, 0, ',', '.') ?></p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        Rp <?= number_format(($item['product_price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.') ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 3: Pembayaran -->
                    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-medium">3</div>
                            <h2 class="text-lg font-semibold text-gray-800">Pembayaran</h2>
                        </div>

                        <p class="text-sm text-gray-600 mb-4">Silakan transfer ke salah satu rekening berikut:</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <?php foreach ($bankAccounts ?? [] as $bank): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-light transition-colors">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-lg bg-primary-light flex items-center justify-center">
                                            <i data-lucide="landmark" class="w-5 h-5 text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm"><?= esc($bank['bank_name'] ?? '') ?></p>
                                            <p class="text-xs text-gray-500">a.n. <?= esc($bank['account_name'] ?? '') ?></p>
                                        </div>
                                    </div>
                                    <p class="text-lg font-bold text-gray-800 tracking-wide"><?= esc($bank['account_number'] ?? '') ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Pesanan (opsional)</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="input-field"
                                placeholder="Contoh: Tolong kemas dengan bubble wrap"><?= old('notes') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-6 sticky top-24">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pembayaran</h3>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal (<?= count($cartItems ?? []) ?> item)</span>
                                <span class="font-medium text-gray-800">Rp <?= number_format($totalAmount ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ongkos Kirim</span>
                                <span class="font-medium text-gray-800">Rp <?= number_format($shippingCost ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <?php if (!empty($discountAmount) && $discountAmount > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-success flex items-center gap-1">
                                    <i data-lucide="ticket" class="w-3.5 h-3.5"></i>
                                    Diskon (<?= esc($voucherCode ?? '') ?>)
                                </span>
                                <span class="font-medium text-success">-Rp <?= number_format($discountAmount ?? 0, 0, ',', '.') ?></span>
                            </div>
                            <?php endif; ?>
                            <hr class="border-gray-100">
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-800">Total</span>
                                <span class="text-lg font-bold text-primary">Rp <?= number_format($finalAmount ?? 0, 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <?php if (!empty($firstPurchaseVoucher)): ?>
                        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-2">
                            <i data-lucide="ticket-check" class="w-5 h-5 text-green-600 flex-shrink-0"></i>
                            <div>
                                <p class="text-sm font-medium text-green-800">Voucher Digunakan!</p>
                                <p class="text-xs text-green-600">Diskon pembelian pertama otomatis diterapkan</p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Promo Perilaku Pelanggan (Bulanan) -->
                        <?php if (!empty($behaviorReward['is_qualified']) && empty($firstPurchaseVoucher)): ?>
                        <div class="mb-4 bg-pink-50 border border-primary/30 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="sparkles" class="w-4 h-4 text-primary"></i>
                                    <p class="text-xs font-bold text-primary-dark">Reward Perilaku Bulanan</p>
                                </div>
                                <span class="bg-primary-light text-primary-dark text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    <?= esc($behaviorReward['active_reward']['badge'] ?? '') ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mb-2">
                                Anda telah membeli <strong><?= (int) $monthlyProductCount ?> produk</strong> bulan ini dan berhak mendapatkan <?= esc($behaviorReward['active_reward']['description'] ?? '') ?>!
                            </p>
                            <?php if (($voucherCodeInput ?? '') === 'BEHAVIOR_REWARD'): ?>
                                <div class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded border border-green-300 text-xs text-green-700 font-semibold">
                                    <span>Reward Sedang Digunakan</span>
                                    <a href="<?= base_url('checkout') ?>" class="text-red-500 hover:underline">Batal</a>
                                </div>
                            <?php else: ?>
                                <a href="<?= base_url('checkout?voucher_code=BEHAVIOR_REWARD') ?>" class="inline-flex items-center justify-center w-full py-1.5 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold transition-colors">
                                    Gunakan Reward Perilaku
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Input Kode Voucher -->
                        <div class="mb-4" x-data="{ showVoucherInput: <?= !empty($voucherCodeInput ?? '') ? 'true' : 'false' ?>, voucherCode: '<?= esc($voucherCodeInput ?? '') ?>' }">
                            <?php if (!empty($appliedVoucher ?? [])): ?>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="ticket-check" class="w-5 h-5 text-green-600 flex-shrink-0"></i>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-green-800">Voucher "<?= esc($appliedVoucher['code'] ?? '') ?>" diterapkan!</p>
                                            <p class="text-xs text-green-600"><?= esc($appliedVoucher['promotion_name'] ?? $appliedVoucher['name'] ?? '') ?></p>
                                        </div>
                                        <a href="<?= base_url('checkout') ?>" class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 rounded hover:bg-red-50">Hapus</a>
                                    </div>
                                </div>
                            <?php elseif (empty($firstPurchaseVoucher ?? [])): ?>
                                <button type="button" @click="showVoucherInput = !showVoucherInput" 
                                    class="flex items-center gap-2 text-sm text-primary hover:text-primary-dark font-medium">
                                    <i data-lucide="ticket" class="w-4 h-4"></i>
                                    Punya kode voucher?
                                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="showVoucherInput ? 'rotate-180' : ''"></i>
                                </button>
                                <div x-show="showVoucherInput" x-transition class="mt-3">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="voucherCode" placeholder="Masukkan kode voucher..."
                                            class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 uppercase">
                                        <a :href="'<?= base_url('checkout') ?>?voucher_code=' + voucherCode" 
                                            class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors whitespace-nowrap">
                                            Terapkan
                                        </a>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Masukkan kode voucher dan klik Terapkan untuk melihat diskon</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info Poin Loyalitas -->
                        <div class="mb-4 bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="star" class="w-5 h-5 text-purple-600 flex-shrink-0"></i>
                                <p class="text-sm font-medium text-purple-800">Poin Loyalitas</p>
                            </div>
                            <p class="text-xs text-purple-600">
                                Anda akan mendapatkan <span class="font-bold"><?= $basePoints ?? 0 ?> poin</span> dari pesanan ini.
                                <?php if (($bonusPoints ?? 0) > 0): ?>
                                    <br><span class="font-bold text-purple-700">+<?= $bonusPoints ?> poin bonus</span> (belanja &gt; Rp 500.000)
                                <?php endif; ?>
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full py-3 rounded-lg bg-gradient-to-r from-primary to-primary-dark text-white font-medium hover:shadow-md transition-all text-sm">
                            Buat Pesanan
                        </button>

                        <a href="<?= base_url('cart') ?>"
                            class="w-full mt-3 py-2.5 rounded-lg border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition-colors text-center block text-sm">
                            Kembali ke Keranjang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Cross-selling: Pelanggan Juga Membeli -->
    <?php if (!empty($crossSellProducts ?? [])): ?>
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-primary-light/30 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
            <i data-lucide="sparkles" class="w-5 h-5 text-primary"></i>
            Lengkapi Belanjamu
        </h2>
        <p class="text-sm text-gray-500 mb-6">Produk yang sering dibeli bersamaan dengan item di keranjangmu</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($crossSellProducts as $cp): ?>
                <a href="<?= base_url('product/' . ($cp['id'] ?? '')) ?>" class="group bg-cream/50 rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-gray-100 overflow-hidden">
                        <?php if (!empty($cp['image'] ?? '')): ?>
                            <img src="<?= base_url('uploads/products/' . ($cp['image'] ?? '')) ?>"
                                alt="<?= esc($cp['name'] ?? '') ?>"
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