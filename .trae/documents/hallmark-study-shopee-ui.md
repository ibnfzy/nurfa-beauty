# Plan: Hallmark Study — Deskripsi UI Shopee

## Ringkasan
Menjalankan `hallmark study` terhadap `https://shopee.co.id/` untuk mengekstrak DNA desain UI-nya, lalu menyimpan hasil diagnosis ke file MD di root project.

---

## Analisis Kondisi Saat Ini

- Project: CodeIgniter 4 + Tailwind CSS di `/Users/pallakadev/nurfa-beauty/`
- Folder `.trae/documents/` belum ada (dibuat sekarang untuk plan file ini)
- Sudah ada file analisis MD lain di root: `analisis-handayani.ac.id.md` dan `analisis-ui-handayani.ac.id.md`
- Shopee adalah SPA React — WebFetch hanya mengembalikan JS shell (bukan HTML ter-render), sehingga **URL mode** menggunakan knowledge-based fallback yang dikombinasikan dengan data publik Shopee Design System
- Diagnosis Hallmark Study sudah dijalankan di sesi sebelumnya dan hasilnya lengkap

---

## Rencana Perubahan

### File yang Akan Dibuat
**`/Users/pallakadev/nurfa-beauty/shopee-ui-dna.md`**

Isi file mencakup diagnosis Hallmark Study lengkap dengan skema DNA:

1. **Macrostructure** — Mega Catalogue Feed / Scrollable Commerce Index
2. **Surface (Warna)** — Palet lengkap dengan nilai eksak (#EE4D2D, #F5F5F5, dll.)
3. **Tipografi** — ShopeeSans / system-ui, single family, semua role
4. **Komponen & Arsitektur Layout** — Hero, Nav, Pitch, Footer dengan archetype Hallmark
5. **Motion** — CSS transition, countdown timer, tanpa library berat
6. **Anti-patterns yang terdeteksi** — hover-scale, badge overload, transition-all
7. **Schema JSON** — DNA portabel (design.md format)
8. **Keterbatasan analisis** — SPA blind spot, rhythm tidak observable
9. **Follow-up options** — build, lock DNA, redesign

---

## Keputusan & Asumsi

- **Nama file:** `shopee-ui-dna.md` (konsisten dengan konvensi penamaan file MD yang ada)
- **Lokasi:** root project (sama dengan file analisis lainnya)
- **Mode:** knowledge-based study (bukan URL-extracted murni) karena Shopee SPA memblokir WebFetch
- **Bahasa:** Indonesia

---

## Langkah Implementasi

1. Buat file `shopee-ui-dna.md` di root project dengan diagnosis Hallmark Study lengkap
2. Tidak ada file lain yang perlu diubah

---

## Verifikasi
- File `shopee-ui-dna.md` ada di root project
- Konten mencakup semua 9 bagian DNA yang ditentukan di atas
