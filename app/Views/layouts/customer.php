<!DOCTYPE html>
<html lang="id">
<head>
    <?= $this->include('partials/meta') ?>
    <?= $this->include('partials/assets') ?>
</head>
<body class="font-poppins bg-cream min-h-screen flex flex-col">
    <!-- Navbar -->
    <?= $this->include('components/navbar') ?>

    <!-- Flash Toast -->
    <?= $this->include('components/toast') ?>

    <!-- Page Content -->
    <main class="flex-1">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('components/footer') ?>

    <?= $this->include('partials/scripts') ?>
</body>
</html>
