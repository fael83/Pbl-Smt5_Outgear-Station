# Catatan Perbaikan Backend — OutGear Station

Dokumen ini merangkum perbaikan hasil code review yang dikerjakan bertahap.
Setiap fase diakhiri dengan `vendor/bin/pint`, `php artisan test`, dan satu commit
terpisah. Tidak ada perubahan pada `.env`, view/JS/CSS, maupun data Supabase.

Hasil akhir: **94 test lulus, 429 assertion**, `route:cache` dan `config:cache`
berhasil.

---

## Fase 1 — Keamanan route admin & otorisasi

### Masalah
- Halaman `/admin/*` masih berupa closure dan belum konsisten dilindungi role,
  sehingga `php artisan route:cache` gagal.
- Aksi tulis peralatan dapat diakses peran selain admin.
- Ada route `/adminAlat` duplikat.
- Assertion test dashboard masih memakai teks lama.

### Perbaikan
- Closure diganti controller invokable:
  `BerandaController`, `DashboardController`, `KatalogController`, dan
  `Admin\{Dashboard,Pelanggan,Pemesanan,Verifikasi,Denda,Laporan}Controller`.
- `routes/web.php`:
  - `/` publik, `/katalog` publik.
  - `/dashboard` memakai `auth` + `verified`, lalu diarahkan sesuai peran
    (staff ke `admin.dashboard`, customer ke `/katalog`).
  - Grup `/admin` memakai `auth` + `role:admin,owner,petugas`.
  - Aksi tulis peralatan (`POST/PUT/DELETE`) dibatasi `role:admin`.
  - Route `/adminAlat` duplikat dihapus.
- `AlatRequest::authorize()` memakai policy (`create` untuk POST, `update` untuk
  route-bound `alat`).
- `InventarisController` menambahkan `authorize('viewAny', Alat::class)` pada
  `index`/`tabel` dan `authorize('delete', $alat)` pada `destroy`.
- Test baru `tests/Feature/AdminAccessTest.php`: guest diarahkan ke login,
  customer mendapat 403, petugas/owner hanya baca, admin CRUD penuh, dan
  redirect `/dashboard` per peran.

### Catatan / deviasi
- Grup baca admin memakai `role:admin,owner,petugas` (sesuai kebutuhan menu
  petugas), sedangkan operasi tulis tetap `role:admin` saja dan selaras dengan
  `AlatPolicy`.

---

## Fase 2 — Filter alat, login akun nonaktif, registrasi, hapus akun

### Masalah
- `InventarisController::queryAlat()` menaruh `orWhereKey()` di luar grup
  `where(...)`, sehingga hasil pencarian bisa lolos dari filter kategori/kondisi.
- Kata kunci LIKE tidak di-escape (`%` dan `_`).
- Akun berstatus `nonaktif` tetap bisa login.
- Validasi registrasi belum memakai FormRequest dan nomor telepon belum
  divalidasi format Indonesia.
- Menghapus profil memakai `delete()` sehingga berpotensi melanggar foreign key
  riwayat pemesanan.

### Perbaikan
- Semua kondisi pencarian (termasuk pencarian ID) dipindah ke dalam satu grup
  `where(function ...)`; wildcard di-escape dengan `addcslashes($search, '%_\\')`.
- `LoginRequest::authenticate()` menolak akun yang bukan `aktif`: sesi logout
  dan muncul pesan "Akun Anda sedang tidak aktif...".
- `App\Http\Requests\Auth\RegisterRequest` baru:
  - `no_telepon` wajib cocok `^(\+62|62|0)8[1-9][0-9]{6,11}$`.
  - `RegisteredUserController` memakai `$request->validated()`, `Role::Customer`,
    dan tidak lagi memanggil `Hash::make()` (hash ditangani cast `hashed`).
- `ProfileController::destroy()` menonaktifkan akun (`status_akun = 'nonaktif'`)
  lalu logout, bukan menghapus baris `users`. Riwayat pemesanan tetap utuh.
- Test ditambah/diperbarui:
  - pencarian angka + filter kategori/kondisi,
  - login akun nonaktif,
  - registrasi valid (role customer, password ter-hash) dan nomor tidak valid,
  - nonaktifkan akun beserta riwayat pemesanan.

---

## Fase 3 — Konsistensi seeder & lama sewa

### Masalah
- `DemoTransaksiSeeder` memakai lama sewa hardcode `3` dan menghitung denda
  kerusakan dari `tarif_per_hari`, bukan `nilai_pengganti`.
- Seeder tidak atomik dan tetap menjalankan data demo di produksi.
- Password akun seeder hardcode `password`.
- Belum ada satu sumber perhitungan lama sewa.

### Perbaikan
- `Pemesanan::lamaSewa()` baru: selisih hari **inklusif** (`1–3 Oktober = 3 hari`,
  minimal 1 hari).
- `DemoTransaksiSeeder`:
  - memakai `$pemesanan->lamaSewa()` untuk subtotal,
  - denda kerusakan ringan = `nilai_pengganti × persentase / 100`
    (`nilai_dasar` = `nilai_pengganti`, `nilai_tarif_terkait` tetap tarif harian),
  - seluruh pembuatan data dibungkus `DB::transaction()`.
- `PenggunaSeeder` memakai `env('SEED_PASSWORD', 'password')`.
- `DatabaseSeeder` melewati `DemoTransaksiSeeder` saat `app()->environment('production')`.
- `.env.example` mendapat `SEED_PASSWORD=` beserta komentar.
- Test: lama sewa inklusif, subtotal demo, denda dari nilai pengganti, dan
  seeding produksi tidak membuat transaksi demo.

### Tindakan manual di server
Isi `SEED_PASSWORD` dengan password kuat sebelum menjalankan seeder di
produksi. Jangan jalankan `migrate:fresh`/`db:seed` pada database Supabase yang
sudah berisi data.

---

## Fase 4 — Verifikasi akhir

- `vendor/bin/pint` (seluruh project) — bersih.
- `php artisan test` — 94 lulus, 429 assertion.
- `php artisan route:cache` — berhasil (tidak ada closure).
- `php artisan config:cache` — berhasil.
- `php artisan optimize:clear` — berhasil.
- `.gitignore` sudah memuat `.env`, `.env.backup`, dan `.env.production`.
- Tidak ada migration baru; struktur database tidak berubah.

### Perintah verifikasi
```sh
vendor/bin/pint --test
php artisan test
php artisan route:cache
php artisan optimize:clear
```
