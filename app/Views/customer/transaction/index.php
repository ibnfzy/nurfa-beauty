<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $transactions */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $status */
/** @var array $itemCounts */
?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <?= view('components/breadcrumb', [
        'items' => [
            ['label' => 'Beranda', 'url' => base_url()],
            ['label' => 'Pesanan Saya'],
        ]
    ]) ?>

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Pesanan Saya</h1>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="<?= base_url('transactions') ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= !($status ?? '') ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-light border border-gray-200' ?>">
            Semua
        </a>
        <a href="<?= base_url('transactions?status=pending_payment') ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($status ?? '') === 'pending_payment' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-light border border-gray-200' ?>">
            Menunggu Bayar
        </a>
        <a href="<?= base_url('transactions?status=paid') ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($status ?? '') === 'paid' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-light border border-gray-200' ?>">
            Sudah Bayar
        </a>
        <a href="<?= base_url('transactions?status=processing') ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($status ?? '') === 'processing' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-light border border-gray-200' ?>">
            Diproses
        </a>
        <a href="<?= base_url('transactions?status=shipped') ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($status ?? '') === 'shipped' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-light border border-gray-200' ?>">
            Dikirim
        </a>
        <a href="<?= base_url('transactions?status=completed') ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($status ?? '') === 'completed' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-light border border-gray-200' ?>">
            Selesai
        </a>
        <a href="<?= base_url('transactions?status=cancelled') ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors <?= ($status ?? '') === 'cancelled' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-light border border-gray-200' ?>">
            Dibatalkan
        </a>
    </div>

    <?php if (empty($transactions)): ?>
        <!-- Empty State -->
        <?= view('components/empty-state', [
            'icon'        => 'package',
            'title'       => 'Belum ada pesanan',
            'description' => 'Anda belum memiliki pesanan. Mulai belanja sekarang!',
            'actionText'  => 'Mulai Belanja',
            'actionUrl'   => base_url('/'),
        ]) ?>
    <?php else: ?>
        <!-- Transaction Cards -->
        <div class="space-y-4">
            <?php foreach ($transactions ?? [] as $trx): ?>
                <?php
                $statusConfig = match ($trx['status'] ?? '') {
                    'pending_payment' => ['color' => 'warning', 'text' => 'Menunggu Bayar'],
                    'paid'            => ['color' => 'info', 'text' => 'Sudah Bayar'],
                    'processing'      => ['color' => 'primary', 'text' => 'Diproses'],
                    'shipped'         => ['color' => 'info', 'text' => 'Dikirim'],
                    'completed'       => ['color' => 'success', 'text' => 'Selesai'],
                    'cancelled'       => ['color' => 'danger', 'text' => 'Dibatalkan'],
                    default           => ['color' => 'gray', 'text' => ucfirst($trx['status'])],
                };
                ?>
                <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 p-4 sm:p-6 hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="font-semibold text-gray-800"><?= esc($trx['transaction_code'] ?? '') ?></h3>
                                <?= view('components/badge', [
                                    'text'  => $statusConfig['text'],
                                    'color' => $statusConfig['color'],
                                ]) ?>
                            </div>
                            <p class="text-sm text-gray-500">
                                <?= date('d M Y H:i', strtotime($trx['transaction_date'] ?? 'now')) ?>
                            </p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-lg font-bold text-primary">
                                Rp <?= number_format($trx['final_amount'] ?? 0, 0, ',', '.') ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= $itemCounts[$trx['id']] ?? 0 ?> item
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                        <a href="<?= base_url('transactions/' . ($trx['id'] ?? 0)) ?>"
                            class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                            Lihat Detail
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pager && $pager->getPageCount() > 1): ?>
            <?= view('components/pagination', ['pager' => $pager]) ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>