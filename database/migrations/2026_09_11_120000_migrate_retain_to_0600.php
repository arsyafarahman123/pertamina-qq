<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migrasi data "Sampel Retain" (jam_label = 'retain') ke jam 06:00.
 *
 * Konsep "retain" dihapus — semua data masuk ke jam penyaluran langsung.
 * Data retain dipindahkan ke 06:00 selama produk tersebut belum ada di 06:00
 * pada tanggal yang sama (tidak menimpa data yang sudah ada).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Pindahkan data retain_sampel_mts yang belum ada di 06:00
        $retains = DB::table('retain_sampel_mts')
            ->where('jam_label', 'retain')
            ->get();

        foreach ($retains as $retain) {
            $existing = DB::table('retain_sampel_mts')
                ->where('tanggal', $retain->tanggal)
                ->where('jam_label', '06:00')
                ->where('produk', $retain->produk)
                ->exists();

            if (! $existing) {
                DB::table('retain_sampel_mts')
                    ->where('id', $retain->id)
                    ->update(['jam_label' => '06:00']);
            } else {
                // Sudah ada di 06:00, hapus data retain duplikat
                DB::table('retain_sampel_mts')
                    ->where('id', $retain->id)
                    ->delete();
            }
        }

        // 2) Pindahkan foto retain ke 06:00
        $fotoRetains = DB::table('retain_sampel_fotos')
            ->where('jam_label', 'retain')
            ->get();

        foreach ($fotoRetains as $foto) {
            $existing = DB::table('retain_sampel_fotos')
                ->where('tanggal', $foto->tanggal)
                ->where('jam_label', '06:00')
                ->exists();

            if (! $existing) {
                DB::table('retain_sampel_fotos')
                    ->where('id', $foto->id)
                    ->update(['jam_label' => '06:00']);
            } else {
                DB::table('retain_sampel_fotos')
                    ->where('id', $foto->id)
                    ->delete();
            }
        }
    }

    public function down(): void
    {
        // Tidak bisa di-reverse secara otomatis karena data sudah berubah
    }
};
