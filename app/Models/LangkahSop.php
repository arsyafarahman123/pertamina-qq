<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LangkahSop extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_uji_id', 'urutan', 'judul_singkat', 'instruksi',
        'parameter_setting', 'indikator_selesai',
    ];

    public function jenisUji()
    {
        return $this->belongsTo(JenisUji::class);
    }
}
