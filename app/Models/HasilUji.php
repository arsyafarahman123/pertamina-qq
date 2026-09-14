<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilUji extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_uji_id', 'user_id', 'nama_sampel', 'nomor_kkw',
        'data_hasil', 'catatan', 'foto_bukti', 'waktu_uji',
    ];

    protected $casts = [
        'data_hasil' => 'array',
        'waktu_uji' => 'datetime',
    ];

    public function jenisUji()
    {
        return $this->belongsTo(JenisUji::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * URL publik untuk foto bukti pengujian (nullable).
     *
     * Lewat route terkontrol (bukan asset('storage/...')) supaya tidak
     * bergantung pada symlink `public/storage` — di InfinityFree/shared
     * hosting, symlink sering tidak bisa dibuat.
     */
    public function fotoBuktiUrl(): ?string
    {
        return $this->foto_bukti ? route('riwayat.foto-bukti', $this) : null;
    }

    /**
     * True jika file bukti yang tersimpan berupa gambar (bisa ditampilkan via <img>).
     * File non-gambar (pdf, doc, xls, dll) ditampilkan sebagai kartu unduhan.
     */
    public function fotoBuktiIsGambar(): bool
    {
        if (! $this->foto_bukti) {
            return false;
        }

        $ext = strtolower(pathinfo($this->foto_bukti, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true);
    }

    public function fotoBuktiNamaFile(): ?string
    {
        return $this->foto_bukti ? basename($this->foto_bukti) : null;
    }
}
