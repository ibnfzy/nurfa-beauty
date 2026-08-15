# Plan: Redesign UI Halaman Home Nurfa Beauty
## Berdasarkan: Analisis UI Handayani + Hallmark Study Shopee

**Verb:** `hallmark redesign` — ganti lapisan visual/interaksi, pertahankan konten, routing, dan logika bisnis.

---

## Ringkasan

Project ini adalah toko kecantikan online **Nurfa Beauty** (CodeIgniter 4 + Tailwind CSS + Alpine.js). Redesign ini menerapkan pelajaran dari dua referensi:

1. **Analisis UI Handayani** → audit masalah: tidak ada CTA kuat di above-the-fold, hierarki visual flat, whitespace minim, inkonsistensi visual.
2. **Hallmark Study Shopee** → DNA yang diadopsi: navbar sticky dengan search/cart dominan, above-fold berisi promo + kategori + flash deal, product grid konversi-first, badge ringkas, footer Ft3-Index.

**Batas redesign:** hanya lapisan visual — class Tailwind, struktur HTML seksi, token warna/tipografi. Tidak ada perubahan pada route CI4, model, controller, atau logika Alpine.js yang ada.

---

## Kondisi Saat Ini

### Token Desain (`tailwind.config.js`)
```
primary:   #E8A0BF (pink muda)
secondary: #D4A574 (warm gold)
cream:     #FFF8F0
font:      Playfair Display (display), Poppins (body)
```

### Anti-pattern yang ada (dari analisis Handayani + Hallmark)
| # | Anti-pattern | Lokasi |
|---|-------------|--------|
| 1 | Hero hanya banner gradien terpusat tanpa hook produk | `landing.php` Welcome Banner |
| 2 | Tidak ada strip promo / flash deal di atas fold | `landing.php` — tidak ada |
| 3 | Kategori tersembunyi di bawah scroll | `landing.php` — section Kategori |
| 4 | Navbar logo-only tanpa search bar | `components/navbar.php` |
| 5 | Tombol CTA generic (`bg-gradient-to-r`) — tidak ada hierarki CTA | semua section |
| 6 | Voucher banner terputus dari hero, muncul melayang `-mt-6` | `landing.php` Voucher Banner |
| 7 | Section heading semua centered — AI fingerprint | seluruh `landing.php` |
| 8 | Footer `bg-white` — tidak terbedakan dari body | `components/footer.php` |
| 9 | `transition-all` pada banyak elemen | `app.css`, semua view |
| 10 | Badge "TERLARIS" overload tanpa hirarki | product card sections |

---

## Keputusan Desain (Hallmark DNA)

### Macrostructure baru: **Commerce Strip + Catalogue Feed**
Berbeda dari macrostructure saat ini (Hero → Voucher → Kategori → Produk → CTA) yang tipikal AI:

```
NAVBAR         sticky, putih, search center, cart kanan
PROMO STRIP    full-bleed berwarna, satu baris, teks + CTA ringkas
HERO SPLIT     kiri: headline + CTA, kanan: product highlight visual
KATEGORI CHIP  scroll horizontal, ikon + label, no heading terpisah
FLASH DEAL     strip accent dengan countdown, grid produk 4 kolom
PRODUK BARU    heading hanging kiri, grid 5 kolom, badge minimal
CTA BAND       full-bleed primary, bukan gradient
FOOTER         bg-gray-50, 4 kolom Ft3-Index
```

### Fingerprint struktural
| Axis | Pilihan |
|------|---------|
| Section heading | **Hanging** (mengambang di atas section, flush kiri) |
| Body composition | **Asymmetric grid** (hero 2-col: 5fr/7fr) |
| Divider language | **Negative space** — gap adalah divider, tanpa garis |
| Button voice | **Oversized solid** untuk CTA primer, **Outlined** untuk sekunder |
| Image treatment | **Tightly cropped** product card, **Full-bleed** promo strip |
| Reveal pattern | **Fade-up stagger** ringan (CSS only, `@keyframes fadeUp`) |

### Token tambahan (extend Tailwind, tidak mengganti yang ada)
```js
// Ditambahkan di tailwind.config.js → theme.extend
colors: {
  'promo': '#C77DA0',      // primary-dark — strip promo
  'flash': '#E8C47D',      // warning — flash deal accent
}
// Tidak ada token baru yang menimpa yang lama
```

### Tipografi
Pertahankan `Playfair Display` (heading) + `Poppins` (body). Tidak ada perubahan font. Perbaikan: terapkan type scale yang konsisten:
- Display heading: `text-4xl sm:text-5xl font-playfair`
- Section heading: `text-xl font-playfair font-semibold`
- Body: `text-sm font-poppins text-gray-700`
- Label/badge: `text-xs font-poppins font-semibold uppercase tracking-wide`

---

## Perubahan File yang Diperlukan

### 1. `src/css/app.css` — append only
**Apa:** Tambahkan token animasi + perbaikan `transition-all` → `transition-colors`/`transition-shadow`.
**Mengapa:** Hallmark contract — append-only, jangan ganti `@tailwind` directives.
**Bagaimana:** Tambahkan di bawah `@layer components` yang ada:
```css
/* Hallmark: genre=Commerce, tone=warm-feminine, anchor=pink #E8A0BF, structure=Commerce-Strip+Catalogue-Feed */
@layer utilities {
  .fade-up {
    animation: fadeUp 0.4s ease-out both;
  }
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }
}
/* Ganti transition-all di komponen → lebih spesifik */
@layer components {
  .btn-primary   { @apply transition-shadow duration-200; }
  .btn-secondary { @apply transition-shadow duration-200; }
  .btn-outline   { @apply transition-colors duration-200; }
}
```

---

### 2. `app/Views/components/navbar.php` — redesign visual layer
**Apa:** Tambahkan search bar center (static/form GET ke `/catalog?q=`), perkuat badge cart, rapikan layout.
**Mengapa:** Anti-pattern #4 — navbar saat ini tidak ada search; Shopee DNA: search dominan.
**Bagaimana:**
- Layout: `logo (kiri, 1/4) | search bar (tengah, 2/4) | aksi (kanan, 1/4)`
- Search: `<form action="/catalog" method="GET"><input name="q"></form>` — tidak ada logika baru, hanya UI
- Cart badge: ganti `hidden` class → selalu visible dengan `x-show` Alpine
- Tidak ada perubahan logika notifikasi, dropdown, atau autentikasi

---

### 3. `app/Views/customer/home/landing.php` — redesign utama
**Apa:** Ganti struktur seksi berdasarkan macrostructure baru.
**Bagaimana (per seksi):**

#### Hapus: Welcome Banner (gradient hero terpusat)
Diganti dengan **Hero Split** (2 kolom asymmetric).

#### Baru: Promo Strip (di atas hero)
```
Kondisional: tampil jika $welcomeVoucher ada
Full-bleed bg-primary-dark text-white py-2 px-4
Teks ringkas + kode voucher inline + tombol "Klaim" outline-putih kecil
```

#### Baru: Hero Split
```
Grid 2-kolom: col kiri 5/12 (headline + subtext + 2 CTA), col kanan 7/12 (gambar produk hero)
Heading: font-playfair text-4xl, flush kiri (bukan centered)
CTA primer: "Mulai Belanja" oversized solid primary
CTA sekunder: "Lihat Katalog" outlined
```

#### Ganti: Voucher Banner → diintegrasikan ke Promo Strip
Hapus section voucher terpisah yang melayang `-mt-6`.

#### Pertahankan + Rapikan: Kategori
```
Label section: hanging kiri, bukan centered
Tampilan: scroll horizontal chip (flex gap-3 overflow-x-auto)
Setiap chip: ikon + nama kategori, border rounded-full
```

#### Baru: Flash Deal Strip (jika ada $promotions)
```
Strip accent bg-warning/10 border-b border-warning/20
Header: "Flash Deal" label kiri + countdown kanan (Alpine x-data)
Grid 4 kolom produk dengan badge harga coret + harga diskon
```

#### Pertahankan + Rapikan: Produk Terbaru
```
Heading: hanging kiri ("Produk Terbaru")
Grid: 5 kolom desktop, 2 kolom mobile
Card: tightly cropped image, harga ringkas, badge minimal (hanya 1 badge per card)
```

#### Pertahankan + Rapikan: Per-Kategori Sections
```
Heading: hanging kiri, bukan centered
Layout: grid, bukan carousel (lebih SEO-friendly)
```

#### Ganti: CTA Section
```
Full-bleed bg-primary (bukan gradient)
Heading centered, satu tombol outlined-putih
```

---

### 4. `app/Views/components/footer.php` — redesign visual layer
**Apa:** Ubah background, perkuat tipografi kolom.
**Mengapa:** Anti-pattern #8 — footer `bg-white` tidak terbedakan dari body.
**Bagaimana:**
- `bg-white` → `bg-gray-50 border-t border-gray-200`
- Brand kolom: tambahkan tagline pendek + ikon media sosial placeholder
- Heading kolom: ganti `font-semibold text-gray-800` → `text-xs font-poppins uppercase tracking-widest text-gray-400` (kontras + modern)
- Copyright: tambahkan separator hairline lebih tegas

---

## Asumsi & Keputusan

| Keputusan | Alasan |
|-----------|--------|
| Pertahankan token warna yang ada (`primary`, `secondary`, `cream`) | Hallmark contract: reuse token yang ada, jangan shadow |
| Tidak ubah `tailwind.config.js` kecuali extend kecil | Cukup tambahkan `promo` dan `flash` alias |
| Search bar di navbar hanya UI (GET `/catalog?q=`) | Controller catalog sudah ada; tidak butuh logika baru |
| Flash Deal countdown pakai Alpine.js inline | Alpine sudah tersedia; tidak butuh library baru |
| Tidak ubah PHP controller/model | Hallmark scope: visual layer only |
| Gambar hero pakai URL `text_to_image` API | Sesuai Image Guidelines — tidak boleh placeholder |

---

## Verifikasi

Setelah implementasi, pastikan:
- [ ] `src/css/app.css` masih punya `@tailwind base/components/utilities` di baris pertama (append-only)
- [ ] `tailwind.config.js` masih scan `./app/Views/**/*.php`
- [ ] Semua route (`/catalog`, `/cart`, `/auth/login`, dll.) tidak berubah
- [ ] Alpine.js `x-data` / `x-show` di navbar masih berfungsi
- [ ] Halaman home ter-render tanpa error PHP (variabel `$promotions`, `$latestProducts`, dll. tetap dipakai)
- [ ] Mobile menu masih muncul dengan `mobileOpen` toggle
- [ ] Tidak ada `transition-all` tersisa di komponen yang diubah

---

## File yang Diubah (ringkasan)

| File | Jenis Perubahan |
|------|----------------|
| `src/css/app.css` | Append — animasi + perbaikan transition |
| `app/Views/components/navbar.php` | Redesign visual — tambah search bar, perkuat cart badge |
| `app/Views/customer/home/landing.php` | Redesign struktur seksi — macrostructure baru |
| `app/Views/components/footer.php` | Redesign visual — background, heading kolom |
