<?php

namespace App\Http\Controllers;

use App\Models\HasilUji;
use App\Models\JenisUji;
use App\Services\ImageProcessor;
use App\Services\SpecEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = HasilUji::with(['jenisUji', 'user']);

        if ($request->filled('jenis_uji_id')) {
            $query->where('jenis_uji_id', $request->jenis_uji_id);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_sampel', 'like', "%{$cari}%")
                  ->orWhere('nomor_kkw', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('waktu_uji', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('waktu_uji', '<=', $request->sampai_tanggal);
        }

        $daftarJenisUji = JenisUji::orderBy('urutan')->get();

        // ---- Smart Spec Engine: hitung ringkasan verdict atas SELURUH data terfilter ----
        $ringkasan = ['pass' => 0, 'marginal' => 0, 'fail' => 0];
        (clone $query)->get(['id', 'data_hasil', 'nama_sampel'])->each(function ($item) use (&$ringkasan) {
            $verdict = SpecEngine::evaluate($item->data_hasil, $item->nama_sampel)['verdict'];
            match ($verdict) {
                SpecEngine::PASS => $ringkasan['pass']++,
                SpecEngine::MARGINAL => $ringkasan['marginal']++,
                SpecEngine::FAIL => $ringkasan['fail']++,
                default => null,
            };
        });

        $riwayat = $query->latest('waktu_uji')->paginate(15)->withQueryString();

        // ---- Labeli setiap baris halaman ini dengan verdict ----
        foreach ($riwayat as $item) {
            $evaluasi = SpecEngine::evaluate($item->data_hasil, $item->nama_sampel);
            $item->verdict = $evaluasi['verdict'];
        }

        return view('riwayat.index', compact('riwayat', 'daftarJenisUji', 'ringkasan'));
    }

    public function show(HasilUji $hasilUji)
    {
        $hasilUji->load(['jenisUji.langkahSop', 'user']);

        $evaluasi = SpecEngine::evaluate($hasilUji->data_hasil, $hasilUji->nama_sampel);

        return view('riwayat.show', compact('hasilUji', 'evaluasi'));
    }

    public function cetak(HasilUji $hasilUji)
    {
        $hasilUji->load(['jenisUji.langkahSop', 'user']);

        $evaluasi = SpecEngine::evaluate($hasilUji->data_hasil, $hasilUji->nama_sampel);

        return view('riwayat.cetak', compact('hasilUji', 'evaluasi'));
    }

    /**
     * Serve foto bukti langsung dari storage/app/public (tanpa symlink).
     * InfinityFree dan banyak shared hosting tidak mendukung symlink.
     */
    public function fotoBukti(HasilUji $hasilUji)
    {
        abort_unless($hasilUji->foto_bukti, 404);
        abort_unless(Storage::disk('public')->exists($hasilUji->foto_bukti), 404);

        return Storage::disk('public')->response($hasilUji->foto_bukti);
    }

    public function edit(HasilUji $hasilUji)
    {
        $hasilUji->load('jenisUji');
        $fieldConfig = $hasilUji->jenisUji->fieldConfig();
        $daftarSampel = config('uji.sampel_umum');

        return view('riwayat.edit', compact('hasilUji', 'fieldConfig', 'daftarSampel'));
    }

    public function update(Request $request, HasilUji $hasilUji)
    {
        $request->validate([
            'nama_sampel' => ['required', 'string', 'max:100'],
            'nomor_kkw' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
            'data_hasil' => ['required', 'array'],
            'foto_bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,bmp,pdf,doc,docx,xls,xlsx,csv,txt', 'max:10240'],
        ]);

        $data = [
            'nama_sampel' => $request->nama_sampel,
            'nomor_kkw' => $request->nomor_kkw,
            'data_hasil' => $request->data_hasil,
            'catatan' => $request->catatan,
        ];

        if ($request->hasFile('foto_bukti')) {
            if ($hasilUji->foto_bukti) {
                Storage::disk('public')->delete($hasilUji->foto_bukti);
            }
            $data['foto_bukti'] = $request->file('foto_bukti')->store('bukti-uji', 'public');
            ImageProcessor::normalisasi(Storage::disk('public')->path($data['foto_bukti']));
        }

        $hasilUji->update($data);

        return redirect()
            ->route('riwayat.show', $hasilUji)
            ->with('sukses', 'Hasil uji #' . $hasilUji->id . ' berhasil diperbarui.');
    }

    public function destroy(HasilUji $hasilUji)
    {
        if ($hasilUji->foto_bukti) {
            Storage::disk('public')->delete($hasilUji->foto_bukti);
        }

        $hasilUji->delete();

        return redirect()
            ->route('riwayat.index')
            ->with('sukses', 'Hasil uji #' . $hasilUji->id . ' berhasil dihapus.');
    }
}
