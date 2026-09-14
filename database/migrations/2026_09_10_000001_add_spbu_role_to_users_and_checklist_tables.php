<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite/MySQL-safe: ubah kolom enum 'role' agar menerima nilai baru 'spbu'
        // (akun eksternal SPBU/Transportir — view-only checklist mobil tangki miliknya sendiri),
        // dan tambahkan kolom spbu_name untuk mencocokkan checklist milik siapa.
        if (DB::getDriverName() === 'sqlite') {
            // SQLite tidak strict soal enum (disimpan sebagai TEXT), jadi cukup tambah kolom.
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'spbu_name')) {
                    $table->string('spbu_name')->nullable()->after('jabatan');
                }
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','petugas','spbu') NOT NULL DEFAULT 'petugas'");
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'spbu_name')) {
                    $table->string('spbu_name')->nullable()->after('jabatan');
                }
            });
        }

        Schema::create('checklist_mt_maos', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi');
            $table->string('pemilik')->nullable();
            $table->string('tanggal_exp')->nullable();
            $table->date('tanggal_periksa');
            $table->json('tera');       // 4 kompartemen: tinggiTera, tinggiAct, selisih, duduk, volume, ijkBaut, a-g
            $table->json('results');    // key ("3-0", "4", ...) => 'ok' | 'bad'
            $table->json('notes');      // key => catatan/keterangan bebas
            $table->text('ket_tambahan')->nullable();
            $table->string('status')->default('final');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index('nomor_polisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_mt_maos');
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'spbu_name')) {
                $table->dropColumn('spbu_name');
            }
        });
    }
};
