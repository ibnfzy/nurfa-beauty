<?= $this->extend('layouts/customer') ?>

<?php
/** @var string $pageTitle */
/** @var array $addresses */
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<?= view('components/breadcrumb', [
    'items' => [
        ['label' => 'Beranda', 'url' => base_url()],
        ['label' => 'Alamat Pengiriman'],
    ]
]) ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    showCreateModal: false,
    showEditModal: false,
    showDeleteModal: false,
    editData: { id: '', label: '', recipient_name: '', phone: '', address: '', province: '', city: '', district: '', postal_code: '', is_default: false },
    deleteId: '',
    deleteLabel: '',
    openEdit(id, label, recipient_name, phone, address, province, city, district, postal_code, is_default) {
        this.editData = { id, label, recipient_name, phone, address, province, city, district, postal_code, is_default: is_default == 1 };
        this.showEditModal = true;
    },
    openDelete(id, label) {
        this.deleteId = id;
        this.deleteLabel = label;
        this.showDeleteModal = true;
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Alamat Pengiriman</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola alamat pengiriman Anda</p>
        </div>
        <button @click="showCreateModal = true" class="btn-primary inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Alamat
        </button>
    </div>

    <!-- Address List -->
    <?php if (empty($addresses ?? [])): ?>
        <?= view('components/empty-state', [
            'icon'        => 'map-pin',
            'title'       => 'Belum ada alamat',
            'description' => 'Tambahkan alamat pengiriman untuk mempermudah proses checkout.',
        ]) ?>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($addresses ?? [] as $addr): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-semibold text-gray-800"><?= esc($addr['label'] ?? '') ?></h3>
                                <?php if ($addr['is_default'] ?? false): ?>
                                    <?= view('components/badge', ['text' => 'Utama', 'color' => 'success']) ?>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm font-medium text-gray-700"><?= esc($addr['recipient_name'] ?? '') ?></p>
                            <p class="text-sm text-gray-500"><?= esc($addr['phone'] ?? '') ?></p>
                            <p class="text-sm text-gray-600 mt-1"><?= esc($addr['address'] ?? '') ?></p>
                            <p class="text-sm text-gray-500"><?= esc($addr['district'] ? $addr['district'] . ', ' : '') ?><?= esc($addr['city'] ?? '') ?>, <?= esc($addr['province'] ?? '') ?> <?= esc($addr['postal_code'] ?? '') ?></p>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button @click="openEdit(
                                '<?= $addr['id'] ?? 0 ?>',
                                '<?= esc($addr['label'] ?? '', 'js') ?>',
                                '<?= esc($addr['recipient_name'] ?? '', 'js') ?>',
                                '<?= esc($addr['phone'] ?? '', 'js') ?>',
                                '<?= esc($addr['address'] ?? '', 'js') ?>',
                                '<?= esc($addr['province'] ?? '', 'js') ?>',
                                '<?= esc($addr['city'] ?? '', 'js') ?>',
                                '<?= esc($addr['district'] ?? '', 'js') ?>',
                                '<?= esc($addr['postal_code'] ?? '', 'js') ?>',
                                '<?= $addr['is_default'] ?? 0 ?>'
                            )" class="p-2 rounded-lg text-info hover:bg-blue-50 transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <button @click="openDelete('<?= $addr['id'] ?? 0 ?>', '<?= esc($addr['label'] ?? '', 'js') ?>')" class="p-2 rounded-lg text-danger hover:bg-red-50 transition-colors" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                            <?php if (!($addr['is_default'] ?? false)): ?>
                                <form action="<?= base_url('address/set-default/' . ($addr['id'] ?? 0)) ?>" method="POST" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="p-2 rounded-lg text-secondary hover:bg-orange-50 transition-colors" title="Jadikan Alamat Utama">
                                        <i data-lucide="star" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Create Modal -->
    <?= view('components/modal', [
        'showVar' => 'showCreateModal',
        'title'   => 'Tambah Alamat',
        'slot'    => (function () {
            $output = '<form action="' . base_url('address/store') . '" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div>';
            $output .= '<label for="create_label" class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_label" name="label" value="" required maxlength="50" class="input-field" placeholder="Contoh: Rumah, Kantor">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_recipient_name" name="recipient_name" value="" required maxlength="100" class="input-field" placeholder="Nama lengkap penerima">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_phone" name="phone" value="" required maxlength="20" class="input-field" placeholder="Nomor telepon">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-danger">*</span></label>';
            $output .= '<textarea id="create_address" name="address" rows="3" required class="input-field" placeholder="Alamat lengkap"></textarea>';
            $output .= '</div>';
            $output .= '<div class="grid grid-cols-2 gap-4">';
            $output .= '<div>';
            $output .= '<label for="create_province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_province" name="province" value="" required maxlength="100" class="input-field" placeholder="Provinsi">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_city" class="block text-sm font-medium text-gray-700 mb-1">Kota <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_city" name="city" value="" required maxlength="100" class="input-field" placeholder="Kota">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="grid grid-cols-2 gap-4">';
            $output .= '<div>';
            $output .= '<label for="create_district" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>';
            $output .= '<input type="text" id="create_district" name="district" value="" maxlength="100" class="input-field" placeholder="Kecamatan">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="create_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="create_postal_code" name="postal_code" value="" required maxlength="10" class="input-field" placeholder="Kode Pos">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="flex items-center gap-2">';
            $output .= '<input type="checkbox" id="create_is_default" name="is_default" value="1" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">';
            $output .= '<label for="create_is_default" class="text-sm text-gray-700">Jadikan Alamat Utama</label>';
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
        'title'   => 'Edit Alamat',
        'slot'    => (function () {
            $output = '<form :action="\'/address/update/\' + editData.id" method="POST">';
            $output .= csrf_field();
            $output .= '<div class="space-y-4">';
            $output .= '<div>';
            $output .= '<label for="edit_label" class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_label" name="label" x-model="editData.label" required maxlength="50" class="input-field" placeholder="Contoh: Rumah, Kantor">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_recipient_name" name="recipient_name" x-model="editData.recipient_name" required maxlength="100" class="input-field" placeholder="Nama lengkap penerima">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_phone" name="phone" x-model="editData.phone" required maxlength="20" class="input-field" placeholder="Nomor telepon">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-danger">*</span></label>';
            $output .= '<textarea id="edit_address" name="address" rows="3" x-model="editData.address" required class="input-field" placeholder="Alamat lengkap"></textarea>';
            $output .= '</div>';
            $output .= '<div class="grid grid-cols-2 gap-4">';
            $output .= '<div>';
            $output .= '<label for="edit_province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_province" name="province" x-model="editData.province" required maxlength="100" class="input-field" placeholder="Provinsi">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_city" class="block text-sm font-medium text-gray-700 mb-1">Kota <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_city" name="city" x-model="editData.city" required maxlength="100" class="input-field" placeholder="Kota">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="grid grid-cols-2 gap-4">';
            $output .= '<div>';
            $output .= '<label for="edit_district" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>';
            $output .= '<input type="text" id="edit_district" name="district" x-model="editData.district" maxlength="100" class="input-field" placeholder="Kecamatan">';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<label for="edit_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos <span class="text-danger">*</span></label>';
            $output .= '<input type="text" id="edit_postal_code" name="postal_code" x-model="editData.postal_code" required maxlength="10" class="input-field" placeholder="Kode Pos">';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="flex items-center gap-2">';
            $output .= '<input type="checkbox" id="edit_is_default" name="is_default" value="1" x-model="editData.is_default" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">';
            $output .= '<label for="edit_is_default" class="text-sm text-gray-700">Jadikan Alamat Utama</label>';
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
        'title'     => 'Hapus Alamat',
        'maxWidth'  => 'max-w-md',
        'slot'      => (function () {
            $output = '<div class="text-center">';
            $output .= '<div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">';
            $output .= '<i data-lucide="alert-triangle" class="w-8 h-8 text-danger"></i>';
            $output .= '</div>';
            $output .= '<h3 class="text-lg font-semibold text-gray-800 mb-2">Yakin ingin menghapus?</h3>';
            $output .= '<p class="text-sm text-gray-500 mb-6">Alamat <span class="font-semibold text-gray-700" x-text="deleteLabel"></span> akan dihapus secara permanen.</p>';
            $output .= '<div class="flex items-center justify-center gap-3">';
            $output .= '<button type="button" @click="showDeleteModal = false" class="btn-secondary">Batal</button>';
            $output = '<form :action="\'/address/delete/\' + deleteId" method="POST" class="inline">';
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