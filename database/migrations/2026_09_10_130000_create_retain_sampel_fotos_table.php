<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Foto botol sampel (mis. hijau/biru/kuning berjajar) per sesi (tanggal + jam).
 * Satu foto berlaku untuk semua produk pada jam itu — persis seperti broadcast
 * WA aslinya yang cuma satu foto per pukul, bukan per produk.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retain_sampel_fotos', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('jam_label');   // '06:00', '12:00', '18:00', atau 'retain' (foto sampel retain baku)
            $table->string('path');        // lokasi file di storage/app/public/...
            $table->timestamps();

            $table->unique(['tanggal', 'jam_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retain_sampel_fotos');
    }
};
