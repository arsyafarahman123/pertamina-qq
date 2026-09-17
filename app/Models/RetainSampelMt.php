<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Satu baris rekap "Retain Sampel Penyaluran MT" per produk per jam observasi.
 * Density'15 SELALU dihitung otomatis oleh sistem — lihat
 * App\Services\DensityCorrectionService — jadi field ini bukan input manual.
 */
class RetainSampelMt extends Model
{
    use HasFactory;

    protected $table = 'retain_sampel_mts';

    protected $fillable = [
        'tanggal', 'jam_label', 'produk', 'mt_nopol', 'tangki_timbun',
        'foto_path', 'density_obs', 'temperatur', 'density_15', 'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'density_obs' => 'float',
        'temperatur' => 'float',
        'density_15' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDensityObsAttribute($value)
    {
        if ($value === null) return null;
        $num = (float)$value;
        return $num >= 10.0 ? round($num / 1000.0, 4) : $num;
    }

    public function getDensity15Attribute($value)
    {
        if ($this->density_obs !== null && $this->temperatur !== null) {
            return \App\Services\DensityCorrectionService::hitungDensity15((float)$this->density_obs, (float)$this->temperatur);
        }
        if ($value === null) return null;
        $num = (float)$value;
        return $num >= 10.0 ? round($num / 1000.0, 4) : $num;
    }

    public function setDensityObsAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['density_obs'] = null;
        } else {
            $num = (float)$value;
            $this->attributes['density_obs'] = $num >= 10.0 ? round($num / 1000.0, 4) : round($num, 4);
        }
    }

    public function setDensity15Attribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['density_15'] = null;
        } else {
            $num = (float)$value;
            $this->attributes['density_15'] = $num >= 10.0 ? round($num / 1000.0, 4) : round($num, 4);
        }
    }

    /**
     * URL foto botol sampel untuk baris ini (null kalau belum diupload).
     *
     * Sengaja lewat route terkontrol (bukan asset('storage/...')) supaya TIDAK
     * bergantung pada symlink `public/storage` — di banyak setup lokal/Windows
     * symlink itu gagal terbentuk / tidak ikut ter-zip, jadi foto ke-upload
     * tapi gak pernah kelihatan. Lewat route, file langsung dibaca dari
     * storage/app/public tanpa perlu symlink sama sekali.
     */
    public function fotoUrl(): ?string
    {
        return $this->foto_path ? route('retain-sampel.foto', $this) : null;
    }

    /** True kalau file yang diupload adalah PDF (bukan gambar). */
    public function fotoIsPdf(): bool
    {
        return $this->foto_path && str_ends_with(strtolower($this->foto_path), '.pdf');
    }

    /** Daftar produk baku yang dipakai dropdown form + urutan kolom rekap. */
    public static function daftarProduk(): array
    {
        return ['Pertalite', 'Pertamax', 'Pertamax Turbo', 'Biosolar B50', 'Dexlite', 'Pertadex'];
    }

    /** Jam standar broadcast retain (06.00 / 12.00 / 18.00) — tetap bisa isi jam lain manual. */
    public static function jamStandar(): array
    {
        return ['06:00', '12:00', '18:00'];
    }
}
