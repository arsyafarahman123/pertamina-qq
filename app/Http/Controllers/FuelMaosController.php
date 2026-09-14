<?php

namespace App\Http\Controllers;

use App\Models\HasilUji;
use App\Services\FuelMaosService;
use App\Services\SpecEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class FuelMaosController extends Controller
{
    /**
     * Halaman khusus "Asisten Cek Hasil Uji" — chat full page + ringkasan statistik.
     */
    public function halaman()
    {
        $hariIni = HasilUji::whereDate('waktu_uji', now()->toDateString())->get();

        $ringkasan = ['PASS' => 0, 'FAIL' => 0, 'MARGINAL' => 0];
        foreach ($hariIni as $h) {
            $verdict = SpecEngine::evaluate($h->data_hasil, $h->nama_sampel)['verdict'];
            if (isset($ringkasan[$verdict])) {
                $ringkasan[$verdict]++;
            }
        }

        return view('asisten.index', [
            'totalHariIni' => $hariIni->count(),
            'ringkasan' => $ringkasan,
        ]);
    }

    public function chat(Request $request): JsonResponse
    {
        $pesan = (string) $request->input('pesan', '');

        if (trim($pesan) === '') {
            return response()->json([
                'balasan' => 'Tulis pertanyaan dulu ya. 👋',
                'saran' => ['Bantuan', 'SOP Flash Point', 'Batas spesifikasi Solar'],
            ]);
        }

        $jawaban = (new FuelMaosService())->answer($pesan);

        return response()->json([
            'balasan' => $jawaban['answer'],
            'saran' => $jawaban['saran'] ?? [],
            'items' => $jawaban['items'] ?? [],
            'total' => $jawaban['total'] ?? null,
            'lihatSemuaUrl' => $jawaban['lihatSemuaUrl'] ?? null,
        ]);
    }
}
