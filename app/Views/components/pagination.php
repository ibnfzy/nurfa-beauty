<?php
/**
 * Pagination component
 * Usage: echo $pager->links() or include this component with 'pager' variable
 */
if (isset($pager)): ?>
<div class="flex items-center justify-between mt-6">
    <p class="text-sm text-gray-500">
        Menampilkan <?= $pager->getCurrentPage() ?> dari <?= $pager->getPageCount() ?> halaman
    </p>
    <div class="flex items-center gap-1">
        <?= $pager->links() ?>
    </div>
</div>
<?php endif; ?>
