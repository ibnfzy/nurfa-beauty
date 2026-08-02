# Design System - Nurfa Beauty Shop

---

## 1. Color Palette

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

---

## 2. Typography

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

---

## 3. Design Principles

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

---

## 4. Komponen UI Utama

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

## 5. Arsitektur View - Modular Component CI4

### 5.1 Struktur Folder Views

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

### 5.2 Cara Kerja Komponen Modular

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

### 5.3 Contoh Komponen (components/stat-card.php)

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

### 5.4 Contoh Komponen (components/product-card.php)

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

### 5.5 Contoh Komponen (components/sidebar.php)

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

### 5.6 Tailwind Config (tailwind.config.js)

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
