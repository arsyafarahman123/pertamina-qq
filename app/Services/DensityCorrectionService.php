<?php

namespace App\Services;

/**
 * Menghitung Density'15 (density terkoreksi ke 15°C) SECARA OTOMATIS dari
 * Density Obs (hasil baca hidrometer) + Suhu Obs sesuai standar resmi
 * ASTM D1250 / API 2540 / IP 200 (1980) Table 53B (Generalized Products).
 *
 * Rumus:
 *   Δt = t - 15
 *   α15 = K0 / (ρ15^2) + K1 / ρ15
 *   ρ15 = ρt * exp[ α15 * Δt * (1 + 0.8 * α15 * Δt) ]
 *
 * Zona Transisi (770 <= ρ15 < 778 kg/m3):
 *   α15 = A + B / (ρ15^2), dengan A = -0.00336312, B = 2680.3206
 *
 * Konstanta K0 dan K1:
 *   - 653 <= ρ15 < 770 : K0 = 346.4228, K1 = 0.4388  (Gasoline / Naphtha)
 *   - 770 <= ρ15 < 778 : Zona transisi (A & B)
 *   - 778 <= ρ15 < 839 : K0 = 594.5418, K1 = 0.0     (Kerosene / Jet Fuel)
 *   - 839 <= ρ15 <= 1075 : K0 = 186.9696, K1 = 0.48618 (Solar / Gas Oil)
 */
class DensityCorrectionService
{
    /**
     * Hitung koefisien muai termal α15 berdasarkan nilai perkiraan / iterasi ρ15.
     */
    public static function hitungAlpha15(float $rho15): float
    {
        if ($rho15 <= 0.0001) {
            return 0.0;
        }

        if ($rho15 < 770.0) {
            // 653 <= ρ15 < 770 (Gasoline / Naphtha)
            return (346.4228 / ($rho15 ** 2)) + (0.4388 / $rho15);
        } elseif ($rho15 < 778.0) {
            // 770 <= ρ15 < 778 (Zona Transisi)
            return -0.00336312 + (2680.3206 / ($rho15 ** 2));
        } elseif ($rho15 < 839.0) {
            // 778 <= ρ15 < 839 (Kerosene / Jet Fuel)
            return (594.5418 / ($rho15 ** 2));
        } else {
            // 839 <= ρ15 <= 1075 (Solar / Gas Oil)
            return (186.9696 / ($rho15 ** 2)) + (0.48618 / $rho15);
        }
    }

    /**
     * @param float $densityObs Density hasil baca hidrometer (satuan g/mL mis. 0.7340 atau kg/m3 mis. 734.0)
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

        // Jika angka dummy di luar range BBM wajar (< 100 atau > 2000), kembalikan rounded
        if ($rhoObs < 100.0 || $rhoObs > 2000.0) {
            return round($densityObs, 4);
        }

        $dT = $suhuObsC - 15.0;

        // Jika suhu tepat 15°C, tidak ada koreksi suhu
        if (abs($dT) < 0.00001) {
            return round($densityObs, 4);
        }

        // Iterasi: tebakan awal ρ15 ≈ ρt
        $rho15 = $rhoObs;

        for ($i = 0; $i < 15; $i++) {
            $alpha15 = self::hitungAlpha15($rho15);
            
            // Rumus utama: ρ15 = ρt * exp[ α15 * Δt * (1 + 0.8 * α15 * Δt) ]
            $exponent = $alpha15 * $dT * (1.0 + 0.8 * $alpha15 * $dT);
            $rho15Baru = $rhoObs * exp($exponent);

            if (!is_finite($rho15Baru) || $rho15Baru <= 0.0001) {
                break;
            }

            if (abs($rho15Baru - $rho15) < 0.00001) {
                $rho15 = $rho15Baru;
                break;
            }

            $rho15 = $rho15Baru;
        }

        return $isDalamKgM3 ? round($rho15, 4) : round($rho15 / 1000.0, 4);
    }
}
