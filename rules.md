# Rules & Specifications - Nurfa Beauty Shop

---

## 1. Ringkasan Proyek

**Tujuan:** Membangun sistem informasi penjualan berbasis CRM dengan pendekatan Get, Keep, Grow untuk meningkatkan loyalitas pelanggan di Toko Kosmetik Nurfa Beauty Shop.

**Tech Stack:**
- Framework: CodeIgniter 4
- Database: MySQL
- Frontend: Tailwind CSS 3 + Alpine.js
- Auth: CodeIgniter Shield atau custom
- Icons: Heroicons / Lucide
- Font: Playfair Display + Poppins (Google Fonts)

---

## 2. Analisis Kebutuhan

### 2.1 Kebutuhan Fungsional
- Pengelolaan data pelanggan (profil, riwayat pembelian, preferensi)
- Pencatatan transaksi penjualan (online & offline)
- Manajemen stok produk
- Fitur CRM: Get (akuisisi), Keep (retensi), Grow (pengembangan)
- Program loyalitas (poin, voucher, diskon ulang tahun)
- Laporan penjualan dan analisis pelanggan
- Rekomendasi produk personal
- Komunikasi pelanggan (notifikasi, promosi)

### 2.2 Kebutuhan Non-Fungsional
- Responsive web (desktop & mobile)
- Keamanan data pelanggan
- Performa cepat untuk laporan

### 2.3 Batasan Sistem
- CRM terbatas pada tahap Get, Keep, Grow
- Produk: perawatan rambut, wajah, dan badan
- Tidak membahas supplier

---

## 3. Struktur Folder CodeIgniter 4

```
app/
├── Config/
│   ├── Routes.php
│   ├── Database.php
│   └── Auth.php
├── Controllers/
│   ├── Admin/
│   │   ├── Dashboard.php
│   │   ├── Customer.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   ├── Promotion.php
│   │   ├── Loyalty.php
│   │   ├── Report.php
│   │   └── Notification.php
│   ├── Customer/
│   │   ├── Dashboard.php
│   │   ├── Product.php
│   │   ├── Cart.php
│   │   ├── Transaction.php
│   │   ├── Profile.php
│   │   ├── Loyalty.php
│   │   └── Review.php
│   └── Auth/
│       ├── Login.php
│       ├── Register.php
│       └── Logout.php
├── Models/
│   ├── UserModel.php
│   ├── CustomerModel.php
│   ├── ProductModel.php
│   ├── CategoryModel.php
│   ├── TransactionModel.php
│   ├── TransactionItemModel.php
│   ├── PromotionModel.php
│   ├── VoucherModel.php
│   ├── LoyaltyPointModel.php
│   ├── ReviewModel.php
│   ├── NotificationModel.php
│   └── WishlistModel.php
├── Views/
│   ├── admin/
│   ├── customer/
│   ├── auth/
│   └── layouts/
├── Database/
│   └── Migrations/
└── Helpers/
    └── loyalty_helper.php
```

---

## 4. Fitur CRM (Get, Keep, Grow)

### 4.1 GET (Mendapatkan Pelanggan Baru)
| Fitur | Deskripsi |
|-------|-----------|
| Registrasi Online | Pelanggan bisa daftar via web |
| Diskon Pembelian Pertama | Voucher 20% untuk pelanggan baru |
| Katalog Produk | Browsing produk tanpa login |
| Promosi Media Sosial | Link ke Instagram/TikTok |

### 4.2 KEEP (Mempertahankan Pelanggan)
| Fitur | Deskripsi |
|-------|-----------|
| Poin Loyalitas | 1 poin per Rp 10.000 belanja |
| Level Membership | Bronze → Silver → Gold → Platinum |
| Diskon Ulang Tahun | Otomatis kirim voucher H-7 |
| Rekomendasi Produk | Berdasarkan riwayat pembelian |
| Notifikasi Personal | Info promo sesuai preferensi |
| Ulasan & Rating | Pelanggan bisa review produk |

### 4.3 GROW (Mengembangkan Nilai Pelanggan)
| Fitur | Deskripsi |
|-------|-----------|
| Up-selling | Rekomendasi produk premium |
| Cross-selling | "Pelanggan juga membeli..." |
| Bundling Paket | Face Care + Body Care bundle |
| Minimum Spend Bonus | Poin ekstra untuk belanja > Rp 500rb |

---

## 5. Halaman / Views

### 5.1 Admin Panel
| Halaman | URL | Fungsi |
|---------|-----|--------|
| Dashboard | /admin | Statistik penjualan, pelanggan, stok |
| Pelanggan | /admin/customer | CRUD pelanggan, riwayat, loyalitas |
| Produk | /admin/product | CRUD produk, stok |
| Kategori | /admin/category | CRUD kategori |
| Transaksi | /admin/transaction | Input transaksi offline, riwayat |
| **Verifikasi Pembayaran** | /admin/payment | **Validasi bukti transfer pelanggan** |
| Rekening Bank | /admin/bank | Kelola rekening bank tujuan transfer |
| Promosi | /admin/promotion | Kelola diskon, voucher, flash sale |
| Loyalitas | /admin/loyalty | Atur poin, level, rewards |
| Laporan | /admin/report | Laporan penjualan, analisis |
| Notifikasi | /admin/notification | Kirim promo personal |

### 5.2 Customer Portal
| Halaman | URL | Fungsi |
|---------|-----|--------|
| **Alamat Pengiriman** | /address | **Kelola alamat (wajib sebelum belanja)** |
| Dashboard | / | Katalog produk, promo |
| Detail Produk | /product/{id} | Detail, ulasan, tambah keranjang |
| Keranjang | /cart | Daftar belanja |
| Checkout | /checkout | Pilih alamat, lihat rekening, upload bukti transfer |
| Riwayat | /transaction | Daftar transaksi, status pembayaran |
| Profil | /profile | Edit data diri |
| Loyalitas | /loyalty | Poin, level, riwayat poin |
| Wishlist | /wishlist | Produk favorit |

---

## 6. Routing (Routes.php)

```php
// Auth
$routes->get('/login', 'Auth\Login::index');
$routes->post('/login', 'Auth\Login::process');
$routes->get('/register', 'Auth\Register::index');
$routes->post('/register', 'Auth\Register::process');
$routes->get('/logout', 'Auth\Logout::index');

// Customer Routes
$routes->group('/', ['filter' => 'auth'], function($routes) {
    // Alamat Pengiriman (wajib isi sebelum belanja)
    $routes->get('address', 'Customer\Address::index');
    $routes->post('address/store', 'Customer\Address::store');
    $routes->post('address/update/(:num)', 'Customer\Address::update/$1');
    $routes->post('address/delete/(:num)', 'Customer\Address::delete/$1');
    $routes->post('address/set-default/(:num)', 'Customer\Address::setDefault/$1');
    
    $routes->get('/', 'Customer\Dashboard::index');
    $routes->get('product/(:num)', 'Customer\Product::detail/$1');
    $routes->get('cart', 'Customer\Cart::index');
    $routes->post('cart/add', 'Customer\Cart::add');
    $routes->post('cart/update', 'Customer\Cart::update');
    $routes->post('cart/remove', 'Customer\Cart::remove');
    $routes->get('checkout', 'Customer\Checkout::index');
    $routes->post('checkout/process', 'Customer\Checkout::process');
    $routes->post('checkout/upload-proof/(:num)', 'Customer\Checkout::uploadProof/$1');
    $routes->get('transaction', 'Customer\Transaction::index');
    $routes->get('transaction/(:num)', 'Customer\Transaction::detail/$1');
    $routes->get('profile', 'Customer\Profile::index');
    $routes->post('profile/update', 'Customer\Profile::update');
    $routes->get('loyalty', 'Customer\Loyalty::index');
    $routes->get('wishlist', 'Customer\Wishlist::index');
    $routes->post('review/add', 'Customer\Review::add');
});

// Admin Routes
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    
    // Customer
    $routes->get('customer', 'Admin\Customer::index');
    $routes->get('customer/(:num)', 'Admin\Customer::detail/$1');
    $routes->post('customer/update/(:num)', 'Admin\Customer::update/$1');
    
    // Product
    $routes->get('product', 'Admin\Product::index');
    $routes->get('product/create', 'Admin\Product::create');
    $routes->post('product/store', 'Admin\Product::store');
    $routes->get('product/edit/(:num)', 'Admin\Product::edit/$1');
    $routes->post('product/update/(:num)', 'Admin\Product::update/$1');
    $routes->post('product/delete/(:num)', 'Admin\Product::delete/$1');
    
    // Category
    $routes->get('category', 'Admin\Category::index');
    $routes->post('category/store', 'Admin\Category::store');
    $routes->post('category/update/(:num)', 'Admin\Category::update/$1');
    $routes->post('category/delete/(:num)', 'Admin\Category::delete/$1');
    
    // Transaction
    $routes->get('transaction', 'Admin\Transaction::index');
    $routes->get('transaction/create', 'Admin\Transaction::create');
    $routes->post('transaction/store', 'Admin\Transaction::store');
    $routes->get('transaction/(:num)', 'Admin\Transaction::detail/$1');
    
    // Verifikasi Pembayaran
    $routes->get('payment', 'Admin\Payment::index');
    $routes->get('payment/(:num)', 'Admin\Payment::detail/$1');
    $routes->post('payment/verify/(:num)', 'Admin\Payment::verify/$1');
    $routes->post('payment/reject/(:num)', 'Admin\Payment::reject/$1');
    
    // Rekening Bank
    $routes->get('bank', 'Admin\Bank::index');
    $routes->post('bank/store', 'Admin\Bank::store');
    $routes->post('bank/update/(:num)', 'Admin\Bank::update/$1');
    $routes->post('bank/delete/(:num)', 'Admin\Bank::delete/$1');
    
    // Promotion
    $routes->get('promotion', 'Admin\Promotion::index');
    $routes->get('promotion/create', 'Admin\Promotion::create');
    $routes->post('promotion/store', 'Admin\Promotion::store');
    $routes->get('promotion/edit/(:num)', 'Admin\Promotion::edit/$1');
    $routes->post('promotion/update/(:num)', 'Admin\Promotion::update/$1');
    
    // Loyalty
    $routes->get('loyalty', 'Admin\Loyalty::index');
    $routes->post('loyalty/update-rules', 'Admin\Loyalty::updateRules');
    $routes->get('loyalty/customer/(:num)', 'Admin\Loyalty::customerDetail/$1');
    
    // Report
    $routes->get('report', 'Admin\Report::index');
    $routes->get('report/sales', 'Admin\Report::sales');
    $routes->get('report/customer', 'Admin\Report::customer');
    $routes->get('report/loyalty', 'Admin\Report::loyalty');
    $routes->get('report/export/(:any)', 'Admin\Report::export/$1');
    
    // Notification
    $routes->get('notification', 'Admin\Notification::index');
    $routes->post('notification/send', 'Admin\Notification::send');
    $routes->post('notification/broadcast', 'Admin\Notification::broadcast');
});
```

---

## 7. Implementasi Bertahap (Sprint)

### Sprint 1: Setup & Auth (Minggu 1)
- [ ] Install CodeIgniter 4
- [ ] Konfigurasi database MySQL
- [ ] Buat migration semua tabel
- [ ] Implementasi autentikasi (login/register/logout)
- [ ] Buat layout template (admin & customer)
- [ ] Setup middleware/filter auth

### Sprint 2: Master Data (Minggu 2)
- [ ] CRUD Kategori Produk
- [ ] CRUD Produk (dengan upload gambar)
- [ ] Manajemen Stok
- [ ] Daftar Pelanggan (admin view)
- [ ] Detail Pelanggan (riwayat, poin)
- [ ] CRUD Rekening Bank (admin)

### Sprint 3: Transaksi & Penjualan (Minggu 3)
- [ ] Input Transaksi Offline (admin)
- [ ] Katalog Produk (customer)
- [ ] **Kelola Alamat Pengiriman (customer)**
- [ ] Keranjang Belanja
- [ ] **Checkout: Pilih Alamat → Lihat Rekening → Upload Bukti Transfer**
- [ ] **Verifikasi Pembayaran (admin: approve/reject)**
- [ ] Riwayat Transaksi & Status Pembayaran

### Sprint 4: Fitur CRM - GET (Minggu 4)
- [ ] Registrasi Pelanggan Online
- [ ] Diskon Pembelian Pertama
- [ ] Katalog Produk Public
- [ ] Landing Page Promosi

### Sprint 5: Fitur CRM - KEEP (Minggu 5)
- [ ] Sistem Poin Loyalitas
- [ ] Level Membership (Bronze/Silver/Gold/Platinum)
- [ ] Voucher Ulang Tahun (otomatis)
- [ ] Notifikasi Personal
- [ ] Ulasan & Rating Produk

### Sprint 6: Fitur CRM - GROW (Minggu 6)
- [ ] Rekomendasi Produk (berdasarkan riwayat)
- [ ] Up-selling (produk premium)
- [ ] Cross-selling ("Pelanggan juga membeli")
- [ ] Bundling Paket
- [ ] Poin Bonus Minimum Spend

### Sprint 7: Promosi & Marketing (Minggu 7)
- [ ] Kelola Promosi (diskon, flash sale)
- [ ] Generate Voucher
- [ ] Target Segmentasi Pelanggan
- [ ] Kirim Promosi Personal
- [ ] Broadcast Notifikasi

### Sprint 8: Laporan & Analisis (Minggu 8)
- [ ] Dashboard Statistik
- [ ] Laporan Penjualan (harian/mingguan/bulanan)
- [ ] Laporan Pelanggan (baru/aktif/churn)
- [ ] Laporan Loyalitas (poin, level, redempsi)
- [ ] Export Laporan (PDF/Excel)
- [ ] Analisis Tren & Preferensi

### Sprint 9: Testing & Deploy (Minggu 9-10)
- [ ] Unit Testing
- [ ] Integration Testing
- [ ] User Acceptance Testing (UAT)
- [ ] Bug Fixing
- [ ] Deploy ke Hosting
- [ ] Dokumentasi

---

## 8. Struktur Menu Admin

```
Dashboard
├── Statistik Penjualan
├── Pelanggan Baru Hari Ini
├── Stok Menipis
└── Transaksi Terakhir

Master Data
├── Kategori Produk
├── Produk
├── Pelanggan
└── Rekening Bank

Penjualan
├── Transaksi Baru
├── Riwayat Transaksi
├── Detail Transaksi
└── **Verifikasi Pembayaran**
    ├── Menunggu Verifikasi
    ├── Sudah Diverifikasi
    └── Ditolak

CRM
├── Promosi
│   ├── Diskon
│   ├── Voucher
│   └── Flash Sale
├── Loyalitas
│   ├── Aturan Poin
│   ├── Level Membership
│   └── Rewards
└── Komunikasi
    ├── Notifikasi Personal
    └── Broadcast

Laporan
├── Penjualan
├── Pelanggan
├── Loyalitas
└── Export
```

---

## 9. Struktur Menu Customer

```
Beranda
├── Katalog Produk
├── Promo Terbaru
└── Rekomendasi

Produk
├── Kategori (Rambut/Wajah/Badan)
├── Detail Produk
└── Ulasan

Akun Saya
├── Profil
├── **Alamat Pengiriman**
│   ├── Tambah Alamat
│   ├── Edit Alamat
│   └── Atur Alamat Utama
├── Riwayat Pembelian
│   ├── Status Pembayaran
│   └── Upload Bukti Transfer
├── Poin Loyalitas
│   ├── Jumlah Poin
│   ├── Level Membership
│   └── Riwayat Poin
├── Wishlist
└── Voucher Saya

Keranjang
├── Daftar Belanja
└── Checkout
    ├── 1. Pilih Alamat Pengiriman
    ├── 2. Konfirmasi Pesanan
    ├── 3. Transfer ke Rekening & Upload Bukti
    └── 4. Menunggu Verifikasi Admin
```

---

## 10. API Endpoints (untuk AJAX)

```
POST /api/cart/add          - Tambah ke keranjang
POST /api/cart/update       - Update jumlah
POST /api/cart/remove       - Hapus dari keranjang
GET  /api/cart/count        - Jumlah item keranjang
GET  /api/product/search    - Cari produk
GET  /api/loyalty/points    - Cek poin pelanggan
POST /api/review/add        - Tambah ulasan
GET  /api/notification      - Ambil notifikasi
POST /api/notification/read - Tandai dibaca

// Alamat Pengiriman
GET  /api/address/list      - Daftar alamat customer
POST /api/address/store     - Tambah alamat baru
POST /api/address/update    - Update alamat
POST /api/address/delete    - Hapus alamat
POST /api/address/set-default - Atur alamat utama

// Checkout & Pembayaran
GET  /api/bank/list         - Daftar rekening bank tujuan
POST /api/checkout/preview  - Preview pesanan sebelum checkout
POST /api/checkout/process  - Proses checkout
POST /api/transaction/upload-proof - Upload bukti transfer

// Admin - Verifikasi Pembayaran
GET  /api/admin/payment/pending   - Daftar menunggu verifikasi
POST /api/admin/payment/verify    - Verifikasi (approve)
POST /api/admin/payment/reject    - Tolak pembayaran
```

---

## 11. Alur CRM Lengkap

```
PELANGGAN BARU (GET)
├── Daftar Akun
├── **Isi Alamat Pengiriman (Wajib)**
├── Dapat Voucher 20% Pembelian Pertama
├── Belanja Pertama → Dapat Poin 2x
└── Follow Social Media → Dapat Diskon

PELANGGAN SETIA (KEEP)
├── Setiap Belanja → Dapat Poin
├── Ulang Tahun → Dapat Voucher
├── Poin Terkumpul → Naik Level
└── Preferensi → Rekomendasi Personal

PELANGGAN VIP (GROW)
├── Rekomendasi Premium → Up-selling
├── Paket Bundling → Cross-selling
├── Minimum Spend → Bonus Poin
└── Referral → Reward
```

---

## 12. Alur Checkout & Pembayaran

```
1. Pelanggan tambah produk ke keranjang
          ↓
2. Klik Checkout
          ↓
3. Cek: Alamat pengiriman sudah isi?
   ├── BELUM → Redirect ke halaman Alamat (+ form tambah)
   └── SUDAH → Lanjut
          ↓
4. Pilih alamat pengiriman dari daftar
          ↓
5. Konfirmasi pesanan (ringkasan produk, subtotal, ongkir, total)
          ↓
6. Tampilkan rekening bank tujuan transfer
          ↓
7. Pelanggan transfer ke rekening & upload bukti transfer
          ↓
8. Status: MENUNGGU VERIFIKASI
          ↓
9. Admin review bukti transfer
   ├── VALID → Verifikasi → Status: DIPROSES → Konfirmasi ke pelanggan
   └── TIDAK VALID → Tolak (+ alasan) → Pelanggan upload ulang
          ↓
10. Admin proses & kirim barang → Status: DIKIRIM
          ↓
11. Pelanggan konfirmasi terima → Status: SELESAI
          ↓
12. Poin loyalitas otomatis bertambah
```

---

## 13. Status Transaksi

| Status | Keterangan | Trigger |
|--------|-----------|---------|
| `pending_payment` | Menunggu upload bukti transfer | Checkout berhasil |
| `pending_verification` | Bukti transfer menunggu verifikasi | Upload bukti |
| `payment_verified` | Pembayaran valid | Admin verifikasi |
| `payment_rejected` | Pembayaran ditolak | Admin tolak |
| `processing` | Pesanan diproses | Admin mulai proses |
| `shipped` | Barang dikirim | Admin input resi |
| `completed` | Pesanan selesai | Konfirmasi pelanggan / auto |
| `cancelled` | Dibatalkan | Pelanggan / admin |

---

## 14. Level Membership

| Level | Min Spending | Benefit |
|-------|-------------|---------|
| Bronze | Rp 0 | Poin 1x, Standar |
| Silver | Rp 2.000.000 | Poin 1.5x, Diskon 5% |
| Gold | Rp 5.000.000 | Poin 2x, Diskon 10%, Voucher Bulanan |
| Platinum | Rp 10.000.000 | Poin 3x, Diskon 15%, Voucher Mingguan, Prioritas |

---

## 15. Catatan Penting

1. **Keamanan:** Enkripsi password, CSRF protection, input validation
2. **Performance:** Indexing database, caching query, pagination
3. **Responsive:** Mobile-first design untuk customer portal
4. **Backup:** Auto backup database mingguan
5. **SEO:** Meta tags, clean URL untuk katalog produk
