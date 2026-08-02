<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $customer */
/** @var array $user */
?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <?= $this->include('components/breadcrumb', [
        'items' => [
            ['label' => 'Beranda', 'url' => base_url()],
            ['label' => 'Profil Saya'],
        ]
    ]) ?>

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Profil Saya</h1>

    <!-- Membership Info -->
    <?php
    $levelColors = [
        'bronze'   => 'bg-amber-100 text-amber-800',
        'silver'   => 'bg-gray-200 text-gray-700',
        'gold'     => 'bg-yellow-100 text-yellow-800',
        'platinum' => 'bg-purple-100 text-purple-800',
    ];
    $level = $customer['membership_level'] ?? 'bronze';
    $levelColor = $levelColors[$level] ?? $levelColors['bronze'];
    ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-primary-light flex items-center justify-center">
                <i data-lucide="user" class="w-7 h-7 text-primary"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="font-semibold text-gray-800"><?= esc($user['name'] ?? '') ?></h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $levelColor ?> capitalize">
                        <?= esc(ucfirst($level)) ?>
                    </span>
                </div>
                <p class="text-sm text-gray-500"><?= esc($user['email'] ?? '') ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Poin Loyalitas</p>
                <p class="text-xl font-bold text-primary"><?= number_format($customer['loyalty_points'] ?? 0, 0, ',', '.') ?></p>
            </div>
        </div>
    </div>

    <!-- Form Profil -->
    <form action="<?= base_url('profile/update') ?>" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <?= csrf_field() ?>

        <h3 class="text-lg font-semibold text-gray-800 mb-5">Edit Profil</h3>

        <div class="space-y-5">
            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" value="<?= old('name', $user['name'] ?? '') ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm <?= session('errors.name') ? 'border-danger' : '' ?>">
                <?php if (session('errors.name')): ?>
                    <p class="text-danger text-xs mt-1"><?= session('errors.name') ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm <?= session('errors.email') ? 'border-danger' : '' ?>">
                <?php if (session('errors.email')): ?>
                    <p class="text-danger text-xs mt-1"><?= session('errors.email') ?></p>
                <?php endif; ?>
            </div>

            <!-- Telepon -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm">
            </div>

            <!-- Tanggal Lahir -->
            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir</label>
                <input type="date" id="birth_date" name="birth_date" value="<?= old('birth_date', $user['birth_date'] ?? '') ?>"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm">
            </div>

            <!-- Alamat -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                <textarea id="address" name="address" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"><?= old('address', $user['address'] ?? '') ?></textarea>
            </div>

            <hr class="border-gray-100">

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm <?= session('errors.password') ? 'border-danger' : '' ?>">
                <?php if (session('errors.password')): ?>
                    <p class="text-danger text-xs mt-1"><?= session('errors.password') ?></p>
                <?php endif; ?>
                <p class="text-xs text-gray-400 mt-1">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.</p>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end mt-6">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
