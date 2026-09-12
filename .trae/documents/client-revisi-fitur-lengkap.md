# Plan: Revisi Fitur Nurfa Beauty - Client Ibu Puji

## Ringkasan
Implementasi 6 revisi utama dari client untuk meningkatkan user experience:
1. Fitur varian produk dengan pilihan saat add-to-cart
2. Perhitungan ongkos kirim berdasarkan jarak
3. Warning text di dashboard tentang stok keranjang
4. Logo dan tanda tangan di PDF laporan penjualan
5. Badge notifikasi belum dibaca dengan angka counter
6. Penggantian label "trx" menjadi "Transaksi" di laporan

---

## Current State Analysis

### Database
- **products table**: Memiliki kolom `bundle_products` (JSON), `is_bundle`, `stock`, `price`
- **shipping_addresses table**: Tidak memiliki kolom jarak; perlu tambah `distance_km`
- **carts table**: Hanya simpan `customer_id`, `product_id`, `quantity`; tidak ada varian
- **notifications table**: Ada kolom `is_read`, perlu dipastikan ada untuk counter

### Controllers
- **Product Detail** (`app/Controllers/Customer/Product.php`): Menampilkan produk, tidak ada pilihan varian
- **Cart** (`app/Controllers/Customer/Cart.php`): Tambah ke keranjang tanpa varian; perlu validasi varian
- **Checkout** (`app/Controllers/Customer/Checkout.php`): Hardcoded shipping cost Rp 15,000; perlu hitung berdasarkan jarak
- **Report** (`app/Controllers/Admin/Report.php`): Export PDF/Excel tanpa logo & tanda tangan; label "Trx" ada di banyak tempat
- **Dashboard** (`app/Controllers/Admin/Dashboard.php`): Tidak ada warning tentang stok keranjang

### Views
- **Product Detail** (`app/Views/customer/product/detail.php`): Form add-to-cart tanpa pilihan varian
- **Cart** (`app/Views/customer/cart/index.php`): Tampilkan item tanpa info varian
- **Checkout** (`app/Views/customer/checkout/index.php`): Hardcoded ongkir Rp 15,000
- **Dashboard Admin** (`app/Views/admin/dashboard/index.php`): Tidak ada warning section
- **Report Sales** (`app/Views/admin/report/sales.php`): Label "Trx" ada di beberapa tempat
- **Navbar/Components**: Tidak ada notifikasi badge dengan angka

---

## Proposed Changes

### 1. Product Variants (JSON Column)

#### Migration: AddVariantsToProductsTable.php
**File**: `app/Database/Migrations/YYYY-MM-DD-XXXXXX_AddVariantsToProductsTable.php`
**Changes**:
- Tambah kolom `variants` (JSON, nullable) ke tabel `products`
- Format JSON: `[{"name": "size", "options": ["S", "M", "L", "XL"]}, {"name": "color", "options": ["Red", "Blue"]}]`

**Why**: Menyimpan metadata varian tanpa tabel terpisah; fleksibel untuk berbagai jenis produk

#### Model: ProductModel.php
**File**: `app/Models/ProductModel.php`
**Changes**:
- Tambah `'variants'` ke `$allowedFields`
- Tambah method `getProductVariants($productId)` untuk parsing JSON varian

#### Cart Model: CartModel.php
**File**: `app/Models/CartModel.php`
**Changes**:
- Modifikasi struktur: tambah kolom `variant_selection` (JSON) untuk menyimpan pilihan varian per item
  - Format: `{"size": "M", "color": "Blue"}`
- Update migration `CreateCartsTable` untuk add kolom baru

#### Controller: Cart.php
**File**: `app/Controllers/Customer/Cart.php`
**Changes**:
- Method `add()`: Validasi `variant_selection` POST jika produk punya varian; match dengan varian yang valid
- Tambah pengecekan: varian wajib dipilih sebelum add-to-cart jika produk punya varian
- Error message: "Pilih varian produk terlebih dahulu"

#### View: Product Detail
**File**: `app/Views/customer/product/detail.php`
**Changes**:
- Render varian selector (dropdown/button group) berdasarkan `$product['variants']` JSON
- Alpine.js: `x-data="{ variants: {...} }"` untuk track pilihan varian
- Disabled "Tambah ke Keranjang" jika varian belum lengkap dipilih
- Hidden input `variant_selection` untuk POST ke cart/add dengan JSON nilai

#### View: Cart Index
**File**: `app/Views/customer/cart/index.php`
**Changes**:
- Tampilkan `variant_selection` info di setiap item (e.g., "Size: M, Color: Blue")

---

### 2. Shipping Cost by Distance

#### Migration: AddDistanceToShippingAddressesTable.php
**File**: `app/Database/Migrations/YYYY-MM-DD-XXXXXX_AddDistanceToShippingAddressesTable.php`
**Changes**:
- Tambah kolom `distance_km` (DECIMAL(8,2), nullable, default NULL) ke tabel `shipping_addresses`

**Why**: Admin/customer akan input jarak secara manual saat create/edit alamat pengiriman

#### Controller: Checkout.php
**File**: `app/Controllers/Customer/Checkout.php`
**Changes**:
- Method `index()` & `process()`:
  - Ambil `distance_km` dari alamat shipping yang dipilih
  - Hitung shipping cost berdasarkan formula:
    - 0-5 km: Rp 10,000
    - 6-15 km: Rp 20,000
    - 16-30 km: Rp 35,000
    - 31+ km: Rp 50,000
  - Ganti hardcoded `$shippingCost = 15000` dengan dynamic calculation
  - Pass `$shippingCost` dan `$distance_km` ke view

#### View: Checkout
**File**: `app/Views/customer/checkout/index.php`
**Changes**:
- Tampilkan breakdown: "Jarak: {distance_km} km → Ongkir: Rp {shippingCost}"
- Update total calculation jika alamat berubah via AJAX

#### Controller: Address (create/edit)
**File**: `app/Controllers/Customer/Address.php`
**Changes**:
- Method untuk create/edit alamat: tambah input field `distance_km` (text input untuk angka)
- Validasi: `distance_km` harus numeric, min 0.5, max 500

---

### 3. Dashboard Warning About Cart Stock

#### View: Dashboard Customer
**File**: `app/Views/customer/home/index.php` atau landing page
**Changes**:
- Tambah section warning box (warna merah) dengan text:
  ```
  ⚠️ Penting: Ketika Anda sudah memasukkan produk dalam keranjang dan tidak melakukan checkout segera, kami tidak bertanggung jawab jika stok habis dan Anda tidak bisa memesan, karena siapa cepat dia dapat.
  ```
- CSS: `bg-red-50 border border-red-200 text-red-700`
- Icon: warning triangle

**Why**: Inform customer tentang behavior stok yang first-come-first-served

---

### 4. PDF Report with Logo & Signature

#### File Locations
- Logo: `public/assets/logo.svg` (sudah ada atau perlu disediakan)
- Placeholder: Teks "Pejabat Penanggung Jawab: _________________" dengan garis tanda tangan

#### Controller: Report.php
**File**: `app/Controllers/Admin/Report.php`
**Changes**:
- Method `exportSalesPdf()`:
  - Embed base64 logo SVG di HTML sebelum `<h2>Laporan Penjualan...</h2>`
  - Tambah section akhir sebelum closing `</body>`:
    ```html
    <div style="margin-top: 40px; text-align: center;">
        <div style="margin-bottom: 20px;">
            <p style="font-size: 10px; text-align: left; margin: 0;">Pejabat Penanggung Jawab:</p>
            <div style="height: 40px; border-bottom: 1px solid #000; margin-top: 30px;"></div>
            <p style="font-size: 10px; margin-top: 5px;margin-bottom: 0;">(...........................)</p>
        </div>
    </div>
    ```
  - Ganti label "Trx" dengan "Transaksi" di semua tempat (breakdown table, top products)
  - Update kolom header: "Total Trx" → "Total Transaksi"

- Method `exportCustomerPdf()` dan `exportLoyaltyPdf()`:
  - Sama: embed logo, tambah tanda tangan placeholder, ganti "Trx"

---

### 5. Unread Notification Badge Counter

#### Model: NotificationModel.php
**File**: `app/Models/NotificationModel.php`
**Changes**:
- Tambah method `getUnreadCount($customerId)` - return count where `is_read = 0`

#### Controller: API Notification (atau Base Controller)
**File**: `app/Controllers/Api/Notification.php` atau middleware
**Changes**:
- Buat endpoint API: `GET /api/notifications/unread-count` → return `{ "count": 5 }`
- Response: JSON with unread count

#### View: Navbar Component
**File**: `app/Views/components/navbar.php` atau layout customer
**Changes**:
- Tambah Alpine.js x-data untuk fetch unread count saat page load
- Render badge: `<span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" x-text="notificationCount"></span>`
- Init: `async function loadNotificationCount() { const res = await fetch('/api/notifications/unread-count'); const data = await res.json(); this.notificationCount = data.count || 0; }`
- Call `loadNotificationCount()` di Alpine init

#### Controller: Notification Dashboard
**File**: `app/Controllers/Customer/Dashboard.php` atau `Notification.php`
**Changes**:
- Method `markAsRead()`: Update `is_read = 1` saat notification diklik
- Method untuk update badge count saat mark as read (via AJAX)

---

### 6. Replace "Trx" with "Transaksi" in Reports

#### Controller: Report.php
**File**: `app/Controllers/Admin/Report.php`
**Changes**:
- Method `sales()`: Ganti semua `'trx'` / `'Trx'` label dengan `'Transaksi'`
  - Line 481: `'<th>Total Trx</th>'` → `'<th>Total Transaksi</th>'`
  - Line 486: `'trx</span>'` → `'Transaksi</span>'`
  - Excel column: `'C10'` value `'Trx Online'` → `'Transaksi Online'`

- Method `exportSalesPdf()`: Ganti label di HTML
- Method `exportSalesExcel()`: Ganti label di sheet
- Global replace di file: semua `' Trx'`, `'Trx '`, `' trx'`, `'trx '` → `' Transaksi'`, `'Transaksi '`, etc.

#### View: Report Sales
**File**: `app/Views/admin/report/sales.php`
**Changes**:
- Ganti kolom header breakdown: `'Total Trx'` → `'Total Transaksi'`
- Table row: ganti display label

---

## Database Migrations Summary

1. `AddVariantsToProductsTable.php`
   - ADD `variants` JSON column to `products`

2. `AddDistanceToShippingAddressesTable.php`
   - ADD `distance_km` DECIMAL(8,2) NULL to `shipping_addresses`

3. `AddVariantSelectionToCartsTable.php`
   - ADD `variant_selection` JSON column to `carts`

---

## Files to Modify

### Models
- `app/Models/ProductModel.php` - add variants method
- `app/Models/CartModel.php` - add variant_selection column support
- `app/Models/NotificationModel.php` - add getUnreadCount method

### Controllers
- `app/Controllers/Customer/Product.php` - pass variants to view
- `app/Controllers/Customer/Cart.php` - validate variants, add variant_selection
- `app/Controllers/Customer/Checkout.php` - calculate shipping by distance
- `app/Controllers/Customer/Address.php` - add distance_km input
- `app/Controllers/Admin/Report.php` - add logo, signature, replace "Trx"
- `app/Controllers/Api/Notification.php` - new endpoint for unread count (or add to existing)
- `app/Controllers/Customer/Dashboard.php` - add notification mark as read logic

### Views
- `app/Views/customer/product/detail.php` - render variant selector
- `app/Views/customer/cart/index.php` - display variant info
- `app/Views/customer/checkout/index.php` - display shipping cost calculation
- `app/Views/customer/home/index.php` - add warning box
- `app/Views/admin/dashboard/index.php` - add warning section
- `app/Views/admin/report/sales.php` - replace "Trx" labels
- `app/Views/components/navbar.php` - add notification badge counter

### Assets
- Ensure `public/assets/logo.svg` exists or provide placeholder

---

## Assumptions & Decisions

1. **Varian JSON Format**: Single column `variants` dalam format JSON array (flexible)
2. **Jarak Manual**: Admin/customer input distance_km saat create/edit alamat (tidak pakai API eksternal)
3. **Shipping Formula**: Tiered pricing berdasarkan km (0-5: 10k, 6-15: 20k, 16-30: 35k, 31+: 50k)
4. **Logo File**: Assume `public/assets/logo.svg` tersedia; jika PNG di uploads folder, adjust path
5. **Notifikasi Badge**: Tampilkan di navbar ikon notif menggunakan Alpine.js
6. **Penggantian "Trx"**: Semua kemunculan di report (PDF, Excel, halaman) diganti "Transaksi"

---

## Verification Steps

1. **Varian Produk**
   - Admin create produk dengan variants JSON
   - Customer view detail, lihat selector varian
   - Add-to-cart tanpa pilih varian → error
   - Add-to-cart dengan varian lengkap → success, cart tampilkan varian

2. **Ongkir Dinamis**
   - Create alamat dengan distance_km berbeda
   - Checkout: lihat shipping cost berubah sesuai jarak
   - PDF laporan: tampilkan jarak dan ongkir

3. **Warning Dashboard**
   - Cek halaman home/dashboard customer
   - Warning box terlihat dengan warna merah

4. **PDF Logo & Tanda Tangan**
   - Export PDF laporan penjualan
   - Verify logo tampil di atas
   - Verify tanda tangan placeholder di bawah

5. **Notif Badge Counter**
   - Create notifikasi baru
   - Navbar tampilkan badge angka
   - Klik notif → mark as read, badge update

6. **"Trx" → "Transaksi"**
   - View laporan penjualan halaman
   - Export PDF laporan
   - Export Excel laporan
   - Semua label "Trx" berganti "Transaksi"

