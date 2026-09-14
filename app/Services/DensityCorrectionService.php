<?php

namespace App\Services;

/**
 * Menghitung Density'15 (density terkoreksi ke 15°C) SECARA OTOMATIS dari
 * Density Obs (hasil baca hidrometer) + Suhu Obs — menggantikan cara manual
 * "cari di Tabel ASTM 53" satu-satu.
 *
 * Rumus yang dipakai adalah rumus resmi di balik Tabel ASTM-IP Petroleum
 * Measurement Table 53B/54B ("Generalized Products", basis 15°C) —
 * ASTM D1250 / API MPMS Chapter 11.1:
 *
 *   alpha(rho15) = (K0 + K1 * rho15) / rho15^2      [rho dalam kg/m3]
 *   dT           = T_obs - 15                        [°C]
 *   CTL          = exp( -alpha * dT * (1 + 0.8 * alpha * dT) )
 *   rho15        = rho_obs / CTL
 *
 * rho15 muncul di kedua sisi (alpha bergantung rho15), jadi diselesaikan
 * dengan iterasi singkat (konvergen dalam 3-4 langkah karena alpha berubah
 * sangat kecil terhadap rho). Hasilnya identik dengan pembacaan tabel ASTM 53
 * kertas sampai 3-4 digit desimal.
 */
class DensityCorrectionService
{
    /**
     * Pilih konstanta K0/K1 sesuai grup densitas (kg/m3 @15°C) — persis cara
     * Tabel ASTM 53/54 aslinya dibagi per kelompok produk:
     *   - Grup Bensin (653-778)      : Pertalite / Pertamax / Pertamax Turbo
     *   - Grup Jet/Kerosene (778-840): produk ringan lain
     *   - Grup Solar/Fuel Oil (>840) : Solar / Biosolar / Dexlite / Pertadex
     */
    protected static function pilihKoefisien(float $rhoKira2): array
    {
        return match (true) {
            $rhoKira2 < 778.0 => [346.4228, 0.4388],   // bensin
            $rhoKira2 < 839.9 => [594.5418, 0.0],      // kerosene/jet
            default => [186.9696, 0.4862],             // solar/fuel oil
        };
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

        // Fleksibel: deteksi apakah input dalam kg/m3 (> 100) atau g/mL (< 10)
        // Kalau input 500-1100, sudah dalam kg/m3
        // Kalau input 0.5 - 1.2, dalam g/mL -> konversi ke kg/m3
        $isDalamKgM3 = $densityObs > 100.0;
        $rhoObs = $isDalamKgM3 ? $densityObs : ($densityObs * 1000.0);

        // Jika angka dummy di luar range BBM wajar (< 100 atau > 2000), kembalikan rounded
        if ($rhoObs < 100.0 || $rhoObs > 2000.0) {
            return round($densityObs, 4);
        }

        $dT = $suhuObsC - 15.0;

        // Kalau suhu obs pas 15°C, tidak perlu koreksi.
        if (abs($dT) < 0.0001) {
            return round($densityObs, 4);
        }

        [$k0, $k1] = self::pilihKoefisien($rhoObs);
        $rho15 = $rhoObs; // tebakan awal

        for ($i = 0; $i < 12; $i++) {
            if ($rho15 <= 0.0001) {
                break;
            }
            $alpha = ($k0 + $k1 * $rho15) / ($rho15 ** 2);
            $ctl = exp(-$alpha * $dT * (1.0 + 0.8 * $alpha * $dT));
            
            if ($ctl <= 0.000001 || !is_finite($ctl)) {
                break;
            }

            $rho15Baru = $rhoObs / $ctl;

            if (abs($rho15Baru - $rho15) < 0.00001) {
                $rho15 = $rho15Baru;
                break;
            }
            $rho15 = $rho15Baru;
        }

        return $isDalamKgM3 ? round($rho15, 4) : round($rho15 / 1000.0, 4);
    }
}
