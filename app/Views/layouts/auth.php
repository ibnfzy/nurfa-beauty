<!DOCTYPE html>
<html lang="id">
<head>
    <?= $this->include('partials/meta') ?>
    <?= $this->include('partials/assets') ?>
</head>
<body class="font-poppins bg-cream min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="<?= base_url() ?>" class="inline-block">
                <h1 class="text-3xl font-playfair font-bold text-primary">Nurfa Beauty</h1>
                <p class="text-sm text-gray-500 mt-1">Produk Kecantikan Berkualitas</p>
            </a>
        </div>

        <!-- Flash Toast -->
        <?= $this->include('components/toast') ?>

        <!-- Content Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-primary-light/30 p-8">
            <?= $this->renderSection('content') ?>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; <?= date('Y') ?> Nurfa Beauty. All rights reserved.
        </p>
    </div>

    <?= $this->include('partials/scripts') ?>
</body>
</html>
