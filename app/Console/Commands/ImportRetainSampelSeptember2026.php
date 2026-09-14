<?php

namespace App\Console\Commands;

use App\Models\RetainSampelMt;
use App\Services\DensityCorrectionService;
use Illuminate\Console\Command;

/**
 * Import LENGKAP "LOGBOOK PENERIMAAN September 2026" (semua tanggal, semua
 * produk yang ada datanya: Pertalite, Pertamax, Solar, Biosolar B50,
 * Dexlite, Pertadex, FAME B40) ke tabel retain_sampel_mts.
 *
 * Baris yang JAM/Density Obs/Suhu-nya masih kosong di Excel (belum sempat
 * diisi analis) otomatis DILEWATI, bukan dipaksa masuk dengan data kosong.
 * Density'15 dihitung ulang otomatis lewat DensityCorrectionService (bukan
 * ambil dari Excel) supaya konsisten dengan cara sistem menghitung baris
 * lain. mt_nopol diisi No. Sample dari logbook (atau nopol asli untuk
 * FAME), tangki_timbun diisi "Asal Sampel" apa adanya.
 *
 * Baris yang sudah pernah diimport (tanggal 9 Sep, lewat command lama)
 * otomatis DIUPDATE, bukan dobel — kuncinya tanggal + jam + produk.
 *
 * Jalankan sekali: php artisan import:retain-sampel-september2026
 */
class ImportRetainSampelSeptember2026 extends Command
{
    protected $signature = 'import:retain-sampel-september2026';

    protected $description = 'Import SEMUA data Logbook Penerimaan bulan September 2026 ke Rekap Retain Sampel MT';

    public function handle(): int
    {
        // [tanggal, jam_label, produk, mt_nopol, tangki_timbun, density_obs, temperatur]
        $baris = [
            ['2026-09-01', '08:00', 'Pertalite', '01/PLT/IX/2026', 'Jalur pipa penerimaan', 0.732, 25.0],
            ['2026-09-01', '18:00', 'Pertalite', '02/PLT/IX/2026', 'TT10', 0.732, 25.0],
            ['2026-09-02', '16:00', 'Pertalite', '03/PLT/IX/2026', 'Jalur pipa penerimaan', 0.727, 32.0],
            ['2026-09-03', '16:00', 'Pertalite', '04/PLT/IX/2026', 'TT11', 0.733, 26.0],
            ['2026-09-03', '12:00', 'Pertalite', '05/PLT/IX/2026', 'Jalur pipa penerimaan', 0.725, 31.0],
            ['2026-09-03', '21:45', 'Pertalite', '06/PLT/IX/2026', 'TT10', 0.73, 26.0],
            ['2026-09-04', '02:31', 'Pertalite', '07/PLT/IX/2026', 'Jalur pipa penerimaan', 0.726, 31.0],
            ['2026-09-04', '13:45', 'Pertalite', '08/PLT/IX/2026', 'TT11', 0.733, 31.0],
            ['2026-09-04', '19:00', 'Pertalite', '09/PLT/IX/2026', 'Jalur pipa penerimaan', 0.73, 29.0],
            ['2026-09-05', '05:00', 'Pertalite', '10/PLT/IX/2026', 'TT10', 0.731, 25.0],
            ['2026-09-05', '12:30', 'Pertalite', '11/PLT/IX/2026', 'Jalur pipa penerimaan', 0.734, 26.0],
            ['2026-09-05', '16:00', 'Pertalite', '12/PLT/IX/2026', 'TT10', 0.736, 26.0],
            ['2026-09-05', '21:15', 'Pertalite', '13/PLT/IX/2026', 'TT11', 0.734, 26.0],
            ['2026-09-07', '22:30', 'Pertalite', '14/PLT/IX/2026', 'Jalur pipa penerimaan', 0.734, 26.0],
            ['2026-09-07', '04:00', 'Pertalite', '15/PLT/IX/2026', 'TT10', 0.735, 24.0],
            ['2026-09-07', '16:00', 'Pertalite', '16/PLT/IX/2026', 'Jalur pipa penerimaan', 0.727, 30.0],
            ['2026-09-07', '21:00', 'Pertalite', '17/PLT/IX/2026', 'TT10', 0.73, 26.0],
            ['2026-09-09', '09:00', 'Pertalite', '14/PLT/IX/2026', 'Jalur pipa penerimaan', 0.733, 26.0],
            ['2026-09-09', '13:00', 'Pertalite', '15/PLT/IX/2026', 'TT10', 0.731, 26.0],
            ['2026-09-10', '10:00', 'Pertalite', '16/PLT/IX/2026', 'Jalur pipa penerimaan', 0.732, 26.0],
            ['2026-09-03', '03:00', 'Pertamax', '01/PMX/IX/2026', 'Jalur Pipa Penerimaan', 0.738, 25.0],
            ['2026-09-03', '05:30', 'Pertamax', '02/PMX/IX/2026', 'TT05', 0.739, 26.0],
            ['2026-09-03', '11:55', 'Pertamax', '03/PMX/IX/2026', 'TT09', 0.741, 28.0],
            ['2026-09-04', '13:55', 'Pertamax', '04/PMX/IX/2026', 'Jalur Pipa Penerimaan', 0.735, 31.0],
            ['2026-09-04', '15:30', 'Pertamax', '05/PMX/IX/2026', 'TT09', 0.741, 28.0],
            ['2026-09-04', '19:00', 'Pertamax', '06/PMX/IX/2026', 'TT05', 0.741, 29.0],
            ['2026-09-06', '15:00', 'Pertamax', '07/PMX/IX/2026', 'Jalur Pipa Penerimaan', 0.74, 29.0],
            ['2026-09-06', '18:00', 'Pertamax', '08/PMX/IX/2026', 'TT09', 0.743, 25.0],
            ['2026-09-10', '10:00', 'Pertamax', '10/PMX/IX/2026', 'TT05', 0.743, 25.0],
            ['2026-09-02', '14:00', 'Solar', '01/SLR/IX/2026', 'Jalur pipa penerimaan', 0.84, 30.0],
            ['2026-09-02', '17:00', 'Solar', '02/SLR/IX/2026', 'TT01', 0.843, 28.0],
            ['2026-09-03', '22:00', 'Solar', '03/SLR/IX/2026', 'Jalur pipa penerimaan', 0.843, 29.0],
            ['2026-09-04', '02:30', 'Solar', '04/SLR/IX/2026', 'TT07', 0.845, 26.0],
            ['2026-09-05', '11:00', 'Solar', '05/SLR/IX/2026', 'Jalur pipa penerimaan', 0.841, 27.0],
            ['2026-09-06', '12:00', 'Solar', '06/SLR/IX/2026', 'TT02', 0.84, 28.0],
            ['2026-09-07', '05:00', 'Solar', '07/SLR/IX/2026', 'Jalur pipa penerimaan', 0.837, 30.0],
            ['2026-09-07', '08:00', 'Solar', '08/SLR/IX/2026', 'TT07', 0.84, 29.0],
            ['2026-09-07', '14:00', 'Solar', '09/SLR/IX/2026', 'TT01', 0.839, 31.0],
            ['2026-09-08', '13:00', 'Solar', '10/SLR/IX/2026', 'Jalur pipa penerimaan', 0.835, 32.0],
            ['2026-09-09', '00:30', 'Solar', '11/SLR/IX/2026', 'TT02', 0.842, 26.0],
            ['2026-09-09', '02:00', 'Solar', '12/SLR/IX/2026', 'TT01', 0.841, 25.0],
            ['2026-09-01', '01:00', 'Biosolar B50', '01/B50/IX/2026', 'TT07', 0.852, 25.0],
            ['2026-09-02', '01:00', 'Biosolar B50', '02/B50/IX/2026', 'T01', 0.853, 26.0],
            ['2026-09-03', '01:00', 'Biosolar B50', '03/B50/IX/2026', 'TT07', 0.852, 25.0],
            ['2026-09-04', '01:00', 'Biosolar B50', '04/B50/IX/2026', 'TT02', 0.855, 25.0],
            ['2026-09-05', '01:00', 'Biosolar B50', '05/B50/IX/2026', 'TT07', 0.855, 25.0],
            ['2026-09-06', '01:00', 'Biosolar B50', '06/B50/IX/2026', 'T01', 0.854, 26.0],
            ['2026-09-07', '01:00', 'Biosolar B50', '07/B50/IX/2026', 'TT02', 0.852, 26.0],
            ['2026-09-08', '01:00', 'Biosolar B50', '08/B50/IX/2026', 'TT02', 0.852, 26.0],
            ['2026-09-09', '01:00', 'Biosolar B50', '09/B50/IX/2026', 'TT07', 0.852, 25.0],
            ['2026-09-10', '01:00', 'Biosolar B50', '10/B50/IX/2026', 'TT02', 0.852, 26.0],
            ['2026-09-01', '03:00', 'Dexlite', '01/DXT/IX/2026', 'TT06', 0.85, 25.0],
            ['2026-09-02', '02:00', 'Dexlite', '02/DXT/IX/2026', 'TT06', 0.852, 26.0],
            ['2026-09-03', '03:00', 'Dexlite', '03/DXT/IX/2026', 'TT06', 0.85, 25.0],
            ['2026-09-04', '02:00', 'Dexlite', '04/DXT/IX/2026', 'TT06', 0.852, 25.0],
            ['2026-09-05', '02:00', 'Dexlite', '05/DXT/IX/2026', 'TT06', 0.852, 25.0],
            ['2026-09-06', '02:00', 'Dexlite', '06/DXT/IX/2026', 'TT06', 0.853, 26.0],
            ['2026-09-07', '02:00', 'Dexlite', '07/DXT/IX/2026', 'TT06', 0.85, 26.0],
            ['2026-09-08', '02:00', 'Dexlite', '08/DXT/IX/2026', 'TT06', 0.85, 26.0],
            ['2026-09-09', '02:00', 'Dexlite', '09/DXT/IX/2026', 'TT06', 0.852, 25.0],
            ['2026-09-10', '02:00', 'Dexlite', '10/DXT/IX/2026', 'TT06', 0.85, 26.0],
            ['2026-09-07', '17:30', 'Pertadex', '01/DEX/VIII/2026', null, 0.816, 26.0],
            ['2026-09-01', '09:00', 'FAME B40', '01/FAME/IX/2026', 'W 8323  US', 0.864, 30.0],
            ['2026-09-02', '08:30', 'FAME B40', '03/FAME/IX/2026', 'w 8285 u', 0.864, 30.0],
            ['2026-09-03', '08:30', 'FAME B40', '05/FAME/IX/2026', 'L8822 UB', 0.864, 30.0],
            ['2026-09-04', '09:00', 'FAME B40', '07/FAME/IX/2026', 'L 8910 UG', 0.864, 30.0],
            ['2026-09-05', '09:00', 'FAME B40', '09/FAME/IX/2026', 'W 9985US', 0.864, 30.0],
            ['2026-09-06', '09:00', 'FAME B40', '11/FAME/IX/2026', 'L 8511 UK', 0.864, 30.0],
            ['2026-09-07', '09:00', 'FAME B40', '13/FAME/IX/2026', 'W 8163 UQ', 0.864, 30.0],
            ['2026-09-08', '09:00', 'FAME B40', '15/FAME/IX/2026', 'L 8041 UK', 0.864, 30.0],
            ['2026-09-09', '08:00', 'FAME B40', '17/FAME/IX/2026', 'L 8190', 0.864, 30.0],
        ];

        $dibuat = 0;
        $diupdate = 0;

        foreach ($baris as [$tanggal, $jam, $produk, $mtNopol, $tangkiTimbun, $densityObs, $temp]) {
            $density15 = DensityCorrectionService::hitungDensity15($densityObs, $temp);

            $existing = RetainSampelMt::where('tanggal', $tanggal)
                ->where('jam_label', $jam)
                ->where('produk', $produk)
                ->exists();

            RetainSampelMt::updateOrCreate(
                ['tanggal' => $tanggal, 'jam_label' => $jam, 'produk' => $produk],
                [
                    'mt_nopol' => $mtNopol,
                    'tangki_timbun' => $tangkiTimbun,
                    'density_obs' => $densityObs,
                    'temperatur' => $temp,
                    'density_15' => $density15,
                ]
            );

            $existing ? $diupdate++ : $dibuat++;
        }

        $this->info("Selesai. {$dibuat} baris baru dibuat, {$diupdate} baris diupdate (total ".count($baris)." baris, seluruh bulan September 2026).");
        $this->info('Buka /retain-sampel lalu ganti tanggal rekap sesuai baris yang mau dicek (01-10 September 2026).');

        return self::SUCCESS;
    }
}
