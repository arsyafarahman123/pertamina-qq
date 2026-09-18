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

    /**
     * Memecah catatan tambahan (ket_tambahan) menjadi array item temuan terpisah.
     * Mendukung pemisah strip (-), bullet (•), koma (,), titik koma (;), nomor (1., 2.), dan baris baru (\n).
     */
    public function getKetTambahanItems(): array
    {
        $text = $this->ket_tambahan ?? '';
        if ($text === null || trim($text) === '' || trim($text) === '-') {
            return [];
        }

        $raw = trim($text);
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);

        // Split berdasarkan baris baru, strip/bullet dengan koma/spasi, atau koma antar kalimat
        $parts = preg_split('/(?:[\n\r]+|\s*[,;]\s*[-•*]\s*|\s+[-•*]\s*|^[-•*]\s*|\s*[,;]\s*(?=[a-zA-Z0-9]))/u', $raw);

        $items = [];
        foreach ($parts as $p) {
            $clean = trim($p, " \t\n\r\0\x0B-,•;*");
            if ($clean !== '' && $clean !== '-') {
                $items[] = $clean;
            }
        }

        // Fallback jika pemisahan di atas menghasilkan <= 1 item tetapi ada koma / titik koma yang memisahkan
        if (count($items) <= 1 && (str_contains($raw, ',') || str_contains($raw, ';'))) {
            $commaParts = preg_split('/[,;]/', $raw);
            $temp = [];
            foreach ($commaParts as $cp) {
                $clean = trim($cp, " \t\n\r\0\x0B-,•;*");
                if ($clean !== '' && $clean !== '-') {
                    $temp[] = $clean;
                }
            }
            if (count($temp) > 1) {
                $items = $temp;
            }
        }

        return $items;
    }

    /**
     * Mengecek apakah catatan mengindikasikan perbaikan yang telah selesai.
     */
    public function isRepairNote(?string $note): bool
    {
        if (empty($note)) return false;
        $lower = strtolower($note);
        $keywords = ['perbaikan', 'diperbaiki', 'sudah diganti', 'telah diganti', 'diganti', 'repaired', 'selesai', 'dites ulang', 'aman', 'sudah ok', 'sudah sesuai', 'sudah beres', 'oke'];
        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) {
                return true;
            }
        }
        return false;
    }

    /** Jumlah item yang masih berstatus temuan belum diperbaiki (bad). */
    public function flagCount(): int
    {
        $count = 0;
        $results = $this->results ?? [];

        foreach ($results as $k => $v) {
            if ($v === 'bad') {
                $count++;
            }
        }

        // Cek keterangan tambahan yang murni temuan (bukan catatan perbaikan)
        foreach ($this->getKetTambahanItems() as $item) {
            if (!$this->isRepairNote($item)) {
                // Hanya hitung jika results tidak sepenuhnya menandai ok/repaired
                if ($count > 0 || collect($results)->contains('bad')) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function isFlagged(): bool
    {
        return $this->flagCount() > 0;
    }

    /** Mengecek apakah armada ini memiliki riwayat perbaikan yang telah diselesaikan. */
    public function hasPerbaikan(): bool
    {
        $results = $this->results ?? [];
        $notes = $this->notes ?? [];

        // Ada item dengan hasil 'repaired'
        if (collect($results)->contains('repaired')) {
            return true;
        }

        // Ada catatan yang mengindikasikan perbaikan
        foreach ($notes as $k => $note) {
            if ($this->isRepairNote($note)) {
                return true;
            }
        }

        if ($this->isRepairNote($this->ket_tambahan)) {
            return true;
        }

        return false;
    }

    /** Daftar temuan yang BELUM diperbaiki (berstatus bad). */
    public function summaryTemuan(): array
    {
        $flat = \App\Support\ChecklistMtMaosItems::flat();
        $list = [];

        // 1. Masa Tera
        if (($this->results['1'] ?? null) === 'bad') {
            $note = trim($this->notes['1'] ?? '');
            $list[] = 'Masa Tera' . ($note ? ": {$note}" : '');
        }

        // 2. Kompartemen
        if (($this->results['2'] ?? null) === 'bad') {
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

        // 4. Keterangan Tambahan (hanya jika bukan catatan perbaikan)
        if ($this->isFlagged()) {
            foreach ($this->getKetTambahanItems() as $item) {
                if (!$this->isRepairNote($item)) {
                    $list[] = 'Temuan: ' . $item;
                }
            }
        }

        return $list;
    }

    /** Daftar ringkas seluruh perbaikan yang telah dilakukan dan catatannya. */
    public function summaryPerbaikan(): array
    {
        $flat = \App\Support\ChecklistMtMaosItems::flat();
        $list = [];

        // 1. Masa Tera
        if (($this->results['1'] ?? null) === 'repaired' || ($this->isRepairNote($this->notes['1'] ?? null))) {
            $note = trim($this->notes['1'] ?? 'Masa Tera telah diperbarui/sesuai');
            $list[] = 'Masa Tera: ' . $note;
        }

        // 2. Kompartemen
        if (($this->results['2'] ?? null) === 'repaired' || ($this->isRepairNote($this->notes['2'] ?? null))) {
            $note = trim($this->notes['2'] ?? 'Kompartemen tera telah diperbaiki');
            $list[] = 'Kompartemen Tera: ' . $note;
        }

        // 3. Item 3-17
        foreach ($flat as $row) {
            $res = $this->results[$row['key']] ?? null;
            $note = trim($this->notes[$row['key']] ?? '');
            if ($res === 'repaired' || ($note && $this->isRepairNote($note))) {
                $list[] = $row['label'] . ($note ? ": {$note}" : ': Telah diperbaiki');
            }
        }

        // 4. Keterangan Tambahan jika berisi info perbaikan
        foreach ($this->getKetTambahanItems() as $item) {
            if ($this->isRepairNote($item)) {
                $list[] = 'Perbaikan: ' . $item;
            }
        }

        return $list;
    }

    /** Label status human-readable untuk badge UI. */
    public function statusLabel(): string
    {
        if ($this->isFlagged()) {
            return $this->flagCount() . ' Temuan';
        }
        if ($this->hasPerbaikan()) {
            return 'Selesai Perbaikan';
        }
        return 'Sesuai Standar';
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
