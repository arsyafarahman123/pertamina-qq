-- Database Dump Pertamina QQ Fuel Terminal Maos
-- Compatible with MySQL / MariaDB (cPanel & InfinityFree)

SET FOREIGN_KEY_CHECKS=0;

-- Table structure for `migrations`
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `migration` VARCHAR(191) NOT NULL,
  `batch` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2026_08_31_000001_create_jenis_ujis_table', 1),
(3, '2026_08_31_000002_create_langkah_sops_table', 1),
(4, '2026_08_31_000003_create_hasil_ujis_table', 1),
(5, '2026_08_31_000004_add_role_to_users_table', 1),
(6, '2026_09_02_000005_add_foto_bukti_to_hasil_ujis_table', 1),
(7, '2026_09_03_082825_create_cache_table', 1),
(8, '2026_09_10_000001_add_spbu_role_to_users_and_checklist_tables', 2),
(9, '2026_09_10_100248_fix_users_role_constraint_for_spbu', 3),
(10, '2026_09_10_120000_create_retain_sampel_mts_table', 3),
(11, '2026_09_10_130000_create_retain_sampel_fotos_table', 4),
(12, '2026_09_10_140000_add_foto_path_to_retain_sampel_mts_table', 4),
(13, '2026_09_11_120000_migrate_retain_to_0600', 5);

-- Table structure for `password_reset_tokens`
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(191) PRIMARY KEY,
  `token` VARCHAR(191) NOT NULL,
  `created_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `sessions`
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(191) PRIMARY KEY,
  `user_id` BIGINT NULL,
  `ip_address` VARCHAR(191) NULL,
  `user_agent` TEXT NULL,
  `payload` TEXT NOT NULL,
  `last_activity` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `jenis_ujis`
DROP TABLE IF EXISTS `jenis_ujis`;
CREATE TABLE `jenis_ujis` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kode` VARCHAR(191) NOT NULL,
  `nama` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NOT NULL,
  `deskripsi` TEXT NULL,
  `icon` VARCHAR(191) NULL,
  `aktif` BIGINT NOT NULL,
  `urutan` BIGINT NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `jenis_ujis` (`id`, `kode`, `nama`, `slug`, `deskripsi`, `icon`, `aktif`, `urutan`, `created_at`, `updated_at`) VALUES
(1, 'FLASHPOINT', 'Flash Point Tester', 'flash-point-tester', 'Pengujian titik nyala pada Solar, Biosolar, Pertamina Dex, dan Dexlite.', 'flame', 1, 1, '2026-09-04 10:55:54', '2026-09-04 10:55:54'),
(2, 'COLORIMETER', 'Colorimeter', 'colorimeter', 'Menentukan warna minyak (Solar/Gasoil).', 'palette', 1, 2, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(3, 'VISKOSITAS', 'Viskositas', 'viskositas', 'Pengujian kekentalan (viskositas) sampel BBM.', 'droplet', 1, 3, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(4, 'WATER_CONTENT', 'Water Content', 'water-content', 'Pengujian kadar air pada sampel BBM.', 'droplets', 1, 4, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(5, 'TAN', 'TAN (Total Acid Number)', 'tan-total-acid-number', 'Pengujian angka asam pada sampel BBM.', 'flask-conical', 1, 5, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(6, 'DESTILASI', 'Destilasi', 'destilasi', 'Melihat kemurnian sampel dan mendeteksi kontaminan.', 'thermometer', 1, 6, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(7, 'DENSITY', 'Density', 'density', 'Pengujian densitas menggunakan thermometer & hidrometer.', 'gauge', 1, 7, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(8, 'RON', 'RON (Research Octane Number)', 'ron-research-octane-number', 'Pengujian bilangan oktana riset pada bensin (Pertalite, Pertamax, Pertamax Turbo).', 'zap', 1, 8, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(9, 'SULFUR', 'Kandungan Sulfur', 'kandungan-sulfur', 'Pengujian kandungan sulfur pada seluruh jenis BBM (bensin & diesel).', 'flask-round', 1, 9, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(10, 'UJI_BBM_SPEK', 'Uji Kesesuaian Spesifikasi BBM', 'uji-kesesuaian-spesifikasi-bbm', 'Pengujian kesesuaian mutu BBM (gasoline & gasoil) terhadap batas spesifikasi Dirjen Migas, dari menu Pengujian BBM Terpadu.', 'clipboard-check', 0, 999, '2026-09-07 11:01:46', '2026-09-07 11:01:46');

-- Table structure for `langkah_sops`
DROP TABLE IF EXISTS `langkah_sops`;
CREATE TABLE `langkah_sops` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `jenis_uji_id` BIGINT NOT NULL,
  `urutan` BIGINT NOT NULL,
  `judul_singkat` VARCHAR(191) NOT NULL,
  `instruksi` TEXT NOT NULL,
  `parameter_setting` VARCHAR(191) NULL,
  `indikator_selesai` VARCHAR(191) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `langkah_sops` (`id`, `jenis_uji_id`, `urutan`, `judul_singkat`, `instruksi`, `parameter_setting`, `indikator_selesai`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Reset alat', 'Reset alat Flash Point Tester ke kondisi awal sebelum digunakan.', NULL, NULL, '2026-09-04 10:55:54', '2026-09-04 10:55:54'),
(2, 1, 2, 'Masukkan sampel', 'Masukkan sampel BBM sesuai batas takar yang ditentukan pada wadah uji.', NULL, NULL, '2026-09-04 10:55:54', '2026-09-04 10:55:54'),
(3, 1, 3, 'Masukkan ke alat', 'Masukkan wadah berisi sampel ke dalam alat Flash Point Tester.', NULL, NULL, '2026-09-04 10:55:54', '2026-09-04 10:55:54'),
(4, 1, 4, 'Tutup dan tekan', 'Tutup rapat alat lalu tekan penutup hingga terkunci.', NULL, NULL, '2026-09-04 10:55:54', '2026-09-04 10:55:54'),
(5, 1, 5, 'Atur suhu 60°C', 'Atur setting suhu alat ke 60°C.', 'Suhu 60°C', NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(6, 1, 6, 'Input nomor KKW/Kereta', 'Masukkan nomor KKW atau nomor kereta sampel, contoh: KKW 325.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(7, 1, 7, 'Start', 'Tekan tombol start untuk memulai pengujian.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(8, 1, 8, 'Nyalakan api', 'Nyalakan sumber api pemantik pada alat.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(9, 1, 9, 'Tunggu ±6 menit', 'Tunggu proses uji berjalan selama kurang lebih 6 menit sampai alat berbunyi.', 'Waktu ±6 menit', 'Alarm bunyi + layar hijau', '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(10, 2, 1, 'Zero alat', 'Lakukan kalibrasi/zeroing pada alat colorimeter sebelum pengujian.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(11, 2, 2, 'Isi kuvet', 'Masukkan sampel ke dalam kuvet sebanyak 3/4 bagian.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(12, 2, 3, 'Lap bagian bening', 'Lap bagian bening kuvet agar bisa terbaca dengan baik oleh sensor.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(13, 2, 4, 'Masukkan ke alat', 'Masukkan kuvet ke alat dengan bagian bening diarahkan ke lubang sensor.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(14, 2, 5, 'Read', 'Tekan tombol read untuk membaca hasil warna.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(15, 2, 6, 'Input nama sampel', 'Masukkan nama sampel lalu tekan enter untuk menyimpan hasil.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(16, 3, 1, 'Masukkan sampel', 'Masukkan sampel ke wadah sesuai batas takar yang ditentukan.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(17, 3, 2, 'Beri nama sampel', 'Beri label/nama sampel pada wadah atau input sistem.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(18, 3, 3, 'Next / mulai uji', 'Lanjutkan ke proses pengujian pada alat.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(19, 4, 1, 'Siapkan sampel', 'Siapkan sampel sesuai prosedur pengambilan sampel standar.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(20, 4, 2, 'Jalankan pengujian', 'Jalankan pengujian kadar air pada alat sesuai SOP alat yang digunakan.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(21, 4, 3, 'Catat hasil', 'Catat hasil pembacaan kadar air (%vol atau ppm).', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(22, 5, 1, 'Siapkan sampel', 'Siapkan sampel dan reagen titrasi sesuai standar pengujian TAN.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(23, 5, 2, 'Lakukan titrasi', 'Lakukan proses titrasi hingga titik ekivalen tercapai.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(24, 5, 3, 'Catat nilai TAN', 'Catat nilai TAN dalam satuan mg KOH/g sampel.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(25, 6, 1, 'Siapkan sampel', 'Masukkan sampel ke labu destilasi sesuai takaran standar.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(26, 6, 2, 'Jalankan proses destilasi', 'Panaskan sampel dan catat suhu awal serta suhu akhir penguapan.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(27, 6, 3, 'Periksa kontaminan', 'Periksa residu/endapan untuk mengidentifikasi indikasi kontaminan.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(28, 7, 1, 'Siapkan gelas ukur', 'Tuang sampel ke dalam gelas ukur density sesuai batas takar.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(29, 7, 2, 'Masukkan hidrometer', 'Masukkan hidrometer ke dalam sampel secara perlahan, hindari gelembung udara.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(30, 7, 3, 'Baca thermometer', 'Baca suhu sampel menggunakan thermometer yang terpasang.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(31, 7, 4, 'Catat hasil density', 'Catat nilai density (kg/m3) dan suhu pengukuran, lalu lakukan koreksi suhu jika diperlukan.', NULL, NULL, '2026-09-04 10:55:55', '2026-09-04 10:55:55'),
(32, 8, 1, 'Panaskan mesin uji', 'Nyalakan & panaskan CFR Engine (mesin uji oktan) sampai kondisi operasi stabil sesuai SOP alat.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(33, 8, 2, 'Masukkan sampel', 'Isi tangki bahan bakar mesin uji dengan sampel bensin sesuai takaran yang ditentukan.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(34, 8, 3, 'Atur rasio kompresi', 'Atur rasio kompresi mesin uji sampai terjadi ketukan (knocking) standar sesuai metode ASTM D2699.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(35, 8, 4, 'Baca & catat RON', 'Baca nilai RON pada alat/knock meter, lalu catat nomor KKW dan hasilnya di sistem.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(36, 9, 1, 'Siapkan sampel', 'Siapkan sampel sesuai prosedur pengambilan sampel standar, pastikan wadah bersih dan kering.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(37, 9, 2, 'Kalibrasi alat', 'Kalibrasi Sulfur Analyzer (X-ray fluorescence) sesuai SOP alat sebelum pengujian.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(38, 9, 3, 'Jalankan pengujian', 'Masukkan sampel ke sel uji lalu jalankan pengujian sesuai metode ASTM D2622/D4294/D5453.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24'),
(39, 9, 4, 'Catat hasil', 'Catat hasil kandungan sulfur dalam satuan % m/m.', NULL, NULL, '2026-09-04 11:35:24', '2026-09-04 11:35:24');

-- Table structure for `hasil_ujis`
DROP TABLE IF EXISTS `hasil_ujis`;
CREATE TABLE `hasil_ujis` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `jenis_uji_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `nama_sampel` VARCHAR(191) NOT NULL,
  `nomor_kkw` VARCHAR(191) NULL,
  `data_hasil` TEXT NOT NULL,
  `catatan` TEXT NULL,
  `waktu_uji` DATETIME NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `foto_bukti` VARCHAR(191) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `hasil_ujis` (`id`, `jenis_uji_id`, `user_id`, `nama_sampel`, `nomor_kkw`, `data_hasil`, `catatan`, `waktu_uji`, `created_at`, `updated_at`, `foto_bukti`) VALUES
(7, 10, 2, 'Pertalite', NULL, '{"RON":"93","SULFUR":"0.04","DESTILASI_10":"2","DESTILASI_50":"79","DESTILASI_90":"2","DESTILASI_FBP":"3","DESTILASI_RESIDU":"1","DENSITY":"720","_uji_bbm":true,"_kategori":"gasoline","_jenis_key":"pertalite","_nama_lengkap":"Pertalite","_verdict":"PASS","_parameters":[{"parameter":"RON","value":93,"unit":"RON","min":90,"max":"-","status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"SULFUR","value":0.04,"unit":"% m\\/m","min":"-","max":0.05,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_10","value":2,"unit":"\\u00b0C","min":"-","max":74,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_50","value":79,"unit":"\\u00b0C","min":77,"max":125,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_90","value":2,"unit":"\\u00b0C","min":"-","max":180,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_FBP","value":3,"unit":"\\u00b0C","min":"-","max":215,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_RESIDU","value":1,"unit":"% vol","min":"-","max":2,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DENSITY","value":720,"unit":"kg\\/m\\u00b3","min":715,"max":770,"status":"PASS","message":"Memenuhi spesifikasi"}]}', 'Uji Kesesuaian Spesifikasi BBM (Gasoline) — diproses otomatis via menu Pengujian BBM Terpadu.', '2026-09-07 11:01:46', '2026-09-07 11:01:46', '2026-09-07 11:01:46', 'bukti-uji/mmVkps1EgH3fQyucLXt4C1V6KVKfdBh1wSLtBEIq.jpg'),
(8, 10, 2, 'Pertalite', '222', '{"RON":"90","SULFUR":"0.05","DESTILASI_10":"4","DESTILASI_50":"99","DESTILASI_90":"3","DESTILASI_FBP":"2","DESTILASI_RESIDU":"2","DENSITY":"715","_uji_bbm":true,"_kategori":"gasoline","_jenis_key":"pertalite","_nama_lengkap":"Pertalite","_verdict":"PASS","_parameters":[{"parameter":"RON","value":90,"unit":"RON","min":90,"max":"-","status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"SULFUR","value":0.05,"unit":"% m\\/m","min":"-","max":0.05,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_10","value":4,"unit":"\\u00b0C","min":"-","max":74,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_50","value":99,"unit":"\\u00b0C","min":77,"max":125,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_90","value":3,"unit":"\\u00b0C","min":"-","max":180,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_FBP","value":2,"unit":"\\u00b0C","min":"-","max":215,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_RESIDU","value":2,"unit":"% vol","min":"-","max":2,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DENSITY","value":715,"unit":"kg\\/m\\u00b3","min":715,"max":770,"status":"PASS","message":"Memenuhi spesifikasi"}]}', 'Uji Kesesuaian Spesifikasi BBM (Gasoline) — diproses otomatis via menu Pengujian BBM Terpadu.', '2026-09-07 11:35:47', '2026-09-07 11:35:47', '2026-09-07 11:35:47', NULL),
(9, 10, 2, 'Pertalite', NULL, '{"RON":"56","SULFUR":"65","DESTILASI_10":"5","DESTILASI_50":"4","DESTILASI_90":"5","DESTILASI_FBP":"3","DESTILASI_RESIDU":"3","DENSITY":"5","_uji_bbm":true,"_kategori":"gasoline","_jenis_key":"pertalite","_nama_lengkap":"Pertalite","_verdict":"FAIL","_parameters":[{"parameter":"RON","value":56,"unit":"RON","min":90,"max":"-","status":"FAIL","message":"Di bawah batas minimum (90)"},{"parameter":"SULFUR","value":65,"unit":"% m\\/m","min":"-","max":0.05,"status":"FAIL","message":"Di atas batas maksimum (0.05)"},{"parameter":"DESTILASI_10","value":5,"unit":"\\u00b0C","min":"-","max":74,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_50","value":4,"unit":"\\u00b0C","min":77,"max":125,"status":"FAIL","message":"Di bawah batas minimum (77)"},{"parameter":"DESTILASI_90","value":5,"unit":"\\u00b0C","min":"-","max":180,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_FBP","value":3,"unit":"\\u00b0C","min":"-","max":215,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_RESIDU","value":3,"unit":"% vol","min":"-","max":2,"status":"FAIL","message":"Di atas batas maksimum (2)"},{"parameter":"DENSITY","value":5,"unit":"kg\\/m\\u00b3","min":715,"max":770,"status":"FAIL","message":"Di bawah batas minimum (715)"}]}', 'Uji Kesesuaian Spesifikasi BBM (Gasoline) — diproses otomatis via menu Pengujian BBM Terpadu.', '2026-09-07 11:58:11', '2026-09-07 11:58:11', '2026-09-07 11:58:11', NULL),
(10, 10, 1, 'Pertamax', 'kk4', '{"RON":"92","SULFUR":"0.04","DESTILASI_10":"4","DESTILASI_50":"75","DESTILASI_90":"130","DESTILASI_FBP":"2","DESTILASI_RESIDU":"1","DENSITY":"720","_uji_bbm":true,"_kategori":"gasoline","_jenis_key":"pertamax","_nama_lengkap":"Pertamax","_verdict":"PASS","_parameters":[{"parameter":"RON","value":92,"unit":"RON","min":92,"max":"-","status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"SULFUR","value":0.04,"unit":"% m\\/m","min":"-","max":0.04,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_10","value":4,"unit":"\\u00b0C","min":"-","max":70,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_50","value":75,"unit":"\\u00b0C","min":75,"max":125,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_90","value":130,"unit":"\\u00b0C","min":130,"max":180,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_FBP","value":2,"unit":"\\u00b0C","min":"-","max":215,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_RESIDU","value":1,"unit":"% vol","min":"-","max":2,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DENSITY","value":720,"unit":"kg\\/m\\u00b3","min":715,"max":770,"status":"PASS","message":"Memenuhi spesifikasi"}]}', 'Uji Kesesuaian Spesifikasi BBM (Gasoline) — diproses otomatis via menu Pengujian BBM Terpadu.', '2026-09-08 08:42:20', '2026-09-08 08:42:20', '2026-09-08 08:42:20', 'bukti-uji/GqMV291Mc90uyAUH7RhjtR7GP5hLoOVvHl2CJHXv.jpg'),
(11, 10, 1, 'Dexlite', 'KKW67', '{"FLASHPOINT":"52","COLORIMETER":"2","VISKOSITAS":"3","WATER_CONTENT":"4","TAN":"0.5","DESTILASI_10":"1","DESTILASI_50":"1","DESTILASI_90":"1","DESTILASI_FBP":"24","DESTILASI_RESIDU":"1","DENSITY":"816","SULFUR":"0.11","_uji_bbm":true,"_kategori":"gasoil","_jenis_key":"dexlite_b40","_nama_lengkap":"Dexlite (B40)","_verdict":"PASS","_parameters":[{"parameter":"FLASHPOINT","value":52,"unit":"\\u00b0C","min":52,"max":"-","status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"COLORIMETER","value":2,"unit":"No. ASTM","min":"-","max":3,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"VISKOSITAS","value":3,"unit":"mm\\u00b2\\/s","min":2,"max":5,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"WATER_CONTENT","value":4,"unit":"mg\\/kg","min":"-","max":380,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"TAN","value":0.5,"unit":"mg KOH\\/g","min":"-","max":0.6,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_90","value":1,"unit":"\\u00b0C","min":"-","max":370,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DENSITY","value":816,"unit":"kg\\/m\\u00b3","min":815,"max":880,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"SULFUR","value":0.11,"unit":"% m\\/m","min":"-","max":0.12,"status":"PASS","message":"Memenuhi spesifikasi"}]}', 'Uji Kesesuaian Spesifikasi BBM (Gasoil) — diproses otomatis via menu Pengujian BBM Terpadu.', '2026-09-09 14:05:51', '2026-09-09 14:05:51', '2026-09-09 14:05:51', 'bukti-uji/v0a7P26fWJvVGxqKMs4iGy9lH9ZwlCQsPEninqKX.png'),
(12, 10, 1, 'Pertalite', NULL, '{"RON":"4","SULFUR":"9","DESTILASI_10":"o","DESTILASI_50":"[","DESTILASI_90":"[","DESTILASI_FBP":"0","DESTILASI_RESIDU":"0","DENSITY":"0","_uji_bbm":true,"_kategori":"gasoline","_jenis_key":"pertalite","_nama_lengkap":"Pertalite","_verdict":"FAIL","_parameters":[{"parameter":"RON","value":4,"unit":"RON","min":90,"max":"-","status":"FAIL","message":"Di bawah batas minimum (90)"},{"parameter":"SULFUR","value":9,"unit":"% m\\/m","min":"-","max":0.05,"status":"FAIL","message":"Di atas batas maksimum (0.05)"},{"parameter":"DESTILASI_10","value":0,"unit":"\\u00b0C","min":"-","max":74,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_50","value":0,"unit":"\\u00b0C","min":77,"max":125,"status":"FAIL","message":"Di bawah batas minimum (77)"},{"parameter":"DESTILASI_90","value":0,"unit":"\\u00b0C","min":"-","max":180,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_FBP","value":0,"unit":"\\u00b0C","min":"-","max":215,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DESTILASI_RESIDU","value":0,"unit":"% vol","min":"-","max":2,"status":"PASS","message":"Memenuhi spesifikasi"},{"parameter":"DENSITY","value":0,"unit":"kg\\/m\\u00b3","min":715,"max":770,"status":"FAIL","message":"Di bawah batas minimum (715)"}]}', 'Uji Kesesuaian Spesifikasi BBM (Gasoline) — diproses otomatis via menu Pengujian BBM Terpadu.', '2026-09-14 12:50:24', '2026-09-14 12:50:24', '2026-09-14 12:50:24', NULL);

-- Table structure for `cache`
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(191) PRIMARY KEY,
  `value` TEXT NOT NULL,
  `expiration` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `cache_locks`
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(191) PRIMARY KEY,
  `owner` VARCHAR(191) NOT NULL,
  `expiration` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `checklist_mt_maos`
DROP TABLE IF EXISTS `checklist_mt_maos`;
CREATE TABLE `checklist_mt_maos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nomor_polisi` VARCHAR(191) NOT NULL,
  `pemilik` VARCHAR(191) NULL,
  `tanggal_exp` VARCHAR(191) NULL,
  `tanggal_periksa` DATE NOT NULL,
  `tera` TEXT NOT NULL,
  `results` TEXT NOT NULL,
  `notes` TEXT NOT NULL,
  `ket_tambahan` TEXT NULL,
  `status` VARCHAR(191) NOT NULL,
  `user_id` BIGINT NULL,
  `created_by` VARCHAR(191) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `checklist_mt_maos` (`id`, `nomor_polisi`, `pemilik`, `tanggal_exp`, `tanggal_periksa`, `tera`, `results`, `notes`, `ket_tambahan`, `status`, `user_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'AB 4532 RT', 'PT Suci', 'Januari 2023', '2026-09-10 00:00:00', '[{"komp":"1","tinggiTera":"33","tinggiAct":"2","selisih":"22","duduk":"5","volume":"24","ijkBaut":"2","a":"5","b":"4","c":"3","d":"1","e":"3","f":"33","g":"9"},{"komp":"2","tinggiTera":"13","tinggiAct":"3","selisih":"3","duduk":"1","volume":"22","ijkBaut":"3","a":null,"b":null,"c":null,"d":null,"e":null,"f":null,"g":null},{"komp":"3","tinggiTera":"1","tinggiAct":"2","selisih":"3","duduk":"4","volume":"56","ijkBaut":"6","a":"-","b":null,"c":null,"d":null,"e":null,"f":null,"g":null},{"komp":"4","tinggiTera":"3","tinggiAct":"3","selisih":"3","duduk":"3","volume":"3","ijkBaut":"33","a":null,"b":null,"c":null,"d":null,"e":null,"f":null,"g":null}]', '{"3-0":"ok","3-1":"ok","3-2":"ok","3-3":"ok","3-4":"ok","4":"ok","5":"ok","6":"ok","7":"ok","8-0":"ok","8-1":"ok","8-2":"ok","8-3":"ok","9":"ok","10-0":"ok","10-1":"ok","11":"ok","12":"ok","13":"ok","14":"ok","15":"ok","16":"ok","17":"ok"}', '{"3-0":null,"3-1":null,"3-2":null,"3-3":null,"3-4":null,"4":null,"5":null,"6":null,"7":null,"8-0":null,"8-1":null,"8-2":null,"8-3":null,"9":null,"10-0":null,"10-1":null,"11":null,"12":null,"13":null,"14":null,"15":null,"16":null,"17":null}', NULL, 'final', 1, 'Admin Lab QQ', '2026-09-10 10:26:00', '2026-09-10 10:26:00');

-- Table structure for `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `email_verified_at` DATETIME NULL,
  `password` VARCHAR(191) NOT NULL,
  `remember_token` VARCHAR(191) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `role` VARCHAR(191) NOT NULL,
  `jabatan` VARCHAR(191) NULL,
  `spbu_name` VARCHAR(191) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `jabatan`, `spbu_name`) VALUES
(1, 'Admin Lab QQ', 'admin@labqq.test', NULL, '$2y$12$rKeoMgDJDxo5y1XBJKA9turlZ1Mwxd.f0v/Onn9NGOonQo8qthUtG', 'SlEKvQf5H1LoJUnBD7ewPKnaNQ2Uoem6r1FXK53El4AEKf2Uh0WEKRPkkyZG', '2026-09-04 10:55:54', '2026-09-11 15:37:07', 'admin', 'Supervisor Lab QQ', NULL),
(2, 'Petugas QC & Lapangan', 'petugas@labqq.test', NULL, '$2y$12$fyeoGRUnGFua7ELhj1c5SuqzeMGHWL0eL1FkZf84Vng7O2AFfDRua', NULL, '2026-09-04 10:55:54', '2026-09-11 15:37:08', 'petugas', 'Analis Lab & Checklist MT', NULL),
(3, 'User Viewer (SPBU / Tamu)', 'viewer@labqq.test', NULL, '$2y$12$OWQNsXZ/rRcIUQFoCqGDpOyrLNldTRW6hzUMw0BhC5T0jYgm43bKK', NULL, '2026-09-11 15:37:09', '2026-09-11 15:37:09', 'spbu', 'Viewer (Hanya Lihat / Read-Only)', 'PT Suci'),
(4, 'PT Contoh Transportir', 'spbu@contoh.test', NULL, '$2y$12$bNaF76ptt32fp7.CJFyQiO4jMVVdy1lY.tXSImmkPDfKoxaY1klSm', NULL, '2026-09-11 15:37:10', '2026-09-11 15:37:10', 'spbu', 'Viewer SPBU/Transportir', 'PT Contoh Transportir');

-- Table structure for `retain_sampel_mts`
DROP TABLE IF EXISTS `retain_sampel_mts`;
CREATE TABLE `retain_sampel_mts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tanggal` DATE NOT NULL,
  `jam_label` VARCHAR(191) NOT NULL,
  `produk` VARCHAR(191) NOT NULL,
  `mt_nopol` VARCHAR(191) NULL,
  `tangki_timbun` VARCHAR(191) NULL,
  `density_obs` DOUBLE NOT NULL,
  `temperatur` DOUBLE NOT NULL,
  `density_15` DOUBLE NOT NULL,
  `user_id` BIGINT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `foto_path` VARCHAR(191) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `retain_sampel_mts` (`id`, `tanggal`, `jam_label`, `produk`, `mt_nopol`, `tangki_timbun`, `density_obs`, `temperatur`, `density_15`, `user_id`, `created_at`, `updated_at`, `foto_path`) VALUES
(1, '2026-09-10 00:00:00', '06:00', 'Pertalite', 'N 3762 0', '4', 5, 5, 5, 1, '2026-09-10 10:52:52', '2026-09-11 14:34:43', NULL),
(2, '2026-09-10 00:00:00', '12:00', 'Pertalite', 'N 3762 0', '12', 12, 2, 12, 1, '2026-09-10 11:23:19', '2026-09-11 14:34:43', NULL),
(3, '2026-09-10 00:00:00', '06:00', 'Pertalite', 'N 3762 0', '5', 725, 28, 736.871, 1, '2026-09-10 11:30:57', '2026-09-11 14:34:43', 'retain-sampel/JuUNpJJOSA4eGLbCMuw2LiLmHB2SGPpdVhQjwH0E.jpg'),
(4, '2026-09-10 00:00:00', '06:00', 'Pertamax', 'R 3762 0, R 7627 HD, B 7637 GH', '6', 728, 30, 741.6602, 1, '2026-09-10 11:32:53', '2026-09-11 14:39:27', NULL),
(5, '2026-09-10 00:00:00', '18:00', 'Pertadex', 'R 3881', '5', 734, 20, 738.5476, 1, '2026-09-10 11:37:56', '2026-09-11 14:34:43', NULL),
(6, '2026-09-09 00:00:00', '09:00', 'Pertalite', '14/PLT/IX/2026', 'Jalur pipa penerimaan', 0.733, 26, 0.743, NULL, '2026-09-10 13:32:04', '2026-09-10 13:32:04', NULL),
(7, '2026-09-09 00:00:00', '13:00', 'Pertalite', '15/PLT/IX/2026', 'TT10', 0.731, 26, 0.741, NULL, '2026-09-10 13:32:05', '2026-09-10 13:32:05', NULL),
(8, '2026-09-09 00:00:00', '00:00', 'Pertamax', '09/PMX/IX/2026', 'Jalur Pipa Penerimaan', 0.74, 27, 0.7508, NULL, '2026-09-10 13:32:05', '2026-09-10 13:32:05', NULL),
(9, '2026-09-09 00:00:00', '00:30', 'Solar', '11/SLR/IX/2026', 'TT02', 0.842, 26, 0.8498, NULL, '2026-09-10 13:32:05', '2026-09-10 13:32:05', NULL),
(10, '2026-09-09 00:00:00', '02:00', 'Solar', '12/SLR/IX/2026', 'TT01', 0.841, 25, 0.8481, NULL, '2026-09-10 13:32:05', '2026-09-10 13:32:05', NULL),
(11, '2026-09-09 00:00:00', '01:00', 'Biosolar B50', '09/B50/IX/2026', 'TT07', 0.852, 25, 0.8591, NULL, '2026-09-10 13:32:05', '2026-09-10 13:32:05', NULL),
(12, '2026-09-09 00:00:00', '02:00', 'Dexlite', '09/DXT/IX/2026', 'TT06', 0.852, 25, 0.8591, NULL, '2026-09-10 13:32:05', '2026-09-10 13:32:05', NULL),
(13, '2026-09-09 00:00:00', '08:00', 'FAME B40', 'L 8190', NULL, 0.864, 30, 0.8745, NULL, '2026-09-10 13:32:05', '2026-09-10 13:32:05', NULL),
(14, '2026-09-01 00:00:00', '08:00', 'Pertalite', '01/PLT/IX/2026', 'Jalur pipa penerimaan', 0.732, 25, 0.7411, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(15, '2026-09-01 00:00:00', '18:00', 'Pertalite', '02/PLT/IX/2026', 'TT10', 0.732, 25, 0.7411, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(16, '2026-09-02 00:00:00', '16:00', 'Pertalite', '03/PLT/IX/2026', 'Jalur pipa penerimaan', 0.727, 32, 0.7425, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(17, '2026-09-03 00:00:00', '16:00', 'Pertalite', '04/PLT/IX/2026', 'TT11', 0.733, 26, 0.743, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(18, '2026-09-03 00:00:00', '12:00', 'Pertalite', '05/PLT/IX/2026', 'Jalur pipa penerimaan', 0.725, 31, 0.7396, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(19, '2026-09-03 00:00:00', '21:45', 'Pertalite', '06/PLT/IX/2026', 'TT10', 0.73, 26, 0.74, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(20, '2026-09-04 00:00:00', '02:31', 'Pertalite', '07/PLT/IX/2026', 'Jalur pipa penerimaan', 0.726, 31, 0.7406, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(21, '2026-09-04 00:00:00', '13:45', 'Pertalite', '08/PLT/IX/2026', 'TT11', 0.733, 31, 0.7475, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(22, '2026-09-04 00:00:00', '19:00', 'Pertalite', '09/PLT/IX/2026', 'Jalur pipa penerimaan', 0.73, 29, 0.7427, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(23, '2026-09-05 00:00:00', '05:00', 'Pertalite', '10/PLT/IX/2026', 'TT10', 0.731, 25, 0.7401, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(24, '2026-09-05 00:00:00', '12:30', 'Pertalite', '11/PLT/IX/2026', 'Jalur pipa penerimaan', 0.734, 26, 0.744, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(25, '2026-09-05 00:00:00', '16:00', 'Pertalite', '12/PLT/IX/2026', 'TT10', 0.736, 26, 0.746, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(26, '2026-09-05 00:00:00', '21:15', 'Pertalite', '13/PLT/IX/2026', 'TT11', 0.734, 26, 0.744, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(27, '2026-09-07 00:00:00', '22:30', 'Pertalite', '14/PLT/IX/2026', 'Jalur pipa penerimaan', 0.734, 26, 0.744, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(28, '2026-09-07 00:00:00', '04:00', 'Pertalite', '15/PLT/IX/2026', 'TT10', 0.735, 24, 0.7432, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(29, '2026-09-07 00:00:00', '16:00', 'Pertalite', '16/PLT/IX/2026', 'Jalur pipa penerimaan', 0.727, 30, 0.7407, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(30, '2026-09-07 00:00:00', '21:00', 'Pertalite', '17/PLT/IX/2026', 'TT10', 0.73, 26, 0.74, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(31, '2026-09-09 00:00:00', '09:00', 'Pertalite', '14/PLT/IX/2026', 'Jalur pipa penerimaan', 0.733, 26, 0.743, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(32, '2026-09-09 00:00:00', '13:00', 'Pertalite', '15/PLT/IX/2026', 'TT10', 0.731, 26, 0.741, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(33, '2026-09-10 00:00:00', '10:00', 'Pertalite', '16/PLT/IX/2026', 'Jalur pipa penerimaan', 0.732, 26, 0.742, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(34, '2026-09-03 00:00:00', '03:00', 'Pertamax', '01/PMX/IX/2026', 'Jalur Pipa Penerimaan', 0.738, 25, 0.7471, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(35, '2026-09-03 00:00:00', '05:30', 'Pertamax', '02/PMX/IX/2026', 'TT05', 0.739, 26, 0.749, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(36, '2026-09-03 00:00:00', '11:55', 'Pertamax', '03/PMX/IX/2026', 'TT09', 0.741, 28, 0.7527, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(37, '2026-09-04 00:00:00', '13:55', 'Pertamax', '04/PMX/IX/2026', 'Jalur Pipa Penerimaan', 0.735, 31, 0.7495, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(38, '2026-09-04 00:00:00', '15:30', 'Pertamax', '05/PMX/IX/2026', 'TT09', 0.741, 28, 0.7527, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(39, '2026-09-04 00:00:00', '19:00', 'Pertamax', '06/PMX/IX/2026', 'TT05', 0.741, 29, 0.7536, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(40, '2026-09-06 00:00:00', '15:00', 'Pertamax', '07/PMX/IX/2026', 'Jalur Pipa Penerimaan', 0.74, 29, 0.7526, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(41, '2026-09-06 00:00:00', '18:00', 'Pertamax', '08/PMX/IX/2026', 'TT09', 0.743, 25, 0.752, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(42, '2026-09-10 00:00:00', '10:00', 'Pertamax', '10/PMX/IX/2026', 'TT05', 0.743, 25, 0.752, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(43, '2026-09-02 00:00:00', '14:00', 'Solar', '01/SLR/IX/2026', 'Jalur pipa penerimaan', 0.84, 30, 0.8506, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(44, '2026-09-02 00:00:00', '17:00', 'Solar', '02/SLR/IX/2026', 'TT01', 0.843, 28, 0.8522, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(45, '2026-09-03 00:00:00', '22:00', 'Solar', '03/SLR/IX/2026', 'Jalur pipa penerimaan', 0.843, 29, 0.8529, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(46, '2026-09-04 00:00:00', '02:30', 'Solar', '04/SLR/IX/2026', 'TT07', 0.845, 26, 0.8528, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(47, '2026-09-05 00:00:00', '11:00', 'Solar', '05/SLR/IX/2026', 'Jalur pipa penerimaan', 0.841, 27, 0.8495, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(48, '2026-09-06 00:00:00', '12:00', 'Solar', '06/SLR/IX/2026', 'TT02', 0.84, 28, 0.8492, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(49, '2026-09-07 00:00:00', '05:00', 'Solar', '07/SLR/IX/2026', 'Jalur pipa penerimaan', 0.837, 30, 0.8476, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(50, '2026-09-07 00:00:00', '08:00', 'Solar', '08/SLR/IX/2026', 'TT07', 0.84, 29, 0.8499, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(51, '2026-09-07 00:00:00', '14:00', 'Solar', '09/SLR/IX/2026', 'TT01', 0.839, 31, 0.8502, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(52, '2026-09-08 00:00:00', '13:00', 'Solar', '10/SLR/IX/2026', 'Jalur pipa penerimaan', 0.835, 32, 0.847, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(53, '2026-09-09 00:00:00', '00:30', 'Solar', '11/SLR/IX/2026', 'TT02', 0.842, 26, 0.8498, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(54, '2026-09-09 00:00:00', '02:00', 'Solar', '12/SLR/IX/2026', 'TT01', 0.841, 25, 0.8481, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(55, '2026-09-01 00:00:00', '01:00', 'Biosolar B50', '01/B50/IX/2026', 'TT07', 0.852, 25, 0.8591, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(56, '2026-09-02 00:00:00', '01:00', 'Biosolar B50', '02/B50/IX/2026', 'T01', 0.853, 26, 0.8608, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(57, '2026-09-03 00:00:00', '01:00', 'Biosolar B50', '03/B50/IX/2026', 'TT07', 0.852, 25, 0.8591, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(58, '2026-09-04 00:00:00', '01:00', 'Biosolar B50', '04/B50/IX/2026', 'TT02', 0.855, 25, 0.862, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(59, '2026-09-05 00:00:00', '01:00', 'Biosolar B50', '05/B50/IX/2026', 'TT07', 0.855, 25, 0.862, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(60, '2026-09-06 00:00:00', '01:00', 'Biosolar B50', '06/B50/IX/2026', 'T01', 0.854, 26, 0.8618, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(61, '2026-09-07 00:00:00', '01:00', 'Biosolar B50', '07/B50/IX/2026', 'TT02', 0.852, 26, 0.8598, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(62, '2026-09-08 00:00:00', '01:00', 'Biosolar B50', '08/B50/IX/2026', 'TT02', 0.852, 26, 0.8598, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(63, '2026-09-09 00:00:00', '01:00', 'Biosolar B50', '09/B50/IX/2026', 'TT07', 0.852, 25, 0.8591, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(64, '2026-09-10 00:00:00', '01:00', 'Biosolar B50', '10/B50/IX/2026', 'TT02', 0.852, 26, 0.8598, NULL, '2026-09-10 13:45:46', '2026-09-10 14:51:18', 'retain-sampel/9DeiPdFRFBtYrYDwn9eH7EzG5rmTtwpxMvyQOkI2.png'),
(65, '2026-09-01 00:00:00', '03:00', 'Dexlite', '01/DXT/IX/2026', 'TT06', 0.85, 25, 0.8571, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(66, '2026-09-02 00:00:00', '02:00', 'Dexlite', '02/DXT/IX/2026', 'TT06', 0.852, 26, 0.8598, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(67, '2026-09-03 00:00:00', '03:00', 'Dexlite', '03/DXT/IX/2026', 'TT06', 0.85, 25, 0.8571, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(68, '2026-09-04 00:00:00', '02:00', 'Dexlite', '04/DXT/IX/2026', 'TT06', 0.852, 25, 0.8591, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(69, '2026-09-05 00:00:00', '02:00', 'Dexlite', '05/DXT/IX/2026', 'TT06', 0.852, 25, 0.8591, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(70, '2026-09-06 00:00:00', '02:00', 'Dexlite', '06/DXT/IX/2026', 'TT06', 0.853, 26, 0.8608, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(71, '2026-09-07 00:00:00', '02:00', 'Dexlite', '07/DXT/IX/2026', 'TT06', 0.85, 26, 0.8578, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(72, '2026-09-08 00:00:00', '02:00', 'Dexlite', '08/DXT/IX/2026', 'TT06', 0.85, 26, 0.8578, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(73, '2026-09-09 00:00:00', '02:00', 'Dexlite', '09/DXT/IX/2026', 'TT06', 0.852, 25, 0.8591, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(74, '2026-09-10 00:00:00', '02:00', 'Dexlite', '10/DXT/IX/2026', 'TT06', 0.85, 26, 0.8578, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(75, '2026-09-07 00:00:00', '17:30', 'Pertadex', '01/DEX/VIII/2026', NULL, 0.816, 26, 0.824, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(76, '2026-09-01 00:00:00', '09:00', 'FAME B40', '01/FAME/IX/2026', 'W 8323  US', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(77, '2026-09-02 00:00:00', '08:30', 'FAME B40', '03/FAME/IX/2026', 'w 8285 u', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(78, '2026-09-03 00:00:00', '08:30', 'FAME B40', '05/FAME/IX/2026', 'L8822 UB', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(79, '2026-09-04 00:00:00', '09:00', 'FAME B40', '07/FAME/IX/2026', 'L 8910 UG', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(80, '2026-09-05 00:00:00', '09:00', 'FAME B40', '09/FAME/IX/2026', 'W 9985US', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(81, '2026-09-06 00:00:00', '09:00', 'FAME B40', '11/FAME/IX/2026', 'L 8511 UK', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(82, '2026-09-07 00:00:00', '09:00', 'FAME B40', '13/FAME/IX/2026', 'W 8163 UQ', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(83, '2026-09-08 00:00:00', '09:00', 'FAME B40', '15/FAME/IX/2026', 'L 8041 UK', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(84, '2026-09-09 00:00:00', '08:00', 'FAME B40', '17/FAME/IX/2026', 'L 8190', 0.864, 30, 0.8745, NULL, '2026-09-10 13:45:46', '2026-09-10 13:45:46', NULL),
(87, '2026-09-10 00:00:00', '06:00', 'Pertamax Turbo', 'B 3838 FR', '2', 3, 4, 3, 1, '2026-09-11 11:09:43', '2026-09-11 14:34:43', NULL),
(88, '2026-09-11 00:00:00', '06:00', 'Pertalite', 'N 3762 0', '5', 5, 30, 5, 1, '2026-09-11 12:19:54', '2026-09-11 14:34:43', NULL),
(89, '2026-09-11 00:00:00', '06:00', 'Pertamax', 'N 3762 0', '03', 0.742, 24, 0.7501, 1, '2026-09-11 14:25:07', '2026-09-11 15:18:07', NULL);

-- Table structure for `retain_sampel_fotos`
DROP TABLE IF EXISTS `retain_sampel_fotos`;
CREATE TABLE `retain_sampel_fotos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tanggal` DATE NOT NULL,
  `jam_label` VARCHAR(191) NOT NULL,
  `path` VARCHAR(191) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `retain_sampel_fotos` (`id`, `tanggal`, `jam_label`, `path`, `created_at`, `updated_at`) VALUES
(1, '2026-09-10 00:00:00', '06:00', 'retain-sampel/sesi/Pa3KfUUhw8XSHJ9YWPDmQWmFQIxsIEd2IchB04gq.png', '2026-09-11 10:26:16', '2026-09-11 11:03:45'),
(2, '2026-09-09 00:00:00', '06:00', 'retain-sampel/sesi/XWRCC1Q2ZaNDDeGuSd3wAaMpu6VIOj5Jpcl2COcN.jpg', '2026-09-11 10:51:35', '2026-09-11 10:51:35'),
(3, '2026-09-11 00:00:00', '06:00', 'retain-sampel/sesi/iG1SRrkSIg8jA0FfjGjR82AsSzxoh80qdxjRA3cR.png', '2026-09-11 12:19:40', '2026-09-11 13:50:12'),
(4, '2026-09-10 00:00:00', '06:00_visual_mt', 'retain-sampel/sesi/a981Ti9mJPqVyKQ4y0IhXdkG52MpXcnRLhYc8TKj.png', '2026-09-11 14:38:57', '2026-09-11 14:38:57'),
(5, '2026-09-10 00:00:00', '06:00_visual_tangki', 'retain-sampel/sesi/fIamSamoUW3BWBfBAdHIpq4TCNzY6YdEHHUEglsM.png', '2026-09-11 14:39:07', '2026-09-11 14:39:07');

SET FOREIGN_KEY_CHECKS=1;
