<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Checklist digital "Form Pemeriksaan Mobil Tangki" — Fuel Terminal Maos.
 * Menggantikan form kertas PT Pertamina Patra Niaga x PT Patra Logistik.
 */
class ChecklistMtMaos extends Model
{
    use HasFactory;

    protected $table = 'checklist_mt_maos';

    protected $fillable = [
        'nomor_polisi', 'pemilik', 'tanggal_exp', 'tanggal_periksa',
        'tera', 'results', 'notes', 'ket_tambahan', 'status',
        'user_id', 'created_by',
    ];

    protected $casts = [
        'tera' => 'array',
        'results' => 'array',
        'notes' => 'array',
        'tanggal_periksa' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Jumlah item yang ditandai "temuan" (tidak sesuai). */
    public function flagCount(): int
    {
        return collect($this->results ?? [])->filter(fn ($v) => $v === 'bad')->count();
    }

    public function isFlagged(): bool
    {
        return $this->flagCount() > 0;
    }

    /** Blok tera kosong untuk 4 kompartemen — dipakai saat render form baru. */
    public static function blankTera(): array
    {
        return collect(range(1, 4))->map(fn ($k) => [
            'komp' => $k, 'tinggiTera' => '', 'tinggiAct' => '', 'selisih' => '',
            'duduk' => '', 'volume' => '', 'ijkBaut' => '',
            'a' => '', 'b' => '', 'c' => '', 'd' => '', 'e' => '', 'f' => '', 'g' => '',
        ])->all();
    }
}
