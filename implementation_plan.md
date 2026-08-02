# Implementation Plan: Sistem Informasi Penjualan Berbasis CRM
## Toko Kosmetik Nurfa Beauty Shop - CodeIgniter 4

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

## 2. Design System - Feminin & Elegan

### 2.1 Color Palette

```css
/* Primary Colors */
--primary: #E8A0BF        /* Rose Pink */
--primary-dark: #C77DA0   /* Deep Rose */
--primary-light: #F5D5E0  /* Soft Pink */

/* Secondary Colors */
--secondary: #D4A574      /* Gold */
--secondary-dark: #B8895A /* Dark Gold */
--secondary-light: #F0D4B4 /* Light Gold */

/* Neutral Colors */
--white: #FFFFFF
--cream: #FFF8F0          /* Warm Cream */
--gray-50: #FAF7F5
--gray-100: #F5F0EC
--gray-200: #E8E0D8
--gray-300: #D4C8BC
--gray-400: #A89888
--gray-500: #8A7A6A
--gray-600: #6B5D50
--gray-700: #4D4038
--gray-800: #2E2420
--gray-900: #1A1412

/* Accent / Status */
--success: #7DB88F        /* Sage Green */
--warning: #E8C47D        /* Soft Yellow */
--danger: #D47D7D         /* Soft Red */
--info: #7DA8D4           /* Soft Blue */
```

### 2.2 Typography

```css
/* Heading Font */
font-family: 'Playfair Display', serif;

/* Body Font */
font-family: 'Poppins', sans-serif;

/* Font Sizes */
--text-xs: 0.75rem     /* 12px */
--text-sm: 0.875rem    /* 14px */
--text-base: 1rem      /* 16px */
--text-lg: 1.125rem    /* 18px */
--text-xl: 1.25rem     /* 20px */
--text-2xl: 1.5rem     /* 24px */
--text-3xl: 1.875rem   /* 30px */
--text-4xl: 2.25rem    /* 36px */
```

### 2.3 Design Principles

| Elemen | Aturan |
|--------|--------|
| **Card** | Rounded-xl, shadow-sm, border soft pink |
| **Button** | Rounded-lg, gradient primary, hover shadow |
| **Input** | Rounded-lg, border gray-200, focus ring pink |
| **Spacing** | Generous whitespace, padding 4-6 |
| **Shadow** | Subtle, tidak terlalu gelap |
| **Border** | Halus, warna cream/beige |
| **Icon** | Rounded, ukuran konsisten 20px/24px |
| **Image** | Rounded-xl, aspect-ratio konsisten |

### 2.4 Komponen UI Utama

```
Navbar        → Putih, logo kiri, menu tengah, akun kanan
Sidebar Admin → Cream, icon + text, collapsible
Card Produk   → Gambar atas, info bawah, hover shadow-md
Card Stat     → Gradient primary/secondary, icon besar
Table         → Striped cream, rounded, hover row
Modal         → Rounded-2xl, overlay blur
Badge         → Rounded-full, warna status
Toast         → Rounded-lg, icon kiri, auto dismiss
```

---

## 3. Arsitektur View - Modular Component CI4

### 3.1 Struktur Folder Views

```
app/Views/
├── layouts/
│   ├── admin.php              ← Layout utama admin
│   ├── customer.php           ← Layout utama customer
│   └── auth.php               ← Layout halaman login/register
│
├── components/
│   ├── navbar.php             ← Navigasi atas
│   ├── sidebar.php            ← Sidebar admin
│   ├── footer.php             ← Footer
│   ├── card.php               ← Card component
│   ├── modal.php              ← Modal component
│   ├── table.php              ← Table component
│   ├── badge.php              ← Badge status
│   ├── toast.php              ← Notifikasi toast
│   ├── breadcrumb.php         ← Breadcrumb
│   ├── pagination.php         ← Pagination
│   ├── search.php             ← Search input
│   ├── stat-card.php          ← Card statistik
│   ├── product-card.php       ← Card produk
│   └── empty-state.php        ← State kosong
│
├── partials/
│   ├── meta.php               ← Meta tags, CSS
│   ├── scripts.php            ← JS global
│   └── assets.php             ← CDN links
│
├── admin/
│   ├── dashboard/
│   │   └── index.php
│   ├── customer/
│   │   ├── index.php
│   │   ├── detail.php
│   │   └── form.php
│   ├── product/
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── detail.php
│   ├── category/
│   │   └── index.php
│   ├── transaction/
│   │   ├── index.php
│   │   ├── create.php
│   │   └── detail.php
│   ├── promotion/
│   │   ├── index.php
│   │   ├── create.php
│   │   └── edit.php
│   ├── loyalty/
│   │   ├── index.php
│   │   └── customer.php
│   ├── report/
│   │   ├── index.php
│   │   ├── sales.php
│   │   ├── customer.php
│   │   └── loyalty.php
│   └── notification/
│       └── index.php
│
├── customer/
│   ├── home/
│   │   └── index.php          ← Landing page katalog
│   ├── product/
│   │   ├── index.php          ← List produk
│   │   └── detail.php         ← Detail produk
│   ├── cart/
│   │   └── index.php
│   ├── checkout/
│   │   └── index.php
│   ├── transaction/
│   │   ├── index.php          ← Riwayat
│   │   └── detail.php
│   ├── profile/
│   │   └── index.php
│   ├── loyalty/
│   │   └── index.php
│   ├── wishlist/
│   │   └── index.php
│   └── review/
│       └── form.php
│
└── auth/
    ├── login.php
    ├── register.php
    └── forgot-password.php
```

### 3.2 Cara Kerja Komponen Modular

**Layout Utama (layouts/admin.php):**
```php
<!DOCTYPE html>
<html lang="id">
<head>
    <?= view('partials/meta') ?>
</head>
<body class="bg-cream font-poppins">
    <?= view('components/navbar') ?>
    <?= view('components/sidebar') ?>
    
    <main class="ml-64 p-6">
        <?= $this->renderSection('content') ?>
    </main>
    
    <?= view('components/toast') ?>
    <?= view('partials/scripts') ?>
</body>
</html>
```

**Halaman View (admin/dashboard/index.php):**
```php
<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    
    <?= view('components/breadcrumb', ['items' => [
        ['label' => 'Dashboard', 'url' => '/admin']
    ]]) ?>
    
    <!-- Stat Cards -->
    <div class="grid grid-cols-4 gap-4">
        <?= view('components/stat-card', [
            'title' => 'Total Penjualan',
            'value' => 'Rp 2.600.812.200',
            'icon' => 'currency-dollar',
            'color' => 'primary',
            'change' => '+12%'
        ]) ?>
        <?= view('components/stat-card', [
            'title' => 'Pelanggan Baru',
            'value' => '2192',
            'icon' => 'users',
            'color' => 'success',
            'change' => '+8%'
        ]) ?>
        <!-- ... more stat cards -->
    </div>
    
    <!-- Table Transaksi Terakhir -->
    <?= view('components/card', [
        'title' => 'Transaksi Terakhir',
        'content' => view('components/table', [
            'headers' => ['Kode', 'Pelanggan', 'Total', 'Status'],
            'rows' => $recent_transactions
        ])
    ]) ?>
    
</div>
<?= $this->endSection() ?>
```

### 3.3 Contoh Komponen (components/stat-card.php)

```php
<?php
// Parameter: $title, $value, $icon, $color, $change
$colorClasses = [
    'primary' => 'bg-gradient-to-br from-primary to-primary-dark',
    'secondary' => 'bg-gradient-to-br from-secondary to-secondary-dark',
    'success' => 'bg-gradient-to-br from-success to-green-600',
    'info' => 'bg-gradient-to-br from-info to-blue-600',
];
?>

<div class="<?= $colorClasses[$color] ?? $colorClasses['primary'] ?> 
            rounded-xl p-6 text-white shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-white/80 text-sm"><?= $title ?></p>
            <p class="text-2xl font-bold mt-1"><?= $value ?></p>
            <?php if (!empty($change)): ?>
                <span class="text-xs text-white/90 mt-2 inline-block">
                    <?= $change ?> dari bulan lalu
                </span>
            <?php endif; ?>
        </div>
        <div class="bg-white/20 rounded-full p-3">
            <i data-lucide="<?= $icon ?>" class="w-6 h-6"></i>
        </div>
    </div>
</div>
```

### 3.4 Contoh Komponen (components/product-card.php)

```php
<?php // Parameter: $product ?>

<div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all 
            border border-primary-light/30 overflow-hidden group">
    <div class="relative overflow-hidden">
        <img src="<?= $product['image'] ?>" 
             alt="<?= $product['name'] ?>"
             class="w-full h-48 object-cover group-hover:scale-105 transition-transform">
        <?php if ($product['discount']): ?>
            <span class="absolute top-2 right-2 bg-danger text-white 
                         text-xs px-2 py-1 rounded-full">
                -<?= $product['discount'] ?>%
            </span>
        <?php endif; ?>
    </div>
    <div class="p-4">
        <p class="text-xs text-gray-400"><?= $product['category'] ?></p>
        <h3 class="font-poppins font-semibold text-gray-800 mt-1 
                   line-clamp-2"><?= $product['name'] ?></h3>
        <div class="flex items-center gap-1 mt-2">
            <?php for ($i = 0; $i < 5; $i++): ?>
                <svg class="w-3.5 h-3.5 <?= $i < $product['rating'] ? 'text-secondary fill-secondary' : 'text-gray-300' ?>" ...></svg>
            <?php endfor; ?>
            <span class="text-xs text-gray-400 ml-1">(<?= $product['reviews'] ?>)</span>
        </div>
        <div class="flex items-center justify-between mt-3">
            <div>
                <?php if ($product['discount']): ?>
                    <p class="text-xs text-gray-400 line-through">Rp <?= number_format($product['price']) ?></p>
                <?php endif; ?>
                <p class="text-primary font-bold">Rp <?= number_format($product['final_price']) ?></p>
            </div>
            <button class="bg-primary-light hover:bg-primary hover:text-white 
                           text-primary rounded-lg px-3 py-1.5 text-sm transition-colors">
                + Keranjang
            </button>
        </div>
    </div>
</div>
```

### 3.5 Contoh Komponen (components/sidebar.php)

```php
<?php // Sidebar Admin ?>

<aside class="fixed left-0 top-0 h-full w-64 bg-cream border-r border-primary-light/30 z-40">
    <!-- Logo -->
    <div class="p-6 border-b border-primary-light/30">
        <h1 class="font-playfair text-xl font-bold text-primary-dark">
            Nurfa Beauty
        </h1>
        <p class="text-xs text-gray-400 mt-1">Admin Panel</p>
    </div>
    
    <!-- Menu -->
    <nav class="p-4 space-y-1">
        <?php
        $menus = [
            ['icon' => 'layout-dashboard', 'label' => 'Dashboard', 'url' => '/admin', 'active' => $currentPage === 'dashboard'],
            ['icon' => 'users', 'label' => 'Pelanggan', 'url' => '/admin/customer', 'active' => $currentPage === 'customer'],
            ['icon' => 'package', 'label' => 'Produk', 'url' => '/admin/product', 'active' => $currentPage === 'product'],
            ['icon' => 'shopping-cart', 'label' => 'Transaksi', 'url' => '/admin/transaction', 'active' => $currentPage === 'transaction'],
            ['icon' => 'megaphone', 'label' => 'Promosi', 'url' => '/admin/promotion', 'active' => $currentPage === 'promotion'],
            ['icon' => 'heart', 'label' => 'Loyalitas', 'url' => '/admin/loyalty', 'active' => $currentPage === 'loyalty'],
            ['icon' => 'bar-chart-3', 'label' => 'Laporan', 'url' => '/admin/report', 'active' => $currentPage === 'report'],
            ['icon' => 'bell', 'label' => 'Notifikasi', 'url' => '/admin/notification', 'active' => $currentPage === 'notification'],
        ];
        ?>
        
        <?php foreach ($menus as $menu): ?>
            <a href="<?= $menu['url'] ?>" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors
                      <?= $menu['active'] 
                          ? 'bg-primary text-white shadow-sm' 
                          : 'text-gray-600 hover:bg-primary-light hover:text-primary-dark' ?>">
                <i data-lucide="<?= $menu['icon'] ?>" class="w-5 h-5"></i>
                <span class="text-sm"><?= $menu['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <!-- User Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-primary-light/30">
        <div class="flex items-center gap-3">
            <div class="bg-primary rounded-full w-9 h-9 flex items-center justify-center">
                <span class="text-white text-sm font-bold">A</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800"><?= session()->get('name') ?></p>
                <p class="text-xs text-gray-400">Administrator</p>
            </div>
        </div>
    </div>
</aside>
```

### 3.6 Tailwind Config (tailwind.config.js)

```javascript
module.exports = {
    content: [
        './app/Views/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#E8A0BF',
                    dark: '#C77DA0',
                    light: '#F5D5E0',
                },
                secondary: {
                    DEFAULT: '#D4A574',
                    dark: '#B8895A',
                    light: '#F0D4B4',
                },
                cream: '#FFF8F0',
                success: '#7DB88F',
                warning: '#E8C47D',
                danger: '#D47D7D',
                info: '#7DA8D4',
            },
            fontFamily: {
                playfair: ['Playfair Display', 'serif'],
                poppins: ['Poppins', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
```

---

## 4. Analisis Kebutuhan

### 4.1 Kebutuhan Fungsional
- Pengelolaan data pelanggan (profil, riwayat pembelian, preferensi)
- Pencatatan transaksi penjualan (online & offline)
- Manajemen stok produk
- Fitur CRM: Get (akuisisi), Keep (retensi), Grow (pengembangan)
- Program loyalitas (poin, voucher, diskon ulang tahun)
- Laporan penjualan dan analisis pelanggan
- Rekomendasi produk personal
- Komunikasi pelanggan (notifikasi, promosi)

### 4.2 Kebutuhan Non-Fungsional
- Responsive web (desktop & mobile)
- Keamanan data pelanggan
- Performa cepat untuk laporan

### 4.3 Batasan Sistem
- CRM terbatas pada tahap Get, Keep, Grow
- Produk: perawatan rambut, wajah, dan badan
- Tidak membahas supplier

---

## 5. Database Design

### 5.1 Tabel Utama

```sql
-- Tabel Users (Admin & Pelanggan)
users
- id (PK)
- name
- email
- password
- role (admin/customer)
- phone
- address
- birth_date
- created_at, updated_at

-- Tabel Pelanggan (Extended Profile)
customers
- id (PK)
- user_id (FK)
- loyalty_points
- membership_level (bronze/silver/gold/platinum)
- total_spending
- first_purchase_date
- last_purchase_date
- created_at, updated_at

-- Tabel Kategori Produk
categories
- id (PK)
- name (perawatan rambut/wajah/badan)
- description
- created_at, updated_at

-- Tabel Produk
products
- id (PK)
- category_id (FK)
- name
- description
- price
- stock
- image
- is_active
- created_at, updated_at

-- Tabel Transaksi
transactions
- id (PK)
- customer_id (FK)
- shipping_address_id (FK)
- transaction_code
- transaction_date
- total_amount
- shipping_cost
- discount_amount
- final_amount
- payment_method (transfer)
- payment_proof (file path bukti transfer)
- payment_status (pending/verified/rejected)
- payment_verified_at
- payment_verified_by (admin user_id)
- payment_rejection_reason
- status (pending_payment/paid/processing/shipped/completed/cancelled)
- notes
- created_at, updated_at

-- Tabel Alamat Pengiriman
shipping_addresses
- id (PK)
- customer_id (FK)
- label (Rumah/Kantor/etc)
- recipient_name
- phone
- address (alamat lengkap)
- province
- city
- district
- postal_code
- is_default
- created_at, updated_at

-- Tabel Rekening Bank (untuk transfer)
bank_accounts
- id (PK)
- bank_name
- account_number
- account_name
- is_active
- created_at, updated_at

-- Tabel Detail Transaksi
transaction_items
- id (PK)
- transaction_id (FK)
- product_id (FK)
- quantity
- price
- subtotal
- created_at, updated_at

-- Tabel Promosi
promotions
- id (PK)
- name
- type (discount/voucher/flash_sale/birthday)
- discount_type (percentage/fixed)
- discount_value
- min_purchase
- start_date
- end_date
- target_segment (all/new/loyal/vip)
- is_active
- created_at, updated_at

-- Tabel Voucher
vouchers
- id (PK)
- promotion_id (FK)
- customer_id (FK, nullable)
- code
- is_used
- used_at
- expires_at
- created_at, updated_at

-- Tabel Poin Loyalitas
loyalty_points_log
- id (PK)
- customer_id (FK)
- points (positive=earn, negative=redeem)
- type (earn/redeem/expired)
- description
- transaction_id (FK, nullable)
- created_at

-- Tabel Ulasan Produk
reviews
- id (PK)
- customer_id (FK)
- product_id (FK)
- rating (1-5)
- comment
- created_at, updated_at

-- Tabel Notifikasi
notifications
- id (PK)
- customer_id (FK)
- title
- message
- type (promotion/birthday/loyalty/general)
- is_read
- created_at

-- Tabel Wishlist
wishlists
- id (PK)
- customer_id (FK)
- product_id (FK)
- created_at
```

---

## 6. Struktur Folder CodeIgniter 4

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
│   │   ├── dashboard/index.php
│   │   ├── customer/index.php
│   │   ├── product/index.php
│   │   ├── transaction/index.php
│   │   ├── promotion/index.php
│   │   ├── loyalty/index.php
│   │   └── report/index.php
│   ├── customer/
│   │   ├── dashboard/index.php
│   │   ├── product/detail.php
│   │   ├── cart/index.php
│   │   ├── transaction/history.php
│   │   ├── profile/index.php
│   │   └── loyalty/index.php
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   └── layouts/
│       ├── admin.php
│       ├── customer.php
│       └── auth.php
├── Database/
│   └── Migrations/
│       ├── 001_create_users_table.php
│       ├── 002_create_customers_table.php
│       ├── 003_create_categories_table.php
│       ├── 004_create_products_table.php
│       ├── 005_create_transactions_table.php
│       ├── 006_create_transaction_items_table.php
│       ├── 007_create_promotions_table.php
│       ├── 008_create_vouchers_table.php
│       ├── 009_create_loyalty_points_log_table.php
│       ├── 010_create_reviews_table.php
│       ├── 011_create_notifications_table.php
│       └── 012_create_wishlists_table.php
└── Helpers/
    └── loyalty_helper.php
```

---

## 7. Fitur CRM (Get, Keep, Grow)

### 5.1 GET (Mendapatkan Pelanggan Baru)
| Fitur | Deskripsi |
|-------|-----------|
| Registrasi Online | Pelanggan bisa daftar via web |
| Diskon Pembelian Pertama | Voucher 20% untuk pelanggan baru |
| Katalog Produk | Browsing produk tanpa login |
| Promosi Media Sosial | Link ke Instagram/TikTok |

### 5.2 KEEP (Mempertahankan Pelanggan)
| Fitur | Deskripsi |
|-------|-----------|
| Poin Loyalitas | 1 poin per Rp 10.000 belanja |
| Level Membership | Bronze → Silver → Gold → Platinum |
| Diskon Ulang Tahun | Otomatis kirim voucher H-7 |
| Rekomendasi Produk | Berdasarkan riwayat pembelian |
| Notifikasi Personal | Info promo sesuai preferensi |
| Ulasan & Rating | Pelanggan bisa review produk |

### 5.3 GROW (Mengembangkan Nilai Pelanggan)
| Fitur | Deskripsi |
|-------|-----------|
| Up-selling | Rekomendasi produk premium |
| Cross-selling | "Pelanggan juga membeli..." |
| Bundling Paket | Face Care + Body Care bundle |
| Minimum Spend Bonus | Poin ekstra untuk belanja > Rp 500rb |

---

## 8. Halaman / Views

### 6.1 Admin Panel
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

### 6.2 Customer Portal
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

## 9. Routing (Routes.php)

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

## 10. Implementasi Bertahap (Sprint)

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

## 11. Struktur Menu Admin

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

## 12. Struktur Menu Customer

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

## 13. API Endpoints (untuk AJAX)

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

## 14. Alur CRM Lengkap

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

## 14.1 Alur Checkout & Pembayaran

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

## 14.2 Status Transaksi

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

## 15. Level Membership

| Level | Min Spending | Benefit |
|-------|-------------|---------|
| Bronze | Rp 0 | Poin 1x, Standar |
| Silver | Rp 2.000.000 | Poin 1.5x, Diskon 5% |
| Gold | Rp 5.000.000 | Poin 2x, Diskon 10%, Voucher Bulanan |
| Platinum | Rp 10.000.000 | Poin 3x, Diskon 15%, Voucher Mingguan, Prioritas |

---

## 16. Catatan Penting

1. **Keamanan:** Enkripsi password, CSRF protection, input validation
2. **Performance:** Indexing database, caching query, pagination
3. **Responsive:** Mobile-first design untuk customer portal
4. **Backup:** Auto backup database mingguan
5. **SEO:** Meta tags, clean URL untuk katalog produk

---

*Dibuat berdasarkan proposal skripsi: "Sistem Informasi Penjualan Berbasis CRM Untuk Meningkatkan Loyalitas Pelanggan Di Toko Kosmetik Nurfa Beauty Shop"*
