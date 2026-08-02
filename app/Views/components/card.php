<?php
/**
 * Card component
 * Usage: $this->include('components/card', ['class' => '...', 'padding' => 'p-6'])
 */
?>
<div class="bg-white rounded-xl shadow-sm border border-primary-light/30 <?= $padding ?? 'p-6' ?> <?= $class ?? '' ?>">
    <?php if (isset($title)): ?>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800"><?= $title ?></h3>
            <?php if (isset($action)): ?>
                <?= $action ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?= $slot ?? '' ?>
</div>
