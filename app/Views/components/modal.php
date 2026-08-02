<?php
/**
 * Modal component
 * Usage: Use with Alpine.js x-data="{ showModal: false }"
 * <button @click="showModal = true">Open</button>
 * <?= $this->include('components/modal', ['id' => 'myModal', 'title' => 'Title']) ?>
 */
?>
<div x-show="<?= $showVar ?? 'showModal' ?>" x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50" @click="<?= $showVar ?? 'showModal' ?> = false"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="<?= $showVar ?? 'showModal' ?>" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="bg-white rounded-xl shadow-xl <?= $maxWidth ?? 'max-w-lg' ?> w-full <?= $modalClass ?? '' ?>">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800"><?= $title ?? 'Modal' ?></h3>
                    <button @click="<?= $showVar ?? 'showModal' ?> = false" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <!-- Body -->
                <div class="px-6 py-4">
                    <?= $slot ?? '' ?>
                </div>
                <?php if (isset($footer)): ?>
                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                    <?= $footer ?>
                </div>
                <?php endif; ?>
        </div>
    </div>
</div>
