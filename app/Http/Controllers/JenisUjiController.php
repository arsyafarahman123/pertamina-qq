<?php

namespace App\Http\Controllers;

use App\Models\HasilUji;
use App\Models\JenisUji;
use App\Services\ImageProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JenisUjiController extends Controller
{
    public function index()
    {
        $daftarUji = JenisUji::where('aktif', true)
            ->withCount('langkahSop')
            ->orderBy('urutan')
            ->get();

        return view('uji.index', compact('daftarUji'));
    }

    public function show(JenisUji $jenisUji)
    {
        $jenisUji->load('langkahSop');
        $fieldConfig = $jenisUji->fieldConfig();
        $daftarSampel = config('uji.sampel_umum');

        return view('uji.show', compact('jenisUji', 'fieldConfig', 'daftarSampel'));
    }

    public function simpanHasil(Request $request, JenisUji $jenisUji)
    {
        $request->validate([
            'nama_sampel' => ['required', 'string', 'max:100'],
            'nomor_kkw' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
            'data_hasil' => ['required', 'array'],
            'foto_bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,bmp,pdf,doc,docx,xls,xlsx,csv,txt', 'max:10240'],
        ]);

        $fotoBukti = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoBukti = $request->file('foto_bukti')->store('bukti-uji', 'public');
            ImageProcessor::normalisasi(Storage::disk('public')->path($fotoBukti));
        }

        HasilUji::create([
            'jenis_uji_id' => $jenisUji->id,
            'user_id' => Auth::id(),
            'nama_sampel' => $request->nama_sampel,
            'nomor_kkw' => $request->nomor_kkw,
            'data_hasil' => $request->data_hasil,
            'catatan' => $request->catatan,
            'foto_bukti' => $fotoBukti,
            'waktu_uji' => now(),
        ]);

        return redirect()
            ->route('uji.show', $jenisUji)
            ->with('sukses', 'Hasil uji "' . $jenisUji->nama . '" berhasil disimpan.');
    }
}
