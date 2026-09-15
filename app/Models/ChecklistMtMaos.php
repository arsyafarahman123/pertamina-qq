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

    /** Jumlah item yang ditandai "temuan" (tidak sesuai / ada catatan tambahan pemeriksa). */
    public function flagCount(): int
    {
        $count = collect($this->results ?? [])->filter(fn ($v) => $v === 'bad')->count();
        if (!empty(trim($this->ket_tambahan ?? ''))) {
            $count += 1;
        }
        return $count;
    }

    public function isFlagged(): bool
    {
        return $this->flagCount() > 0;
    }

    /** Daftar ringkas semua temuan dan keterangannya untuk ditampilkan di dashboard/tabel. */
    public function summaryTemuan(): array
    {
        $flat = \App\Support\ChecklistMtMaosItems::flat();
        $list = [];

        // 1. Masa Tera
        if (($this->results['1'] ?? null) === 'bad' || !empty($this->notes['1'])) {
            $note = trim($this->notes['1'] ?? '');
            $list[] = 'Masa Tera' . ($note ? ": {$note}" : '');
        }

        // 2. Kompartemen
        if (($this->results['2'] ?? null) === 'bad' || !empty($this->notes['2'])) {
            $note = trim($this->notes['2'] ?? '');
            $list[] = 'Kompartemen Tera' . ($note ? ": {$note}" : '');
        }

        // 3. Item 3-17
        foreach ($flat as $row) {
            if (($this->results[$row['key']] ?? null) === 'bad') {
                $note = trim($this->notes[$row['key']] ?? '');
                $list[] = $row['label'] . ($note ? ": {$note}" : '');
            }
        }

        // 4. Keterangan Tambahan
        if (!empty(trim($this->ket_tambahan ?? ''))) {
            $list[] = 'Keterangan: ' . trim($this->ket_tambahan);
        }

        return $list;
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
