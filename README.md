# Sistem Digital SOP & Pencatatan Hasil Uji — Laboratorium QQ

Aplikasi web (Laravel 10) yang mengubah SOP pengujian BBM (Flash Point, Colorimeter,
Viskositas, Water Content, TAN, Destilasi, Density) dari kertas/hafalan menjadi panduan
step-by-step digital, sekaligus tempat mencatat & mencari riwayat hasil uji.

## Fitur

- **Dashboard** — ringkasan jumlah pengujian hari ini/bulan ini, rekap per parameter, aktivitas terbaru.
- **Panduan Uji (SOP digital)** — 7 jenis pengujian, tiap jenis punya langkah SOP terurut
  dengan checklist interaktif (bisa dicentang saat dikerjakan), lengkap dengan parameter
  setting (mis. suhu) dan indikator selesai (mis. "alarm bunyi + layar hijau").
- **Form hasil uji dinamis** — tiap jenis uji punya field hasil pengukuran sendiri
  (dikonfigurasi di `config/uji.php`), jadi tidak perlu ubah kode untuk sesuaikan field.
- **Riwayat hasil uji** — pencarian by nama sampel/nomor KKW, filter by jenis uji &
  rentang tanggal, detail per hasil uji.
- **Login & role** — Admin (Supervisor) dan Petugas Lab, siap dikembangkan untuk
  pembatasan akses lanjutan lewat middleware `admin`.

## Struktur Data Inti

| Tabel | Isi |
|---|---|
| `jenis_ujis` | 7 jenis pengujian (Flash Point, Colorimeter, dst) |
| `langkah_sops` | Langkah-langkah SOP per jenis uji, terurut |
| `hasil_ujis` | Hasil pengujian (sampel, nomor KKW, hasil ukur dalam JSON, petugas, waktu) |
| `users` | Akun login, dengan kolom `role` (admin/petugas) |

## Instalasi (Lokal)

```bash
# 1. Install dependency PHP
composer install

# 2. Copy environment & generate key
cp .env.example .env
php artisan key:generate

# 3. Buat database MySQL, lalu sesuaikan .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 4. Jalankan migrasi + seeder (otomatis isi 7 jenis uji, SOP-nya, dan 2 akun demo)
php artisan migrate --seed

# 5. Jalankan server
php artisan serve
```

Buka `http://localhost:8000` — akan redirect ke halaman login.

### Akun Demo (dari seeder)

| Role | Email | Password |
|---|---|---|
| Admin / Supervisor | admin@labqq.test | password123 |
| Petugas Lab | petugas@labqq.test | password123 |

## Menambah / Mengubah Jenis Uji dan SOP

Edit `database/seeders/JenisUjiSeeder.php` untuk menambah jenis uji baru atau mengubah
langkah SOP yang sudah ada, lalu jalankan ulang:

```bash
php artisan db:seed --class=JenisUjiSeeder
```

## Mengubah Field Hasil Pengukuran per Jenis Uji

Edit `config/uji.php` bagian `fields`. Tiap jenis uji (key = kode jenis uji, mis.
`FLASHPOINT`) punya daftar field dengan `label`, `type` (`text`/`number`), dan `default`
opsional — form hasil uji di halaman SOP akan otomatis menyesuaikan.

## Struktur Folder Penting

```
app/Models/            JenisUji, LangkahSop, HasilUji, User
app/Http/Controllers/  AuthController, DashboardController, JenisUjiController, RiwayatController
app/Http/Middleware/   AdminMiddleware
config/uji.php         Konfigurasi field dinamis per jenis uji + daftar sampel umum
database/migrations/   Struktur tabel
database/seeders/      Data 7 jenis uji + SOP-nya, akun demo
resources/views/       Blade views (layout, auth, dashboard, uji, riwayat)
routes/web.php         Semua route aplikasi
```

## Rencana Pengembangan Lanjutan (opsional)

- Export riwayat hasil uji ke Excel/PDF
- Notifikasi kalau hasil uji di luar batas toleransi SNI
- Modul Inventaris & Kalibrasi Alat (terpisah, sudah dirancang sebelumnya — tabel
  `jenis_ujis` bisa direlasikan ke tabel alat fisik kalau modul ini digabung nanti)
