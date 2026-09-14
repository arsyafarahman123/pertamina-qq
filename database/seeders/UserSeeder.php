<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@labqq.test'],
            [
                'name' => 'Admin Lab QQ',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'jabatan' => 'Supervisor Lab QQ',
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas@labqq.test'],
            [
                'name' => 'Petugas QC & Lapangan',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'jabatan' => 'Analis Lab & Checklist MT',
            ]
        );

        // Akun contoh SPBU/Transportir / User Viewer — Read-Only
        User::updateOrCreate(
            ['email' => 'viewer@labqq.test'],
            [
                'name' => 'User Viewer (SPBU / Tamu)',
                'password' => Hash::make('password123'),
                'role' => 'spbu',
                'jabatan' => 'Viewer (Hanya Lihat / Read-Only)',
                'spbu_name' => 'PT Suci',
            ]
        );

        User::updateOrCreate(
            ['email' => 'spbu@contoh.test'],
            [
                'name' => 'PT Contoh Transportir',
                'password' => Hash::make('password123'),
                'role' => 'spbu',
                'jabatan' => 'Viewer SPBU/Transportir',
                'spbu_name' => 'PT Contoh Transportir',
            ]
        );
    }
}
