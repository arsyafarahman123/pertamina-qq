<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Foto botol sampel (satu foto berlaku untuk semua produk di jam yang sama). */
class RetainSampelFoto extends Model
{
    protected $table = 'retain_sampel_fotos';

    protected $fillable = ['tanggal', 'jam_label', 'path'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /** Lewat route terkontrol (bukan asset('storage/...')) — tidak bergantung symlink public/storage. */
    public function url(): string
    {
        return route('retain-sampel.foto-sesi.lihat', $this);
    }

    /** True kalau file yang diupload adalah PDF (bukan gambar). */
    public function isPdf(): bool
    {
        return str_ends_with(strtolower($this->path), '.pdf');
    }
}
