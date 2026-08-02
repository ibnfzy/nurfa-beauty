<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h2 class="text-2xl font-playfair font-bold text-gray-800 text-center mb-6">Masuk ke Akun</h2>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <p class="text-sm text-danger"><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('auth/login/process') ?>" method="POST" class="space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <div class="relative">
            <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input type="email" id="email" name="email" value="<?= old('email') ?>"
                   placeholder="Masukkan email Anda"
                   class="input-field pl-10" required>
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative" x-data="{ show: false }">
            <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input :type="show ? 'text' : 'password'" id="password" name="password"
                   placeholder="Masukkan password"
                   class="input-field pl-10 pr-10" required>
            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-primary w-full py-3 text-center">
        Masuk
    </button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Belum punya akun?
    <a href="<?= base_url('auth/register') ?>" class="text-primary font-medium hover:underline">Daftar sekarang</a>
</p>

<p class="text-center text-sm text-gray-500 mt-3">
    <a href="<?= base_url('/') ?>" class="text-gray-500 font-medium hover:text-primary hover:underline inline-flex items-center gap-1">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Beranda
    </a>
</p>
<?= $this->endSection() ?>
