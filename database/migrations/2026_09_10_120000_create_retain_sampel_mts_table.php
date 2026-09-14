<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekap harian "Retain Sampel Penyaluran MT" (mengikuti format broadcast
 * PERTAMINA PATRA NIAGA - FT MAOS). Density Obs diinput manual dari
 * hidrometer, Density'15 dihitung OTOMATIS oleh sistem (lihat
 * App\Services\DensityCorrectionService) memakai tabel konversi
 * ASTM-IP Petroleum Measurement Table 53B/54B (Generalized Products,
 * basis 15°C) — bukan lagi dicari manual di tabel ASTM kertas/PDF.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retain_sampel_mts', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');                 // tanggal penyaluran/retain
            $table->string('jam_label');              // '06:00', '12:00', '18:00', atau custom
            $table->string('produk');                 // Pertalite, Pertamax, Biosolar B50, Dexlite, Pertadex, dst
            $table->string('mt_nopol')->nullable();   // Nopol MT / No. Segel sumber sampel
            $table->string('tangki_timbun')->nullable();

            $table->decimal('density_obs', 7, 4);     // diinput manual dari hidrometer
            $table->decimal('temperatur', 6, 2);      // suhu observasi (°C)
            $table->decimal('density_15', 7, 4);      // HASIL OTOMATIS (koreksi ke 15°C)

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal', 'jam_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retain_sampel_mts');
    }
};
