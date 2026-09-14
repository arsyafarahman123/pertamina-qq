<?php

namespace App\Console\Commands;

use App\Models\HasilUji;
use App\Services\ImageProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Merapikan ulang semua foto bukti yang SUDAH tersimpan sebelumnya
 * (auto-rotate + resize), dan menandai file yang bentuknya mencurigakan
 * (sangat memanjang / rasio ekstrem) sebagai kemungkinan salah unggah
 * -- misalnya screenshot sertifikat yang ter-upload ulang sebagai foto bukti.
 *
 * Jalankan: php artisan foto-bukti:rapikan
 */
class RapikanFotoBukti extends Command
{
    protected $signature = 'foto-bukti:rapikan {--cek-saja : hanya tampilkan laporan, tidak mengubah file}';

    protected $description = 'Normalisasi orientasi & ukuran semua foto bukti pengujian yang sudah tersimpan';

    public function handle(): int
    {
        $cekSaja = (bool) $this->option('cek-saja');
        $daftar = HasilUji::whereNotNull('foto_bukti')->get();

        $ditemukanMencurigakan = 0;

        foreach ($daftar as $hasil) {
            if (! $hasil->fotoBuktiIsGambar()) {
                continue;
            }

            $pathAbsolut = Storage::disk('public')->path($hasil->foto_bukti);
            if (! is_file($pathAbsolut)) {
                continue;
            }

            $info = @getimagesize($pathAbsolut);
            if (! $info) {
                continue;
            }

            [$lebar, $tinggi] = $info;
            $rasio = $tinggi > 0 ? $lebar / $tinggi : 1;

            // Sertifikat yang di-screenshot ulang biasanya sangat memanjang
            // ke bawah (rasio lebar:tinggi kecil, di bawah ~0.5) karena berisi
            // header + tabel + foto lain di dalamnya.
            $mencurigakan = $rasio < 0.5 && $tinggi > 2000;

            if ($mencurigakan) {
                $ditemukanMencurigakan++;
                $this->warn(sprintf(
                    '[Cek manual] HasilUji #%d — "%s" (KKW: %s) — file %s (%dx%d px). ' .
                    'Kemungkinan ini screenshot sertifikat lama yang ter-upload sebagai foto bukti, bukan foto asli.',
                    $hasil->id,
                    $hasil->nama_sampel,
                    $hasil->nomor_kkw ?? '-',
                    $hasil->foto_bukti,
                    $lebar,
                    $tinggi
                ));
            }

            if (! $cekSaja) {
                ImageProcessor::normalisasi($pathAbsolut);
            }
        }

        $this->info(sprintf(
            'Selesai. %d foto diproses. %d file terindikasi salah unggah (lihat peringatan di atas).',
            $daftar->count(),
            $ditemukanMencurigakan
        ));

        if ($ditemukanMencurigakan > 0) {
            $this->line('Untuk yang terindikasi salah unggah: buka Riwayat > Edit data terkait, lalu ganti foto buktinya dengan foto asli.');
        }

        return self::SUCCESS;
    }
}
