<?php
/**
 * Table component
 * Usage: $this->include('components/table', ['headers' => ['No', 'Nama', 'Aksi'], 'slot' => '...'])
 */
?>
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <?php foreach (($headers ?? []) as $header): ?>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600 text-xs uppercase tracking-wider"><?= $header ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?= $slot ?? '' ?>
        </tbody>
    </table>
</div>
