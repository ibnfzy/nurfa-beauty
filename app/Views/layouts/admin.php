<!DOCTYPE html>
<html lang="id">
<head>
    <?= $this->include('partials/meta') ?>
    <?= $this->include('partials/assets') ?>
</head>
<body class="font-poppins bg-cream min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <?= $this->include('components/sidebar') ?>

        <!-- Main Content -->
        <div class="flex-1 min-h-screen">
            <!-- Top Navbar -->
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <h1 class="text-lg font-semibold text-gray-800"><?= $pageTitle ?? 'Dashboard' ?></h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center">
                                <i data-lucide="user" class="w-4 h-4 text-primary-dark"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700"><?= session()->get('name') ?? 'Admin' ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <?= $this->include('components/toast') ?>
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <?= $this->include('partials/scripts') ?>
</body>
</html>
