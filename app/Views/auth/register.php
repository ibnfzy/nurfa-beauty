<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<h2 class="text-2xl font-playfair font-bold text-gray-800 text-center mb-6">Buat Akun Baru</h2>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <p class="text-sm text-danger"><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('auth/register/process') ?>" method="POST" class="space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <div class="relative">
            <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input type="text" id="name" name="name" value="<?= old('name') ?>"
                   placeholder="Masukkan nama lengkap"
                   class="input-field pl-10" required>
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <div class="relative">
            <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input type="text" id="email" name="email" value="<?= old('email') ?>"
                   placeholder="Masukkan username"
                   class="input-field pl-10" required>
        </div>
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. HP <span class="text-gray-400">(opsional)</span></label>
        <div class="relative">
            <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input type="text" id="phone" name="phone" value="<?= old('phone') ?>"
                   placeholder="08xxxxxxxxxx"
                   class="input-field pl-10">
        </div>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative" x-data="{ show: false }">
            <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input :type="show ? 'text' : 'password'" id="password" name="password"
                   placeholder="Minimal 6 karakter"
                   class="input-field pl-10 pr-10" required>
            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <div>
        <label for="pass_confirm" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
        <div class="relative" x-data="{ show: false }">
            <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input :type="show ? 'text' : 'password'" id="pass_confirm" name="pass_confirm"
                   placeholder="Ulangi password"
                   class="input-field pl-10 pr-10" required>
            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-primary w-full py-3 text-center">
        Daftar
    </button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Sudah punya akun?
    <a href="<?= base_url('auth/login') ?>" class="text-primary font-medium hover:underline">Masuk</a>
</p>

<p class="text-center text-sm text-gray-500 mt-3">
    <a href="<?= base_url('/') ?>" class="text-gray-500 font-medium hover:text-primary hover:underline inline-flex items-center gap-1">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Beranda
    </a>
</p>
<?= $this->endSection() ?>
