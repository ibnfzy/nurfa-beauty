<?= $this->extend('layouts/admin') ?>
<?php
/** @var string $pageTitle */
/** @var array $bankAccounts */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Rekening Bank'],
    ]
]) ?>

<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    showDeleteModal: false,
    editData: { id: '', bank_name: '', account_number: '', account_name: '', is_active: 1 },
    deleteId: '',
    deleteName: '',
    openEdit(id, bank_name, account_number, account_name, is_active) {
        this.editData = { id, bank_name, account_number, account_name, is_active: is_active ? 1 : 0 };
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
            <h2 class="text-xl font-semibold text-gray-800">Daftar Rekening Bank</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola rekening bank untuk pembayaran</p>
        </div>
        <button @click="showCreateModal = true" class="btn-primary inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Rekening
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-primary-light/30 overflow-hidden">
        <?php if (empty($banks)): ?>
            <?= view('components/empty-state', [
                'icon'        => 'landmark',
                'title'       => 'Belum ada rekening bank',
                'description' => 'Mulai tambahkan rekening bank untuk menerima pembayaran.',
                'actionText'  => 'Tambah Rekening',
            ]) ?>
        <?php else: ?>
            <?= view('components/table', [
                'headers' => ['No', 'Nama Bank', 'Nomor Rekening', 'Atas Nama', 'Status', 'Aksi'],
                'slot'    => (function() use ($banks) {
                    $output = '';
                    $no = 1;
                    foreach ($banks as $bank) {
                        $isActive = $bank['is_active'] ?? 1;
                        $statusColor = $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                        $statusText = $isActive ? 'Aktif' : 'Nonaktif';

                        $output .= '<tr class="hover:bg-cream/50 transition-colors">';
                        $output .= '<td class="py-3 px-4 text-gray-500">' . $no++ . '</td>';
                        $output .= '<td class="py-3 px-4 font-medium text-gray-800">' . esc($bank['bank_name']) . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($bank['account_number']) . '</td>';
                        $output .= '<td class="py-3 px-4 text-gray-600">' . esc($bank['account_name']) . '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $statusColor . '">' . $statusText . '</span>';
                        $output .= '</td>';
                        $output .= '<td class="py-3 px-4">';
                        $output .= '<div class="flex items-center gap-2">';
                        $output .= '<button @click="openEdit(\'' . $bank['id'] . '\', \'' . esc($bank['bank_name'], 'js') . '\', \'' . esc($bank['account_number'], 'js') . '\', \'' . esc($bank['account_name'], 'js') . '\', ' . ($bank['is_active'] ?? 1) . ')" class="p-1.5 rounded-lg text-info hover:bg-blue-50 transition-colors" title="Edit">';
                        $output .= '<i data-lucide="pencil" class="w-4 h-4"></i>';
                        $output .= '</button>';
                        $output .= '<button @click="openDelete(\'' . $bank['id'] . '\', \'' . esc($bank['bank_name'], 'js') . ' - ' . esc($bank['account_number'], 'js') . '\')" class="p-1.5 rounded-lg text-danger hover:bg-red-50 transition-colors" title="Hapus">';
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

    <!-- Create Modal -->
    <?= view('components/modal', [
        'showVar' => 'showCreateModal',
        'title'   => 'Tambah Rekening Bank',
        'slot'    => (function() {
            $output = '<form action="' . base_url('admin/banks/store') . '" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div>';
            $output .= '<label for="create_bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_bank_name" name="bank_name" value="" required maxlength="50" class="input-field" placeholder="Contoh: BCA, Mandiri, BRI">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_account_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_account_number" name="account_number" value="" required maxlength="50" class="input-field" placeholder="Masukkan nomor rekening">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_account_name" class="block text-sm font-medium text-gray-700 mb-1">Atas Nama <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_account_name" name="account_name" value="" required maxlength="100" class="input-field" placeholder="Masukkan nama pemilik rekening">';
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
        'title'   => 'Edit Rekening Bank',
        'slot'    => (function() {
            $output = '<form :action="\'/admin/banks/update/\' + editData.id" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div>';
            $output .= '<label for="edit_bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bank <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_bank_name" name="bank_name" x-model="editData.bank_name" required maxlength="50" class="input-field" placeholder="Contoh: BCA, Mandiri, BRI">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_account_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_account_number" name="account_number" x-model="editData.account_number" required maxlength="50" class="input-field" placeholder="Masukkan nomor rekening">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_account_name" class="block text-sm font-medium text-gray-700 mb-1">Atas Nama <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_account_name" name="account_name" x-model="editData.account_name" required maxlength="100" class="input-field" placeholder="Masukkan nama pemilik rekening">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label class="flex items-center gap-2 cursor-pointer">';
            $output .= '<input type="checkbox" name="is_active" value="1" x-model="editData.is_active" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">';
            $output .= '<span class="text-sm font-medium text-gray-700">Aktif</span>';
            $output .= '</label>';
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
        'title'     => 'Hapus Rekening Bank',
        'maxWidth'  => 'max-w-md',
        'slot'      => (function() {
            $output = '<div class="text-center">';
            $output .= '<div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">';
            $output .= '<i data-lucide="alert-triangle" class="w-8 h-8 text-danger"></i>';
            $output .= '</div>';
            $output .= '<h3 class="text-lg font-semibold text-gray-800 mb-2">Yakin ingin menghapus?</h3>';
            $output .= '<p class="text-sm text-gray-500 mb-6">Rekening <span class="font-semibold text-gray-700" x-text="deleteName"></span> akan dihapus secara permanen.</p>';
            $output .= '<div class="flex items-center justify-center gap-3">';
            $output .= '<button type="button" @click="showDeleteModal = false" class="btn-secondary">Batal</button>';
            $output .= '<form :action="\'/admin/banks/delete/\' + deleteId" method="POST" class="inline">';
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
