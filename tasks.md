# Tasks - Nurfa Beauty Shop

> Checklist implementasi berdasarkan sprint plan. Centang `[x]` setelah selesai dikerjakan.

---

## Sprint 1: Setup & Auth

### Setup Project
- [x] Install CodeIgniter 4 fresh
- [x] Konfigurasi database MySQL di `.env` (database: nurfa, user: root)
- [x] Install & setup Tailwind CSS 3
- [x] Install Alpine.js
- [x] Setup Google Fonts (Playfair Display + Poppins)
- [x] Setup Lucide Icons
- [x] Konfigurasi `tailwind.config.js` (warna primary, secondary, cream, dll sesuai design system)

### Database Migrations
- [x] `2026-07-31-112523_CreateUsersTable.php` - users
- [x] `2026-07-31-112542_CreateCustomersTable.php` - customers
- [x] `2026-07-31-112559_CreateCategoriesTable.php` - categories
- [x] `2026-07-31-112604_CreateProductsTable.php` - products
- [x] `2026-07-31-112605_CreateShippingAddressesTable.php` - shipping_addresses
- [x] `2026-07-31-112607_CreateTransactionsTable.php` - transactions
- [x] `2026-07-31-112612_CreateBankAccountsTable.php` - bank_accounts
- [x] `2026-07-31-112616_CreateTransactionItemsTable.php` - transaction_items
- [x] `2026-07-31-112618_CreatePromotionsTable.php` - promotions
- [x] `2026-07-31-112619_CreateVouchersTable.php` - vouchers
- [x] `2026-07-31-112620_CreateLoyaltyPointsLogTable.php` - loyalty_points_log
- [x] `2026-07-31-112626_CreateReviewsTable.php` - reviews
- [x] `2026-07-31-112627_CreateNotificationsTable.php` - notifications
- [x] `2026-07-31-112629_CreateWishlistsTable.php` - wishlists

### Auth
- [x] Buat `Auth\Login` controller (index + process)
- [x] Buat `Auth\Register` controller (index + process)
- [x] Buat `Auth\Logout` controller
- [x] Buat view `auth/login.php`
- [x] Buat view `auth/register.php`
- [x] Setup auth filter/protect routes

### Layouts & Components
- [x] Buat `layouts/admin.php` - layout utama admin (sidebar + navbar + content area)
- [x] Buat `layouts/customer.php` - layout utama customer (navbar + content + footer)
- [x] Buat `layouts/auth.php` - layout halaman login/register
- [x] Buat `components/navbar.php`
- [x] Buat `components/sidebar.php` (admin)
- [x] Buat `components/footer.php`
- [x] Buat `components/card.php`
- [x] Buat `components/modal.php`
- [x] Buat `components/table.php`
- [x] Buat `components/badge.php`
- [x] Buat `components/toast.php`
- [x] Buat `components/breadcrumb.php`
- [x] Buat `components/pagination.php`
- [x] Buat `components/search.php`
- [x] Buat `components/stat-card.php`
- [x] Buat `components/product-card.php`
- [x] Buat `components/empty-state.php`
- [x] Buat `partials/meta.php`
- [x] Buat `partials/scripts.php`
- [x] Buat `partials/assets.php`

### Seeding
- [x] Buat seeder admin default
- [x] Buat seeder kategori dummy (Perawatan Rambut, Wajah, Badan)

---

## Sprint 2: Master Data

### Kategori Produk
- [x] Buat `CategoryModel.php`
- [x] Buat `Admin\Category` controller (index, store, update, delete)
- [x] Buat view `admin/category/index.php` (list + form modal CRUD)
- [x] Validasi input & CSRF protection

### Produk
- [x] Buat `ProductModel.php`
- [x] Buat `Admin\Product` controller (index, create, store, edit, update, delete)
- [x] Buat view `admin/product/index.php` (list produk + search + filter kategori)
- [x] Buat view `admin/product/create.php` (form tambah + upload gambar)
- [x] Buat view `admin/product/edit.php` (form edit + preview gambar)
- [x] Setup upload gambar produk ke `writable/uploads/products/`
- [x] Manajemen stok (update stok langsung dari list)

### Pelanggan
- [x] Buat `CustomerModel.php`
- [x] Buat `Admin\Customer` controller (index, detail, update)
- [x] Buat view `admin/customer/index.php` (list pelanggan + search)
- [x] Buat view `admin/customer/detail.php` (profil, riwayat transaksi, poin loyalitas)

### Rekening Bank
- [x] Buat `BankAccountModel.php`
- [x] Buat `Admin\Bank` controller (index, store, update, delete)
- [x] Buat view admin bank (list rekening + form modal CRUD)

---

## Sprint 3: Transaksi & Penjualan

### Alamat Pengiriman (Customer)
- [x] Buat `ShippingAddressModel.php`
- [x] Buat `Customer\Address` controller (index, store, update, delete, setDefault)
- [x] Buat view `customer/address/index.php` (list alamat + form tambah/edit)
- [x] Validasi: minimal 1 alamat sebelum checkout

### Katalog Produk (Customer)
- [x] Buat `Customer\Dashboard` controller (index - tampilkan katalog)
- [x] Buat view `customer/home/index.php` (grid produk, filter kategori, search)
- [x] Buat `Customer\Product` controller (detail)
- [x] Buat view `customer/product/detail.php` (detail, harga, stok, rating, tombol tambah keranjang)

### Keranjang Belanja
- [x] Buat `CartModel.php` (session-based atau DB-based)
- [x] Buat `Customer\Cart` controller (index, add, update, remove)
- [x] Buat view `customer/cart/index.php` (list item, ubah qty, hapus, subtotal)
- [x] API AJAX: POST /api/cart/add, /api/cart/update, /api/cart/remove, GET /api/cart/count

### Checkout
- [x] Buat `Customer\Checkout` controller (index, process, uploadProof)
- [x] Buat view `customer/checkout/index.php`:
  - [x] Step 1: Pilih alamat pengiriman
  - [x] Step 2: Ringkasan pesanan (produk, subtotal, ongkir, total)
  - [x] Step 3: Tampilkan rekening bank tujuan + form upload bukti transfer
- [x] Proses checkout: buat transaksi + kurangi stok
- [x] Upload bukti transfer ke `writable/uploads/payment_proofs/`
- [x] Update status transaksi: pending_payment → pending_verification

### Input Transaksi Offline (Admin)
- [x] Buat `Admin\Transaction` controller (index, create, store, detail)
- [x] Buat view `admin/transaction/index.php` (list transaksi + filter status)
- [x] Buat view `admin/transaction/create.php` (form input transaksi offline)
- [x] Buat view `admin/transaction/detail.php` (detail transaksi)

### Verifikasi Pembayaran (Admin)
- [x] Buat `Admin\Payment` controller (index, detail, verify, reject)
- [x] Buat view `admin/payment/index.php` (list menunggu verifikasi)
- [x] Buat view `admin/payment/detail.php` (lihat bukti transfer + tombol verify/reject)
- [x] Proses verify: update status → payment_verified → processing
- [x] Proses reject: update status → payment_rejected + input alasan

### Riwayat Transaksi (Customer)
- [x] Buat `Customer\Transaction` controller (index, detail)
- [x] Buat view `customer/transaction/index.php` (list transaksi + status badge)
- [x] Buat view `customer/transaction/detail.php` (detail + status timeline)

---

## Sprint 4: Fitur CRM - GET

### Registrasi Pelanggan Online
- [x] Perbaiki flow register → otomatis buat record di tabel `customers`
- [x] Kirim voucher 20% pembelian pertama otomatis setelah registrasi
- [x] Tampilkan banner "Selamat Datang" + voucher di dashboard customer

### Diskon Pembelian Pertama
- [x] Buat logic cek first purchase di checkout
- [x] Apply voucher diskon 20% otomatis jika first purchase
- [x] Update `first_purchase_date` setelah transaksi selesai

### Katalog Produk Public
- [x] Buat akses katalog tanpa login (buka filter auth untuk halaman katalog)
- [x] Tampilkan promo badge di produk

### Landing Page Promosi
- [x] Buat view landing page promosi di customer dashboard
- [x] Tampilkan banner promo, produk diskon, produk terbaru

---

## Sprint 5: Fitur CRM - KEEP

### Sistem Poin Loyalitas
- [x] Buat `LoyaltyPointsLogModel.php`
- [x] Buat helper `loyalty_helper.php` (hitung poin berdasarkan level)
- [x] Logic: 1 poin per Rp 10.000 belanja (kalikan multiplier level)
- [x] Auto-tambah poin setelah transaksi selesai (status completed)
- [x] Kurangi poin saat redeem

### Level Membership
- [x] Logic auto-upgrade level berdasarkan total_spending:
  - Bronze: Rp 0
  - Silver: Rp 2.000.000 (Poin 1.5x, Diskon 5%)
  - Gold: Rp 5.000.000 (Poin 2x, Diskon 10%, Voucher Bulanan)
  - Platinum: Rp 10.000.000 (Poin 3x, Diskon 15%, Voucher Mingguan)
- [x] Tampilkan level & benefit di halaman loyalitas customer

### Voucher Ulang Tahun
- [x] Buat cron/scheduler cek pelanggan yang ulang tahun H-7
- [x] Auto-generate voucher diskon ulang tahun
- [x] Kirim notifikasi ke pelanggan

### Notifikasi Personal
- [x] Buat `NotificationModel.php`
- [x] Buat `Admin\Notification` controller (index, send, broadcast)
- [x] Buat view `admin/notification/index.php`
- [x] Buat API GET /api/notification + POST /api/notification/read

### Ulasan & Rating Produk
- [x] Buat `ReviewModel.php`
- [x] Buat `Customer\Review` controller (add)
- [x] Tampilkan form review di detail transaksi (status completed)
- [x] Tampilkan rating & review di halaman detail produk

---

## Sprint 6: Fitur CRM - GROW

### Rekomendasi Produk
- [x] Logic rekomendasi berdasarkan kategori riwayat pembelian
- [x] Tampilkan section "Rekomendasi untuk Anda" di dashboard customer

### Up-selling
- [x] Logic tampilkan produk premium di kategori sama (harga lebih tinggi)
- [x] Tampilkan di detail produk: "Produk Premium Serupa"

### Cross-selling
- [x] Logic "Pelanggan juga membeli" berdasarkan co-occurrence di transaksi
- [x] Tampilkan di detail produk & halaman checkout

### Bundling Paket
- [x] Tambah field `is_bundle` dan `bundle_products` di tabel products (atau tabel baru)
- [x] Admin bisa buat paket bundling
- [x] Tampilkan section "Paket Hemat" di katalog

### Poin Bonus Minimum Spend
- [x] Logic bonus poin untuk belanja > Rp 500.000
- [x] Tampilkan info bonus poin di halaman checkout

---

## Sprint 7: Promosi & Marketing

### Kelola Promosi (Admin)
- [x] Buat `PromotionModel.php`
- [x] Buat `Admin\Promotion` controller (index, create, store, edit, update)
- [x] Buat view `admin/promotion/index.php` (list promosi)
- [x] Buat view `admin/promotion/create.php` (form buat promosi)
- [x] Buat view `admin/promotion/edit.php` (form edit promosi)
- [x] Tipe: discount, voucher, flash_sale, birthday

### Generate Voucher
- [x] Buat `VoucherModel.php`
- [x] Auto-generate kode voucher unik
- [x] Assign voucher ke customer atau umum
- [x] Logic apply voucher di checkout (validasi kode, expiry, min purchase)

### Target Segmentasi Pelanggan
- [x] Filter pelanggan berdasarkan segment: all, new, loyal, vip
- [x] Pilih target saat buat promosi

### Kirim Promosi Personal
- [x] Kirim notifikasi promosi ke pelanggan terpilih
- [x] Tampilkan di halaman notifikasi customer

### Broadcast Notifikasi
- [x] Fitur broadcast ke semua pelanggan atau segment tertentu
- [x] Tampilkan badge notifikasi baru di navbar customer

---

## Sprint 8: Laporan & Analisis

### Dashboard Statistik (Admin)
- [x] Buat `Admin\Dashboard` controller dengan data statistik
- [x] Buat view `admin/dashboard/index.php`:
  - [x] Stat card: Total Penjualan, Pelanggan Baru, Stok Menipis, Transaksi Terakhir
  - [x] Chart penjualan (harian/mingguan/bulanan)
  - [x] Tabel transaksi terakhir

### Laporan Penjualan
- [x] Buat `Admin\Report` controller (index, sales, customer, loyalty, export)
- [x] Buat view `admin/report/sales.php` (filter tanggal, tabel, grafik)
- [x] Data: harian, mingguan, bulanan

### Laporan Pelanggan
- [x] Buat view `admin/report/customer.php`
- [x] Data: pelanggan baru, aktif, churn

### Laporan Loyalitas
- [x] Buat view `admin/report/loyalty.php`
- [x] Data: distribusi poin, level membership, redempsi

### Export Laporan
- [x] Export ke PDF (gunakan library Dompdf)
- [x] Export ke Excel (gunakan PhpSpreadsheet)

---

## Sprint 9: Testing & Deploy

### Profil Customer
- [x] Buat `Customer\Profile` controller (index, update)
- [x] Buat view `customer/profile/index.php` (edit data diri)

### Wishlist
- [x] Buat `WishlistModel.php`
- [x] Buat `Customer\Wishlist` controller (index, toggle, remove)
- [x] Buat view `customer/wishlist/index.php`
- [x] API toggle wishlist dari detail produk

### Loyalty Customer View
- [x] Buat `Customer\Loyalty` controller (index)
- [x] Buat view `customer/loyalty/index.php` (poin, level, riwayat poin)

### Testing
- [ ] Unit testing models
- [ ] Unit testing controllers (auth, checkout, payment verify)
- [ ] Integration testing alur checkout lengkap
- [ ] Integration testing alur CRM (poin, level upgrade, voucher)
- [ ] User Acceptance Testing (UAT)

### Bug Fixing & Polish
- [ ] Fix semua bug dari testing
- [ ] Responsive check mobile & desktop
- [ ] Performance check (query optimization, indexing)
- [ ] CSRF protection check semua form
- [ ] Input validation check

### Deploy
- [ ] Setup hosting & domain
- [ ] Konfigurasi production database
- [ ] Upload project ke server
- [ ] Setup cron job (voucher ulang tahun, auto backup DB)
- [ ] Final testing di production

---

*Catatan: Update checklist ini setiap kali ada task yang selesai.*
