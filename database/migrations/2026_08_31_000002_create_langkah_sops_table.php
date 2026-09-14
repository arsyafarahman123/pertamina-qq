<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('langkah_sops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_uji_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('urutan');
            $table->string('judul_singkat');          // "Reset alat"
            $table->text('instruksi');                 // deskripsi lengkap step
            $table->string('parameter_setting')->nullable(); // "Suhu 60°C"
            $table->string('indikator_selesai')->nullable();  // "Alarm bunyi + layar hijau"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('langkah_sops');
    }
};
