# Laporan Analisis Teknis Komprehensif
## Website: https://handayani.ac.id/
**Tanggal Analisis:** 16 Agustus 2026

---

## 1. DOM Tree Mapping

### 1.1 Struktur Hirarki Elemen Utama

```
<html>
└── <head>
│   ├── <meta charset="UTF-8">
│   ├── <meta name="viewport" ...>
│   ├── <meta name="generator" content="WordPress 7.0.4">
│   ├── <link rel="stylesheet"> (virtue.css, default.css, Google Fonts)
│   └── <script> (jQuery, Bootstrap, tema Virtue)
└── <body id="wrapper">
    ├── <div id="topbar">                        ← Bar atas (info kontak, akses cepat)
    │   └── <div id="topbar-search">             ← Formulir pencarian
    ├── <header class="banner headerclass">
    │   ├── <div id="logo"> / <div id="thelogo"> ← Logo institusi
    │   ├── <nav id="nav-main">                  ← Menu navigasi primer
    │   └── <nav id="nav-second">                ← Menu navigasi sekunder
    ├── <section id="cat_nav" class="navclass">  ← Navigasi kategori
    ├── <div id="imageslider">                   ← Hero slider/carousel
    ├── <div id="content">                       ← Konten utama
    │   ├── <div id="carouselcontainer-portfolio">
    │   └── <div id="portfolio-carousel">
    ├── <div class="home_blog_title">            ← Daftar postingan blog/berita
    └── <footer id="containerfooter" class="footerclass">
```

### 1.2 Elemen Struktural Kunci

| Elemen | ID / Class | Deskripsi |
|--------|-----------|-----------|
| Top Bar | `#topbar`, `#topbar-search` | Bar informasi & pencarian |
| Header | `.banner.headerclass` | Header utama dengan schema markup |
| Logo | `#logo`, `#thelogo` | Div logo institusi |
| Navigasi Utama | `#nav-main` | Menu navigasi primer (schema: SiteNavigationElement) |
| Navigasi Kedua | `#nav-second` | Menu navigasi sekunder |
| Kategori Nav | `#cat_nav .navclass` | Navigasi kategori konten |
| Hero Slider | `#imageslider` | Slider gambar beranda (FlexSlider) |
| Konten Utama | `#content` | Area konten utama |
| Portofolio Carousel | `#carouselcontainer-portfolio`, `#portfolio-carousel` | Carousel program studi |
| Blog Posts | `.home_blog_title` | Daftar artikel/pengumuman terbaru |
| Skip Link | `#kt-skip-link` | Aksesibilitas: skip to content |
| Footer | `#containerfooter .footerclass` | Footer dengan schema markup |

### 1.3 Deteksi Framework CSS

**Framework: Bootstrap 3.x**

Bukti:
- File eksplisit: `wp-content/themes/virtue/assets/js/min/bootstrap-min.js?ver=3.4.15`
- `data-toggle="tooltip"` — sintaks Bootstrap 3 klasik
- Class patterns: `col-`, `row`, `container`, `btn-`, `nav-`, `navbar`, `dropdown`, `carousel`
- Tidak ada kelas Tailwind (`text-gray-500`, `flex`, `px-4`, `rounded-lg`, dsb.)

### 1.4 Naming Convention CSS

- WordPress native: `menu-item`, `entry-title`, `postmeta`, `postdate`, `postauthortop`
- Virtue theme: `headerclass`, `footerclass`, `navclass`, `headerfont`, `bg-lightgray`, `color_gray`, `kt-flexslider`
- Bootstrap 3: `clearfix`, `dropdown`, `tooltip`
- Semantic markup: `vcard`, `fn`, `author` (hCard microformat)

---

## 2. State & Data Flow Analysis

### 2.1 Rendering Model: SSR (Server-Side Rendering)

Website menggunakan **SSR murni via WordPress PHP**.

Bukti:
- HTML dikirim lengkap dari server dalam satu respons HTTP
- Tidak ada tanda hydration JavaScript (`__NEXT_DATA__`, `window.__nuxt__`, dll.)
- Tidak ada bundler modern (`_next/`, `/_nuxt/`, `/static/js/chunk-*.js`)
- `content-type: text/html; charset=UTF-8` dari server LiteSpeed

### 2.2 Embedded JSON Objects

**Speculation Rules API (Prefetch):**
```json
{
  "prefetch": [{
    "source": "document",
    "where": {
      "and": [
        {"href_matches": "/*"},
        {"not": {"href_matches": ["/wp-*.php", "/wp-admin/*", "/wp-content/uploads/*"]}},
        {"not": {"selector_matches": "a[rel~=\"nofollow\"]"}}
      ]
    }
  }]
}
```

**Virtue Theme Lightbox Config:**
```json
{
  "loading": "Loading...",
  "of": "%curr% of %total%",
  "error": "The Image could not be loaded."
}
```

**WordPress Emoji Settings:**
```json
{
  "baseUrl": "https://s.w.org/images/core/emoji/17.0.2/72x72/",
  "ext": ".png",
  "source": {
    "concatemoji": "https://handayani.ac.id/wp-includes/js/wp-emoji-release.min.js?ver=7.0.4"
  }
}
```

### 2.3 API Endpoints

| Endpoint | Deskripsi | Status |
|----------|-----------|--------|
| `/wp-json/` | Root REST API discovery | Terbuka |
| `/wp-json/wp/v2/pages` | Daftar semua halaman statis | Terbuka |
| `/wp-json/wp/v2/posts` | Daftar semua posting | Terbuka |
| `/wp-json/wp/v2/pages/2` | Halaman beranda (ID 2) | Terbuka |
| `/wp-json/oembed/1.0/embed` | oEmbed endpoint | Terbuka |
| `/xmlrpc.php` | XML-RPC (legacy API) | Terekspos |
| `/feed/` | RSS Feed | Terbuka |
| `/comments/feed/` | Komentar RSS | Terbuka |

> **Catatan Keamanan:** `xmlrpc.php` masih terekspos — merupakan vektor serangan brute-force dan DDoS yang dikenal, sebaiknya dinonaktifkan.

### 2.4 Pola Transfer Data

- **Server → Browser:** HTML penuh dari PHP (SSR) → Bootstrap + jQuery di-load → DOM siap
- **Lazy Content:** Gambar di-load on-demand via `imagesloaded.js` + `masonry.js`
- **REST API:** Data posting dapat diambil tanpa token via `wp-json/wp/v2/posts`
- **Speculation Rules:** Browser melakukan prefetch link internal secara otomatis

---

## 3. Sitemap & Routing Discovery

### 3.1 Menu Navigasi Utama

```
Beranda                         /
Tentang Kami
  ├── Sejarah                   /sejarah/
  ├── Struktur Organisasi       /struktur-organisasi/
  ├── Yayasan Pendidikan        /yayasan-pendidikan-handayani/
  ├── Riwayat Akreditasi        /riwayat-akreditasi/
  ├── Sarana Pendidikan         /sarana-pendidikan/
  └── Dosen                     /dosen/
Akademik
  ├── Kalender Akademik         /kalender-akademik/
  ├── Peraturan Akademik        /peraturan-akademik/
  └── Biaya Pendidikan          /biaya-pendidikan/
Program Studi
  ├── Sarjana                   /portfolio/sarjana/
  ├── Pascasarjana              /portfolio/pascasarjana/
  └── Fakultas Hukum & Sosial   /portfolio/fakultas-hukum-dan-ilmu-sosial/
Kemahasiswaan
  ├── Lembaga Kemahasiswaan     /lembaga-kemahasiswaan/
  ├── HMTI                      /himpunan-mahasiswa-teknik-informatika-hmti/
  ├── HIMAKOM                   /himpunan-mahasiswa-sistem-komputer-himakom/
  ├── HM MAINFORAKASI           /himpunan-mahasiswa-manajemen-informatika-komputer-akuntansi-hm-mainforakasi/
  ├── LDK FKMI                  /lembaga-dakwah-kampus-forum-komunikasi-mahasiswa-islam-ldk-fkmi/
  └── PMK                       /persekutuan-mahasiswa-kristen-pmk/
Riset
  └── Pusat Pengembangan        /pusat-pengembangan/
Hubungi Kami                    /hubungi-kami/
```

### 3.2 Sistem Akademik Eksternal (nav-second)

| Link | URL |
|------|-----|
| SIAKAD Mahasiswa | `https://siakadhandayani.web.id/mahasiswa` |
| SIAKAD Dosen | `https://siakadhandayani.web.id/dosen` |
| SIAKAD Keuangan | `https://siakadhandayani.web.id/keu` |
| My SIAKAD | `https://siakadhandayani.web.id/mysiakad` |

### 3.3 Pola URL

- **Halaman statis:** `/{slug}/` — format slug WordPress standar
- **Portofolio/Program Studi:** `/portfolio/{slug}/` — Custom Post Type
- **Posting/Berita:** `/{year}/{month}/{slug}/` atau `/{slug}/`
- **Kategori:** `/category/{nama-kategori}/`
- **Feed:** `/feed/`, `/comments/feed/`

---

## 4. Technology Stack Detection

### 4.1 CMS & Platform

| Teknologi | Versi | Keterangan |
|-----------|-------|-----------|
| WordPress | 7.0.4 | Terdeteksi via meta generator |
| PHP | - | Backend language (tidak terekspos versinya) |
| LiteSpeed | - | Web server (via response header `X-Powered-By`) |

### 4.2 Theme & UI Framework

| Teknologi | Versi | Keterangan |
|-----------|-------|-----------|
| Virtue Theme (Kadence) | - | `wp-content/themes/virtue/` |
| Bootstrap | 3.4.15 | CSS/JS framework |
| jQuery | - | JavaScript library (bundled WordPress) |
| FlexSlider | - | Hero image slider (`kt-flexslider`) |
| Masonry.js | - | Layout grid gambar |
| imagesloaded.js | - | Lazy image loading helper |
| FancyBox | - | Lightbox untuk gambar (`virtue_lightbox`) |

### 4.3 WordPress Plugins yang Terdeteksi

| Plugin | Bukti Deteksi |
|--------|--------------|
| Kadence Blocks / Virtue child | Class prefix `kad-`, `kt-` di DOM |
| WP REST API | `wp-json/` endpoint aktif |
| oEmbed | `/wp-json/oembed/1.0/embed` aktif |
| Speculation Rules (Core WP 7.x) | JSON `prefetch` rules disisipkan di `<head>` |

### 4.4 Font & Asset External

| Layanan | URL |
|---------|-----|
| Google Fonts | `fonts.googleapis.com` |
| WordPress Emoji CDN | `s.w.org/images/core/emoji/17.0.2/` |

### 4.5 Analytics & Tracking

Tidak terdeteksi script analytics pihak ketiga (Google Analytics, Facebook Pixel, Hotjar, dsb.) pada halaman beranda.

### 4.6 Keamanan & Header

| Item | Status | Keterangan |
|------|--------|-----------|
| HTTPS | Aktif | Sertifikat SSL aktif |
| xmlrpc.php | Terekspos | Risiko keamanan — disarankan diblokir |
| REST API (GET) | Terbuka tanpa auth | Data posting/halaman dapat diakses publik |
| WP Generator tag | Terekspos | Versi WordPress terlihat di `<meta name="generator">` |
| Login URL | `/wp-admin/` (default) | Tidak ada perlindungan URL login kustom |

### 4.7 Ringkasan Stack

```
Backend   : PHP + WordPress 7.0.4
Server    : LiteSpeed
Theme     : Virtue (Kadence) + Bootstrap 3.4.15
JS        : jQuery + FlexSlider + Masonry + FancyBox
Font      : Google Fonts
Rendering : SSR (Server-Side Rendering via PHP)
API       : WordPress REST API v2
```

---

## 5. Rekomendasi

| Prioritas | Item | Tindakan |
|-----------|------|---------|
| Tinggi | `xmlrpc.php` terekspos | Nonaktifkan atau blokir via `.htaccess` / firewall |
| Tinggi | Meta generator WordPress | Sembunyikan versi WP untuk mencegah fingerprinting |
| Sedang | REST API publik | Batasi endpoint sensitif dengan autentikasi |
| Sedang | URL admin default `/wp-admin/` | Pertimbangkan plugin untuk mengubah URL login |
| Rendah | Bootstrap 3.x | Versi EOL, pertimbangkan upgrade ke Bootstrap 5 jangka panjang |
| Rendah | Tidak ada analytics | Pertimbangkan integrasi Google Analytics / Matomo |
