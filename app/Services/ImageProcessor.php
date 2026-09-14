<?php

namespace App\Services;

/**
 * Merapikan foto bukti pengujian setelah di-upload:
 * - Auto-rotate sesuai data EXIF kamera HP (biar tidak miring/kesamping).
 * - Resize kalau sisi terpanjang kelewat besar (biar tidak berat & tidak blur
 *   saat di-scale ulang oleh html2canvas / browser saat print).
 * - Re-encode dengan kualitas yang wajar supaya file tetap tajam tapi ringkas.
 *
 * Dipanggil setelah file disimpan ke storage (path absolut di disk).
 */
class ImageProcessor
{
    /** Sisi terpanjang maksimum (px) setelah dirapikan. */
    private const MAX_DIMENSI = 1600;

    /** Kualitas re-encode untuk JPEG (0-100). */
    private const KUALITAS_JPEG = 88;

    public static function normalisasi(string $pathAbsolut): void
    {
        if (! extension_loaded('gd') || ! is_file($pathAbsolut)) {
            return;
        }

        $ekstensi = strtolower(pathinfo($pathAbsolut, PATHINFO_EXTENSION));
        if (! in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'gif'], true)) {
            return; // bukan gambar (pdf/doc/xls dll) — biarkan apa adanya
        }

        $info = @getimagesize($pathAbsolut);
        if (! $info) {
            return;
        }

        [$lebarAsli, $tinggiAsli, $tipe] = $info;

        $gambar = self::muatGambar($pathAbsolut, $tipe);
        if (! $gambar) {
            return;
        }

        // 1) Koreksi rotasi berdasarkan EXIF (khusus JPEG dari kamera HP).
        if ($tipe === IMAGETYPE_JPEG) {
            $gambar = self::koreksiOrientasi($gambar, $pathAbsolut);
            $lebarAsli = imagesx($gambar);
            $tinggiAsli = imagesy($gambar);
        }

        // 2) Resize proporsional kalau sisi terpanjang melebihi batas.
        $sisiTerpanjang = max($lebarAsli, $tinggiAsli);
        if ($sisiTerpanjang > self::MAX_DIMENSI) {
            $rasio = self::MAX_DIMENSI / $sisiTerpanjang;
            $lebarBaru = (int) round($lebarAsli * $rasio);
            $tinggiBaru = (int) round($tinggiAsli * $rasio);

            $gambarResize = imagecreatetruecolor($lebarBaru, $tinggiBaru);
            imagealphablending($gambarResize, false);
            imagesavealpha($gambarResize, true);
            $transparan = imagecolorallocatealpha($gambarResize, 0, 0, 0, 127);
            imagefilledrectangle($gambarResize, 0, 0, $lebarBaru, $tinggiBaru, $transparan);

            imagecopyresampled(
                $gambarResize, $gambar,
                0, 0, 0, 0,
                $lebarBaru, $tinggiBaru, $lebarAsli, $tinggiAsli
            );

            imagedestroy($gambar);
            $gambar = $gambarResize;
        }

        // 3) Simpan kembali ke path yang sama, format menyesuaikan tipe asal.
        switch ($tipe) {
            case IMAGETYPE_PNG:
                imagesavealpha($gambar, true);
                imagepng($gambar, $pathAbsolut, 6);
                break;
            case IMAGETYPE_GIF:
                imagegif($gambar, $pathAbsolut);
                break;
            case IMAGETYPE_BMP:
                imagejpeg($gambar, $pathAbsolut, self::KUALITAS_JPEG);
                break;
            default: // JPEG / WEBP dll -> simpan sebagai JPEG berkualitas baik
                imageinterlace($gambar, true); // progressive, render bertahap lebih halus
                imagejpeg($gambar, $pathAbsolut, self::KUALITAS_JPEG);
                break;
        }

        imagedestroy($gambar);
    }

    private static function muatGambar(string $path, int $tipe)
    {
        return match ($tipe) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            IMAGETYPE_BMP => function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($path) : false,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }

    /**
     * Baca tag EXIF Orientation dan putar/mirror gambar supaya tegak,
     * lalu hapus ketergantungan pada flag EXIF (karena html2canvas dan
     * sebagian browser tidak membacanya saat mengambil screenshot / print).
     */
    private static function koreksiOrientasi($gambar, string $path)
    {
        if (! function_exists('exif_read_data')) {
            return $gambar;
        }

        $exif = @exif_read_data($path);
        $orientasi = $exif['Orientation'] ?? 1;

        switch ($orientasi) {
            case 3: // 180°
                $gambar = imagerotate($gambar, 180, 0);
                break;
            case 6: // 90° CW dibutuhkan
                $gambar = imagerotate($gambar, -90, 0);
                break;
            case 8: // 90° CCW dibutuhkan
                $gambar = imagerotate($gambar, 90, 0);
                break;
            case 2:
                imageflip($gambar, IMG_FLIP_HORIZONTAL);
                break;
            case 4:
                imageflip($gambar, IMG_FLIP_VERTICAL);
                break;
            case 5:
                $gambar = imagerotate($gambar, -90, 0);
                imageflip($gambar, IMG_FLIP_HORIZONTAL);
                break;
            case 7:
                $gambar = imagerotate($gambar, 90, 0);
                imageflip($gambar, IMG_FLIP_HORIZONTAL);
                break;
            default:
                break; // 1 atau tidak ada tag = sudah tegak
        }

        return $gambar;
    }
}
