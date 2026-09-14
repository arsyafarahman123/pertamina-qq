<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'jabatan', 'spbu_name',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasilUji()
    {
        return $this->hasMany(HasilUji::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /** Bisa input & ubah data (Admin Lab QQ & Petugas Lapangan). */
    public function canManage(): bool
    {
        return in_array($this->role, ['admin', 'petugas'], true);
    }

    /** SPBU/Transportir — akun view-only, hanya melihat checklist mobil tangki. */
    public function isSpbu(): bool
    {
        return $this->role === 'spbu';
    }

    public function checklistMtMaos()
    {
        return $this->hasMany(ChecklistMtMaos::class);
    }
}
