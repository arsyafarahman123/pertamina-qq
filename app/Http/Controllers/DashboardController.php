<?php

namespace App\Http\Controllers;

use App\Models\ChecklistMtMaos;
use App\Models\HasilUji;
use App\Models\JenisUji;
use App\Models\RetainSampelMt;
use App\Services\SpecEngine;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalJenisUji = JenisUji::where('aktif', true)->count();
        $totalHasilHariIni = HasilUji::whereDate('waktu_uji', today())->count();
        $totalHasilBulanIni = HasilUji::whereMonth('waktu_uji', now()->month)
            ->whereYear('waktu_uji', now()->year)
            ->count();

        $rekapPerJenis = JenisUji::where('kode', '!=', 'UJI_BBM_SPEK')
            ->withCount([
                'hasilUji as jumlah_bulan_ini' => function ($q) {
                    $q->whereMonth('waktu_uji', now()->month)
                      ->whereYear('waktu_uji', now()->year);
                },
            ])->orderBy('urutan')->get();

        $aktivitasTerbaru = HasilUji::with(['jenisUji', 'user'])
            ->latest('waktu_uji')
            ->limit(8)
            ->get();

        // ---- Smart Spec Engine: hitung verdict untuk semua hasil uji bulan ini ----
        $hasilBulanIni = HasilUji::whereMonth('waktu_uji', now()->month)
            ->whereYear('waktu_uji', now()->year)
            ->get();

        $verdictCounts = ['PASS' => 0, 'FAIL' => 0, 'MARGINAL' => 0];
        $hasilTerlabel = [];
        $seriTrend = [];
        $offSpec = [];

        foreach ($hasilBulanIni as $item) {
            $evaluasi = SpecEngine::evaluate($item->data_hasil, $item->nama_sampel);
            $item->verdict = $evaluasi['verdict'];
            $item->evaluasi = $evaluasi;

            if (isset($verdictCounts[$evaluasi['verdict']])) {
                $verdictCounts[$evaluasi['verdict']]++;
            }

            $hasilTerlabel[] = $item;

            if (in_array($evaluasi['verdict'], [SpecEngine::FAIL, SpecEngine::MARGINAL], true)) {
                $offSpec[] = $item;
            }

            $bulan = $item->waktu_uji->format('Y-m-d');
            $seriTrend[$bulan][] = $evaluasi['verdict'];
        }

        $totalDinilai = $verdictCounts['PASS'] + $verdictCounts['FAIL'] + $verdictCounts['MARGINAL'];
        $persenOnSpec = $totalDinilai > 0
            ? round($verdictCounts['PASS'] / $totalDinilai * 100, 1)
            : 0;

        // ---- Data trend untuk chart ----
        ksort($seriTrend);
        $trendLabels = array_keys($seriTrend);
        $trendPass = [];
        $trendFail = [];
        $trendMarginal = [];

        foreach ($seriTrend as $counts) {
            $tally = array_count_values($counts);
            $trendPass[] = $tally['PASS'] ?? 0;
            $trendFail[] = $tally['FAIL'] ?? 0;
            $trendMarginal[] = $tally['MARGINAL'] ?? 0;
        }

        // ---- Rekap khusus "Uji Spesifikasi BBM" (wizard kesesuaian mutu per produk) ----
        $bbmRekap = ['gasoline' => [], 'gasoil' => []];
        foreach (['gasoline', 'gasoil'] as $kat) {
            foreach (config("spesifikasi_bbm.{$kat}", []) as $key => $conf) {
                $bbmRekap[$kat][$key] = [
                    'nama' => $conf['nama'],
                    'jumlah' => 0,
                    'pass' => 0,
                    'fail' => 0,
                    'terakhir' => null,
                    'terakhir_waktu' => null,
                ];
            }
        }

        $hasilBbmBulanIni = HasilUji::whereHas('jenisUji', fn ($q) => $q->where('kode', 'UJI_BBM_SPEK'))
            ->whereMonth('waktu_uji', now()->month)
            ->whereYear('waktu_uji', now()->year)
            ->latest('waktu_uji')
            ->get();

        foreach ($hasilBbmBulanIni as $item) {
            $kat = $item->data_hasil['_kategori'] ?? null;
            $key = $item->data_hasil['_jenis_key'] ?? null;
            if (! $kat || ! $key || ! isset($bbmRekap[$kat][$key])) {
                continue;
            }

            $verdict = $item->data_hasil['_verdict'] ?? SpecEngine::evaluate($item->data_hasil, $item->nama_sampel)['verdict'];

            $bbmRekap[$kat][$key]['jumlah']++;
            if ($verdict === SpecEngine::PASS) {
                $bbmRekap[$kat][$key]['pass']++;
            } elseif ($verdict === SpecEngine::FAIL) {
                $bbmRekap[$kat][$key]['fail']++;
            }

            if (! $bbmRekap[$kat][$key]['terakhir']) {
                $bbmRekap[$kat][$key]['terakhir'] = $verdict; // sudah diurutkan latest() -> data pertama = terbaru
                $bbmRekap[$kat][$key]['terakhir_waktu'] = $item->waktu_uji;
            }
        }

        $totalBbmBulanIni = $hasilBbmBulanIni->count();
        $totalBbmPass = $hasilBbmBulanIni->filter(fn ($i) => ($i->data_hasil['_verdict'] ?? null) === SpecEngine::PASS)->count();
        $persenBbmOnSpec = $totalBbmBulanIni > 0 ? round($totalBbmPass / $totalBbmBulanIni * 100, 1) : 0;

        // ---- Ringkasan "Modul Operasional" (gaya S&D One) — Penerimaan (Retain
        // Sampel MT) & Penyaluran (Checklist Mobil Tangki) pakai data ASLI dari
        // sistem ini. Modul yang belum dibangun (Penyimpanan/HSSE/Buku
        // Tamu/MWT Online) ditandai jelas "belum tersedia", BUKAN dikarang.
        $retainBulanIni = RetainSampelMt::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->get();
        $modulPenerimaan = [
            'total' => $retainBulanIni->count(),
            'ada_foto' => $retainBulanIni->whereNotNull('foto_path')->count(),
            'belum_foto' => $retainBulanIni->whereNull('foto_path')->count(),
            'total_semua' => RetainSampelMt::count(),
        ];

        $checklistQuery = ChecklistMtMaos::query();
        if (Auth::user() && Auth::user()->isSpbu()) {
            $checklistQuery->where('pemilik', 'like', '%'.Auth::user()->spbu_name.'%');
        }
        $checklistSemua = $checklistQuery->get();
        $modulPenyaluran = [
            'total' => $checklistSemua->count(),
            'sesuai' => $checklistSemua->filter(fn ($c) => ! $c->isFlagged())->count(),
            'temuan' => $checklistSemua->filter(fn ($c) => $c->isFlagged())->count(),
            '7_hari' => $checklistSemua->filter(fn ($c) => $c->tanggal_periksa->diffInDays(now()) <= 7)->count(),
        ];

        return view('dashboard.index', compact(
            'totalJenisUji', 'totalHasilHariIni', 'totalHasilBulanIni',
            'rekapPerJenis', 'aktivitasTerbaru',
            'verdictCounts', 'persenOnSpec', 'offSpec', 'hasilTerlabel',
            'trendLabels', 'trendPass', 'trendFail', 'trendMarginal',
            'bbmRekap', 'totalBbmBulanIni', 'totalBbmPass', 'persenBbmOnSpec',
            'modulPenerimaan', 'modulPenyaluran'
        ));
    }
}
