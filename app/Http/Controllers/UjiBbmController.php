<?php

namespace App\Http\Controllers;

use App\Models\HasilUji;
use App\Models\JenisUji;
use App\Services\ImageProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UjiBbmController extends Controller
{
    public function index()
    {
        $kategoriGasoline = config('spesifikasi_bbm.gasoline');
        $kategoriGasoil = config('spesifikasi_bbm.gasoil');
        $toolFields = config('spesifikasi_bbm.tool_fields');
        $toolSop = config('spesifikasi_bbm.tool_sop');

        return view('uji-bbm.index', compact('kategoriGasoline', 'kategoriGasoil', 'toolFields', 'toolSop'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:gasoline,gasoil',
            'jenis' => 'required|string',
            'hasil_json' => 'required|string',
            'nomor_kkw' => 'nullable|string|max:50',
            'foto_bukti' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,bmp|max:10240',
        ]);

        $kategori = $request->kategori;
        $jenis = $request->jenis;
        $hasil = json_decode($request->input('hasil_json'), true);

        if (! is_array($hasil)) {
            return response()->json(['error' => 'Data hasil pengukuran tidak valid'], 422);
        }

        $bbmConfig = config("spesifikasi_bbm.{$kategori}.{$jenis}");

        if (! $bbmConfig) {
            return response()->json(['error' => 'Jenis BBM tidak ditemukan'], 404);
        }

        $specs = $bbmConfig['specs'];
        $report = [];
        $kesimpulanStatus = 'PASS';

        foreach ($hasil as $key => $value) {
            if (! isset($specs[$key])) {
                continue; // Ignore fields not in specs (though UI shouldn't send them)
            }

            if ($value === null || $value === '') {
                $report[] = [
                    'parameter' => $key,
                    'value' => '-',
                    'unit' => $specs[$key]['unit'] ?? '',
                    'min' => $specs[$key]['min'] ?? '-',
                    'max' => $specs[$key]['max'] ?? '-',
                    'status' => 'INVALID',
                    'message' => 'Nilai tidak diisi',
                ];
                $kesimpulanStatus = 'FAIL';
                continue;
            }

            $numericValue = (float) $value;
            $min = $specs[$key]['min'] ?? null;
            $max = $specs[$key]['max'] ?? null;
            $status = 'PASS';
            $message = 'Memenuhi spesifikasi';

            if ($min !== null && $numericValue < $min) {
                $status = 'FAIL';
                $message = "Di bawah batas minimum ($min)";
            }
            if ($max !== null && $numericValue > $max) {
                $status = 'FAIL';
                $message = "Di atas batas maksimum ($max)";
            }

            if ($status === 'FAIL') {
                $kesimpulanStatus = 'FAIL';
            }

            $report[] = [
                'parameter' => $key,
                'value' => $numericValue,
                'unit' => $specs[$key]['unit'] ?? '',
                'min' => $min ?? '-',
                'max' => $max ?? '-',
                'status' => $status,
                'message' => $message,
            ];
        }

        // ---- Simpan bukti foto (upload file atau hasil jepretan kamera) ----
        $fotoBukti = null;
        if ($request->hasFile('foto_bukti')) {
            $fotoBukti = $request->file('foto_bukti')->store('bukti-uji', 'public');
            ImageProcessor::normalisasi(Storage::disk('public')->path($fotoBukti));
        }

        // ---- Simpan ke Riwayat Hasil Uji supaya masuk juga ke Asisten Fuel Maos ----
        $jenisUji = JenisUji::firstOrCreate(
            ['kode' => 'UJI_BBM_SPEK'],
            [
                'nama' => 'Uji Kesesuaian Spesifikasi BBM',
                'slug' => Str::slug('Uji Kesesuaian Spesifikasi BBM'),
                'deskripsi' => 'Pengujian kesesuaian mutu BBM (gasoline & gasoil) terhadap batas spesifikasi Dirjen Migas, dari menu Pengujian BBM Terpadu.',
                'icon' => 'clipboard-check',
                'aktif' => false, // disembunyikan dari daftar SOP /uji, hanya dipakai sebagai referensi Riwayat
                'urutan' => 999,
            ]
        );

        $namaSampel = $this->normalisasiNamaSampel($bbmConfig['nama']);

        $hasilUji = HasilUji::create([
            'jenis_uji_id' => $jenisUji->id,
            'user_id' => Auth::id(),
            'nama_sampel' => $namaSampel,
            'nomor_kkw' => $request->input('nomor_kkw') ?: null,
            'data_hasil' => array_merge($hasil, [
                '_uji_bbm' => true,
                '_kategori' => $kategori,
                '_jenis_key' => $jenis,
                '_nama_lengkap' => $bbmConfig['nama'],
                '_verdict' => $kesimpulanStatus,
                '_parameters' => $report,
            ]),
            'catatan' => 'Uji Kesesuaian Spesifikasi BBM (' . ($kategori === 'gasoline' ? 'Gasoline' : 'Gasoil') . ') — diproses otomatis via menu Pengujian BBM Terpadu.',
            'foto_bukti' => $fotoBukti,
            'waktu_uji' => now(),
        ]);

        return response()->json([
            'nama_bbm' => $bbmConfig['nama'],
            'status' => $kesimpulanStatus,
            'report' => $report,
            'foto_url' => $hasilUji->fotoBuktiUrl(),
            'hasil_uji_id' => $hasilUji->id,
            'riwayat_url' => route('riwayat.show', $hasilUji),
        ]);
    }

    /**
     * Selaraskan nama sampel dengan konvensi yang dipakai di seluruh sistem
     * (mis. "Biosolar B40" -> "Biosolar", "Pertamina Dex (B0)" -> "Pertamina Dex")
     * supaya konsisten saat dicari/dibandingkan di menu Riwayat & Asisten Fuel Maos.
     */
    protected function normalisasiNamaSampel(string $nama): string
    {
        return trim(preg_replace('/\s*\(?B\d0\)?\s*$/i', '', $nama));
    }
}
