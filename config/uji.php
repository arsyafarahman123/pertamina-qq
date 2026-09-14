<?php

/**
 * Konfigurasi field dinamis untuk FORM HASIL UJI di setiap jenis pengujian.
 * Setiap field: key => [label, type, unit?, options?]
 * type: text | number | select
 */

return [

    'sampel_umum' => ['Solar', 'Biosolar', 'Pertamina Dex', 'Dexlite', 'Pertalite', 'Pertamax', 'Pertamax Turbo', 'FAME B40'],

    'fields' => [

        'FLASHPOINT' => [
            'suhu_setting' => ['label' => 'Suhu Setting', 'type' => 'text', 'default' => '60°C'],
            'waktu_uji_menit' => ['label' => 'Waktu Uji (menit)', 'type' => 'number', 'default' => 6],
            'hasil_flash_point' => ['label' => 'Hasil Flash Point (°C)', 'type' => 'number'],
        ],

        'COLORIMETER' => [
            'hasil_warna' => ['label' => 'Hasil Pembacaan Warna', 'type' => 'text'],
        ],

        'VISKOSITAS' => [
            'hasil_viskositas' => ['label' => 'Hasil Viskositas @40°C (mm2/s)', 'type' => 'number'],
        ],

        'WATER_CONTENT' => [
            'hasil_kadar_air' => ['label' => 'Kandungan Air (mg/kg)', 'type' => 'number'],
        ],

        'TAN' => [
            'hasil_tan' => ['label' => 'Bilangan Asam Total / TAN (mg KOH/g)', 'type' => 'number'],
        ],

        'DESTILASI' => [
            'suhu_awal' => ['label' => 'Suhu Awal Distilasi (°C)', 'type' => 'number'],
            'suhu_akhir' => ['label' => 'Suhu Akhir Distilasi (°C)', 'type' => 'number'],
            'catatan_kontaminan' => ['label' => 'Indikasi Kontaminan', 'type' => 'text'],
        ],

        'DENSITY' => [
            'hasil_density' => ['label' => 'Hasil Density (kg/m3)', 'type' => 'number'],
            'suhu_pengukuran' => ['label' => 'Suhu Pengukuran (°C)', 'type' => 'number'],
        ],

        'RON' => [
            'hasil_ron' => ['label' => 'Hasil RON (Research Octane Number)', 'type' => 'number'],
        ],

        'SULFUR' => [
            'hasil_sulfur' => ['label' => 'Kandungan Sulfur (% m/m)', 'type' => 'number'],
        ],
    ],

];
