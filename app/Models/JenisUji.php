<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisUji extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode', 'nama', 'slug', 'deskripsi', 'icon', 'aktif', 'urutan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function langkahSop()
    {
        return $this->hasMany(LangkahSop::class)->orderBy('urutan');
    }

    public function hasilUji()
    {
        return $this->hasMany(HasilUji::class);
    }

    /**
     * Konfigurasi field dinamis untuk form hasil uji (di config/uji.php)
     */
    public function fieldConfig(): array
    {
        return config('uji.fields.' . $this->kode, []);
    }
}
