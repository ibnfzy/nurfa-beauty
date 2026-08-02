<?php
/**
 * Toast notification component
 * Usage: Use with session flash messages
 * In controller: session()->setFlashdata('toast', ['type' => 'success', 'message' => 'Berhasil!'])
 */
$toast = session()->getFlashdata('toast');
if ($toast): ?>
<div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed top-4 right-4 z-[100] max-w-sm w-full">
    <div class="bg-white rounded-lg shadow-lg border border-gray-100 p-4 flex items-start gap-3
                <?php
                $borderColor = match($toast['type'] ?? 'info') {
                    'success' => 'border-l-4 border-l-success',
                    'error'   => 'border-l-4 border-l-danger',
                    'warning' => 'border-l-4 border-l-warning',
                    default   => 'border-l-4 border-l-info',
                };
                echo $borderColor;
                ?>">
        <div class="flex-shrink-0 mt-0.5">
            <?php
            $icon = match($toast['type'] ?? 'info') {
                'success' => 'check-circle',
                'error'   => 'x-circle',
                'warning' => 'alert-triangle',
                default   => 'info',
            };
            ?>
            <i data-lucide="<?= $icon ?>" class="w-5 h-5 text-gray-500"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-800"><?= $toast['message'] ?? '' ?></p>
        </div>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>
<?php endif; ?>
