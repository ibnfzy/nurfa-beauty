# Rencana Perubahan Logic Variant Produk (Minimal 1 Varian)

## Summary
Mengubah logika validasi pemilihan varian produk pada halaman detail produk, proses penambahan ke keranjang, dan proses checkout agar pembeli dapat memasukkan produk ke keranjang atau membeli produk cukup dengan memilih **minimal 1 varian** (tidak harus semua varian produk dipilih).

---

## Analysis & Current State

### Halaman Detail Produk (`app/Views/customer/product/detail.php`)
- **Kondisi saat ini**: Menggunakan logika Alpine.js `hasAllVariantsSelected()` yang mewajibkan `Object.keys(this.variantOptions).length === Object.keys(this.selectedVariant).length`.
- **Pesan error saat ini**: `"Silakan pilih semua varian produk terlebih dahulu."`

### Controller Cart (`app/Controllers/Customer/Cart.php`)
- **Kondisi saat ini**: Method `add()` mengecek `count($selectedVariant) !== count($available)`. Jika tidak semua varian dipilih, sistem menolak request dengan pesan `"Silakan pilih semua varian produk."`

### Controller Checkout (`app/Controllers/Customer/Checkout.php`)
- **Kondisi saat ini**: Method `process()` mengecek `count($selection) !== count($variants)`. Jika item dalam keranjang tidak memiliki seluruh varian, checkout dibatalkan dengan pesan `"Silakan pilih semua varian produk sebelum checkout."`

### Flow Transaksi & Laporan
- **Keranjang (`app/Views/customer/cart/index.php`)**: Menampilkan `variant_selection` dengan membaca objek JSON secara dinamis via `implode(', ', ...)` (sudah mendukung 1 atau banyak opsi varian).
- **Detail Transaksi Customer & Admin (`app/Views/customer/transaction/detail.php` & `app/Views/admin/transaction/detail.php`)**: Membaca dan menampilkan `variant_selection` secara dinamis.
- **Laporan Penjualan Admin (`app/Controllers/Admin/Report.php` & `app/Views/admin/report/sales.php`)**: Menggabungkan dan mendecode string JSON `variant_selection` secara dinamis untuk produk terlaris.

---

## Proposed Changes

### 1. Update Halaman Detail Produk (`app/Views/customer/product/detail.php`)
- Ubah method Alpine.js `hasAllVariantsSelected()` menjadi `hasAtLeastOneVariantSelected()`:
  - Memeriksa apakah `Object.keys(this.variantOptions).length === 0` ATAU `(Object.keys(this.selectedVariant).length > 0 && Object.values(this.selectedVariant).some(v => !!v))`.
- Update method `validateVariant()` dan pesan error jika tidak ada varian yang dipilih:
  - Pesan error: `"Silakan pilih minimal satu varian produk terlebih dahulu."`

### 2. Update Controller Cart (`app/Controllers/Customer/Cart.php`)
- Pada method `add()`, perbarui validasi varian:
  - Izinkan jika `is_array($selectedVariant) && count($selectedVariant) >= 1`.
  - Ubah pesan error jika belum memilih varian menjadi `"Silakan pilih minimal satu varian produk."`
  - Validasi bahwa varian yang dipilih memang ada dalam daftar atribut varian produk.

### 3. Update Controller Checkout (`app/Controllers/Customer/Checkout.php`)
- Pada method `process()`, perbarui validasi varian sebelum checkout:
  - Ganti pengecekan `count($selection) !== count($variants)` menjadi `count($selection) < 1` (jika produk memiliki daftar varian).
  - Pesan error jika tidak valid: `"Silakan pilih minimal satu varian produk sebelum checkout."`

---

## Verification Steps

1. **Uji Coba Halaman Detail Produk (`/product/detail/{id}`)**:
   - Buka produk yang memiliki lebih dari 1 jenis atribut varian (misal: Warna dan Ukuran).
   - Klik submit tanpa memilih varian -> harus muncul pesan error `"Silakan pilih minimal satu varian produk terlebih dahulu."`
   - Pilih hanya 1 varian (misal: Warna saja) -> Form dapat disubmit dan berhasil ditambahkan ke keranjang.

2. **Uji Coba Keranjang Belanja (`/cart`)**:
   - Pastikan produk dengan 1 varian terpilih tampil dengan benar di keranjang belanja.

3. **Uji Coba Checkout (`/checkout`)**:
   - Lanjutkan proses checkout dengan item yang memiliki 1 varian terpilih.
   - Buat pesanan dan pastikan transaksi berhasil dibuat tanpa kendala validasi.

4. **Uji Coba Transaksi & Laporan Penjualan**:
   - Periksa Halaman Detail Transaksi (Customer & Admin) -> varian terpilih ditampilkan secara presisi.
   - Periksa Halaman Laporan Penjualan Admin (`/admin/report/sales`) -> data produk terlaris & varian terpilih direkap dengan benar.
