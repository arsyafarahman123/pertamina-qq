<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // SQLite akan menangani perubahan struktur tabel
        });

        // SQLite tidak mendukung perubahan CHECK constraint secara langsung.
        // Kita perlu rebuild tabel users.
        Schema::disableForeignKeyConstraints();

        Schema::create('users_new', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->string('role')
                ->default('petugas')
                ->check("role IN ('admin', 'petugas', 'spbu')");

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
    }

    public function down(): void
    {
        // Tidak perlu rollback otomatis karena perubahan constraint
        // merupakan perbaikan struktur database.
    }
};