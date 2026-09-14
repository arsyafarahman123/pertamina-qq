<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Foto per-baris (per produk per jam) — supaya tiap entri (mis. baris
 * "Pertalite | N 37620 | 725.0000 | 725.0063") bisa punya foto botolnya
 * sendiri, bukan cuma satu foto gabungan per sesi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retain_sampel_mts', function (Blueprint $table) {
            $table->string('foto_path')->nullable()->after('tangki_timbun');
        });
    }

    public function down(): void
    {
        Schema::table('retain_sampel_mts', function (Blueprint $table) {
            $table->dropColumn('foto_path');
        });
    }
};
