# Rencana Membangun Fitur Variant Produk

## Ringkasan

Menambahkan pengelolaan variant berbasis atribut pada form tambah dan edit produk. Admin dapat membuat beberapa kombinasi atribut seperti `Warna: Merah` dan `Ukuran: M`, lalu menyimpannya ke kolom JSON `products.variants`. Harga dan stok tetap menggunakan produk utama; variant hanya menjadi pilihan atribut customer.

## Analisis Kondisi Saat Ini

- `products.variants` sudah tersedia sebagai kolom `TEXT` dan `ProductModel` sudah mengizinkan field `variants`.
- Route create/store/edit/update produk sudah tersedia sehingga tidak perlu route baru atau controller variant terpisah.
- [Product.php](file:///Users/pallakadev/nurfa-beauty/app/Controllers/Admin/Product.php) belum membaca, memvalidasi, atau menyimpan `variants` pada `store()` dan `update()`.
- [create.php](file:///Users/pallakadev/nurfa-beauty/app/Views/admin/product/create.php) dan [edit.php](file:///Users/pallakadev/nurfa-beauty/app/Views/admin/product/edit.php) sudah menggunakan Alpine.js untuk input dinamis bundle; pola yang sama dapat digunakan untuk variant.
- [detail.php](file:///Users/pallakadev/nurfa-beauty/app/Views/customer/product/detail.php) sudah membaca array objek variant dan mengirim pilihan customer sebagai `variant_selection`.
- [Cart.php](file:///Users/pallakadev/nurfa-beauty/app/Controllers/Customer/Cart.php) sudah memvalidasi pilihan variant, tetapi validasinya perlu disesuaikan agar mencocokkan kombinasi lengkap.
- Harga dan stok tetap berasal dari produk utama. Tidak membuat tabel baru, migrasi baru, SKU, harga variant, atau stok per variant.

## Perubahan yang Diusulkan

### 1. Form tambah produk

File: [create.php](file:///Users/pallakadev/nurfa-beauty/app/Views/admin/product/create.php)

- Tambahkan state Alpine.js `variantItems`.
- Tambahkan fungsi `addVariant()`, `removeVariant(index)`, dan `getVariantsJson()`.
- Tambahkan card `Varian Produk` di area informasi produk.
- Sediakan input nama atribut dan nilai atribut pada setiap baris, misalnya `warna` dan `Merah`.
- Sediakan tombol tambah dan hapus baris.
- Kirim hasil state melalui hidden input `name="variants"`.
- Pertahankan pola styling, CSRF, validasi session, dan Alpine.js yang sudah digunakan.

Format data yang disimpan:

```json
[
  {"warna":"Merah","ukuran":"M"},
  {"warna":"Biru","ukuran":"L"}
]
```

### 2. Form edit produk

File: [edit.php](file:///Users/pallakadev/nurfa-beauty/app/Views/admin/product/edit.php)

- Tambahkan state `variantItems` yang diinisialisasi dari `product['variants']`.
- Gunakan JSON escaping yang aman dan fallback `[]` untuk data kosong atau invalid.
- Tambahkan UI tambah/hapus baris yang sama dengan halaman create.
- Tambahkan hidden input `variants` melalui `getVariantsJson()`.
- Gunakan data lama saat halaman dikembalikan setelah validasi gagal jika pola view memungkinkan.

### 3. Simpan dan validasi variant admin

File: [Product.php](file:///Users/pallakadev/nurfa-beauty/app/Controllers/Admin/Product.php)

- Tambahkan pembacaan POST `variants` pada `store()` dan `update()`.
- Buat normalisasi sederhana untuk JSON variant:
  - string kosong menjadi `null`;
  - JSON harus valid dan menghasilkan array;
  - setiap item harus berupa object/array atribut;
  - nama atribut dan nilai wajib tidak kosong;
  - nama atribut dinormalisasi agar konsisten;
  - kombinasi duplikat ditolak;
  - variant kosong dibuang atau ditolak secara konsisten.
- Simpan hasil JSON yang telah dinormalisasi ke field `variants`.
- Saat tidak ada variant, simpan `null` agar customer tetap diperlakukan sebagai produk tanpa variant.
- Tambahkan pesan validasi berbahasa Indonesia melalui flash error yang mengikuti pola controller saat ini.
- Jangan mengubah proses harga, stok utama, bundle, upload gambar, atau status produk.

### 4. Validasi pilihan variant customer

File: [Cart.php](file:///Users/pallakadev/nurfa-beauty/app/Controllers/Customer/Cart.php)

- Pertahankan kewajiban memilih variant bila produk memiliki variant.
- Ubah validasi agar pilihan customer harus cocok dengan satu kombinasi variant lengkap, bukan hanya memastikan setiap nilai pernah muncul pada variant mana pun.
- Pastikan semua atribut yang diperlukan dari kombinasi tersedia.
- Tetap gunakan stok utama `products.stock` untuk validasi quantity.
- Tidak mengubah struktur cart atau membuat tabel baru.

### 5. Pastikan kompatibilitas tampilan customer

File: [detail.php](file:///Users/pallakadev/nurfa-beauty/app/Views/customer/product/detail.php)

- Periksa dan sesuaikan hanya jika diperlukan agar format JSON baru langsung cocok dengan selector yang sudah ada.
- Pertahankan tampilan pilihan atribut dan hidden input `variant_selection`.
- Tidak menambahkan harga/stok per variant.

## Asumsi dan Keputusan

- Model yang dipilih adalah **atribut saja**.
- Harga dan stok tetap berada pada produk utama.
- Penyimpanan tetap menggunakan `products.variants` sebagai JSON.
- Tidak membuat `VariantController`, `VariantModel`, tabel `product_variants`, atau migrasi database baru.
- Tidak menambahkan SKU, status variant, harga variant, atau stok variant.
- Tidak mengubah alur checkout dan histori transaksi karena variant tidak memiliki data komersial terpisah pada scope ini.
- Route existing `/admin/products/create`, `/admin/products/store`, `/admin/products/edit/:id`, dan `/admin/products/update/:id` dipakai.
- Operasi database hanya diperlukan saat aplikasi berjalan untuk menyimpan form; tidak menjalankan migrate, seeder, atau operasi database langsung selama implementasi.

## Verifikasi

1. Buka halaman tambah produk, tambahkan beberapa baris atribut, simpan, lalu pastikan data tersimpan pada `products.variants`.
2. Buka edit produk yang memiliki variant, pastikan semua baris tampil dan dapat diubah atau dihapus.
3. Uji JSON invalid, atribut kosong, dan kombinasi duplikat; pastikan form ditolak dengan pesan yang jelas.
4. Buka detail customer untuk produk ber-variant; pastikan seluruh atribut dan nilai tampil.
5. Uji kombinasi valid dan kombinasi silang yang tidak pernah didefinisikan; hanya kombinasi valid yang boleh masuk cart.
6. Uji produk tanpa variant; customer tetap dapat menambahkan produk tanpa selector variant.
7. Jalankan pemeriksaan sintaks/linter dan test yang tersedia tanpa menjalankan migrasi atau seeder.
