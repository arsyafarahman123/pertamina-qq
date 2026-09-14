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

        $query = ChecklistMtMaos::query()->latest('tanggal_periksa');

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

    /** Payload JSON untuk tombol "Export Excel" (SheetJS, satu checklist). */
    public function exportOne(ChecklistMtMaos $checklist)
    {
        $this->authorizeView($checklist);

        return response()->json([
            'filename' => 'Checklist_MT_' . $checklist->nomor_polisi . '_' . $checklist->tanggal_periksa->format('Y-m-d'),
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
        $checklists = $query->get();

        $flat = ChecklistMtMaosItems::flat();

        $armada = $checklists->map(function (ChecklistMtMaos $c) use ($flat) {
            $temuan = [];
            foreach ($flat as $row) {
                $res = $c->results[$row['key']] ?? null;
                if ($res === 'bad') {
                    $temuan[] = [
                        'label' => $row['label'],
                        'kategori' => $row['temuan'] === 'Mayor' ? 'Mandatory' : 'Non Mandatory',
                        'disp' => $row['disp'],
                        'catatan' => trim($c->notes[$row['key']] ?? ''),
                    ];
                }
            }

            // Item 3.1 "Manhole - Packing" = pengecekan rembesan/kebocoran utama -> dipakai
            // sebagai indikator ringkas "Pemeriksaan Kekedapan Mobil Tangki".
            $resKedap = $c->results['3-0'] ?? null;
            $kedap = $resKedap !== 'bad';

            return [
                'nomor_polisi' => $c->nomor_polisi,
                'pemilik' => $c->pemilik ?: '-',
                'tanggal' => $c->tanggal_periksa->translatedFormat('d F Y'),
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
        $rows[] = ['Nomor Polisi', $checklist->nomor_polisi, 'Pemilik', $checklist->pemilik, 'Tanggal', (string) $checklist->tanggal_periksa->format('Y-m-d'), 'Exp Tera', $checklist->tanggal_exp];
        $rows[] = [];
        $rows[] = ['No', 'Item', 'Temuan', 'Dispensasi', 'Hasil', 'Keterangan'];

        foreach (ChecklistMtMaosItems::flat() as $row) {
            $res = $checklist->results[$row['key']] ?? null;
            $catatanTambahan = trim($checklist->notes[$row['key']] ?? '');
            $keterangan = $row['ket'] . ($catatanTambahan ? ' | Catatan: ' . $catatanTambahan : '');

            $rows[] = [
                $row['no'], $row['label'], $row['temuan'], $row['disp'],
                $res === 'ok' ? 'Sesuai' : ($res === 'bad' ? 'Temuan' : '-'),
                $keterangan,
            ];
        }

        $rows[] = [];
        $rows[] = ['Keterangan Tambahan', $checklist->ket_tambahan];

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
