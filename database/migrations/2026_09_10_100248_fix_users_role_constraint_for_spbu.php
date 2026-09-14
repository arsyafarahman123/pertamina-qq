<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite tidak mendukung ALTER COLUMN / CHECK constraint.
            // Rebuild tabel users agar role bisa terima 'admin','petugas','spbu'.
            Schema::disableForeignKeyConstraints();

            Schema::create('users_new', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->string('role')->default('petugas');
                $table->string('jabatan')->nullable();
                $table->string('spbu_name')->nullable();
            });

            DB::statement('
                INSERT INTO users_new
                (id, name, email, email_verified_at, password, remember_token,
                 created_at, updated_at, role, jabatan, spbu_name)
                SELECT
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, role, jabatan, spbu_name
                FROM users
            ');

            Schema::drop('users');
            Schema::rename('users_new', 'users');
            Schema::enableForeignKeyConstraints();
        } else {
            // MySQL/MariaDB: ubah enum role agar menerima 'spbu'.
            // Kolom role sudah di-ALTER di migration sebelumnya; ini hanya memastikan.
            try {
                DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'petugas'");
            } catch (\Throwable $e) {
                // Abaikan jika kolom sudah sesuai
            }
        }
    }

    public function down(): void
    {
        // Tidak perlu rollback otomatis karena perubahan constraint
        // merupakan perbaikan struktur database.
    }
};