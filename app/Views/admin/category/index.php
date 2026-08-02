<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $categories */
/** @var \CodeIgniter\Pager\Pager|null $pager */
/** @var string|null $search */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Kategori'],
    ]
]) ?>

<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    showDeleteModal: false,
    editData: { id: '', name: '', description: '' },
    deleteId: '',
    deleteName: '',
    openEdit(id, name, description) {
        this.editData = { id, name, description: description || '' };
        this.showEditModal = true;
    },
    openDelete(id, name) {
        this.deleteId = id;
        this.deleteName = name;
        this.showDeleteModal = true;
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Daftar Kategori</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori produk Anda</p>
        </div>
        <button @click="showCreateModal = true" class="btn-primary inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Kategori
        </button>
    </div>

    <!-- Search -->
    <div class="mb-6 max-w-md">
        <?= view('components/search', [
            'placeholder' => 'Cari kategori...',
            'action'      => base_url('admin/categories'),
        ]) ?>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($categories)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'tags',
                'title'       => 'Belum ada kategori',
                'description' => 'Mulai tambahkan kategori untuk mengelompokkan produk Anda.',
                'actionText'  => 'Tambah Kategori',
            ]) ?>
        <?php else: ?>
            <?= view('components/table', [
                'headers' => ['No', 'Nama Kategori', 'Deskripsi', 'Jumlah Produk', 'Aksi'],
                'slot'    => table_rows($categories, [
                    ['key' => '#no', 'class' => 'text-gray-500'],
                    ['key' => 'name', 'class' => 'font-medium text-gray-800'],
                    ['key' => 'description', 'class' => 'text-gray-600 max-w-xs truncate'],
                    ['render' => fn($row) => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-light text-primary-dark">' . ($row['product_count'] ?? 0) . ' produk</span>'],
                    ['render' => function ($row) {
                        $id   = $row['id'];
                        $name = esc($row['name'], 'js');
                        $desc = esc($row['description'] ?? '', 'js');
                        return '<div class="flex items-center gap-2">'
                            . '<button @click="openEdit(\'' . $id . '\', \'' . $name . '\', \'' . $desc . '\')" class="p-1.5 rounded-lg text-info hover:bg-blue-50 transition-colors" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></button>'
                            . '<button @click="openDelete(\'' . $id . '\', \'' . $name . '\')" class="p-1.5 rounded-lg text-danger hover:bg-red-50 transition-colors" title="Hapus"><i data-lucide="trash-2" class="w-4 h-4"></i></button>'
                            . '</div>';
                    }],
                ], $pager),
            ]) ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <?= view('components/pagination', ['pager' => $pager]) ?>
    <?php endif; ?>

    <!-- Create Modal -->
    <?= view('components/modal', [
        'showVar' => 'showCreateModal',
        'title'   => 'Tambah Kategori',
        'slot'    => (function () {
            $output = '<form action="' . base_url('admin/categories/store') . '" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div>';
            $output .= '<label for="create_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_name" name="name" value="" required maxlength="100" class="input-field" placeholder="Masukkan nama kategori">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>';
            $output .= '<textarea id="create_description" name="description" rows="3" class="input-field" placeholder="Masukkan deskripsi kategori (opsional)"></textarea>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="flex items-center justify-end gap-3 mt-6">';
            $output .= '<button type="button" @click="showCreateModal = false" class="btn-secondary">Batal</button>';
            $output .= '<button type="submit" class="btn-primary">Simpan</button>';
            $output .= '</div>';
            $output .= '</form>';
            return $output;
        })(),
    ]) ?>

    <!-- Edit Modal -->
    <?= view('components/modal', [
        'showVar' => 'showEditModal',
        'title'   => 'Edit Kategori',
        'slot'    => (function () {
            $output = '<form :action="\'/admin/categories/update/\' + editData.id" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div>';
            $output .= '<label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_name" name="name" x-model="editData.name" required maxlength="100" class="input-field" placeholder="Masukkan nama kategori">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>';
            $output .= '<textarea id="edit_description" name="description" rows="3" x-model="editData.description" class="input-field" placeholder="Masukkan deskripsi kategori (opsional)"></textarea>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="flex items-center justify-end gap-3 mt-6">';
            $output .= '<button type="button" @click="showEditModal = false" class="btn-secondary">Batal</button>';
            $output .= '<button type="submit" class="btn-primary">Perbarui</button>';
            $output .= '</div>';
            $output .= '</form>';
            return $output;
        })(),
    ]) ?>

    <!-- Delete Modal -->
    <?= view('components/modal', [
        'showVar'   => 'showDeleteModal',
        'title'     => 'Hapus Kategori',
        'maxWidth'  => 'max-w-md',
        'slot'      => (function () {
            $output = '<div class="text-center">';
            $output .= '<div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">';
            $output .= '<i data-lucide="alert-triangle" class="w-8 h-8 text-danger"></i>';
            $output .= '</div>';
            $output .= '<h3 class="text-lg font-semibold text-gray-800 mb-2">Yakin ingin menghapus?</h3>';
            $output .= '<p class="text-sm text-gray-500 mb-6">Kategori <span class="font-semibold text-gray-700" x-text="deleteName"></span> akan dihapus secara permanen.</p>';
            $output .= '<div class="flex items-center justify-center gap-3">';
            $output .= '<button type="button" @click="showDeleteModal = false" class="btn-secondary">Batal</button>';
            $output .= '<form :action="\'/admin/categories/delete/\' + deleteId" method="POST" class="inline">';
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