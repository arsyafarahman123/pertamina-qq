<?php

namespace App\Http\Controllers;

use App\Models\ChecklistMtMaos;
use App\Support\ChecklistMtMaosItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistMtMaosController extends Controller
{
    /** Dashboard: Admin/Petugas Lab QQ melihat semua, SPBU hanya melihat checklist miliknya sendiri. */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ChecklistMtMaos::query()->latest('tanggal_periksa')->latest('id');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_polisi', 'like', "%{$search}%")
                  ->orWhere('pemilik', 'like', "%{$search}%");
            });
        }

        $checklists = $query->get();

        $stats = [
            'total' => $checklists->count(),
            'this_week' => $checklists->filter(fn ($c) => $c->tanggal_periksa->diffInDays(now()) <= 7)->count(),
            'flagged' => $checklists->filter(fn ($c) => $c->isFlagged())->count(),
        ];
        $stats['clean'] = $stats['total'] - $stats['flagged'];

        return view('checklist-mt-maos.index', compact('checklists', 'stats'));
    }

    public function create()
    {
        $checklist = new ChecklistMtMaos([
            'tanggal_periksa' => now()->toDateString(),
            'tera' => ChecklistMtMaos::blankTera(),
            'results' => [],
            'notes' => [],
        ]);

        return view('checklist-mt-maos.form', [
            'checklist' => $checklist,
            'items' => ChecklistMtMaosItems::all(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::user()->name;
        $data['status'] = 'final';

        $checklist = ChecklistMtMaos::create($data);

        return redirect()->route('checklist-mt-maos.show', $checklist)->with('sukses', 'Checklist mobil tangki berhasil disimpan.');
    }

    public function show(ChecklistMtMaos $checklist)
    {
        $this->authorizeView($checklist);

        return view('checklist-mt-maos.show', [
            'checklist' => $checklist,
            'flat' => ChecklistMtMaosItems::flat(),
            'grouped' => ChecklistMtMaosItems::all(),
        ]);
    }

    public function cetak(ChecklistMtMaos $checklist)
    {
        $this->authorizeView($checklist);

        return view('checklist-mt-maos.cetak', [
            'checklist' => $checklist,
            'flat' => ChecklistMtMaosItems::flat(),
            'grouped' => ChecklistMtMaosItems::all(),
        ]);
    }

    public function cetakBanyak(Request $request)
    {
        $user = Auth::user();
        $query = ChecklistMtMaos::query()->latest('tanggal_periksa')->latest('id');
        if ($user->isSpbu()) {
            $query->where('pemilik', 'like', '%' . $user->spbu_name . '%');
        }

        if ($idsParam = $request->query('ids')) {
            $ids = is_array($idsParam) ? $idsParam : explode(',', (string) $idsParam);
            $ids = array_filter(array_map('intval', $ids));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        $checklists = $query->get();

        if ($checklists->isEmpty()) {
            return redirect()->route('checklist-mt-maos.index')->with('gagal', 'Tidak ada data checklist yang dipilih.');
        }

        return view('checklist-mt-maos.cetak-banyak', [
            'checklists' => $checklists,
            'flat' => ChecklistMtMaosItems::flat(),
            'grouped' => ChecklistMtMaosItems::all(),
        ]);
    }

    public function edit(ChecklistMtMaos $checklist)
    {
        return view('checklist-mt-maos.form', [
            'checklist' => $checklist,
            'items' => ChecklistMtMaosItems::all(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, ChecklistMtMaos $checklist)
    {
        $checklist->update($this->validated($request));

        return redirect()->route('checklist-mt-maos.show', $checklist)->with('sukses', 'Checklist mobil tangki berhasil diperbarui.');
    }

    public function destroy(ChecklistMtMaos $checklist)
    {
        $checklist->delete();

        return redirect()->route('checklist-mt-maos.index')->with('sukses', 'Checklist #' . $checklist->id . ' berhasil dihapus.');
    }

    /** Payload JSON untuk tombol "Export Excel" (satu checklist). */
    public function exportOne(ChecklistMtMaos $checklist)
    {
        $this->authorizeView($checklist);

        $flat = ChecklistMtMaosItems::flat();
        $grouped = ChecklistMtMaosItems::all();

        return response()->json([
            'filename' => 'Checklist_MT_' . preg_replace('/[^A-Za-z0-9]+/', '', $checklist->nomor_polisi ?: 'Checklist') . '_' . $checklist->tanggal_periksa->format('Y-m-d'),
            'nomor_polisi' => $checklist->nomor_polisi,
            'pemilik' => $checklist->pemilik ?: '-',
            'tanggal' => $checklist->tanggal_periksa->translatedFormat('d F Y'),
            'exp_tera' => $checklist->tanggal_exp ?: '-',
            'created_by' => $checklist->created_by ?: '-',
            'status_label' => $checklist->isFlagged() ? ($checklist->flagCount() . ' Temuan') : 'Sesuai Standar',
            'is_flagged' => $checklist->isFlagged(),
            'tera' => $checklist->tera ?: ChecklistMtMaos::blankTera(),
            'results' => $checklist->results ?: [],
            'notes' => $checklist->notes ?: [],
            'ket_tambahan' => $checklist->ket_tambahan ?: '',
            'items_flat' => $flat,
            'items_grouped' => $grouped,
            'sheets' => [$this->toSheetRows($checklist)],
        ]);
    }

    /**
     * Payload JSON untuk rekap Excel SEMUA armada (satu sheet gabungan,
     * gaya "PEMERIKSAAN MOBIL TANGKI — Fuel Terminal Maos"): tiap checklist
     * yang tersimpan di database otomatis muncul jadi satu blok baris
     * armada berisi daftar Temuan Pemeriksaan (Mandatory/Non Mandatory)
     * + status Kekedapan Mobil Tangki. Data permanen — diambil langsung
     * dari tabel checklist_mt_maos, jadi kalau ada 1000 checklist tersimpan,
     * hasil rekap ini otomatis berisi 1000 blok pemeriksaan armada.
     */
    public function exportAll(Request $request)
    {
        $user = Auth::user();
        $query = ChecklistMtMaos::query()->orderBy('tanggal_periksa')->orderBy('id');
        if ($user->isSpbu()) {
            $query->where('pemilik', 'like', '%' . $user->spbu_name . '%');
        }

        // Filter berdasarkan checklist yang dicentang / dipilih
        if ($idsParam = $request->query('ids')) {
            $ids = is_array($idsParam) ? $idsParam : explode(',', (string) $idsParam);
            $ids = array_filter(array_map('intval', $ids));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        $checklists = $query->get();

        $flat = ChecklistMtMaosItems::flat();

        $armada = $checklists->map(function (ChecklistMtMaos $c) use ($flat) {
            $temuan = [];

            // 1. Cek Masa Sertifikat Tera
            if (($c->results['1'] ?? null) === 'bad' || !empty($c->notes['1'])) {
                $temuan[] = [
                    'label' => 'Masa Sertifikat Tera' . ($c->tanggal_exp ? ' (Exp: ' . $c->tanggal_exp . ')' : ''),
                    'kategori' => 'Mandatory',
                    'disp' => '-',
                    'catatan' => trim($c->notes['1'] ?? '') ?: ($c->tanggal_exp ? 'Exp: ' . $c->tanggal_exp : 'Pemeriksaan masa berlaku sertifikat tera'),
                ];
            }

            // 2. Cek Kompartemen Tera (jika ada catatan khusus atau ditandai)
            if (($c->results['2'] ?? null) === 'bad' || !empty($c->notes['2'])) {
                $temuan[] = [
                    'label' => 'Pengukuran Kompartemen Tangki & Tera',
                    'kategori' => 'Mandatory',
                    'disp' => '-',
                    'catatan' => trim($c->notes['2'] ?? '') ?: 'Pengecekan fisik kompartemen dan segel tera',
                ];
            }

            // 3. Item 3-17 (Kondisi Fisik & Perlengkapan)
            foreach ($flat as $row) {
                $res = $c->results[$row['key']] ?? null;
                $note = trim($c->notes[$row['key']] ?? '');
                if ($res === 'bad') {
                    $temuan[] = [
                        'label' => $row['label'],
                        'kategori' => $row['temuan'] === 'Mayor' ? 'Mandatory' : 'Non Mandatory',
                        'disp' => $row['disp'] ?: '-',
                        'catatan' => $note ?: $row['ket'],
                    ];
                }
            }

            // 4. Keterangan Tambahan jika ada
            foreach ($c->getKetTambahanItems() as $kItem) {
                $temuan[] = [
                    'label' => $kItem,
                    'kategori' => 'Catatan',
                    'disp' => '-',
                    'catatan' => $kItem,
                ];
            }

            // Item 3.1 "Manhole - Packing" = pengecekan rembesan/kebocoran utama -> dipakai
            // sebagai indikator ringkas "Pemeriksaan Kekedapan Mobil Tangki".
            $resKedap = $c->results['3-0'] ?? null;
            $kedap = $resKedap !== 'bad';

            return [
                'nomor_polisi' => $c->nomor_polisi,
                'pemilik' => $c->pemilik ?: '-',
                'tanggal' => $c->tanggal_periksa->translatedFormat('d F Y'),
                'exp_tera' => $c->tanggal_exp ?: '-',
                'temuan' => $temuan,
                'kedap' => $kedap,
                'ket_tambahan' => $c->ket_tambahan ?: '',
            ];
        });

        return response()->json([
            'filename' => 'Rekap_Pemeriksaan_Mobil_Tangki_' . now()->format('Y-m-d'),
            'judul' => 'PEMERIKSAAN MOBIL TANGKI',
            'subjudul' => 'Fuel Terminal Maos',
            'armada' => $armada,
        ]);
    }

    private function toSheetRows(ChecklistMtMaos $checklist): array
    {
        $rows = [];
        $rows[] = ['FORM PEMERIKSAAN MOBIL TANGKI — FUEL TERMINAL MAOS'];
        $rows[] = ['Nomor Polisi', $checklist->nomor_polisi, 'Pemilik', $checklist->pemilik, 'Tanggal', (string) $checklist->tanggal_periksa->format('Y-m-d'), 'Exp Tera', $checklist->tanggal_exp ?: '-'];
        $rows[] = [];
        $rows[] = ['No', 'Item Pemeriksaan', 'Kategori', 'Dispensasi', 'Hasil', 'Catatan / Keterangan'];

        // Baris 1: Masa Tera
        $res1 = $checklist->results['1'] ?? null;
        $note1 = trim($checklist->notes['1'] ?? '');
        $rows[] = [
            '1', 'Masa Sertifikat Tera', 'Mayor', '-',
            $res1 === 'ok' ? 'Sesuai' : ($res1 === 'bad' ? 'Temuan' : ($checklist->tanggal_exp ? 'Sesuai' : '-')),
            ($checklist->tanggal_exp ? 'Exp: ' . $checklist->tanggal_exp : 'Pengecekan masa tera') . ($note1 ? ' | Catatan: ' . $note1 : ''),
        ];

        // Baris 2: Kompartemen 1-4
        $teraList = $checklist->tera ?: ChecklistMtMaos::blankTera();
        foreach ($teraList as $idx => $t) {
            $kompNo = $t['komp'] ?? ($idx + 1);
            $teraRingkas = sprintf(
                'T2 Tera: %s, T2 Act: %s, Selisih: %s, Dudukan: %s, Volume: %s, Ijk/Segel: %s',
                $t['tinggiTera'] ?: ($t['a'] ?? '-'),
                $t['tinggiAct'] ?: ($t['b'] ?? '-'),
                $t['selisih'] ?: ($t['c'] ?? '-'),
                $t['duduk'] ?: ($t['d'] ?? '-'),
                $t['volume'] ?: ($t['e'] ?? '-'),
                $t['ijkBaut'] ?: ($t['f'] ?? '-')
            );
            $rows[] = [
                '2.' . $kompNo,
                'Kompartemen ' . $kompNo . ' (Pengukuran & Tera)',
                'Mayor',
                '-',
                'Tercatat',
                $teraRingkas,
            ];
        }

        // Baris 3-17: Item Fisik
        foreach (ChecklistMtMaosItems::flat() as $row) {
            $res = $checklist->results[$row['key']] ?? null;
            $catatanTambahan = trim($checklist->notes[$row['key']] ?? '');
            $keterangan = $row['ket'] . ($catatanTambahan ? ' | CATATAN KHUSUS: ' . $catatanTambahan : '');

            $rows[] = [
                $row['no'],
                $row['label'],
                $row['temuan'],
                $row['disp'],
                $res === 'ok' ? 'Sesuai' : ($res === 'bad' ? 'Temuan' : '-'),
                $keterangan,
            ];
        }

        $rows[] = [];
        $rows[] = ['Keterangan Tambahan', $checklist->ket_tambahan ?: '-'];

        return [
            'name' => \Illuminate\Support\Str::limit(preg_replace('/[\\\\\/\?\*\[\]:]/', '', $checklist->nomor_polisi ?: 'Checklist'), 28, ''),
            'rows' => $rows,
        ];
    }

    private function authorizeView(ChecklistMtMaos $checklist): void
    {
        $user = Auth::user();
        if ($user->isSpbu() && !str_contains(strtolower($checklist->pemilik ?? ''), strtolower($user->spbu_name ?? '__none__'))) {
            abort(403, 'Anda tidak memiliki akses untuk melihat checklist ini.');
        }
    }

    public function exportJsonBackup()
    {
        $all = ChecklistMtMaos::orderBy('tanggal_periksa', 'asc')->orderBy('id', 'asc')->get();
        return response()->json($all, 200, ['Content-Disposition' => 'inline; filename="checklists_backup.json"'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function runImportSeed()
    {
        $jsonPath = base_path('checklists_seed.json');
        if (!\Illuminate\Support\Facades\File::exists($jsonPath)) {
            return response()->json(['status' => 'error', 'message' => 'checklists_seed.json not found'], 404);
        }

        $records = json_decode(\Illuminate\Support\Facades\File::get($jsonPath), true);
        if (!$records) {
            return response()->json(['status' => 'error', 'message' => 'failed to decode json'], 500);
        }

        $count = 0;
        foreach ($records as $item) {
            $existing = ChecklistMtMaos::where('nomor_polisi', $item['nomor_polisi'])
                ->where('tanggal_periksa', $item['tanggal_periksa'])
                ->first();

            if (!$existing) {
                ChecklistMtMaos::create($item);
                $count++;
            } else {
                $existing->update($item);
                $count++;
            }
        }

        return response()->json([
            'status' => 'ok',
            'message' => "Berhasil mengimpor / memperbarui {$count} data Checklist Mobil Tangki ke database!",
            'total_imported' => $count,
            'total_in_db' => ChecklistMtMaos::count(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nomor_polisi' => ['required', 'string', 'max:20'],
            'pemilik' => ['nullable', 'string', 'max:150'],
            'tanggal_exp' => ['nullable', 'string', 'max:50'],
            'tanggal_periksa' => ['required', 'date'],
            'tera' => ['required', 'array'],
            'results' => ['nullable', 'array'],
            'notes' => ['nullable', 'array'],
            'ket_tambahan' => ['nullable', 'string'],
        ]);
    }
}

