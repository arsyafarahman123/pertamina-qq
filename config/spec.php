<?php

/**
 * Spesifikasi mutu BBM (batas on-spec) per produk sampel, per field hasil pengukuran.
 *
 * Sumber: dokumen resmi "Spesifikasi_Bahan_Bakar_Lengkap" (Pertalite, Pertamax,
 * Pertamax Turbo, Dexlite B40, Biosolar B40, Solar B0, Pertamina Dex B0, FAME B40)
 * -- SNI/ASTM. Hanya parameter yang memang tercakup oleh field form aplikasi ini
 * (Flash Point, Viskositas, Kadar Air, TAN/Bilangan Asam Total, Density, Distilasi,
 * Warna, RON, Sulfur) yang dipetakan ke sini. Parameter yang tidak ada di dokumen
 * untuk suatu produk (mis. Viskositas untuk bensin) SENGAJA tidak diberi entri di
 * sini -> dianggap informatif, tidak dinilai PASS/FAIL/MARGINAL, supaya tidak
 * menampilkan batas yang tidak benar-benar dispesifikasikan.
 *
 * Catatan Sulfur: pada dokumen sumber, beberapa produk (Pertamax, Dexlite, Biosolar,
 * Solar) punya nilai batas Sulfur bertingkat dengan catatan kaki angka (superscript)
 * untuk varian/wilayah tertentu (mis. "0,04; 0,035¹; 0,03²; 0,005³"), tapi teks
 * definisi catatan kaki tersebut tidak tercakup dalam dokumen yang diberikan. Supaya
 * tidak menebak, yang dipakai di sini adalah angka batas UTAMA/dasar (nilai pertama
 * yang tertulis di setiap sel), bukan varian bertanda superscript.
 *
 * Struktur: 'produk' => [ 'field_key' => ['label' => ..., 'satuan' => ..., 'min' => ..., 'max' => ...] ]
 * Field yang tidak memiliki entri akan dianggap "informatif" (tidak dinilai PASS/FAIL).
 */

return [

    'marginal' => 0.05, // toleransi ambang MARGINAL: +-5% dari batas

    'produk' => [

        // ---- Solar B0 ----
        'Solar' => [
            'hasil_flash_point' => ['label' => 'Titik Nyala (Flash Point)', 'satuan' => '°C', 'min' => 55],
            'hasil_viskositas' => ['label' => 'Viskositas (40°C)', 'satuan' => 'mm2/s', 'min' => 2.0, 'max' => 4.5],
            'hasil_kadar_air' => ['label' => 'Kandungan Air', 'satuan' => 'mg/kg', 'max' => 400],
            'hasil_tan' => ['label' => 'Bilangan Asam Total (TAN)', 'satuan' => 'mg KOH/g', 'max' => 0.3],
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 815, 'max' => 870],
            'suhu_akhir' => ['label' => 'Distilasi 90% Vol Penguapan', 'satuan' => '°C', 'max' => 370],
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur', 'satuan' => '% m/m', 'max' => 0.25],
        ],

        // ---- Biosolar B40 ----
        'Biosolar' => [
            'hasil_flash_point' => ['label' => 'Titik Nyala (Flash Point)', 'satuan' => '°C', 'min' => 52],
            'hasil_viskositas' => ['label' => 'Viskositas (40°C)', 'satuan' => 'mm2/s', 'min' => 2.0, 'max' => 5.0],
            'hasil_kadar_air' => ['label' => 'Kandungan Air', 'satuan' => 'mg/kg', 'min' => 0, 'max' => 380],
            'hasil_tan' => ['label' => 'Bilangan Asam Total (TAN)', 'satuan' => 'mg KOH/g', 'max' => 0.6],
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 815, 'max' => 880],
            'suhu_akhir' => ['label' => 'Distilasi 90% Vol Penguapan', 'satuan' => '°C', 'max' => 370],
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur', 'satuan' => '% m/m', 'max' => 0.2],
        ],

        // ---- Pertamina Dex B0 ----
        'Pertamina Dex' => [
            'hasil_flash_point' => ['label' => 'Titik Nyala (Flash Point)', 'satuan' => '°C', 'min' => 55],
            'hasil_viskositas' => ['label' => 'Viskositas (40°C)', 'satuan' => 'mm2/s', 'min' => 2.0, 'max' => 4.5],
            'hasil_kadar_air' => ['label' => 'Kandungan Air', 'satuan' => 'mg/kg', 'max' => 280],
            'hasil_tan' => ['label' => 'Bilangan Asam Total (TAN)', 'satuan' => 'mg KOH/g', 'max' => 0.3],
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 810, 'max' => 850],
            'suhu_akhir' => ['label' => 'Distilasi 90% Vol Penguapan', 'satuan' => '°C', 'max' => 370],
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur', 'satuan' => '% m/m', 'max' => 0.005],
        ],

        // ---- Dexlite B40 ----
        'Dexlite' => [
            'hasil_flash_point' => ['label' => 'Titik Nyala (Flash Point)', 'satuan' => '°C', 'min' => 52],
            'hasil_viskositas' => ['label' => 'Viskositas (40°C)', 'satuan' => 'mm2/s', 'min' => 2.0, 'max' => 5.0],
            'hasil_kadar_air' => ['label' => 'Kandungan Air', 'satuan' => 'mg/kg', 'max' => 380],
            'hasil_tan' => ['label' => 'Bilangan Asam Total (TAN)', 'satuan' => 'mg KOH/g', 'max' => 0.6],
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 815, 'max' => 880],
            'suhu_akhir' => ['label' => 'Distilasi 90% Vol Penguapan', 'satuan' => '°C', 'max' => 370],
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur', 'satuan' => '% m/m', 'max' => 0.12],
        ],

        // ---- BBM jenis bensin (Pertalite / Pertamax / Pertamax Turbo) ----
        // Flash Point, Viskositas & Kandungan Air tidak ada di dokumen spesifikasi resmi
        // untuk bensin -> sengaja tidak diberi entri (informatif, tidak dinilai PASS/FAIL).
        // suhu_awal dipetakan ke "10% Vol Penguapan" (batas MAX) dan suhu_akhir ke
        // "Titik Didih Akhir" (batas MAX), sesuai kolom Distilasi pada dokumen.

        'Pertalite' => [
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 715, 'max' => 770],
            'suhu_awal' => ['label' => 'Distilasi 10% Vol Penguapan', 'satuan' => '°C', 'max' => 74],
            'suhu_akhir' => ['label' => 'Titik Didih Akhir', 'satuan' => '°C', 'max' => 215],
            'hasil_warna' => ['label' => 'Warna (Visual)', 'satuan' => '', 'expected' => 'Hijau'],
            'hasil_ron' => ['label' => 'Bilangan Oktana Riset (RON)', 'satuan' => 'RON', 'min' => 90.0],
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur', 'satuan' => '% m/m', 'max' => 0.05],
        ],

        'Pertamax' => [
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 715, 'max' => 770],
            'suhu_awal' => ['label' => 'Distilasi 10% Vol Penguapan', 'satuan' => '°C', 'max' => 70],
            'suhu_akhir' => ['label' => 'Titik Didih Akhir', 'satuan' => '°C', 'max' => 215],
            'hasil_warna' => ['label' => 'Warna (Visual)', 'satuan' => '', 'expected' => 'Biru'],
            'hasil_ron' => ['label' => 'Bilangan Oktana Riset (RON)', 'satuan' => 'RON', 'min' => 92.0],
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur', 'satuan' => '% m/m', 'max' => 0.04],
        ],

        'Pertamax Turbo' => [
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 715, 'max' => 770],
            'suhu_awal' => ['label' => 'Distilasi 10% Vol Penguapan', 'satuan' => '°C', 'max' => 70],
            'suhu_akhir' => ['label' => 'Titik Didih Akhir', 'satuan' => '°C', 'max' => 215],
            'hasil_warna' => ['label' => 'Warna (Visual)', 'satuan' => '', 'expected' => 'Merah'],
            'hasil_ron' => ['label' => 'Bilangan Oktana Riset (RON)', 'satuan' => 'RON', 'min' => 98.0],
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur', 'satuan' => '% m/m', 'max' => 0.005],
        ],

        // Catatan: Solar/Biosolar/Dexlite/Pertamina Dex memang punya baris "Warna" di
        // dokumen, tapi diukur sebagai ANGKA No. ASTM (metode ASTM D1500), bukan nama
        // warna visual seperti bensin -- sedangkan field 'hasil_warna' pada form
        // Colorimeter aplikasi ini bertipe teks bebas. Supaya tidak salah menilai
        // (mis. user isi "kuning muda" dibandingkan ke angka "3,0"), parameter Warna
        // untuk produk diesel SENGAJA tetap dibiarkan informatif dan tidak dinilai
        // PASS/FAIL/MARGINAL di sini. RON juga tidak berlaku untuk produk diesel
        // (diesel memakai Angka Setana/Cetane, yang tidak dicatat oleh form aplikasi
        // ini) -> sengaja tidak diberi entri hasil_ron untuk produk diesel.

        // ---- FAME B40 (bahan baku pencampur biodiesel) ----
        // Hanya parameter FAME B40 yang memang tercakup oleh stasiun uji yang sudah
        // ada di aplikasi ini (Flash Point, Viskositas, Kadar Air, Angka Asam/TAN,
        // Density, Distilasi 90%) yang dipetakan. Parameter lain di dokumen (Kadar
        // Ester Metil, Gliserol Bebas/Total, Angka Iodium, Kestabilan Oksidasi
        // Rancimat/PetroOxy, Monogliserida, CFPP, Fosfor, Abu Tersulfatkan, Logam
        // Na+K, Total Kontaminan, dll) tidak punya stasiun uji yang sesuai di
        // aplikasi ini sehingga tidak dimasukkan -- supaya tidak salah menampilkan
        // batas untuk parameter yang tidak benar-benar diukur di sini. Nilai batas
        // "Logam II (Ca + Mg)" pada dokumen sumber tertutup pantulan/tidak terbaca,
        // sehingga TIDAK dimasukkan sama sekali (tidak ditebak).
        'FAME B40' => [
            'hasil_flash_point' => ['label' => 'Titik Nyala (Flash Point)', 'satuan' => '°C', 'min' => 150],
            'hasil_viskositas' => ['label' => 'Viskositas (40°C)', 'satuan' => 'mm2/s', 'min' => 2.3, 'max' => 6.0],
            'hasil_kadar_air' => ['label' => 'Kadar Air', 'satuan' => 'ppm', 'max' => 320],
            'hasil_tan' => ['label' => 'Angka Asam (Acid Value)', 'satuan' => 'mg KOH/g', 'max' => 0.4],
            'hasil_density' => ['label' => 'Berat Jenis (15°C)', 'satuan' => 'kg/m3', 'min' => 850, 'max' => 890],
            'suhu_akhir' => ['label' => 'Distilasi 90%', 'satuan' => '°C', 'max' => 360],
        ],

    ],

];
