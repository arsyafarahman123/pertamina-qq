<?php

namespace App\Support;

/**
 * Struktur baku "Form Pemeriksaan Mobil Tangki — Fuel Terminal Maos".
 * Meniru persis form kertas item 3-17 (item 1-2 "Masa Sertifikat Tera" ditangani
 * terpisah sebagai blok 4 kompartemen — lihat ChecklistMtMaos::blankTera()).
 */
class ChecklistMtMaosItems
{
    public static function all(): array
    {
        return [
            [
                'no' => '3', 'item' => 'Manhole', 'group' => true,
                'sub' => [
                    ['label' => 'Packing', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak adanya rembesan dan kebocoran.'],
                    ['label' => 'Las Titik Flange', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik.'],
                    ['label' => 'Las Titik Engsel', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik.'],
                    ['label' => 'Palang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las palang dalam kondisi baik.'],
                    ['label' => 'Kebersihan', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan kebersihan area manhole (tidak hitam).'],
                ],
            ],
            ['no' => '4', 'item' => 'T2 Coaming', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan angka T2 sesuai Tera.'],
            ['no' => '5', 'item' => 'Tanda Sah Lemping Volume Nominal', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan lemping sesuai dan kondisi segel.'],
            ['no' => '6', 'item' => 'Saluran Buangan Air', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan tidak tersumbat aliran air.'],
            ['no' => '7', 'item' => 'Pengecekan Kompartemen Dalam', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan kebersihan dalam tangki.'],
            [
                'no' => '8', 'item' => 'Bracket', 'group' => true,
                'sub' => [
                    ['label' => 'Keefektifan bracket', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan bracket berfungsi dengan baik (digoyangkan).'],
                    ['label' => 'Las Titik Engsel', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan las titik dalam kondisi baik.'],
                    ['label' => 'Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak ada ganjalan pada Pen.'],
                    ['label' => 'Pengikat Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan pengikat tidak mudah lepas.'],
                ],
            ],
            ['no' => '9', 'item' => 'Seal bottom Loader', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan seal yang digunakan telah standard. Menahan Handle & Bracket (Mayor).'],
            [
                'no' => '10', 'item' => 'Sight Glass', 'group' => true,
                'sub' => [
                    ['label' => 'Kebersihan', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Memastikan fungsi dari sight glass.'],
                    ['label' => 'Las titik', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik.'],
                ],
            ],
            ['no' => '11', 'item' => 'Indikator Produk', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Memastikan indikator produk tersedia.'],
            ['no' => '12', 'item' => 'Copy Sertifikat Tera Terpasang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan copy sertifikat tera terpasang pada box.'],
            ['no' => '13', 'item' => 'Kebersihan area bottom loading', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan kebersihan area bottom Loading.'],
            ['no' => '14', 'item' => 'Las Titik Foot Valve', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan las titik dalam kondisi baik.'],
            ['no' => '15', 'item' => 'Selang Bongkar', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Tersedianya selang bongkar 3" & 4".'],
            ['no' => '16', 'item' => 'Drainase Rumah Selang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan drainase rumah selang berfungsi.'],
            ['no' => '17', 'item' => 'Tanda Jaminan Pengikat TUM & Chassis', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan keadaan segel jaminan di chasis.'],
        ];
    }

    /** Ratakan jadi list [key, label, temuan, disp, ket] untuk loop export/detail. */
    public static function flat(): array
    {
        $out = [];
        foreach (self::all() as $sec) {
            if (!empty($sec['group'])) {
                foreach ($sec['sub'] as $i => $s) {
                    $out[] = [
                        'key' => $sec['no'] . '-' . $i,
                        'no' => $sec['no'] . '.' . ($i + 1),
                        'label' => $sec['item'] . ' - ' . $s['label'],
                        'temuan' => $s['temuan'],
                        'disp' => $s['disp'],
                        'ket' => $s['ket'],
                    ];
                }
            } else {
                $out[] = [
                    'key' => $sec['no'],
                    'no' => $sec['no'],
                    'label' => $sec['item'],
                    'temuan' => $sec['temuan'],
                    'disp' => $sec['disp'],
                    'ket' => $sec['ket'],
                ];
            }
        }
        return $out;
    }
}
