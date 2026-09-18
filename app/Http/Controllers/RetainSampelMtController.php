<?php

namespace App\Http\Controllers;

use App\Models\RetainSampelFoto;
use App\Models\RetainSampelMt;
use App\Services\DensityCorrectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Rekap harian "Sampel Penyaluran MT — FT MAOS".
 *
 * Alur otomatis: petugas cuma input Density Obs + Suhu (+ Nopol MT, Produk,
 * Tangki Timbun, opsional Foto botol) -> sistem otomatis menghitung
 * Density'15 (tidak perlu cari manual di Tabel ASTM 53 lagi) -> tersimpan
 * PERMANEN dengan jejak waktu input (created_at) -> langsung terekap per
 * tanggal & per jam (06.00 / 12.00 / 18.00), TIGA JAM SEJAJAR dalam satu
 * tampilan laporan harian — bisa langsung screenshot untuk broadcast.
 *
 * "Waktu observasi" (tanggal + jam_label) BEDA dengan "waktu input"
 * (created_at) — tanggal/jam observasi diisi manual sesuai kapan sampel
 * diambil, sedangkan waktu input dicatat otomatis oleh sistem supaya
 * ketahuan data itu benar-benar dimasukkan kapan (anti-backdate/klaim).
 */
class RetainSampelMtController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());

        $rekap = $this->ambilRekap($tanggal);
        $entriesByJam = $this->ambilEntriesByJam($tanggal);
        $fotoSesi = $this->ambilFotoSesi($tanggal);

        $tanggalTersedia = RetainSampelMt::selectRaw('tanggal')
            ->distinct()
            ->orderByDesc('tanggal')
            ->limit(30)
            ->pluck('tanggal');

        $totalDataKeseluruhan = RetainSampelMt::count();

        return view('retain-sampel.index', [
            'tanggal' => $tanggal,
            'rekap' => $rekap,
            'entriesByJam' => $entriesByJam,
            'fotoSesi' => $fotoSesi,
            'produkList' => RetainSampelMt::daftarProduk(),
            'jamStandar' => RetainSampelMt::jamStandar(),
            'tanggalTersedia' => $tanggalTersedia,
            'totalDataKeseluruhan' => $totalDataKeseluruhan,
        ]);
    }

    /**
     * Ambil rekap 1 tanggal dalam bentuk pivot [jam_label => [produk => entry]].
     * Semua jam (06:00, 12:00, 18:00) masuk di sini — tidak ada lagi pemisahan
     * "retain" vs "penyaluran".
     */
    protected function ambilRekap(string $tanggal): array
    {
        $entries = RetainSampelMt::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_label')
            ->orderBy('id')
            ->get();

        $rekap = [];
        foreach ($entries as $e) {
            $rekap[$e->jam_label][$e->produk] = $e;
        }
        ksort($rekap);

        return $rekap;
    }

    /**
     * Ambil seluruh entries 1 tanggal dikelompokkan per jam_label (koleksi lengkap,
     * mendukung multiple sampel untuk produk yang sama seperti 2x Pertalite).
     */
    protected function ambilEntriesByJam(string $tanggal)
    {
        return RetainSampelMt::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_label')
            ->orderBy('id')
            ->get()
            ->groupBy('jam_label');
    }

    /**
     * Foto "Sampel Penyaluran" per jam untuk 1 tanggal, dalam bentuk
     * [jam_label => RetainSampelFoto].
     */
    protected function ambilFotoSesi(string $tanggal): array
    {
        return RetainSampelFoto::whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('jam_label')
            ->all();
    }

    /**
     * Simpan/ganti foto sesi per jam — satu foto berlaku untuk SEMUA produk di
     * jam itu, bukan per baris produk.
     */
    public function simpanFotoSesi(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jam_label' => 'required|string|max:20',
            'foto' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $path = $request->file('foto')->store('retain-sampel/sesi', 'public');

        $lama = RetainSampelFoto::whereDate('tanggal', $data['tanggal'])
            ->where('jam_label', $data['jam_label'])
            ->first();
        if ($lama) {
            Storage::disk('public')->delete($lama->path);
            $lama->update(['path' => $path]);
        } else {
            RetainSampelFoto::create([
                'tanggal' => $data['tanggal'],
                'jam_label' => $data['jam_label'],
                'path' => $path,
            ]);
        }

        $label = "Sampel Penyaluran pukul {$data['jam_label']}";

        return redirect()
            ->back()
            ->with('success', "Foto {$label} tersimpan.");
    }

    /** Tampilkan foto sesi langsung dari storage/app/public (tanpa symlink). */
    public function fotoSesi(RetainSampelFoto $fotoSesi)
    {
        abort_unless(Storage::disk('public')->exists($fotoSesi->path), 404);

        return Storage::disk('public')->response($fotoSesi->path);
    }

    /** Halaman cetak/unduh — 3 kolom sejajar (06:00 | 12:00 | 18:00), lengkap foto per sesi. */
    public function cetak(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());

        return view('retain-sampel.cetak', [
            'tanggal' => $tanggal,
            'rekap' => $this->ambilRekap($tanggal),
            'fotoSesi' => $this->ambilFotoSesi($tanggal),
            'produkList' => RetainSampelMt::daftarProduk(),
        ]);
    }

    /** Unduh rekap 1 tanggal sebagai Excel (.xls). */
    public function exportExcel(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->toDateString());
        $rekap = $this->ambilRekap($tanggal);
        $produkList = RetainSampelMt::daftarProduk();

        $namaFile = 'Sampel-Penyaluran-MT-FT-MAOS-'.$tanggal.'.xls';

        $html = view('retain-sampel.export-excel', compact('tanggal', 'rekap', 'produkList'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$namaFile.'"',
        ]);
    }

    /**
     * Unduh SELURUH riwayat data (semua tanggal, semua jam) sebagai Excel.
     */
    public function exportExcelAll()
    {
        $semua = RetainSampelMt::with('user')
            ->orderByDesc('tanggal')
            ->orderBy('jam_label')
            ->get()
            ->groupBy(fn ($e) => $e->tanggal->toDateString().'|'.$e->jam_label);

        $totalData = RetainSampelMt::count();

        $namaFile = 'Sampel-Penyaluran-MT-FT-MAOS-Riwayat-Lengkap-'.now()->format('Ymd-His').'.xls';

        $html = view('retain-sampel.export-excel-all', compact('semua', 'totalData'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$namaFile.'"',
        ]);
    }

    /**
     * Rekap SEMUA tanggal sekaligus dalam 1 halaman.
     */
    public function semua(Request $request)
    {
        $urutan = $request->get('urutan', 'desc') === 'asc' ? 'asc' : 'desc';

        $entries = RetainSampelMt::with('user')
            ->orderBy('tanggal', $urutan)
            ->orderBy('jam_label')
            ->get();

        // Pivot 2 tingkat: [tanggal => [jam_label => [produk => entry]]]
        $rekapSemua = [];
        foreach ($entries as $e) {
            $tgl = $e->tanggal->toDateString();
            $rekapSemua[$tgl][$e->jam_label][$e->produk] = $e;
        }
        $urutan === 'asc' ? ksort($rekapSemua) : krsort($rekapSemua);
        foreach ($rekapSemua as &$perTanggal) {
            ksort($perTanggal);
        }
        unset($perTanggal);

        return view('retain-sampel.semua', [
            'rekapSemua' => $rekapSemua,
            'produkList' => RetainSampelMt::daftarProduk(),
            'totalDataKeseluruhan' => RetainSampelMt::count(),
            'urutan' => $urutan,
        ]);
    }

    public function create(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $jamLabel = $request->query('jam_label', '06:00');

        return view('retain-sampel.form', [
            'produkList' => RetainSampelMt::daftarProduk(),
            'jamStandar' => RetainSampelMt::jamStandar(),
            'entry' => null,
            'defaultTanggal' => $tanggal,
            'defaultJamLabel' => $jamLabel,
        ]);
    }

    /** Endpoint AJAX ringan: hitung preview Density'15 sebelum disimpan. */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'density_obs' => 'required|numeric|min:0.0001',
            'temperatur' => 'required|numeric',
        ]);

        $density15 = DensityCorrectionService::hitungDensity15(
            (float) $data['density_obs'],
            (float) $data['temperatur']
        );

        return response()->json(['density_15' => number_format($density15, 4, '.', '')]);
    }

    /** Simpan file foto (kalau diupload) & kembalikan path-nya. */
    protected function simpanFoto(Request $request): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        return $request->file('foto')->store('retain-sampel', 'public');
    }

    /**
     * Tampilkan/unduh file foto langsung dari storage/app/public.
     */
    public function foto(RetainSampelMt $retainSampel)
    {
        abort_unless($retainSampel->foto_path, 404);
        abort_unless(Storage::disk('public')->exists($retainSampel->foto_path), 404);

        return Storage::disk('public')->response($retainSampel->foto_path);
    }

    public function store(Request $request)
    {
        // Mendukung input multi-produk sekaligus (array of items)
        if ($request->has('items') && is_array($request->input('items'))) {
            $request->validate([
                'tanggal' => 'required|date',
                'jam_label' => 'required|string|max:20',
                'items' => 'required|array|min:1',
                'items.*.produk' => 'required|string|max:50',
                'items.*.mt_nopol' => 'nullable|string|max:50',
                'items.*.tangki_timbun' => 'nullable|string|max:50',
                'items.*.density_obs' => 'nullable|numeric|min:0.0001',
                'items.*.temperatur' => 'nullable|numeric',
                'items.*.foto' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            ]);

            $tanggal = $request->input('tanggal');
            $jamLabel = $request->input('jam_label');
            $items = $request->input('items', []);
            $files = $request->file('items', []);
            $savedCount = 0;
            $produkNames = [];

            foreach ($items as $idx => $item) {
                // Lewati baris yang density_obs atau temperatur-nya kosong (tidak diisi user)
                if (empty($item['produk']) || !isset($item['density_obs']) || $item['density_obs'] === '' || !isset($item['temperatur']) || $item['temperatur'] === '') {
                    continue;
                }

                $density15 = DensityCorrectionService::hitungDensity15(
                    (float) $item['density_obs'],
                    (float) $item['temperatur']
                );

                $data = [
                    'tanggal' => $tanggal,
                    'jam_label' => $jamLabel,
                    'produk' => $item['produk'],
                    'mt_nopol' => $item['mt_nopol'] ?? null,
                    'tangki_timbun' => $item['tangki_timbun'] ?? null,
                    'density_obs' => $item['density_obs'],
                    'temperatur' => $item['temperatur'],
                    'density_15' => $density15,
                    'user_id' => Auth::id(),
                ];

                if (isset($files[$idx]['foto']) && $files[$idx]['foto']->isValid()) {
                    $data['foto_path'] = $files[$idx]['foto']->store('retain-sampel', 'public');
                }

                if (!empty($item['id'])) {
                    $existing = RetainSampelMt::find($item['id']);
                    if ($existing) {
                        if (isset($data['foto_path']) && $existing->foto_path) {
                            Storage::disk('public')->delete($existing->foto_path);
                        }
                        $existing->update($data);
                        $savedCount++;
                        $produkNames[] = $item['produk'];
                        continue;
                    }
                }

                RetainSampelMt::create($data);
                $savedCount++;
                $produkNames[] = $item['produk'];
            }

            if ($savedCount === 0) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['items' => 'Silakan isi setidaknya satu baris produk (Density Obs & Temperatur) sebelum menyimpan.']);
            }

            $tglIndo = \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('d M Y');
            $daftarStr = implode(', ', $produkNames);
            return redirect()
                ->route('retain-sampel.index', ['tanggal' => $tanggal])
                ->with('success', "Berhasil menyimpan {$savedCount} produk retain sampel ({$daftarStr}) untuk {$tglIndo} pukul {$jamLabel} WIB. Density'15 otomatis dihitung sesuai ASTM Table 53B.");
        }

        // Single entry fallback
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jam_label' => 'required|string|max:20',
            'produk' => 'required|string|max:50',
            'mt_nopol' => 'nullable|string|max:50',
            'tangki_timbun' => 'nullable|string|max:50',
            'density_obs' => 'required|numeric|min:0.0001',
            'temperatur' => 'required|numeric',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $data['density_15'] = DensityCorrectionService::hitungDensity15(
            (float) $data['density_obs'],
            (float) $data['temperatur']
        );
        $data['user_id'] = Auth::id();

        $fotoBaru = $this->simpanFoto($request);
        if ($fotoBaru) {
            $data['foto_path'] = $fotoBaru;
        }
        unset($data['foto']);

        $entry = RetainSampelMt::create($data);

        return redirect()
            ->route('retain-sampel.index', ['tanggal' => $data['tanggal']])
            ->with('success', "Rekap {$data['produk']} jam {$data['jam_label']} tersimpan (waktu input: {$entry->created_at->translatedFormat('d M Y, H:i')} WIB). Density'15 otomatis: {$data['density_15']}.");
    }

    public function edit(RetainSampelMt $retainSampel)
    {
        return view('retain-sampel.form', [
            'produkList' => RetainSampelMt::daftarProduk(),
            'jamStandar' => RetainSampelMt::jamStandar(),
            'entry' => $retainSampel,
        ]);
    }

    public function update(Request $request, RetainSampelMt $retainSampel)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jam_label' => 'required|string|max:20',
            'produk' => 'required|string|max:50',
            'mt_nopol' => 'nullable|string|max:50',
            'tangki_timbun' => 'nullable|string|max:50',
            'density_obs' => 'required|numeric|min:0.0001',
            'temperatur' => 'required|numeric',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $data['density_15'] = DensityCorrectionService::hitungDensity15(
            (float) $data['density_obs'],
            (float) $data['temperatur']
        );

        $fotoBaru = $this->simpanFoto($request);
        if ($fotoBaru) {
            if ($retainSampel->foto_path) {
                Storage::disk('public')->delete($retainSampel->foto_path);
            }
            $data['foto_path'] = $fotoBaru;
        }
        unset($data['foto']);

        $retainSampel->update($data);

        return redirect()
            ->route('retain-sampel.index', ['tanggal' => $data['tanggal']])
            ->with('success', "Rekap diperbarui. Density'15 otomatis: {$data['density_15']}.");
    }

    public function destroy(RetainSampelMt $retainSampel)
    {
        $tanggal = $retainSampel->tanggal->toDateString();
        if ($retainSampel->foto_path) {
            Storage::disk('public')->delete($retainSampel->foto_path);
        }
        $retainSampel->delete();

        return redirect()
            ->route('retain-sampel.index', ['tanggal' => $tanggal])
            ->with('success', 'Data rekap dihapus.');
    }
}