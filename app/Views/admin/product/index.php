<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $products */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
/** @var string|null $categoryId */
/** @var array $categories */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Produk'],
    ]
]) ?>

<?php $categories = $categories ?? []; ?>

<div x-data="{
    showDeleteModal: false,
    deleteId: '',
    deleteName: '',
    openDelete(id, name) {
        this.deleteId = id;
        this.deleteName = name;
        this.showDeleteModal = true;
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Daftar Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola produk Anda</p>
        </div>
        <a href="<?= base_url('admin/products/create') ?>" class="btn-primary inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Produk
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1 max-w-md">
            <?= view('components/search', [
                'placeholder' => 'Cari produk...',
                'action'      => base_url('admin/products'),
            ]) ?>
        </div>
        <div class="relative">
            <select
                onchange="window.location.href = '<?= base_url('admin/products') ?>?q=<?= esc($search ?? '') ?>&category=' + this.value"
                class="input-field pr-10 appearance-none cursor-pointer min-w-[180px]">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($category ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($products)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'package',
                'title'       => 'Belum ada produk',
                'description' => 'Mulai tambahkan produk untuk ditampilkan di toko Anda.',
                'actionText'  => 'Tambah Produk',
                'actionUrl'   => base_url('admin/products/create'),
            ]) ?>
        <?php else: ?>
            <?= view('components/table', [
                'headers' => ['No', 'Gambar', 'Nama Produk', 'Kategori', 'Harga', 'Stok', 'Status', 'Aksi'],
                'slot'    => (function () use ($products, $pager) {
                    $output = '';
                    $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1;
                    foreach ($products as $product) {
                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';

                        // No
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';

                        // Gambar
                        $output .= '<td class="py-3 px-4">';
                        if ($product['image']) {
                            $output .= '<img src="' . base_url('uploads/products/' . $product['image']) . '" alt="' . esc($product['name']) . '" class="w-12 h-12 rounded-lg object-cover border border-gray-100">';
                        } else {
                            $output .= '<div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">';
                            $output .= '<i data-lucide="image" class="w-5 h-5 text-gray-400"></i>';
                            $output .= '</div>';
                        }
                        $output .= '</td>';

                        // Nama Produk
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($product['name']) . '</td>';

                        // Kategori
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-primary-dark">' . esc($product['category_name'] ?? '-') . '</span>';
                        $output .= '</td>';

                        // Harga
                        $output .= '<td class="py-3 px-4 text-gray-700 font-medium">Rp ' . number_format($product['price'], 0, ',', '.') . '</td>';

                        // Stok
                        $output .= '<td class="py-3 px-4">';
                        if ($product['stock'] > 10) {
                            $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">' . $product['stock'] . '</span>';
                        } elseif ($product['stock'] > 0) {
                            $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">' . $product['stock'] . '</span>';
                        } else {
                            $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">' . $product['stock'] . '</span>';
                        }
                        $output .= '</td>';

                        // Status
                        $output .= '<td class="py-3 px-4">';
                        if ($product['is_active']) {
                            $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>';
                        } else {
                            $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>';
                        }
                        $output .= '</td>';

                        // Aksi
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<div class="flex items-center gap-2">';
                        $output .= '<a href="' . base_url('admin/products/edit/' . $product['id']) . '" class="p-1.5 rounded-lg text-info hover:bg-blue-50 transition-colors" title="Edit">';
                        $output .= '<i data-lucide="pencil" class="w-4 h-4"></i>';
                        $output .= '</a>';
                        $output .= '<button @click="openDelete(\'' . $product['id'] . '\', \'' . esc($product['name'], 'js') . '\')" class="p-1.5 rounded-lg text-danger hover:bg-red-50 transition-colors" title="Hapus">';
                        $output .= '<i data-lucide="trash-2" class="w-4 h-4"></i>';
                        $output .= '</button>';
                        $output .= '</div>';
                        $output .= '</td>';

                        $output .= '</tr>';
                    }
                    return $output;
                })(),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <?= view('components/pagination', ['pager' => $pager]) ?>
    <?php endif; ?>

    <!-- Delete Modal -->
    <?= view('components/modal', [
        'showVar'   => 'showDeleteModal',
        'title'     => 'Hapus Produk',
        'maxWidth'  => 'max-w-md',
        'slot'      => (function () {
            $output = '<div class="text-center">';
            $output .= '<div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">';
            $output .= '<i data-lucide="alert-triangle" class="w-8 h-8 text-danger"></i>';
            $output .= '</div>';
            $output .= '<h3 class="text-lg font-semibold text-gray-800 mb-2">Yakin ingin menghapus?</h3>';
            $output .= '<p class="text-sm text-gray-500 mb-6">Produk <span class="font-semibold text-gray-700" x-text="deleteName"></span> akan dihapus secara permanen beserta gambarnya.</p>';
            $output .= '<div class="flex items-center justify-center gap-3">';
            $output .= '<button type="button" @click="showDeleteModal = false" class="btn-secondary">Batal</button>';
            $output .= '<form :action="\'/admin/products/delete/\' + deleteId" method="POST" class="inline">';
            $output .= csrf_field();
            $output .= '<button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">Hapus</button>';
            $output .= '</form>';
            $output .= '</div>';
            $output .= '</div>';
            return $output;
        })(),
    ]) ?>

</div>

<?= $this->endSection() ?>