# Analisis UI - Halaman Home
## Website: https://handayani.ac.id/
**Tanggal Analisis:** 16 Agustus 2026
**Viewport Analisis:** 1440 x 900px (Desktop)
**Total Tinggi Halaman:** 3.336px

---

## 1. Ringkasan Visual Keseluruhan

Halaman home Universitas Handayani Makassar menggunakan layout vertikal multi-seksi yang tipikal untuk website institusi akademik. Desain bersifat **konvensional dan informatif**, tidak mengikuti tren desain modern. Kesan visual dominan adalah formal dan institusional.

**Judul Halaman:**
> "Universitas Handayani Makassar – A Technopreneurship Campus College of Informatics Management and Computer Handayani Makassar"

---

## 2. Palet Warna

| Elemen | Warna | Kode RGB | Hex (estimasi) |
|--------|-------|----------|----------------|
| Top Bar (latar) | Biru tua | `rgb(45, 92, 136)` | `#2D5C88` |
| Header (latar) | Putih | `rgb(255, 255, 255)` | `#FFFFFF` |
| Body (latar) | Putih | `rgb(255, 255, 255)` | `#FFFFFF` |
| Footer (latar) | Abu-abu muda | `rgb(242, 242, 242)` | `#F2F2F2` |
| Teks navigasi | Gelap (dark gray/hitam) | — | `#333` (estimasi) |
| Aksen/tombol | Biru (turunan Bootstrap 3) | — | `#337AB7` (estimasi) |

**Catatan Palet:**
- Palet warna sangat minim: dominasi putih, abu-abu, dan satu warna brand biru tua.
- Tidak ada penggunaan warna sekunder atau aksen yang kuat untuk CTA.
- Kontras warna antara top bar (biru `#2D5C88`) dan teks putih di atasnya: **rasio kontras cukup baik (~5:1)**, memenuhi standar WCAG AA.

---

## 3. Tipografi

| Elemen | Font |
|--------|------|
| Body / Paragraph | `Verdana, Geneva, sans-serif` |
| Heading (H1, H2) | Google Fonts (diload dari `fonts.googleapis.com`) |
| Navigasi | Mengikuti font body (Verdana) |

**Catatan Tipografi:**
- Verdana adalah font lama yang mudah dibaca di layar namun terkesan dated secara desain.
- Tidak terdeteksi sistem tipografi yang konsisten (type scale) — ukuran heading tampak diatur per-elemen secara ad hoc.
- Penggunaan dua sumber font (system font Verdana + Google Fonts) tanpa penyeragaman menimbulkan inkonsistensi visual.

---

## 4. Layout & Grid

```
┌──────────────────────────────────────────────────────────────┐
│  TOP BAR  (biru #2D5C88) — KUISIONER | E-SIAKAD | E-SISTER  │
│           E-LEARNING | LPM + Search Bar                      │
├──────────────────────────────────────────────────────────────┤
│  HEADER   (putih) — Logo kiri + Navigasi Utama kanan         │
├──────────────────────────────────────────────────────────────┤
│  CATEGORY NAV — Navigasi Kategori Tambahan                   │
├──────────────────────────────────────────────────────────────┤
│  HERO SLIDER — 4 slide gambar (FlexSlider)                   │
│  [1440px lebar penuh]                                        │
├──────────────────────────────────────────────────────────────┤
│  PROGRAM STUDI CAROUSEL — Portfolio item carousel            │
│  [Grid horizontal, scrollable]                               │
├──────────────────────────────────────────────────────────────┤
│  BERITA / BLOG — 4 artikel terbaru (2 kolom atau list)       │
│  Tanggal + Judul + Excerpt + READ MORE                       │
├──────────────────────────────────────────────────────────────┤
│  WELCOME SECTION — Teks sambutan institusi                   │
│  ⚠️  Ada konten spam/injeksi teks asing (kasino online)      │
├──────────────────────────────────────────────────────────────┤
│  QUICK LINKS — Digilib | E-learning | Classroom | E-Journal  │
│               Tracer Study                                   │
├──────────────────────────────────────────────────────────────┤
│  FOOTER   (abu #F2F2F2)                                      │
│  ├── About UHM (teks deskripsi)                              │
│  ├── Support By (logo mitra)                                 │
│  ├── Kontak Kami (alamat, telp, email)                       │
│  ├── Follow Us (media sosial)                                │
│  └── Aplikasi Pengumuman + Menu Footer                       │
└──────────────────────────────────────────────────────────────┘
```

**Sistem Grid:** Bootstrap 3 12-kolom. Tidak menggunakan CSS Grid atau Flexbox modern.

---

## 5. Komponen UI Detail

### 5.1 Top Bar
- **Latar:** Biru tua `#2D5C88`
- **Konten:** Link cepat ke KUISIONER, E-SIAKAD (Mahasiswa, Dosen, Keuangan, Operator), E-SISTER, E-LEARNING, LPM
- **Desain:** Teks kecil horizontal, tanpa ikon. Terkesan padat dan kurang user-friendly di mobile.
- **Masalah:** Terlalu banyak item dalam satu baris; pada viewport kecil kemungkinan overflow.

### 5.2 Header & Logo
- **Logo:** File PNG — `mainlogo.png` (diupload 2022)
- **Posisi:** Logo di kiri, navigasi di kanan — layout standar
- **Background:** Putih bersih
- **Navbar:** Navigasi horizontal dengan dropdown submenu

### 5.3 Menu Navigasi Utama
Menu menggunakan pola **mega dropdown** dengan Bootstrap navbar:

| Item | Sub-menu |
|------|---------|
| Home | — |
| Profil | Yayasan, Sejarah UHM, Visi & Misi, Struktur Organisasi, Dosen, Sarana Pendidikan, Pusat Pengembangan, Riwayat Akreditasi, Logo UHM |
| Akademik | — |
| Fakultas | Fakultas Ilmu Komputer → (S2 Sistem Komputer, S1 Sistem Komputer, S1 Teknik Informatika, S1 Sistem Informasi, S1 Pendidikan Teknologi Informasi, D3 Manajemen Informatika) |
| Kemahasiswaan | — |
| Riset | — |
| Hubungi Kami | — |

**Masalah navigasi:**
- Kedalaman submenu hingga 3 level → meningkatkan cognitive load pengguna
- Label "Profil »" tidak deskriptif untuk institusi perguruan tinggi
- Tidak ada indikator visual untuk item aktif (active state) yang jelas

### 5.4 Hero Image Slider
- **Jumlah Slide:** 4 gambar
- **Library:** FlexSlider (`kt-flexslider`)
- **Lebar:** Full-width (1440px)
- **Kontrol:** Arrow kiri/kanan + dot indicator (standar FlexSlider)
- **Masalah:** Tidak terdeteksi teks overlay atau CTA button di atas slider — slider hanya berfungsi dekoratif tanpa pesan nilai (value proposition) yang jelas.

### 5.5 Section Program Studi (Portfolio Carousel)
- **Tipe:** Horizontal carousel
- **ID:** `#portfolio-carousel`, `#carouselcontainer-portfolio`
- **Konten:** Kartu program studi
- **Masalah:** Konten carousel tidak ter-index dengan baik untuk SEO; isi item tidak dapat diambil via DOM query standar (kemungkinan dirender via JS setelah load).

### 5.6 Section Berita / Blog
- **Jumlah Artikel:** 4 artikel terbaru
- **Format:** Tanggal (Agu 2026) + Judul + Separator `|` + Excerpt + tombol "READ MORE"
- **Artikel Terbaru:**
  1. "Resmi Dibuka Pendaftaran Semester Pendek TA 2025/2026" — 4 Agu 2026
  2. "Mahasiswa KKN Angkatan XXVI UHM Siap Mengabdi di Makassar dan Takalar" — 4 Agu 2026
  3. "Pengumuman Batas Akhir Pelunasan Biaya KKN Angkatan XXVI Tahun 2026" — 13 Jul 2026
- **Masalah:**
  - Tombol "READ MORE" dalam bahasa Inggris, tidak konsisten dengan konten artikel yang berbahasa Indonesia
  - Tidak ada gambar thumbnail artikel → kurang menarik secara visual
  - Separator `|` antara metadata terkesan kuno

### 5.7 Welcome Section
- **Konten:** Teks "Selamat Datang di Universitas Handayani Makassar"
- **MASALAH KRITIS:** Section ini mengandung **konten spam berbahasa asing** (Belanda, Prancis, Polandia) yang berkaitan dengan kasino online (`online gokkasten echt geld`, `neteller casino canada`, `echeck casino`, `kasyno online przelewy24`). Ini merupakan indikasi **website telah diinjeksi konten spam/SEO spam (SEO poisoning)** oleh pihak tidak bertanggung jawab.

### 5.8 Quick Links
- **Item:** Digilib, E-learning, Classroom, E-Journal, Tracer Study
- **Tampilan:** Link teks horizontal
- **Masalah:** Tidak ada ikon visual pendukung; tampilan kurang menonjol

### 5.9 Footer
- **Background:** Abu-abu muda `#F2F2F2`
- **Konten:**
  - Kolom 1: Deskripsi singkat UHM (didirikan 1996, Magister Komputer pertama di Indonesia Timur)
  - Kolom 2: Support By (logo mitra)
  - Kolom 3: Kontak — Jl. Adhyaksa Baru No. 1, Telp. (0411) 4673395, Email: info@handayani.ac.id
  - Kolom 4: Follow Us (media sosial) + Aplikasi Pengumuman
- **Menu Footer:** Beranda | LPPM | LPM | ICT Center | Alumni | Hubungi Kami
- **Copyright:** © 2026 Universitas Handayani Makassar

---

## 6. Penilaian UX

### 6.1 Hierarki Visual
| Aspek | Nilai | Catatan |
|-------|-------|---------|
| Kejelasan CTA | Buruk | Tidak ada tombol CTA utama yang jelas (misal: "Daftar Sekarang") |
| Keterbacaan | Sedang | Font Verdana terbaca, tapi ukuran teks kecil di top bar |
| Konsistensi | Buruk | Campuran bahasa (Indonesia/Inggris), gaya tombol tidak seragam |
| Whitespace | Buruk | Elemen terlalu padat, minim ruang napas |

### 6.2 Navigasi
| Aspek | Nilai | Catatan |
|-------|-------|---------|
| Kemudahan Navigasi | Sedang | Struktur menu ada tapi terlalu dalam (3 level) |
| Breadcrumb | Tidak Ada | Tidak terdeteksi breadcrumb di homepage |
| Search | Ada | Terdapat di top bar |
| Mobile-friendliness | Belum Diuji | Bootstrap 3 memiliki responsive tapi implementasi perlu diverifikasi |

### 6.3 Aksesibilitas
| Aspek | Status | Catatan |
|-------|--------|---------|
| Skip Link | Ada | `#kt-skip-link` tersedia |
| Alt Text Gambar | Belum Diverifikasi | Perlu audit manual |
| Kontras Warna | Cukup | Top bar biru vs putih ~5:1 (WCAG AA) |
| Keyboard Navigation | Belum Diuji | Perlu pengujian manual |
| ARIA Labels | Belum Diverifikasi | Perlu audit manual |

---

## 7. Masalah UI yang Ditemukan

### Kritis
1. **Injeksi Konten Spam** — Section "Selamat Datang" berisi teks kasino online berbahasa Belanda, Prancis, dan Polandia. Indikasi website telah dikompromikan via SEO spam injection.

### Tinggi
2. **Tidak Ada CTA Utama** — Tidak ada tombol "Daftar Mahasiswa Baru" atau ajakan bertindak yang jelas di above-the-fold.
3. **Hero Slider Tanpa Pesan** — 4 slide gambar tidak memiliki teks overlay atau tombol aksi; kehilangan kesempatan komunikasi nilai institusi.

### Sedang
4. **Inkonsistensi Bahasa** — Tombol "READ MORE" bahasa Inggris pada konten berbahasa Indonesia.
5. **Tipografi Outdated** — Verdana sebagai font utama terkesan tidak modern.
6. **Navigasi Terlalu Dalam** — Submenu hingga 3 level meningkatkan cognitive load.
7. **Top Bar Terlalu Padat** — Terlalu banyak link penting dijejal dalam satu baris.

### Rendah
8. **Tidak Ada Thumbnail Berita** — Kartu artikel tanpa gambar kurang menarik secara visual.
9. **Palet Warna Minim** — Satu warna brand tanpa variasi aksen membuat halaman terasa flat.
10. **Footer Sederhana** — Kurang informasi tambahan seperti peta lokasi atau jam operasional.

---

## 8. Rekomendasi Perbaikan UI

| Prioritas | Rekomendasi | Dampak |
|-----------|-------------|--------|
| Kritis | Bersihkan injeksi konten spam di section Welcome | Keamanan & kepercayaan |
| Tinggi | Tambahkan tombol CTA "Daftar Sekarang" di hero section | Konversi pendaftar |
| Tinggi | Tambahkan teks overlay + CTA pada hero slider | Komunikasi nilai |
| Tinggi | Modernisasi tipografi dengan Google Fonts yang lebih modern | Kesan visual |
| Sedang | Sederhanakan menu navigasi menjadi maks. 2 level | Kemudahan navigasi |
| Sedang | Seragamkan bahasa seluruh tombol ke Bahasa Indonesia | Konsistensi |
| Sedang | Tambahkan thumbnail gambar pada kartu berita | Daya tarik visual |
| Rendah | Perluas palet warna dengan warna aksen sekunder | Estetika |
| Rendah | Tambahkan lebih banyak whitespace antar seksi | Keterbacaan |
| Rendah | Pertimbangkan upgrade Bootstrap 3 → Bootstrap 5 | Modernisasi & responsif |
