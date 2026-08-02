<?php
/**
 * Badge component
 * Usage: <?= view('components/badge', ['text' => 'Aktif', 'color' => 'success']) ?>
 * Colors: success, warning, danger, info, primary, secondary, gray
 */
$colorMap = [
    'success'  => 'bg-green-100 text-green-700',
    'warning'  => 'bg-yellow-100 text-yellow-700',
    'danger'   => 'bg-red-100 text-red-700',
    'info'     => 'bg-blue-100 text-blue-700',
    'primary'  => 'bg-primary-light text-primary-dark',
    'secondary' => 'bg-secondary-light text-secondary-dark',
    'gray'     => 'bg-gray-100 text-gray-600',
];
$colorClass = $colorMap[$color ?? 'gray'] ?? $colorMap['gray'];
?>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colorClass ?> <?= $class ?? '' ?>">
    <?= $text ?? '' ?>
</span>
