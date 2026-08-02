<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $notifications */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
/** @var string|null $type */
/** @var array $customers */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= $this->include('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Notifikasi'],
    ]
]) ?>

<div x-data="{ showSendModal: false, showBroadcastModal: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Notifikasi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola notifikasi pelanggan</p>
        </div>
        <div class="flex gap-2">
            <button @click="showSendModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
                <i data-lucide="send" class="w-4 h-4"></i>
                Kirim Notifikasi
            </button>
            <button @click="showBroadcastModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-secondary to-secondary-dark text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
                <i data-lucide="megaphone" class="w-4 h-4"></i>
                Broadcast
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="<?= base_url('admin/notifications') ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= empty($type) ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="bell" class="w-4 h-4"></i>
            Semua
        </a>
        <a href="<?= base_url('admin/notifications?type=promotion') ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($type ?? '') === 'promotion' ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="tag" class="w-4 h-4"></i>
            Promosi
        </a>
        <a href="<?= base_url('admin/notifications?type=birthday') ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($type ?? '') === 'birthday' ? 'bg-warning text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="cake" class="w-4 h-4"></i>
            Birthday
        </a>
        <a href="<?= base_url('admin/notifications?type=loyalty') ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($type ?? '') === 'loyalty' ? 'bg-success text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="award" class="w-4 h-4"></i>
            Loyalitas
        </a>
        <a href="<?= base_url('admin/notifications?type=general') ?>"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= ($type ?? '') === 'general' ? 'bg-info text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-cream' ?>">
            <i data-lucide="info" class="w-4 h-4"></i>
            Umum
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6 max-w-md">
        <?= $this->include('components/search', [
            'placeholder' => 'Cari judul atau nama pelanggan...',
            'action'      => base_url('admin/notifications') . (($type ?? '') ? '?type=' . ($type ?? '') : ''),
        ]) ?>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($notifications)): ?>
            <?= $this->include('components/empty-state', [
                'icon'        => 'bell-off',
                'title'       => 'Tidak ada notifikasi',
                'description' => 'Belum ada notifikasi yang dikirim ke pelanggan.',
            ]) ?>
        <?php else: ?>
            <?php
            $typeColors = [
                'promotion' => 'primary',
                'birthday'  => 'warning',
                'loyalty'   => 'success',
                'general'   => 'info',
            ];
            $typeLabels = [
                'promotion' => 'Promosi',
                'birthday'  => 'Birthday',
                'loyalty'   => 'Loyalitas',
                'general'   => 'Umum',
            ];
            $badgeColorMap = [
                'success'   => 'bg-green-100 text-green-700',
                'warning'   => 'bg-yellow-100 text-yellow-700',
                'danger'    => 'bg-red-100 text-red-700',
                'info'      => 'bg-blue-100 text-blue-700',
                'primary'   => 'bg-primary-light text-primary-dark',
                'secondary' => 'bg-secondary-light text-secondary-dark',
                'gray'      => 'bg-gray-100 text-gray-600',
            ];
            ?>
            <?= $this->include('components/table', [
                'headers' => ['No', 'Pelanggan', 'Judul', 'Tipe', 'Status', 'Tanggal'],
                'slot'    => (function () use ($notifications, $pager, $typeColors, $typeLabels, $badgeColorMap) {
                    $output = '';
                    $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1;
                    foreach ($notifications as $notif) {
                        $tc = $typeColors[$notif['type'] ?? ''] ?? 'gray';
                        $tl = $typeLabels[$notif['type'] ?? ''] ?? ucfirst($notif['type'] ?? '');
                        $tcc = $badgeColorMap[$tc] ?? $badgeColorMap['gray'];

                        $isRead = ($notif['is_read'] ?? 0) == 1;
                        $statusColor = $isRead ? 'success' : 'warning';
                        $statusLabel = $isRead ? 'Dibaca' : 'Belum Dibaca';
                        $scc = $badgeColorMap[$statusColor] ?? $badgeColorMap['gray'];

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($notif['customer_name'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($notif['title'] ?? '-') . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $tcc . '">' . $tl . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $scc . '">' . $statusLabel . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . ($notif['created_at'] ?? '' ? date('d M Y H:i', strtotime($notif['created_at'])) : '-') . '</td>';
                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <?= $this->include('components/pagination', ['pager' => $pager]) ?>
    <?php endif; ?>

    <!-- Kirim Notifikasi Modal -->
    <div x-show="showSendModal" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showSendModal = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4" @click.stop>
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Kirim Notifikasi</h3>
                <button @click="showSendModal = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-gray-400"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/notifications/send') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pelanggan <span class="text-danger">*</span></label>
                        <select name="customer_id" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            <option value="">Pilih Pelanggan...</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>"><?= esc($customer['name']) ?> (<?= esc($customer['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" required maxlength="255"
                            placeholder="Judul notifikasi..."
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan <span class="text-danger">*</span></label>
                        <textarea name="message" rows="3" required maxlength="1000"
                            placeholder="Tulis pesan notifikasi..."
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span class="text-danger">*</span></label>
                        <select name="type" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            <option value="">Pilih Tipe...</option>
                            <option value="promotion">Promosi</option>
                            <option value="birthday">Birthday</option>
                            <option value="loyalty">Loyalitas</option>
                            <option value="general">Umum</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                    <button type="button" @click="showSendModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Broadcast Modal -->
    <div x-show="showBroadcastModal" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showBroadcastModal = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4" @click.stop>
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Broadcast Notifikasi</h3>
                <button @click="showBroadcastModal = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-gray-400"></i>
                </button>
            </div>
            <form action="<?= base_url('admin/notifications/broadcast') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Penerima <span class="text-danger">*</span></label>
                        <select name="target" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            <option value="">Pilih Target...</option>
                            <option value="all">Semua Pelanggan</option>
                            <option value="bronze">Bronze</option>
                            <option value="silver">Silver</option>
                            <option value="gold">Gold</option>
                            <option value="platinum">Platinum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" required maxlength="255"
                            placeholder="Judul notifikasi..."
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan <span class="text-danger">*</span></label>
                        <textarea name="message" rows="3" required maxlength="1000"
                            placeholder="Tulis pesan broadcast..."
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span class="text-danger">*</span></label>
                        <select name="type" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            <option value="">Pilih Tipe...</option>
                            <option value="promotion">Promosi</option>
                            <option value="birthday">Birthday</option>
                            <option value="loyalty">Loyalitas</option>
                            <option value="general">Umum</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                    <button type="button" @click="showBroadcastModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-secondary to-secondary-dark text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
                        <i data-lucide="megaphone" class="w-4 h-4"></i>
                        Kirim Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
