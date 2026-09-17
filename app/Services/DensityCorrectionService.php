<?php

namespace App\Services;

/**
 * Menghitung Density'15 (density terkoreksi ke 15°C) SECARA OTOMATIS dari
 * Density Obs (hasil baca hidrometer) + Suhu Obs sesuai acuan resmi
 * Tabel 53B (Generalized Products / ASTM D1250 / API 2540 / IP 200).
 *
 * Mengikuti nilai Tabel 53B:
 *   - Pertalite (0.7330 pada 26°C) -> 0.7418 (741.8 kg/m3)
 *   - Pertamax  (0.7430 pada 26°C) -> 0.7517 (751.7 kg/m3)
 *   - Biosolar  (0.8150 pada 26°C) -> 0.8225 (822.5 kg/m3)
 */
class DensityCorrectionService
{
    /**
     * Titik jangkar koefisien koreksi per °C berdasarkan Tabel 53B
     * [density_kg_m3, slope_per_degC]
     */
    protected static array $tabel53bAnchors = [
        [690.0, 0.8650],
        [700.0, 0.8500],
        [710.0, 0.8364],
        [720.0, 0.8182],
        [730.0, 0.8091],
        [733.0, 0.8000],
        [740.0, 0.7909],
        [743.0, 0.7909],
        [750.0, 0.7727],
        [759.0, 0.7636],
        [800.0, 0.7000],
        [810.0, 0.6909],
        [815.0, 0.6818],
        [820.0, 0.6818],
        [830.0, 0.6727],
        [839.0, 0.6636],
        [840.0, 0.6636],
        [850.0, 0.6545],
        [860.0, 0.6545],
        [869.0, 0.6455]
    ];

    /**
     * Hitung koefisien muai termal / faktor koreksi berdasarkan Tabel 53B
     */
    public static function hitungSlope53B(float $rho): float
    {
        $anchors = self::$tabel53bAnchors;
        $count = count($anchors);

        if ($rho <= $anchors[0][0]) {
            return $anchors[0][1];
        }
        if ($rho >= $anchors[$count - 1][0]) {
            return $anchors[$count - 1][1];
        }

        for ($i = 0; $i < $count - 1; $i++) {
            $r1 = $anchors[$i][0];
            $s1 = $anchors[$i][1];
            $r2 = $anchors[$i + 1][0];
            $s2 = $anchors[$i + 1][1];

            if ($rho >= $r1 && $rho <= $r2) {
                $frac = ($r2 > $r1) ? (($rho - $r1) / ($r2 - $r1)) : 0.0;
                return $s1 + $frac * ($s2 - $s1);
            }
        }

        return 0.75;
    }

    /**
     * @param float $densityObs Density hasil baca hidrometer (satuan g/mL mis. 0.7330 atau kg/m3 mis. 733.0)
     * @param float $suhuObsC   Suhu sampel saat dibaca (°C)
     * @return float Density'15 dibulatkan 4 desimal
     */
    public static function hitungDensity15(float $densityObs, float $suhuObsC): float
    {
        if ($densityObs <= 0.0001) {
            return 0.0;
        }

        // Deteksi apakah input dalam kg/m3 (> 100) atau g/mL (< 10)
        $isDalamKgM3 = $densityObs > 100.0;
        $rhoObs = $isDalamKgM3 ? $densityObs : ($densityObs * 1000.0);

        // Jika angka dummy di luar range wajar, kembalikan rounded
        if ($rhoObs < 100.0 || $rhoObs > 2000.0) {
            return round($densityObs, 4);
        }

        $dT = $suhuObsC - 15.0;

        if (abs($dT) < 0.00001) {
            return round($densityObs, 4);
        }

        // Hitung faktor koreksi Tabel 53B
        $slope = self::hitungSlope53B($rhoObs);
        $rho15 = $rhoObs + ($slope * $dT);

        return $isDalamKgM3 ? round($rho15, 4) : round($rho15 / 1000.0, 4);
    }
}

