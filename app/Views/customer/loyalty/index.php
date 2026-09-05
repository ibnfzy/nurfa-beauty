<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $customer */
/** @var string $level */
/** @var array $benefits */
/** @var array|null $nextLevel */
/** @var array $pointHistory */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var array $vouchers */
/** @var int $monthlyProductCount */
/** @var array $monthlyBehaviorReward */
?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <?= view('components/breadcrumb', [
        'items' => [
            ['label' => 'Beranda', 'url' => base_url()],
            ['label' => 'Loyalitas Saya'],
        ]
    ]) ?>

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Loyalitas Saya</h1>

    <!-- Membership Card -->
    <?php
    $gradientMap = [
        'bronze'   => 'from-amber-700 to-amber-500',
        'silver'   => 'from-gray-500 to-gray-300',
        'gold'     => 'from-yellow-600 to-yellow-400',
        'platinum' => 'from-purple-700 to-purple-400',
    ];
    $gradient = $gradientMap[$level] ?? $gradientMap['bronze'];
    ?>
    <div class="bg-gradient-to-r <?= $gradient ?> rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-8 -mb-8"></div>

        <div class="relative z-10">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-white/80 text-sm">Membership</p>
                    <h2 class="text-xl font-bold"><?= esc($customer['name'] ?? '') ?></h2>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm uppercase tracking-wider">
                    <?= esc($benefits['label'] ?? ucfirst($level)) ?>
                </span>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-white/70 text-sm mb-1">Total Poin</p>
                    <p class="text-3xl font-bold"><?= number_format($customer['loyalty_points'] ?? 0, 0, ',', '.') ?></p>
                </div>
                <div>
                    <p class="text-white/70 text-sm mb-1">Total Spending</p>
                    <p class="text-xl font-semibold">Rp <?= number_format($customer['total_spending'] ?? 0, 0, ',', '.') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress to Next Level -->
    <?php if ($nextLevel): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">Progress ke Level Berikutnya</h3>
                <span class="text-sm text-gray-500"><?= $nextLevel['progress'] ?? 0 ?>%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 mb-3">
                <div class="bg-gradient-to-r from-primary to-secondary h-3 rounded-full transition-all duration-500" style="width: <?= min($nextLevel['progress'] ?? 0, 100) ?>%"></div>
            </div>
            <p class="text-sm text-gray-600">
                <span class="font-medium text-primary">Rp <?= number_format($nextLevel['remaining'] ?? 0, 0, ',', '.') ?></span> lagi untuk naik ke
                <span class="font-semibold capitalize"><?= esc(ucfirst($nextLevel['level'] ?? '')) ?></span>
            </p>
        </div>
    <?php endif; ?>

    <!-- Behavioral Promo Section: Promo Berdasarkan Perilaku Belanja Bulanan -->
    <div class="bg-white rounded-2xl shadow-sm border border-primary-light/40 p-6 sm:p-7 mb-8 overflow-hidden relative">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-100">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-primary-light text-primary-dark mb-2">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Promo Perilaku Pelanggan (Bulanan)
                </div>
                <h2 class="text-xl font-bold text-gray-900">Promo Kuantitas Produk Bulan Ini</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Semakin banyak produk yang Anda beli dalam sebulan, semakin tinggi reward &amp; diskon belanja yang Anda dapatkan!
                </p>
            </div>
            <div class="bg-gradient-to-br from-pink-50 to-primary-light/30 border border-primary/20 rounded-xl p-4 text-center min-w-[200px]">
                <p class="text-xs text-gray-500 uppercase font-semibold">Produk Dibeli Bulan Ini</p>
                <div class="flex items-baseline justify-center gap-1 mt-1">
                    <span class="text-3xl font-extrabold text-primary"><?= (int) $monthlyProductCount ?></span>
                    <span class="text-sm text-gray-600 font-medium">produk</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Periode: <?= date('M Y') ?></p>
            </div>
        </div>

        <!-- Status Reward & Progress -->
        <div class="mt-6">
            <?php if (!empty($monthlyBehaviorReward['active_reward'])): ?>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-500 text-white flex items-center justify-center flex-shrink-0">
                            <i data-lucide="award" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-green-900"><?= esc($monthlyBehaviorReward['active_reward']['title']) ?> Tercapai!</span>
                                <span class="bg-green-200 text-green-800 text-xs px-2 py-0.5 rounded-full font-semibold">Aktif</span>
                            </div>
                            <p class="text-xs text-green-700 mt-0.5">
                                <?= esc($monthlyBehaviorReward['active_reward']['description']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-green-600 text-white rounded-lg">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Siap Pakai di Checkout
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($monthlyBehaviorReward['next_target'])): ?>
                <div class="bg-cream/40 rounded-xl p-4 border border-gray-100 mb-6">
                    <div class="flex items-center justify-between text-xs sm:text-sm mb-2 font-medium text-gray-700">
                        <span>Target Berikutnya: <strong class="text-primary"><?= esc($monthlyBehaviorReward['next_target']['title']) ?></strong> (<?= $monthlyBehaviorReward['next_target']['target'] ?> Produk)</span>
                        <span class="text-primary font-bold"><?= $monthlyBehaviorReward['next_target']['progress'] ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                        <div class="bg-gradient-to-r from-primary to-secondary h-2.5 rounded-full transition-all duration-500" style="width: <?= min($monthlyBehaviorReward['next_target']['progress'], 100) ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-500">
                        Beli <strong class="text-primary"><?= $monthlyBehaviorReward['next_target']['remaining'] ?> produk lagi</strong> di bulan ini untuk mengklaim level reward berikutnya!
                    </p>
                </div>
            <?php endif; ?>

            <!-- Tier Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tier 1: Smart Buyer -->
                <div class="rounded-xl border p-4 transition-all <?= ($monthlyProductCount >= 3) ? 'border-primary bg-primary-light/10 shadow-sm' : 'border-gray-200 bg-white opacity-85' ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded <?= ($monthlyProductCount >= 3) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' ?>">
                            3+ Produk / Bulan
                        </span>
                        <?php if ($monthlyProductCount >= 3): ?>
                            <i data-lucide="check-circle" class="w-4 h-4 text-success"></i>
                        <?php else: ?>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm">Smart Buyer Reward</h3>
                    <p class="text-xs text-primary font-bold mt-1">Diskon 5% (Maks Rp 10.000)</p>
                    <p class="text-xs text-gray-500 mt-1">+ Bonus 20 Poin Loyalitas</p>
                </div>

                <!-- Tier 2: Loyal Shopper -->
                <div class="rounded-xl border p-4 transition-all <?= ($monthlyProductCount >= 5) ? 'border-primary bg-primary-light/10 shadow-sm' : 'border-gray-200 bg-white opacity-85' ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded <?= ($monthlyProductCount >= 5) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' ?>">
                            5+ Produk / Bulan
                        </span>
                        <?php if ($monthlyProductCount >= 5): ?>
                            <i data-lucide="check-circle" class="w-4 h-4 text-success"></i>
                        <?php else: ?>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm">Loyal Shopper Reward</h3>
                    <p class="text-xs text-primary font-bold mt-1">Diskon 10% (Maks Rp 25.000)</p>
                    <p class="text-xs text-gray-500 mt-1">+ Bonus 50 Poin Loyalitas</p>
                </div>

                <!-- Tier 3: Super Fan -->
                <div class="rounded-xl border p-4 transition-all <?= ($monthlyProductCount >= 10) ? 'border-primary bg-primary-light/10 shadow-sm' : 'border-gray-200 bg-white opacity-85' ?>">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded <?= ($monthlyProductCount >= 10) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' ?>">
                            10+ Produk / Bulan
                        </span>
                        <?php if ($monthlyProductCount >= 10): ?>
                            <i data-lucide="check-circle" class="w-4 h-4 text-success"></i>
                        <?php else: ?>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm">Super Fan Reward</h3>
                    <p class="text-xs text-primary font-bold mt-1">Diskon 15% (Maks Rp 50.000)</p>
                    <p class="text-xs text-gray-500 mt-1">+ Bonus 100 Poin Loyalitas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Benefit Membership Anda</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Poin Multiplier -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-primary-light flex items-center justify-center mb-3">
                    <i data-lucide="zap" class="w-5 h-5 text-primary"></i>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm">Poin Multiplier</h3>
                <p class="text-2xl font-bold text-primary mt-1"><?= $benefits['multiplier'] ?? 1 ?>x</p>
                <p class="text-xs text-gray-500 mt-1">Poin per belanja</p>
            </div>

            <!-- Diskon -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center mb-3">
                    <i data-lucide="percent" class="w-5 h-5 text-success"></i>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm">Diskon Eksklusif</h3>
                <p class="text-2xl font-bold text-success mt-1"><?= $benefits['discount'] ?? 0 ?>%</p>
                <p class="text-xs text-gray-500 mt-1">Diskon khusus member</p>
            </div>

            <!-- Perks List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center mb-3">
                    <i data-lucide="gift" class="w-5 h-5 text-info"></i>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm mb-2">Keuntungan</h3>
                <ul class="space-y-1">
                    <?php foreach (($benefits['perks'] ?? []) as $perk): ?>
                        <li class="flex items-center gap-2 text-xs text-gray-600">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-success flex-shrink-0"></i>
                            <?= esc($perk) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Active Vouchers -->
    <div class="mb-8" x-data="{ copiedCode: '' }">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Voucher Aktif</h2>
        <?php if (empty($vouchers)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="ticket" class="w-7 h-7 text-gray-400"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada voucher aktif</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($vouchers as $voucher): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-dashed border-primary/30 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-sm"><?= esc($voucher['promotion_name'] ?? '') ?></h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <?php if (($voucher['discount_type'] ?? '') === 'percentage'): ?>
                                            Diskon <?= $voucher['discount_value'] ?? 0 ?>%
                                        <?php else: ?>
                                            Diskon Rp <?= number_format($voucher['discount_value'] ?? 0, 0, ',', '.') ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-light text-primary">
                                    Aktif
                                </span>
                            </div>

                            <div class="flex items-center gap-2 bg-cream rounded-lg p-3">
                                <span class="text-sm font-mono font-bold text-gray-800 tracking-wider flex-1"><?= esc($voucher['code'] ?? '') ?></span>
                                <button
                                    @click="
                                        navigator.clipboard.writeText('<?= esc($voucher['code'] ?? '', 'js') ?>');
                                        copiedCode = '<?= esc($voucher['code'] ?? '', 'js') ?>';
                                        setTimeout(() => copiedCode = '', 2000);
                                    "
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                    :class="copiedCode === '<?= esc($voucher['code'] ?? '', 'js') ?>' ? 'bg-success text-white' : 'bg-primary text-white hover:bg-primary-dark'"
                                >
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    <span x-text="copiedCode === '<?= esc($voucher['code'] ?? '', 'js') ?>' ? 'Tersalin!' : 'Salin'"></span>
                                </button>
                            </div>

                            <p class="text-xs text-gray-400 mt-3">
                                Berlaku hingga <?= date('d M Y', strtotime($voucher['expires_at'] ?? 'now')) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Point History -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Poin</h2>
        <?php if (empty($pointHistory)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="history" class="w-7 h-7 text-gray-400"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada riwayat poin</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-cream">
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Tipe</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Deskripsi</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Poin</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($pointHistory as $log): ?>
                                <?php
                                $type = $log['type'] ?? 'earn';
                                $typeConfig = match ($type) {
                                    'earn'    => ['color' => 'success', 'text' => 'Earn', 'prefix' => '+', 'badge' => 'bg-success/10 text-success'],
                                    'redeem'  => ['color' => 'danger', 'text' => 'Redeem', 'prefix' => '-', 'badge' => 'bg-danger/10 text-danger'],
                                    'expired' => ['color' => 'gray', 'text' => 'Expired', 'prefix' => '-', 'badge' => 'bg-gray-100 text-gray-500'],
                                    default   => ['color' => 'gray', 'text' => ucfirst($type), 'prefix' => '', 'badge' => 'bg-gray-100 text-gray-500'],
                                };
                                ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeConfig['badge'] ?>">
                                            <?= $typeConfig['text'] ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700"><?= esc($log['description'] ?? '') ?></td>
                                    <td class="px-5 py-4 text-right">
                                        <span class="text-sm font-semibold <?= $type === 'earn' ? 'text-success' : ($type === 'redeem' ? 'text-danger' : 'text-gray-400') ?>">
                                            <?= $typeConfig['prefix'] ?><?= number_format($log['points'] ?? 0, 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm text-gray-500">
                                        <?= date('d M Y', strtotime($log['created_at'] ?? 'now')) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($pager && $pager->getPageCount() > 1): ?>
                <?= view('components/pagination', ['pager' => $pager]) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
