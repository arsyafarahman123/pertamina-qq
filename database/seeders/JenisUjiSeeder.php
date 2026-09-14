<?php

namespace Database\Seeders;

use App\Models\JenisUji;
use App\Models\LangkahSop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JenisUjiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode' => 'FLASHPOINT',
                'nama' => 'Flash Point Tester',
                'deskripsi' => 'Pengujian titik nyala pada Solar, Biosolar, Pertamina Dex, dan Dexlite.',
                'icon' => 'flame',
                'urutan' => 1,
                'langkah' => [
                    ['judul' => 'Reset alat', 'instruksi' => 'Reset alat Flash Point Tester ke kondisi awal sebelum digunakan.'],
                    ['judul' => 'Masukkan sampel', 'instruksi' => 'Masukkan sampel BBM sesuai batas takar yang ditentukan pada wadah uji.'],
                    ['judul' => 'Masukkan ke alat', 'instruksi' => 'Masukkan wadah berisi sampel ke dalam alat Flash Point Tester.'],
                    ['judul' => 'Tutup dan tekan', 'instruksi' => 'Tutup rapat alat lalu tekan penutup hingga terkunci.'],
                    ['judul' => 'Atur suhu 60°C', 'instruksi' => 'Atur setting suhu alat ke 60°C.', 'parameter' => 'Suhu 60°C'],
                    ['judul' => 'Input nomor KKW/Kereta', 'instruksi' => 'Masukkan nomor KKW atau nomor kereta sampel, contoh: KKW 325.'],
                    ['judul' => 'Start', 'instruksi' => 'Tekan tombol start untuk memulai pengujian.'],
                    ['judul' => 'Nyalakan api', 'instruksi' => 'Nyalakan sumber api pemantik pada alat.'],
                    ['judul' => 'Tunggu ±6 menit', 'instruksi' => 'Tunggu proses uji berjalan selama kurang lebih 6 menit sampai alat berbunyi.', 'parameter' => 'Waktu ±6 menit', 'indikator' => 'Alarm bunyi + layar hijau'],
                ],
            ],
            [
                'kode' => 'COLORIMETER',
                'nama' => 'Colorimeter',
                'deskripsi' => 'Menentukan warna minyak (Solar/Gasoil).',
                'icon' => 'palette',
                'urutan' => 2,
                'langkah' => [
                    ['judul' => 'Zero alat', 'instruksi' => 'Lakukan kalibrasi/zeroing pada alat colorimeter sebelum pengujian.'],
                    ['judul' => 'Isi kuvet', 'instruksi' => 'Masukkan sampel ke dalam kuvet sebanyak 3/4 bagian.'],
                    ['judul' => 'Lap bagian bening', 'instruksi' => 'Lap bagian bening kuvet agar bisa terbaca dengan baik oleh sensor.'],
                    ['judul' => 'Masukkan ke alat', 'instruksi' => 'Masukkan kuvet ke alat dengan bagian bening diarahkan ke lubang sensor.'],
                    ['judul' => 'Read', 'instruksi' => 'Tekan tombol read untuk membaca hasil warna.'],
                    ['judul' => 'Input nama sampel', 'instruksi' => 'Masukkan nama sampel lalu tekan enter untuk menyimpan hasil.'],
                ],
            ],
            [
                'kode' => 'VISKOSITAS',
                'nama' => 'Viskositas',
                'deskripsi' => 'Pengujian kekentalan (viskositas) sampel BBM.',
                'icon' => 'droplet',
                'urutan' => 3,
                'langkah' => [
                    ['judul' => 'Masukkan sampel', 'instruksi' => 'Masukkan sampel ke wadah sesuai batas takar yang ditentukan.'],
                    ['judul' => 'Beri nama sampel', 'instruksi' => 'Beri label/nama sampel pada wadah atau input sistem.'],
                    ['judul' => 'Next / mulai uji', 'instruksi' => 'Lanjutkan ke proses pengujian pada alat.'],
                ],
            ],
            [
                'kode' => 'WATER_CONTENT',
                'nama' => 'Water Content',
                'deskripsi' => 'Pengujian kadar air pada sampel BBM.',
                'icon' => 'droplets',
                'urutan' => 4,
                'langkah' => [
                    ['judul' => 'Siapkan sampel', 'instruksi' => 'Siapkan sampel sesuai prosedur pengambilan sampel standar.'],
                    ['judul' => 'Jalankan pengujian', 'instruksi' => 'Jalankan pengujian kadar air pada alat sesuai SOP alat yang digunakan.'],
                    ['judul' => 'Catat hasil', 'instruksi' => 'Catat hasil pembacaan kadar air (%vol atau ppm).'],
                ],
            ],
            [
                'kode' => 'TAN',
                'nama' => 'TAN (Total Acid Number)',
                'deskripsi' => 'Pengujian angka asam pada sampel BBM.',
                'icon' => 'flask-conical',
                'urutan' => 5,
                'langkah' => [
                    ['judul' => 'Siapkan sampel', 'instruksi' => 'Siapkan sampel dan reagen titrasi sesuai standar pengujian TAN.'],
                    ['judul' => 'Lakukan titrasi', 'instruksi' => 'Lakukan proses titrasi hingga titik ekivalen tercapai.'],
                    ['judul' => 'Catat nilai TAN', 'instruksi' => 'Catat nilai TAN dalam satuan mg KOH/g sampel.'],
                ],
            ],
            [
                'kode' => 'DESTILASI',
                'nama' => 'Destilasi',
                'deskripsi' => 'Melihat kemurnian sampel dan mendeteksi kontaminan.',
                'icon' => 'thermometer',
                'urutan' => 6,
                'langkah' => [
                    ['judul' => 'Siapkan sampel', 'instruksi' => 'Masukkan sampel ke labu destilasi sesuai takaran standar.'],
                    ['judul' => 'Jalankan proses destilasi', 'instruksi' => 'Panaskan sampel dan catat suhu awal serta suhu akhir penguapan.'],
                    ['judul' => 'Periksa kontaminan', 'instruksi' => 'Periksa residu/endapan untuk mengidentifikasi indikasi kontaminan.'],
                ],
            ],
            [
                'kode' => 'DENSITY',
                'nama' => 'Density',
                'deskripsi' => 'Pengujian densitas menggunakan thermometer & hidrometer.',
                'icon' => 'gauge',
                'urutan' => 7,
                'langkah' => [
                    ['judul' => 'Siapkan gelas ukur', 'instruksi' => 'Tuang sampel ke dalam gelas ukur density sesuai batas takar.'],
                    ['judul' => 'Masukkan hidrometer', 'instruksi' => 'Masukkan hidrometer ke dalam sampel secara perlahan, hindari gelembung udara.'],
                    ['judul' => 'Baca thermometer', 'instruksi' => 'Baca suhu sampel menggunakan thermometer yang terpasang.'],
                    ['judul' => 'Catat hasil density', 'instruksi' => 'Catat nilai density (kg/m3) dan suhu pengukuran, lalu lakukan koreksi suhu jika diperlukan.'],
                ],
            ],
            [
                'kode' => 'RON',
                'nama' => 'RON (Research Octane Number)',
                'deskripsi' => 'Pengujian bilangan oktana riset pada bensin (Pertalite, Pertamax, Pertamax Turbo).',
                'icon' => 'zap',
                'urutan' => 8,
                'langkah' => [
                    ['judul' => 'Panaskan mesin uji', 'instruksi' => 'Nyalakan & panaskan CFR Engine (mesin uji oktan) sampai kondisi operasi stabil sesuai SOP alat.'],
                    ['judul' => 'Masukkan sampel', 'instruksi' => 'Isi tangki bahan bakar mesin uji dengan sampel bensin sesuai takaran yang ditentukan.'],
                    ['judul' => 'Atur rasio kompresi', 'instruksi' => 'Atur rasio kompresi mesin uji sampai terjadi ketukan (knocking) standar sesuai metode ASTM D2699.'],
                    ['judul' => 'Baca & catat RON', 'instruksi' => 'Baca nilai RON pada alat/knock meter, lalu catat nomor KKW dan hasilnya di sistem.'],
                ],
            ],
            [
                'kode' => 'SULFUR',
                'nama' => 'Kandungan Sulfur',
                'deskripsi' => 'Pengujian kandungan sulfur pada seluruh jenis BBM (bensin & diesel).',
                'icon' => 'flask-round',
                'urutan' => 9,
                'langkah' => [
                    ['judul' => 'Siapkan sampel', 'instruksi' => 'Siapkan sampel sesuai prosedur pengambilan sampel standar, pastikan wadah bersih dan kering.'],
                    ['judul' => 'Kalibrasi alat', 'instruksi' => 'Kalibrasi Sulfur Analyzer (X-ray fluorescence) sesuai SOP alat sebelum pengujian.'],
                    ['judul' => 'Jalankan pengujian', 'instruksi' => 'Masukkan sampel ke sel uji lalu jalankan pengujian sesuai metode ASTM D2622/D4294/D5453.'],
                    ['judul' => 'Catat hasil', 'instruksi' => 'Catat hasil kandungan sulfur dalam satuan % m/m.'],
                ],
            ],
        ];

        foreach ($data as $item) {
            $langkah = $item['langkah'];
            unset($item['langkah']);
            $item['slug'] = Str::slug($item['nama']);
            $item['aktif'] = true;

            $jenisUji = JenisUji::updateOrCreate(['kode' => $item['kode']], $item);

            foreach ($langkah as $i => $step) {
                LangkahSop::updateOrCreate(
                    ['jenis_uji_id' => $jenisUji->id, 'urutan' => $i + 1],
                    [
                        'judul_singkat' => $step['judul'],
                        'instruksi' => $step['instruksi'],
                        'parameter_setting' => $step['parameter'] ?? null,
                        'indikator_selesai' => $step['indikator'] ?? null,
                    ]
                );
            }
        }
    }
}
