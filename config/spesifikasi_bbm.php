<?php

return [
    'gasoline' => [
        'pertalite' => [
            'nama' => 'Pertalite',
            'tools' => ['RON', 'SULFUR', 'DESTILASI', 'DENSITY'],
            'specs' => [
                'RON' => ['min' => 90.0, 'max' => null, 'unit' => 'RON'],
                'SULFUR' => ['min' => null, 'max' => 0.05, 'unit' => '% m/m'],
                'DESTILASI_10' => ['min' => null, 'max' => 74, 'unit' => '°C'],
                'DESTILASI_50' => ['min' => 77, 'max' => 125, 'unit' => '°C'],
                'DESTILASI_90' => ['min' => null, 'max' => 180, 'unit' => '°C'],
                'DESTILASI_FBP' => ['min' => null, 'max' => 215, 'unit' => '°C'],
                'DESTILASI_RESIDU' => ['min' => null, 'max' => 2.0, 'unit' => '% vol'],
                'DENSITY' => ['min' => 715, 'max' => 770, 'unit' => 'kg/m³'],
            ],
        ],
        'pertamax' => [
            'nama' => 'Pertamax',
            'tools' => ['RON', 'SULFUR', 'DESTILASI', 'DENSITY'],
            'specs' => [
                'RON' => ['min' => 92.0, 'max' => null, 'unit' => 'RON'],
                'SULFUR' => ['min' => null, 'max' => 0.04, 'unit' => '% m/m'],
                'DESTILASI_10' => ['min' => null, 'max' => 70, 'unit' => '°C'],
                'DESTILASI_50' => ['min' => 75, 'max' => 125, 'unit' => '°C'],
                'DESTILASI_90' => ['min' => 130, 'max' => 180, 'unit' => '°C'],
                'DESTILASI_FBP' => ['min' => null, 'max' => 215, 'unit' => '°C'],
                'DESTILASI_RESIDU' => ['min' => null, 'max' => 2.0, 'unit' => '% vol'],
                'DENSITY' => ['min' => 715, 'max' => 770, 'unit' => 'kg/m³'],
            ],
        ],
        'pertamax_turbo' => [
            'nama' => 'Pertamax Turbo',
            'tools' => ['RON', 'SULFUR', 'DESTILASI', 'DENSITY'],
            'specs' => [
                'RON' => ['min' => 98.0, 'max' => null, 'unit' => 'RON'],
                'SULFUR' => ['min' => null, 'max' => 0.005, 'unit' => '% m/m'],
                'DESTILASI_10' => ['min' => null, 'max' => 70, 'unit' => '°C'],
                'DESTILASI_50' => ['min' => 75, 'max' => 125, 'unit' => '°C'],
                'DESTILASI_90' => ['min' => 130, 'max' => 180, 'unit' => '°C'],
                'DESTILASI_FBP' => ['min' => null, 'max' => 215, 'unit' => '°C'],
                'DESTILASI_RESIDU' => ['min' => null, 'max' => 2.0, 'unit' => '% vol'],
                'DENSITY' => ['min' => 715, 'max' => 770, 'unit' => 'kg/m³'],
            ],
        ],
    ],

    'gasoil' => [
        'biosolar_b40' => [
            'nama' => 'Biosolar B40',
            'tools' => ['FLASHPOINT', 'COLORIMETER', 'VISKOSITAS', 'WATER_CONTENT', 'TAN', 'DESTILASI', 'DENSITY', 'SULFUR'],
            'specs' => [
                'FLASHPOINT' => ['min' => 52, 'max' => null, 'unit' => '°C'],
                'COLORIMETER' => ['min' => null, 'max' => 3.0, 'unit' => 'No. ASTM'],
                'VISKOSITAS' => ['min' => 2.0, 'max' => 5.0, 'unit' => 'mm²/s'],
                'WATER_CONTENT' => ['min' => null, 'max' => 380, 'unit' => 'mg/kg'],
                'TAN' => ['min' => null, 'max' => 0.6, 'unit' => 'mg KOH/g'],
                'DESTILASI_90' => ['min' => null, 'max' => 370, 'unit' => '°C'],
                'DENSITY' => ['min' => 815, 'max' => 880, 'unit' => 'kg/m³'],
                'SULFUR' => ['min' => null, 'max' => 0.2, 'unit' => '% m/m'],
            ],
        ],
        'dexlite_b40' => [
            'nama' => 'Dexlite (B40)',
            'tools' => ['FLASHPOINT', 'COLORIMETER', 'VISKOSITAS', 'WATER_CONTENT', 'TAN', 'DESTILASI', 'DENSITY', 'SULFUR'],
            'specs' => [
                'FLASHPOINT' => ['min' => 52, 'max' => null, 'unit' => '°C'],
                'COLORIMETER' => ['min' => null, 'max' => 3.0, 'unit' => 'No. ASTM'],
                'VISKOSITAS' => ['min' => 2.0, 'max' => 5.0, 'unit' => 'mm²/s'],
                'WATER_CONTENT' => ['min' => null, 'max' => 380, 'unit' => 'mg/kg'],
                'TAN' => ['min' => null, 'max' => 0.6, 'unit' => 'mg KOH/g'],
                'DESTILASI_90' => ['min' => null, 'max' => 370, 'unit' => '°C'],
                'DENSITY' => ['min' => 815, 'max' => 880, 'unit' => 'kg/m³'],
                'SULFUR' => ['min' => null, 'max' => 0.12, 'unit' => '% m/m'],
            ],
        ],
        'solar_b0' => [
            'nama' => 'Solar B0',
            'tools' => ['FLASHPOINT', 'COLORIMETER', 'VISKOSITAS', 'WATER_CONTENT', 'TAN', 'DESTILASI', 'DENSITY', 'SULFUR'],
            'specs' => [
                'FLASHPOINT' => ['min' => 55, 'max' => null, 'unit' => '°C'],
                'COLORIMETER' => ['min' => null, 'max' => 1.0, 'unit' => 'No. ASTM'],
                'VISKOSITAS' => ['min' => 2.0, 'max' => 4.5, 'unit' => 'mm²/s'],
                'WATER_CONTENT' => ['min' => null, 'max' => 400, 'unit' => 'mg/kg'],
                'TAN' => ['min' => null, 'max' => 0.3, 'unit' => 'mg KOH/g'],
                'DESTILASI_90' => ['min' => null, 'max' => 370, 'unit' => '°C'],
                'DENSITY' => ['min' => 815, 'max' => 870, 'unit' => 'kg/m³'],
                'SULFUR' => ['min' => null, 'max' => 0.25, 'unit' => '% m/m'],
            ],
        ],
        'pertamina_dex_b0' => [
            'nama' => 'Pertamina Dex (B0)',
            'tools' => ['FLASHPOINT', 'COLORIMETER', 'VISKOSITAS', 'WATER_CONTENT', 'TAN', 'DESTILASI', 'DENSITY', 'SULFUR'],
            'specs' => [
                'FLASHPOINT' => ['min' => 55, 'max' => null, 'unit' => '°C'],
                'COLORIMETER' => ['min' => null, 'max' => 1.0, 'unit' => 'No. ASTM'],
                'VISKOSITAS' => ['min' => 2.0, 'max' => 4.5, 'unit' => 'mm²/s'],
                'WATER_CONTENT' => ['min' => null, 'max' => 280, 'unit' => 'mg/kg'],
                'TAN' => ['min' => null, 'max' => 0.3, 'unit' => 'mg KOH/g'],
                'DESTILASI_90' => ['min' => null, 'max' => 370, 'unit' => '°C'],
                'DENSITY' => ['min' => 810, 'max' => 850, 'unit' => 'kg/m³'],
                'SULFUR' => ['min' => null, 'max' => 0.005, 'unit' => '% m/m'],
            ],
        ],
    ],
    
    // Konfigurasi field form untuk setiap tool yang akan dirender di frontend
    'tool_fields' => [
        'RON' => [
            'hasil' => ['label' => 'Hasil RON', 'type' => 'number', 'key' => 'RON']
        ],
        'SULFUR' => [
            'hasil' => ['label' => 'Kandungan Sulfur (% m/m)', 'type' => 'number', 'key' => 'SULFUR', 'step' => '0.001']
        ],
        'DENSITY' => [
            'hasil' => ['label' => 'Hasil Density @15°C (kg/m³)', 'type' => 'number', 'key' => 'DENSITY']
        ],
        'DESTILASI' => [
            'd10' => ['label' => '10% Vol Penguapan (°C)', 'type' => 'number', 'key' => 'DESTILASI_10'],
            'd50' => ['label' => '50% Vol Penguapan (°C)', 'type' => 'number', 'key' => 'DESTILASI_50'],
            'd90' => ['label' => '90% Vol Penguapan (°C)', 'type' => 'number', 'key' => 'DESTILASI_90'],
            'fbp' => ['label' => 'Titik Didih Akhir / FBP (°C)', 'type' => 'number', 'key' => 'DESTILASI_FBP'],
            'residu' => ['label' => 'Residu (% vol)', 'type' => 'number', 'key' => 'DESTILASI_RESIDU', 'step' => '0.1']
        ],
        'FLASHPOINT' => [
            'hasil' => ['label' => 'Titik Nyala / Flash Point (°C)', 'type' => 'number', 'key' => 'FLASHPOINT']
        ],
        'COLORIMETER' => [
            'hasil' => ['label' => 'Hasil Pembacaan Warna (No. ASTM)', 'type' => 'number', 'key' => 'COLORIMETER', 'step' => '0.1']
        ],
        'VISKOSITAS' => [
            'hasil' => ['label' => 'Viskositas @40°C (mm²/s)', 'type' => 'number', 'key' => 'VISKOSITAS', 'step' => '0.01']
        ],
        'WATER_CONTENT' => [
            'hasil' => ['label' => 'Kandungan Air (mg/kg)', 'type' => 'number', 'key' => 'WATER_CONTENT']
        ],
        'TAN' => [
            'hasil' => ['label' => 'Total Acid Number / TAN (mg KOH/g)', 'type' => 'number', 'key' => 'TAN', 'step' => '0.01']
        ]
    ],

    // Panduan langkah kerja (SOP ringkas) per alat, ditampilkan di atas kolom
    // input hasil saat petugas lab mengisi Uji Spesifikasi BBM. Murni panduan
    // operasional alat -- tidak memengaruhi perhitungan PASS/FAIL/MARGINAL.
    'tool_sop' => [
        'FLASHPOINT' => [
            'alat' => 'Flash Point Tester (Pensky-Martens)',
            'langkah' => [
                'Reset alat ke kondisi awal.',
                'Masukkan sampel sesuai batas takar cup.',
                'Masukkan cup ke dalam alat, lalu tutup dan tekan hingga terkunci.',
                'Atur suhu setting ke 60°C.',
                'Catat nomor KKW / kereta sampel (contoh: KKW 325) pada kolom identifikasi.',
                'Tekan Start untuk memulai pemanasan.',
                'Nyalakan api uji (test flame).',
                'Tunggu ± 6 menit sampai alat berbunyi dan layar berubah warna hijau, lalu catat suhu titik nyala.',
            ],
        ],
        'COLORIMETER' => [
            'alat' => 'Colorimeter (penentu warna minyak / gasoil)',
            'langkah' => [
                'Tekan Zero pada alat sebelum pembacaan.',
                'Masukkan sampel ke dalam kuvet sebanyak 3/4 bagian.',
                'Lap bagian bening kuvet agar dapat terbaca sensor dengan baik.',
                'Masukkan kuvet ke alat dengan bagian bening menghadap ke arah lubang sensor.',
                'Tekan Read untuk membaca warna.',
                'Ketik nama sampel lalu tekan Enter untuk menyimpan hasil.',
            ],
        ],
        'VISKOSITAS' => [
            'alat' => 'Viscometer',
            'langkah' => [
                'Masukkan sampel ke wadah sesuai batas takar.',
                'Beri nama sampel pada alat.',
                'Tekan Next untuk memulai pengukuran, lalu catat hasil viskositas @40°C.',
            ],
        ],
        'WATER_CONTENT' => [
            'alat' => 'Water Content Tester (Karl Fischer)',
            'langkah' => [
                'Pastikan alat pada kondisi siap ukur (kondisi nol/kalibrasi).',
                'Masukkan sampel sesuai volume/berat yang disyaratkan alat.',
                'Jalankan pengukuran dan catat hasil kandungan air (mg/kg).',
            ],
        ],
        'TAN' => [
            'alat' => 'Titrator (Bilangan Asam / TAN)',
            'langkah' => [
                'Siapkan sampel dan pelarut sesuai prosedur titrasi.',
                'Jalankan titrasi hingga titik akhir tercapai.',
                'Catat hasil Bilangan Asam Total (mg KOH/g).',
            ],
        ],
        'DESTILASI' => [
            'alat' => 'Distillation Tester',
            'langkah' => [
                'Masukkan sampel ke labu distilasi sesuai volume standar.',
                'Jalankan proses distilasi dan amati suhu pada tiap persentase penguapan.',
                'Catat suhu awal, suhu akhir, dan indikasi kontaminan (jika ada) untuk melihat kemurnian sampel.',
            ],
        ],
        'DENSITY' => [
            'alat' => 'Thermometer & Hidrometer',
            'langkah' => [
                'Tuang sampel ke gelas ukur density, hindari gelembung udara.',
                'Masukkan hidrometer secara perlahan, biarkan mengapung stabil.',
                'Baca skala density pada hidrometer sejajar mata, catat suhu pengukuran dari thermometer.',
            ],
        ],
        'RON' => [
            'alat' => 'RON Analyzer',
            'langkah' => [
                'Masukkan sampel ke alat uji oktan sesuai prosedur alat.',
                'Jalankan pengujian dan catat hasil RON (Research Octane Number).',
            ],
        ],
        'SULFUR' => [
            'alat' => 'Sulfur Analyzer',
            'langkah' => [
                'Masukkan sampel ke chamber alat uji sulfur.',
                'Jalankan pengujian dan catat kandungan sulfur (% m/m).',
            ],
        ],
    ],
];
