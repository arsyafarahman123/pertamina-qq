<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_ujis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_uji_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nama_sampel');            // Solar, Biosolar, Pertamina Dex, Dexlite
            $table->string('nomor_kkw')->nullable();   // Nomor KKW / Kereta
            $table->json('data_hasil');                 // field dinamis per jenis uji (hasil baca, suhu, dll)
            $table->text('catatan')->nullable();

            $table->timestamp('waktu_uji')->useCurrent();
            $table->timestamps();

            $table->index(['jenis_uji_id', 'waktu_uji']);
            $table->index('nomor_kkw');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_ujis');
    }
};
