<?php

namespace App\Services;

use App\Models\HasilUji;
use App\Models\RetainSampelMt;
use App\Models\ChecklistMtMaos;
use App\Support\ChecklistMtMaosItems;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Fuel Maos — Asisten Cerdas Virtual Laboratorium Quality Control (QC) & Sistem Operasional
 * FT Maos (PT Pertamina Patra Niaga).
 *
 * Mampu menjawab SEMUA pertanyaan seputar:
 * 1. Modul Checklist Lapangan:
 *    • Checklist MT Maos (Inspeksi fisik, 4 kompartemen, sertifikat tera, temuan mayor & minor, laik jalan).
 *    • Retain Sampel Penyaluran / Re-tank (06.00, 12.00, 18.00 WIB, Visual 3 MT, Tangki Timbun, ASTM 53B).
 * 2. Modul Pengujian BBM Terpadu & Kesesuaian Spesifikasi Dirjen Migas (8 produk BBM).
 * 3. SOP 9 Stasiun Laboratorium (Flash Point, Density, Distilasi, Viskositas, Water Content, TAN, RON, Sulfur, Colorimeter).
 * 4. Fitur Dashboard, Riwayat Pengujian, Sertifikat Mutu (CoQ), Cetak PDF, Export Excel, Akun & Hak Akses.
 * 5. Tanya-jawab interaktif berbasis data realtime database & kalkulator ASTM.
 */
class FuelMaosService
{
    /**
     * Ubah koleksi HasilUji jadi array data terstruktur.
     */
    protected function itemsFromHasil($hasil): array
    {
        return $hasil->map(function ($h) {
            return [
                'jenis' => $h->jenisUji->nama ?? '-',
                'produk' => $h->nama_sampel,
                'kkw' => $h->nomor_kkw,
                'tanggal' => $h->waktu_uji->translatedFormat('d M Y'),
                'status' => SpecEngine::evaluate($h->data_hasil, $h->nama_sampel)['verdict'],
            ];
        })->values()->all();
    }

    public function answer(string $question): array
    {
        $q = strtolower(trim($question));

        // 1. Deteksi Cek Nilai Spesifikasi Langsung (SpecEngine)
        if ($hasil = $this->detectSpecCheck($q)) {
            return $hasil;
        }

        // 2. Deteksi Query Checklist Lapangan & MT Maos Realtime
        if ($hasil = $this->detectChecklistMtQuery($q)) {
            return $hasil;
        }

        // 3. Deteksi Query Retain Sampel & Penyaluran / Re-tank Realtime
        if ($hasil = $this->detectRetainSampelQuery($q)) {
            return $hasil;
        }

        // 4. Deteksi Perbandingan Produk
        if ($hasil = $this->detectPerbandinganProduk($q)) {
            return $hasil;
        }

        // 4. Deteksi Riwayat Petugas Login
        if ($hasil = $this->detectRiwayatPetugas($q)) {
            return $hasil;
        }

        // 5. Deteksi Query Riwayat Hasil Uji
        if ($hasil = $this->detectRiwayatQuery($q)) {
            return $hasil;
        }

        // 6. Pencocokan Knowledge Base Lengkap (Website & Lab)
        $knowledge = $this->knowledgeBase();

        $best = null;
        $bestScore = 0;

        foreach ($knowledge as $entry) {
            $score = 0;
            foreach ($entry['keys'] as $key) {
                $keyLower = strtolower($key);
                if ($keyLower === '') continue;

                if (str_contains($q, $keyLower)) {
                    // Exact phrase match
                    $score += strlen($keyLower) * 2;
                } else {
                    // Check if all sub-words in the key exist in query
                    $subWords = array_filter(explode(' ', $keyLower), fn($w) => strlen($w) >= 3);
                    if (!empty($subWords)) {
                        $allMatch = true;
                        foreach ($subWords as $sw) {
                            if (!str_contains($q, $sw)) {
                                $allMatch = false;
                                break;
                            }
                        }
                        if ($allMatch) {
                            $score += count($subWords) * 3;
                        }
                    }
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $entry;
            }
        }

        if ($best !== null && $bestScore > 0) {
            return $best;
        }

        // 7. Fallback ke LLM AI jika API Key tersedia
        if ($ai = $this->askAI($question)) {
            return $ai;
        }

        // 8. Fallback Cerdas Default
        return [
            'answer' => "Halo! Saya **Fuel Maos**, asisten cerdas QC BBM FT Maos. 👋\n\nSaya dapat membantu Anda terkait seluruh fitur di website ini, seperti:\n\n• **Retain Sampel MT**: SOP jam 06.00, 12.00, 18.00 WIB, rumus Density'15 (ASTM 53B), 3 MT pertama, tangki timbun, dan cetak slide.\n• **Pengujian BBM Terpadu**: Batas spesifikasi Dirjen Migas 8 produk BBM (Pertalite, Pertamax, Biosolar, Dexlite, dll).\n• **SOP 9 Stasiun Lab**: Flash Point, Distilasi, Viskositas, Water Content, TAN, RON, Sulfur, Colorimeter, dll.\n• **Checklist Mobil Tangki (MT)**: Standar inspeksi fisik MT Maos & sertifikat laik jalan.\n• **Fitur Website**: Cara input, edit data, export Excel, cetak CoQ/PDF, dan manajemen akun.\n\nSilakan pilih topik di bawah atau ketik pertanyaan Anda ya!",
            'saran' => ['Retain Sampel MT', 'Rumus Density 15', 'Batas spesifikasi Solar', 'SOP Flash Point', 'Checklist MT Maos', 'Cara Cetak Laporan'],
        ];
    }

    /**
     * Deteksi pertanyaan seputar Checklist Lapangan / Checklist Mobil Tangki (MT) FT Maos.
     */
    protected function detectChecklistMtQuery(string $q): ?array
    {
        $isChecklistQuery = str_contains($q, 'checklist')
            || str_contains($q, 'mobil tangki')
            || str_contains($q, 'inspeksi mt')
            || str_contains($q, 'laik jalan')
            || str_contains($q, 'temuan mayor')
            || str_contains($q, 'temuan minor')
            || str_contains($q, 'sertifikat tera')
            || str_contains($q, 't2 coaming')
            || str_contains($q, 'bottom loader')
            || str_contains($q, 'manhole');

        // 1. Cek apakah mencari plat nomor polisi tertentu
        if (preg_match('/[a-z]{1,2}\s*\d{1,4}\s*[a-z]{0,3}/i', $q, $m)) {
            $rawPlat = trim($m[0]);
            $platClean = preg_replace('/\s+/', '', strtolower($rawPlat));
            if (strlen($platClean) >= 4 && !in_array($platClean, ['astm53', 'astm53b', 'ron90', 'ron92', 'ron98', 'b50', 'b40', 'b35'])) {
                $target = ChecklistMtMaos::all()->first(function ($c) use ($platClean) {
                    $cleanDb = preg_replace('/\s+/', '', strtolower($c->nomor_polisi));
                    return str_contains($cleanDb, $platClean) || str_contains($platClean, $cleanDb);
                });

                if ($target) {
                    $flagCount = $target->flagCount();
                    $isLaik = $flagCount === 0;
                    $statusBadge = $isLaik ? "🟢 **LAIK JALAN** (Tidak ada temuan)" : "🔴 **DITANDAI / TEMUAN ({$flagCount} Item)**";
                    $tgl = $target->tanggal_periksa ? $target->tanggal_periksa->translatedFormat('d F Y') : '-';
                    $expTera = $target->tanggal_exp ? \Carbon\Carbon::parse($target->tanggal_exp)->translatedFormat('d F Y') : '-';

                    $answer = "📋 **Hasil Pemeriksaan Mobil Tangki (MT) — FT Maos**\n\n"
                        . "• **Nomor Polisi**: **{$target->nomor_polisi}**\n"
                        . "• **Pemilik / Transportir**: {$target->pemilik}\n"
                        . "• **Tanggal Periksa**: {$tgl}\n"
                        . "• **Masa Berlaku Tera**: {$expTera}\n"
                        . "• **Petugas Pemeriksa**: {$target->created_by}\n"
                        . "• **Status Kelayakan**: {$statusBadge}\n\n";

                    if (! $isLaik) {
                        $answer .= "⚠️ **Daftar Item Temuan (Tidak Sesuai):**\n";
                        $flatItems = collect(ChecklistMtMaosItems::flat())->keyBy('key');
                        foreach ($target->results ?? [] as $key => $val) {
                            if ($val === 'bad') {
                                $itemInfo = $flatItems->get($key);
                                $label = $itemInfo['label'] ?? "Item {$key}";
                                $temuan = $itemInfo['temuan'] ?? 'Temuan';
                                $disp = $itemInfo['disp'] ?? '-';
                                $catatan = $target->notes[$key] ?? 'Perlu perbaikan';
                                $answer .= "• [{$temuan}] **{$label}** (Disp: {$disp}) — _{$catatan}_\n";
                            }
                        }
                    } else {
                        $answer .= "✅ Seluruh item pemeriksaan kompartemen, manhole, bracket, seal bottom loader, dan dokumen dinyatakan **memenuhi standar keselamatan Pertamina Patra Niaga**.";
                    }

                    return [
                        'answer' => $answer,
                        'saran' => ['Checklist MT Maos', 'SOP Temuan Mayor & Minor', 'Penyaluran / Re-tank'],
                    ];
                }
            }
        }

        if (! $isChecklistQuery) {
            return null;
        }

        // 2. Query realtime statistik checklist MT
        if (str_contains($q, 'rekap') || str_contains($q, 'status') || str_contains($q, 'minggu ini') || str_contains($q, 'berapa mt') || str_contains($q, 'jumlah')) {
            $total = ChecklistMtMaos::count();
            $recent = ChecklistMtMaos::latest('tanggal_periksa')->take(5)->get();
            $flagged = ChecklistMtMaos::all()->filter(fn ($c) => $c->isFlagged())->count();
            $clean = $total - $flagged;

            $answer = "📋 **Rekap Pemeriksaan Mobil Tangki (Checklist MT Maos)**\n\n"
                . "• **Total MT Terdata**: **{$total} unit**\n"
                . "• 🟢 **Laik Jalan (Clean)**: **{$clean} unit**\n"
                . "• 🔴 **Ada Temuan (Flagged)**: **{$flagged} unit**\n\n"
                . "🚛 **5 Pemeriksaan Terakhir:**\n";

            foreach ($recent as $r) {
                $statusIcon = $r->isFlagged() ? '🔴 Flagged' : '🟢 Laik';
                $tglStr = $r->tanggal_periksa ? $r->tanggal_periksa->format('d/m/Y') : '-';
                $answer .= "• **{$r->nomor_polisi}** ({$r->pemilik}) — {$statusIcon} [{$tglStr}]\n";
            }

            $answer .= "\n💡 _Buka menu **Checklist MT Maos** untuk input inspeksi baru, cetak form fisik, atau export rekap Excel._";

            return [
                'answer' => $answer,
                'saran' => ['Checklist MT Maos', 'SOP Temuan Mayor & Minor', 'Penyaluran / Re-tank'],
            ];
        }

        return null;
    }

    /**
     * Deteksi pertanyaan seputar Retain Sampel Penyaluran / Re-tank (Data Realtime Database & Aturan).
     */
    protected function detectRetainSampelQuery(string $q): ?array
    {
        $isRetainQuery = str_contains($q, 'retain')
            || str_contains($q, 'retank')
            || str_contains($q, 're-tank')
            || str_contains($q, 'penyaluran mt')
            || str_contains($q, 'penyaluran / re-tank')
            || str_contains($q, 'sampel mt')
            || str_contains($q, '3 mt')
            || str_contains($q, 'tangki timbun');

        if (! $isRetainQuery) {
            return null;
        }

        // Query realtime jumlah data retain hari ini
        if (str_contains($q, 'hari ini') || str_contains($q, 'ada berapa') || str_contains($q, 'rekap') || str_contains($q, 'status')) {
            $today = now()->toDateString();
            $data06 = RetainSampelMt::whereDate('tanggal', $today)->where('jam_label', '06:00')->get();
            $data12 = RetainSampelMt::whereDate('tanggal', $today)->where('jam_label', '12:00')->get();
            $data18 = RetainSampelMt::whereDate('tanggal', $today)->where('jam_label', '18:00')->get();

            $totalHariIni = $data06->count() + $data12->count() + $data18->count();

            $answer = "🧪 **Status Retain Sampel Penyaluran MT — Hari Ini (" . now()->translatedFormat('d F Y') . ")**\n\n"
                . "• **Pukul 06.00 WIB (Awal Penyaluran)**: " . ($data06->count() > 0 ? "✅ {$data06->count()} produk terdata (" . $data06->pluck('produk')->implode(', ') . ")" : "⏳ Belum ada input") . "\n"
                . "• **Pukul 12.00 WIB (Re-Tank Siang)**: " . ($data12->count() > 0 ? "✅ {$data12->count()} produk terdata (" . $data12->pluck('produk')->implode(', ') . ")" : "⏳ Belum ada input") . "\n"
                . "• **Pukul 18.00 WIB (Re-Tank Sore)**: " . ($data18->count() > 0 ? "✅ {$data18->count()} produk terdata (" . $data18->pluck('produk')->implode(', ') . ")" : "⏳ Belum ada input") . "\n\n"
                . "📊 **Total Data Hari Ini**: {$totalHariIni} baris pengujian.\n\n"
                . "💡 _Anda dapat melihat/mencetak slide laporan resmi atau mengedit data langsung di menu **Retain Sampel**._";

            return [
                'answer' => $answer,
                'saran' => ['Rumus Density 15', 'Cara Cetak Laporan', 'Batas spesifikasi Solar'],
            ];
        }

        return null;
    }

    /**
     * Level 1: deteksi & jawab pertanyaan cek nilai hasil uji terhadap spesifikasi.
     */
    protected function detectSpecCheck(string $q): ?array
    {
        // Abaikan jika pertanyaan meminta penjelasan rumus, sop, atau panduan
        $ignoreKeywords = ['rumus', 'cara hitung', 'jelaskan', 'apa itu', 'sop', 'tabel', 'astm', 'kenapa', 'bagaimana'];
        foreach ($ignoreKeywords as $ign) {
            if (str_contains($q, $ign)) {
                return null;
            }
        }

        $paramKey = null;
        foreach ($this->mapParameter() as $keyword => $field) {
            if (strlen($keyword) <= 3) {
                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $q)) {
                    $paramKey = $field;
                    break;
                }
            } else {
                if (str_contains($q, $keyword)) {
                    $paramKey = $field;
                    break;
                }
            }
        }

        if (! $paramKey) {
            return null;
        }

        if (! preg_match('/-?\d+(?:[.,]\d+)?/', $q, $m)) {
            return null;
        }

        $nilai = (float) str_replace(',', '.', $m[0]);

        $produk = null;
        foreach ($this->mapProduk() as $keyword => $nama) {
            if (str_contains($q, $keyword)) {
                $produk = $nama;
                break;
            }
        }

        $asumsiProduk = false;
        if (! $produk) {
            $produk = 'Solar';
            $asumsiProduk = true;
        }

        $spec = config('spec.produk.' . $produk . '.' . $paramKey);
        if (! $spec) {
            return null;
        }

        $marginal = (float) config('spec.marginal', 0.05);
        $status = SpecEngine::classify($nilai, $spec, $marginal);
        $catatan = SpecEngine::describe($nilai, $spec, $status);

        $emoji = match ($status) {
            SpecEngine::PASS => '🟢 PASS (Sesuai Spesifikasi)',
            SpecEngine::MARGINAL => '🟡 MARGINAL (Mendekati Batas)',
            SpecEngine::FAIL => '🔴 FAIL (Off-Spec / Tidak Sesuai)',
            default => '⚪ N/A',
        };

        $batas = [];
        if (isset($spec['min'])) {
            $batas[] = 'min ' . SpecEngine::formatNumber((float) $spec['min']);
        }
        if (isset($spec['max'])) {
            $batas[] = 'maks ' . SpecEngine::formatNumber((float) $spec['max']);
        }
        $batasText = implode(', ', $batas) . ' ' . ($spec['satuan'] ?? '');

        $answer = "🔎 **Cek Spesifikasi Laboratorium — {$spec['label']}**\n\n"
            . "• Nilai diuji: **{$nilai} {$spec['satuan']}**\n"
            . "• Produk: **{$produk}**" . ($asumsiProduk ? ' _(asumsi default Solar, sebutkan nama produk jika berbeda)_' : '') . "\n"
            . "• Standar Batas Resmi: **{$batasText}**\n\n"
            . "Status Evaluasi: **{$emoji}**\n{$catatan}";

        if ($status === SpecEngine::FAIL || $status === SpecEngine::MARGINAL) {
            $answer .= "\n\n💡 **Rekomendasi Tindak Lanjut:**\n" . $this->saranTindakLanjut($status, $paramKey);
        }

        return [
            'answer' => $answer,
            'saran' => ['Batas spesifikasi ' . $produk, 'Rumus Density 15', 'Riwayat hasil uji'],
        ];
    }

    /**
     * Kategori 7: saran tindak lanjut singkat berdasarkan status & parameter yang bermasalah.
     */
    protected function saranTindakLanjut(string $status, string $paramKey): string
    {
        $umum = $status === SpecEngine::FAIL
            ? "1. Lakukan pengujian ulang (re-test) untuk memastikan bukan human/instrument error.\n2. Periksa kalibrasi alat & verifikasi standar reagen.\n3. Jika hasil tetap FAIL, segera eskalasi ke Supervisor Lab — tahan penyaluran batch tersebut."
            : "1. Nilai berada di zona kritis mendekati ambang batas, disarankan uji ulang untuk konfirmasi.\n2. Pantau tren parameter ini pada batch pengujian berikutnya.\n3. Laporkan ke Supervisor Lab jika tren terus menurun.";

        $spesifik = match ($paramKey) {
            'hasil_flash_point' => "Khusus Flash Point: Periksa kerapatan tutup wadah sampel dan pastikan suhu pemanas alat berada pada setting standar sebelum uji ulang.",
            'hasil_kadar_air' => "Khusus Kadar Air: Periksa potensi kondensasi/rembesan air di tangki timbun atau jalur pipa penyaluran.",
            'hasil_density', 'suhu_akhir', 'suhu_awal' => "Periksa kemungkinan terjadinya kontaminasi silang dengan produk lain pada kompartemen MT atau manifold pipa.",
            'hasil_ron' => "Khusus RON: Cek potensi degradasi oktan atau ketidaktepatan dosis aditif octane booster.",
            'hasil_sulfur' => "Khusus Sulfur: Pastikan tidak tercampur dengan fraksi minyak berat berkadar sulfur tinggi.",
            default => null,
        };

        return $umum . ($spesifik ? "\n\n{$spesifik}" : '');
    }

    /**
     * Level 2: deteksi & jawab pertanyaan berbasis data riwayat hasil uji tersimpan.
     */
    protected function detectRiwayatQuery(string $q): ?array
    {
        if (str_contains($q, 'kkw') && preg_match('/\d+/', $q, $m)) {
            $nomor = $m[0];

            $base = HasilUji::with('jenisUji')->where('nomor_kkw', 'like', "%{$nomor}%");
            $total = $base->count();
            $hasil = $base->latest('waktu_uji')->limit(5)->get();

            if ($hasil->isEmpty()) {
                return [
                    'answer' => "Tidak ditemukan data hasil uji dengan nomor KKW mengandung \"{$nomor}\". Silakan cek kembali nomor KKW atau cari langsung di menu **Riwayat Hasil Uji**.",
                    'saran' => ['Riwayat hasil uji', 'Cara input hasil uji'],
                ];
            }

            return [
                'answer' => "📋 Ditemukan **{$total} hasil uji** dengan KKW \"{$nomor}\"" . ($total > 5 ? ' (5 data terbaru ditampilkan di bawah):' : ':'),
                'items' => $this->itemsFromHasil($hasil),
                'total' => $total,
                'lihatSemuaUrl' => $total > 5 ? route('riwayat.index') : null,
                'saran' => ['Riwayat hasil uji', 'Apa itu PASS/FAIL?'],
            ];
        }

        $statusMap = ['fail' => 'FAIL', 'gagal' => 'FAIL', 'off-spec' => 'FAIL', 'pass' => 'PASS', 'lolos' => 'PASS', 'on-spec' => 'PASS', 'marginal' => 'MARGINAL'];
        $statusDicari = null;
        foreach ($statusMap as $keyword => $status) {
            if (str_contains($q, $keyword)) {
                $statusDicari = $status;
                break;
            }
        }

        if ($statusDicari && (str_contains($q, 'berapa') || str_contains($q, 'jumlah') || str_contains($q, 'ada berapa'))) {
            [$query, $periodeLabel] = $this->terapkanPeriode(HasilUji::query(), $q);

            $items = $query->get();
            $jumlah = $items->filter(fn ($h) => SpecEngine::evaluate($h->data_hasil, $h->nama_sampel)['verdict'] === $statusDicari)->count();

            $emoji = ['PASS' => '🟢', 'FAIL' => '🔴', 'MARGINAL' => '🟡'][$statusDicari];

            return [
                'answer' => "{$emoji} Ada **{$jumlah} hasil uji {$statusDicari}** untuk periode {$periodeLabel} (dari total {$items->count()} data).\n\nCek detail lengkapnya di menu Riwayat.",
                'saran' => ['Riwayat hasil uji', 'Apa itu PASS/FAIL?'],
            ];
        }

        if (str_contains($q, 'riwayat') || str_contains($q, 'hasil uji') || str_contains($q, 'hasil terakhir')) {
            $produk = null;
            foreach ($this->mapProduk() as $keyword => $nama) {
                if (str_contains($q, $keyword)) {
                    $produk = $nama;
                    break;
                }
            }

            if ($produk) {
                [$query, $periodeLabel] = $this->terapkanPeriode(HasilUji::with('jenisUji')->where('nama_sampel', $produk), $q);
                $total = (clone $query)->count();
                $hasil = $query->latest('waktu_uji')->limit(5)->get();

                if ($hasil->isEmpty()) {
                    return [
                        'answer' => "Belum ada data hasil uji untuk **{$produk}** pada periode {$periodeLabel}.",
                        'saran' => ['Riwayat hasil uji'],
                    ];
                }

                return [
                    'answer' => "📋 **Riwayat {$produk} — {$periodeLabel}**\n\nAda **{$total} hasil uji**" . ($total > 5 ? ', 5 terbaru ditampilkan di bawah.' : '.'),
                    'items' => $this->itemsFromHasil($hasil),
                    'total' => $total,
                    'lihatSemuaUrl' => $total > 5 ? route('riwayat.index') : null,
                    'saran' => ['Riwayat hasil uji', 'Apa itu PASS/FAIL?'],
                ];
            }
        }

        return null;
    }

    /**
     * Terapkan filter periode ke query.
     */
    protected function terapkanPeriode($query, string $q): array
    {
        if (str_contains($q, 'hari ini')) {
            $query->whereDate('waktu_uji', now()->toDateString());
            return [$query, 'hari ini'];
        }

        if (str_contains($q, 'minggu ini')) {
            $query->whereBetween('waktu_uji', [now()->startOfWeek(), now()->endOfWeek()]);
            return [$query, 'minggu ini'];
        }

        if (str_contains($q, 'bulan ini')) {
            $query->whereMonth('waktu_uji', now()->month)->whereYear('waktu_uji', now()->year);
            return [$query, 'bulan ini'];
        }

        return [$query, 'semua waktu'];
    }

    /**
     * Kategori 8: perbandingan batas spesifikasi suatu parameter antar 2+ produk.
     */
    protected function detectPerbandinganProduk(string $q): ?array
    {
        $pemicu = ['beda', 'bedanya', 'perbedaan', 'bandingkan', 'dibanding'];
        $adaPemicu = false;
        foreach ($pemicu as $kata) {
            if (str_contains($q, $kata)) {
                $adaPemicu = true;
                break;
            }
        }
        if (! $adaPemicu) {
            return null;
        }

        $paramKey = null;
        $paramLabel = null;
        foreach ($this->mapParameter() as $keyword => $field) {
            if (strlen($keyword) <= 3) {
                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $q)) {
                    $paramKey = $field;
                    $paramLabel = $keyword;
                    break;
                }
            } else {
                if (str_contains($q, $keyword)) {
                    $paramKey = $field;
                    $paramLabel = $keyword;
                    break;
                }
            }
        }
        if (! $paramKey) {
            return null;
        }

        $produkDitemukan = [];
        foreach ($this->mapProduk() as $keyword => $nama) {
            if (str_contains($q, $keyword) && ! in_array($nama, $produkDitemukan, true)) {
                $produkDitemukan[] = $nama;
            }
        }

        if (count($produkDitemukan) < 2) {
            $produkDitemukan = array_unique(array_values($this->mapProduk()));
        }

        $baris = [];
        foreach ($produkDitemukan as $produk) {
            $spec = config('spec.produk.' . $produk . '.' . $paramKey);
            if (! $spec) {
                continue;
            }
            $batas = [];
            if (isset($spec['min'])) {
                $batas[] = 'min ' . SpecEngine::formatNumber((float) $spec['min']);
            }
            if (isset($spec['max'])) {
                $batas[] = 'maks ' . SpecEngine::formatNumber((float) $spec['max']);
            }
            $baris[] = "• **{$produk}**: " . implode(', ', $batas) . ' ' . ($spec['satuan'] ?? '');
        }

        if (empty($baris)) {
            return null;
        }

        $labelParam = $spec['label'] ?? $paramLabel;

        return [
            'answer' => "📊 **Perbandingan Batas Spesifikasi — {$labelParam}**\n\n" . implode("\n", $baris),
            'saran' => ['Batas spesifikasi Solar', 'Rumus Density 15', 'Riwayat hasil uji'],
        ];
    }

    /**
     * Kategori 9: riwayat hasil uji milik petugas yang sedang login.
     */
    protected function detectRiwayatPetugas(string $q): ?array
    {
        $pemicu = ['saya input', 'aku input', 'punya saya', 'yang saya catat', 'saya catat', 'input saya'];
        $adaPemicu = false;
        foreach ($pemicu as $kata) {
            if (str_contains($q, $kata)) {
                $adaPemicu = true;
                break;
            }
        }
        if (! $adaPemicu) {
            return null;
        }

        $userId = Auth::id();
        if (! $userId) {
            return null;
        }

        [$query, $periodeLabel] = $this->terapkanPeriode(
            HasilUji::with('jenisUji')->where('user_id', $userId),
            $q
        );

        $total = (clone $query)->count();
        $hasil = $query->latest('waktu_uji')->limit(8)->get();

        if ($hasil->isEmpty()) {
            return [
                'answer' => "Belum ada hasil pengujian yang tercatat atas akun Anda untuk periode {$periodeLabel}.",
                'saran' => ['Cara input hasil uji', 'Riwayat hasil uji'],
            ];
        }

        return [
            'answer' => "🧑‍🔬 **Hasil Uji yang Anda Catat — {$periodeLabel}**\n\nTotal terdapat **{$total} hasil uji** di akun Anda (8 terbaru ditampilkan di bawah):",
            'items' => $this->itemsFromHasil($hasil),
            'total' => $total,
            'lihatSemuaUrl' => $total > 8 ? route('riwayat.index') : null,
            'saran' => ['Riwayat hasil uji', 'Cara input hasil uji'],
        ];
    }

    protected function mapParameter(): array
    {
        return [
            'flash point' => 'hasil_flash_point',
            'flashpoint' => 'hasil_flash_point',
            'titik nyala' => 'hasil_flash_point',
            'viskositas' => 'hasil_viskositas',
            'kekentalan' => 'hasil_viskositas',
            'viscosity' => 'hasil_viskositas',
            'kadar air' => 'hasil_kadar_air',
            'water content' => 'hasil_kadar_air',
            'angka asam' => 'hasil_tan',
            'tan' => 'hasil_tan',
            'density' => 'hasil_density',
            'densitas' => 'hasil_density',
            'massa jenis' => 'hasil_density',
            'suhu akhir' => 'suhu_akhir',
            'suhu awal' => 'suhu_awal',
            'ron' => 'hasil_ron',
            'oktan' => 'hasil_ron',
            'oktana' => 'hasil_ron',
            'bilangan oktana' => 'hasil_ron',
            'octane' => 'hasil_ron',
            'sulfur' => 'hasil_sulfur',
            'belerang' => 'hasil_sulfur',
        ];
    }

    protected function mapProduk(): array
    {
        return [
            'pertamax turbo' => 'Pertamax Turbo',
            'turbo' => 'Pertamax Turbo',
            'fame b40' => 'FAME B40',
            'fame' => 'FAME B40',
            'biosolar' => 'Biosolar',
            'pertamina dex' => 'Pertamina Dex',
            'dexlite' => 'Dexlite',
            'dex' => 'Pertamina Dex',
            'pertalite' => 'Pertalite',
            'pertamax' => 'Pertamax',
            'solar' => 'Solar',
        ];
    }

    protected function askAI(string $question): ?array
    {
        $apiKey = config('services.anthropic.key');
        if (! $apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
                ->timeout(20)
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => config('services.anthropic.model', 'claude-sonnet-4-5-20250929'),
                    'max_tokens' => 800,
                    'system' => "Kamu adalah Fuel Maos, asisten virtual resmi sistem Quality Control BBM FT Maos PT Pertamina Patra Niaga. Jawablah dengan profesional, ringkas, jelas, dan akurat.",
                    'messages' => [['role' => 'user', 'content' => $question]],
                ]);

            if ($response->successful()) {
                $teks = collect($response->json('content', []))->where('type', 'text')->pluck('text')->implode("\n");
                if (trim($teks) !== '') {
                    return ['answer' => trim($teks), 'saran' => ['Retain Sampel MT', 'Rumus Density 15', 'Batas spesifikasi Solar']];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('FuelMaos AI fallback error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * =========================================================================
     * KNOWLEDGE BASE LENGKAP MENCAKUP SEMUA FITUR WEBSITE & LABORATORIUM FT MAOS
     * =========================================================================
     */
    protected function knowledgeBase(): array
    {
        return [
            // 1. CHECKLIST LAPANGAN (CHECKLIST MT MAOS & PENYALURAN / RE-TANK)
            [
                'keys' => ['checklist lapangan', 'pemeriksaan lapangan', 'inspeksi lapangan', 'field checklist'],
                'answer' => "🛡️ **Menu Checklist Lapangan — Fuel Terminal Maos**\n\nChecklist Lapangan di FT Maos mencakup 2 modul kendali mutu & keselamatan operasional utama:\n\n1. **[Checklist MT Maos](/checklist-mt-maos)**:\n   • Inspeksi fisik, perlengkapan safety (APAR, grounding, flame trap), dan integritas kompartemen Mobil Tangki.\n   • Verifikasi masa berlaku Sertifikat Tera Metrologi & 4 kompartemen tangki.\n   • Penentuan status **Laik Jalan** vs **Temuan (Mayor/Minor)**.\n\n2. **[Penyaluran / Re-tank](/retain-sampel)**:\n   • **Sesi 06.00 WIB**: Sampel retain awal penyaluran, verifikasi Visual Sales 3 MT pertama vs Tangki Timbun.\n   • **Sesi 12.00 & 18.00 WIB**: Uji sampel re-tank pergantian tangki siang & sore.\n   • Koreksi otomatis Density 15°C standar ASTM D1298 / Table 53B dan cetak banner laporan master.",
                'saran' => ['Checklist MT Maos', 'Penyaluran / Re-tank', 'SOP Temuan Mayor & Minor', 'Rumus Density 15'],
            ],

            [
                'keys' => ['checklist mt', 'checklist mt maos', 'checklist mobil tangki', 'inspeksi mt', 'laik jalan', 'pemeriksaan mt', 'sop checklist mt'],
                'answer' => "📋 **Modul Checklist MT Maos (Form Pemeriksaan Mobil Tangki)**\n\nStandar baku inspeksi armada MT kolaborasi PT Pertamina Patra Niaga x PT Patra Logistik:\n\n• **1. Masa Sertifikat Tera & Kompartemen 1–4**: Tinggi tera vs aktual (selisih mm), volume nominal (KL), lemping T2 coaming, dan segel IJK/baut (a–g).\n• **2. Manhole (Item 3)**: Packing karet (Mayor/rembesan), las titik flange/engsel/palang, dan kebersihan manhole (Minor).\n• **3. Bracket & Seal Bottom Loader (Item 8–9)**: Keefektifan bracket, pen & pengikat pen, serta seal standar penahan handle.\n• **4. Perlengkapan & Aksesoris**: Sight glass, indikator nama BBM, copy sertifikat tera di box, las foot valve, selang bongkar 3\" & 4\", dan segel jaminan TUM/chassis.",
                'saran' => ['SOP Temuan Mayor & Minor', 'Checklist Lapangan', 'Penyaluran / Re-tank', 'Cetak Checklist MT'],
            ],

            [
                'keys' => ['penyaluran / re-tank', 'penyaluran', 're-tank', 'retank', 'retain sampel', 'sampel penyaluran mt', 'rekap retain', '06:00', '12:00', '18:00'],
                'answer' => "🧪 **Modul Penyaluran / Re-tank (Retain Sampel MT) — FT Maos**\n\nRekapitulasi pengujian sampel retain harian BBM sesuai format resmi Pertamina Patra Niaga:\n\n1. **Sesi Pukul 06.00 WIB (Awal Penyaluran)**:\n   • Foto botol sampel retain (Pertalite, Pertamax, Biosolar B50).\n   • Foto visual penyaluran 3 MT pertama vs gelas ukur tangki timbun (misal T.05/T.06).\n   • Tabel data awal (MT Nopol, Density Obs, Density'15, Suhu, Tangki Timbun).\n\n2. **Sesi Pukul 12.00 & 18.00 WIB (Re-Tank Siang & Sore)**:\n   • Sisi kiri: Menampilkan sampel 06.00 yang tetap/fixed.\n   • Sisi kanan: Menampilkan hasil pengujian re-tank pergantian sampel produk.\n\n3. **Rekap Keseluruhan (06.00 - 18.00 WIB)**:\n   • Slide master 1 lembar menggabungkan ketiga jam, galeri foto lengkap, dan tabel matriks terpadu.",
                'saran' => ['Rumus Density 15', 'Cara Cetak Laporan', 'Sesuaikan Nopol 3 MT', 'Checklist MT Maos'],
            ],

            [
                'keys' => ['temuan mayor', 'temuan minor', 'dispensasi', 'sop temuan', 'kategori temuan', 'tidak laik jalan'],
                'answer' => "⚖️ **Kategori Temuan & Dispensasi Checklist Mobil Tangki (MT):**\n\n🔴 **TEMUAN MAYOR (Dispensasi: - / TIDAK LAIK JALAN / DILARANG MENGISI)**:\n1. Packing Manhole rembes/bocor.\n2. Tanda Sah Lemping Nominal rusak/tidak sesuai.\n3. Kompartemen dalam kotor/ada kontaminan/endapan.\n4. Bracket tidak efektif, las engsel retak, pen terganjal, atau pengikat pen lepas.\n5. Seal Bottom Loader tidak standar.\n6. Las Titik Foot Valve rusak/lepas.\n7. Tanda Jaminan Segel TUM & Chassis tidak terpasang/rusak.\n\n🟡 **TEMUAN MINOR (Diberikan Dispensasi Waktu Perbaikan)**:\n• **Dispensasi 3 Hari**: Las titik flange/engsel/palang manhole, kebersihan manhole, T2 coaming, saluran drainase air, las sight glass, copy tera di box, kebersihan bottom loading, dan drainase rumah selang.\n• **Dispensasi 2 Minggu**: Kebersihan sight glass, kelengkapan indikator produk BBM, dan selang bongkar 3\" & 4\".",
                'saran' => ['Checklist MT Maos', 'Checklist Lapangan', 'Penyaluran / Re-tank'],
            ],

            [
                'keys' => ['tera kompartemen', 'sertifikat tera', 'tinggi tera', 't2 coaming', 'lemping tera', 'ijk baut'],
                'answer' => "📏 **Standar Pemeriksaan Tera Metrologi 4 Kompartemen MT:**\n\n• **Tinggi Tera vs Tinggi Aktual (T1)**: Diukur menggunakan mistar ukur standar. Toleransi selisih $\\le 2\\text{ mm}$.\n• **Dudukan Lemping (T2 Coaming)**: Memastikan angka T2 sesuai dengan yang tercantum pada sertifikat tera legal metrologi.\n• **Segel IJK / Baut Pengikat (Titik a s/d g)**: Semua kawat segel timah metrologi harus dalam kondisi utuh, terkunci, dan tidak ada bekas manipulasi.\n• **Masa Berlaku Sertifikat Tera**: Sertifikat kalibrasi metrologi harus masih berlaku (aktif) dan salinannya wajib terpasang di box dokumen MT.",
                'saran' => ['Checklist MT Maos', 'SOP Temuan Mayor & Minor', 'Penyaluran / Re-tank'],
            ],

            [
                'keys' => ['rumus density', 'density 15', 'astm 53', 'astm 53b', 'astm d1298', 'hitung density', 'koreksi suhu', 'vcf', 'alpha 15', 'k0 k1', 'cara hitung density 15'],
                'answer' => "📐 **Rumus Perhitungan Density 15°C (Standar Baku ASTM D1298 & ASTM-IP Table 53B)**\n\nSistem secara otomatis menghitung konversi massa jenis ke basis suhu standar 15°C tanpa perlu mencari manual di buku tabel:\n\n• **Rumus Dasar**: Density 15°C = Density Obs / VCF\n\n**Persamaan Derivasi ASTM Table 53B:**\n1. Selisih Suhu: ΔT = T_obs - 15\n2. Koefisien Ekspansi: α15 = (K0 / ρ15²) + (K1 / ρ15)\n3. Faktor Koreksi Volume: VCF = exp(-α15 × ΔT × (1 + 0.8 × α15 × ΔT))\n\n**Konstanta Kelompok Produk:**\n• **Bensin (Pertalite/Pertamax/Turbo)**: K0 = 346.4228, K1 = 0.4388\n• **Kerosene / Avtur**: K0 = 594.5418, K1 = 0.0\n• **Solar / Biosolar / Dexlite / Pertadex**: K0 = 186.9696, K1 = 0.4862",
                'saran' => ['Penyaluran / Re-tank', 'Batas spesifikasi Solar', 'Cara Cetak Laporan'],
            ],

            [
                'keys' => ['3 mt', '3 mt pertama', 'tangki timbun', 'visual sales', 'nopol 3 mt', 'sesuaikan nopol'],
                'answer' => "🚛 **Sampel 3 MT Pertama vs Sampel Tangki Timbun**\n\nPada sesi **06.00 WIB**, dilakukan verifikasi visual kesesuaian antara BBM yang disalurkan ke Mobil Tangki (MT) dengan BBM dari Tangki Timbun:\n\n• **Visual Sales 3 MT**: Pengambilan sampel visual dari 3 armada MT pertama penyaluran (contoh Pertamax: N 3762 O, R 3762 O, B 9067 SFW).\n• **Visual Tangki Timbun**: Sampel langsung dari dasar/tengah tangki timbun (contoh: T.05, T.06, T.09).\n\n💡 _Anda dapat mengubah nopol 3 MT dan nomor tangki dengan mengklik tombol **'Sesuaikan Nopol 3 MT & Tangki'** pada kartu laporan._",
                'saran' => ['Penyaluran / Re-tank', 'Rumus Density 15', 'Cara Cetak Laporan'],
            ],

            // 3. PENGUJIAN BBM TERPADU & SPESIFIKASI DIRJEN MIGAS
            [
                'keys' => ['pengujian bbm', 'uji bbm', 'wizard', 'uji kesesuaian', 'spesifikasi bbm', 'dirjen migas'],
                'answer' => "🔬 **Menu Pengujian BBM Terpadu (Kesesuaian Spesifikasi)**\n\nFitur pengujian komprehensif untuk mengevaluasi parameter mutu BBM terhadap Surat Keputusan Dirjen Migas:\n\n1. Pilih produk BBM yang diuji (*Pertalite, Pertamax, Pertamax Turbo, Solar B0, Biosolar B50, Dexlite, Pertamina Dex, FAME B40*).\n2. Masukkan nomor KKW/Tangki & hasil pengukuran tiap parameter.\n3. Sistem otomatis menghitung & memberikan status evaluasi:\n   • 🟢 **PASS**: Memenuhi spesifikasi resmi.\n   • 🟡 **MARGINAL**: Mendekati batas kritis toleransi (5%).\n   • 🔴 **FAIL**: Di luar batas spesifikasi (off-spec).\n4. Hasil pengujian dapat langsung diterbitkan menjadi **Sertifikat Mutu (CoQ)**.",
                'saran' => ['Batas spesifikasi Solar', 'SOP Flash Point', 'Rumus Density 15'],
            ],

            // 4. BATAS SPESIFIKASI PRODUK
            [
                'keys' => ['solar', 'spesifikasi solar', 'batas solar', 'solar b0'],
                'answer' => "🛢️ **Batas Spesifikasi Mutu — Solar (B0)**\n\n• **Flash Point**: Min 55,0 °C (ASTM D93)\n• **Density @15°C**: 815,0 – 870,0 kg/m³ (ASTM D1298)\n• **Viskositas @40°C**: 2,0 – 4,5 mm²/s (ASTM D445)\n• **Kandungan Air**: Maks 400 mg/kg (ASTM D6304)\n• **Kandungan Sulfur**: Maks 0,25 % m/m (ASTM D4294)\n• **Total Acid Number (TAN)**: Maks 0,3 mg KOH/g (ASTM D664)\n• **Distilasi T90**: Maks 370,0 °C (ASTM D86)\n• **Warna No. ASTM**: Maks 1,0 (ASTM D1500)",
                'saran' => ['Batas spesifikasi Biosolar', 'Batas spesifikasi Dexlite', 'SOP Flash Point'],
            ],

            [
                'keys' => ['biosolar', 'spesifikasi biosolar', 'biosolar b40', 'biosolar b50', 'b35', 'b40', 'b50'],
                'answer' => "🌱 **Batas Spesifikasi Mutu — Biosolar B50 / B40**\n\n• **Flash Point**: Min 52,0 °C (ASTM D93)\n• **Density @15°C**: 815,0 – 880,0 kg/m³ (ASTM D1298)\n• **Viskositas @40°C**: 2,0 – 5,0 mm²/s (ASTM D445)\n• **Kandungan Air**: Maks 380 mg/kg (ASTM D6304)\n• **Kandungan Sulfur**: Maks 0,20 % m/m (ASTM D4294)\n• **Total Acid Number (TAN)**: Maks 0,6 mg KOH/g (ASTM D664)\n• **Distilasi T90**: Maks 370,0 °C (ASTM D86)\n• **Warna No. ASTM**: Maks 3,0 (ASTM D1500)",
                'saran' => ['Batas spesifikasi Solar', 'Batas spesifikasi Dexlite', 'SOP Flash Point'],
            ],

            [
                'keys' => ['pertalite', 'spesifikasi pertalite', 'batas pertalite', 'ron 90'],
                'answer' => "🟢 **Batas Spesifikasi Mutu — Pertalite (RON 90)**\n\n• **Research Octane Number (RON)**: Min 90,0 (ASTM D2699)\n• **Density @15°C**: 715,0 – 770,0 kg/m³ (ASTM D1298)\n• **Distilasi 10% (T10)**: Maks 74,0 °C\n• **Titik Didih Akhir (FBP)**: Maks 215,0 °C (ASTM D86)\n• **Kandungan Sulfur**: Maks 0,05 % m/m (500 ppm) (ASTM D4294)\n• **Warna Visual**: Hijau (Jernih & Terang)\n• **Doctor Test / Corrosive**: Negative / Class 1 (ASTM D130)",
                'saran' => ['Batas spesifikasi Pertamax', 'SOP RON', 'Rumus Density 15'],
            ],

            [
                'keys' => ['pertamax', 'spesifikasi pertamax', 'batas pertamax', 'ron 92'],
                'answer' => "🔵 **Batas Spesifikasi Mutu — Pertamax (RON 92)**\n\n• **Research Octane Number (RON)**: Min 92,0 (ASTM D2699)\n• **Density @15°C**: 715,0 – 770,0 kg/m³ (ASTM D1298)\n• **Distilasi 10% (T10)**: Maks 70,0 °C\n• **Titik Didih Akhir (FBP)**: Maks 215,0 °C (ASTM D86)\n• **Kandungan Sulfur**: Maks 0,04 % m/m (400 ppm) (ASTM D4294)\n• **Warna Visual**: Biru (Jernih & Terang)\n• **Doctor Test / Corrosive**: Negative / Class 1 (ASTM D130)",
                'saran' => ['Batas spesifikasi Pertamax Turbo', 'SOP RON', 'Rumus Density 15'],
            ],

            [
                'keys' => ['pertamax turbo', 'spesifikasi pertamax turbo', 'turbo', 'ron 98'],
                'answer' => "🔴 **Batas Spesifikasi Mutu — Pertamax Turbo (RON 98)**\n\n• **Research Octane Number (RON)**: Min 98,0 (ASTM D2699)\n• **Density @15°C**: 715,0 – 770,0 kg/m³ (ASTM D1298)\n• **Distilasi 10% (T10)**: Maks 70,0 °C\n• **Titik Didih Akhir (FBP)**: Maks 215,0 °C (ASTM D86)\n• **Kandungan Sulfur**: Maks 0,005 % m/m (50 ppm Euro 4) (ASTM D4294/D5453)\n• **Warna Visual**: Merah (Jernih & Terang)\n• **Doctor Test**: Negative",
                'saran' => ['Batas spesifikasi Pertamax', 'SOP RON', 'Rumus Density 15'],
            ],

            [
                'keys' => ['dexlite', 'spesifikasi dexlite', 'batas dexlite', 'cn 51'],
                'answer' => "⚡ **Batas Spesifikasi Mutu — Dexlite**\n\n• **Cetane Number**: Min 51,0 (Cetane Index min 48,0)\n• **Flash Point**: Min 52,0 °C (ASTM D93)\n• **Density @15°C**: 815,0 – 880,0 kg/m³ (ASTM D1298)\n• **Viskositas @40°C**: 2,0 – 5,0 mm²/s (ASTM D445)\n• **Kandungan Air**: Maks 380 mg/kg (ASTM D6304)\n• **Kandungan Sulfur**: Maks 0,12 % m/m (1200 ppm) (ASTM D4294)\n• **TAN**: Maks 0,6 mg KOH/g",
                'saran' => ['Batas spesifikasi Pertamina Dex', 'Batas spesifikasi Biosolar', 'SOP Flash Point'],
            ],

            [
                'keys' => ['pertamina dex', 'pertadex', 'spesifikasi pertamina dex', 'cn 53'],
                'answer' => "💎 **Batas Spesifikasi Mutu — Pertamina Dex**\n\n• **Cetane Number**: Min 53,0\n• **Flash Point**: Min 55,0 °C (ASTM D93)\n• **Density @15°C**: 810,0 – 850,0 kg/m³ (ASTM D1298)\n• **Viskositas @40°C**: 2,0 – 4,5 mm²/s (ASTM D445)\n• **Kandungan Air**: Maks 280 mg/kg (ASTM D6304)\n• **Kandungan Sulfur**: Maks 0,005 % m/m (50 ppm Euro 4) (ASTM D4294/D5453)\n• **TAN**: Maks 0,3 mg KOH/g\n• **Warna No. ASTM**: Maks 1,0",
                'saran' => ['Batas spesifikasi Dexlite', 'SOP Flash Point', 'Rumus Density 15'],
            ],

            // 5. SOP PENGUJIAN LABORATORIUM
            [
                'keys' => ['sop flash point', 'sop titik nyala', 'pensky martens', 'flash tester'],
                'answer' => "🔥 **SOP Pengujian Flash Point (ASTM D93 - Pensky-Martens Closed Cup)**\n\n1. Pastikan mangkuk uji bersih, kering, dan berada pada suhu kamar.\n2. Tuang sampel BBM tepat pada garis batas mangkuk (kurang lebih 70 mL).\n3. Pasang mangkuk ke alat, pasang thermometer/sensor suhu dan tutup rapat.\n4. Nyalakan pemanas dengan laju kenaikan suhu 5–6 °C/menit dan nyalakan pengaduk (stirrer 90–120 rpm).\n5. Lakukan penyulutan api uji setiap kenaikan suhu 1 °C (untuk FP < 110 °C).\n6. Catat suhu saat terjadi kilatan api pertama di dalam mangkuk sebagai **Flash Point**.",
                'saran' => ['Batas spesifikasi Solar', 'SOP Viskositas', 'SOP Density'],
            ],

            [
                'keys' => ['sop density', 'sop massa jenis', 'hidrometer', 'hydrometer', 'sop hidrometer'],
                'answer' => "📏 **SOP Pengujian Density & Koreksi Suhu (ASTM D1298)**\n\n1. Tuang sampel ke silinder ukur (gelas ukur kaca) perlahan melewati dinding agar tidak timbul gelembung udara.\n2. Tempatkan silinder pada permukaan datar dan celupkan thermometer.\n3. Masukkan hidrometer standar perlahan hingga mengapung bebas di tengah cairan.\n4. Tunggu hingga hidrometer stabil dan catat angka pembacaan pada batas miniskus cairan sebagai **Density Obs**.\n5. Catat suhu cairan tepat saat pembacaan sebagai **Suhu Obs (°C)**.\n6. Masukkan nilai ke sistem untuk dihitung otomatis menjadi **Density'15 (ASTM 53B)**.",
                'saran' => ['Rumus Density 15', 'Retain Sampel MT', 'Batas spesifikasi Pertalite'],
            ],

            [
                'keys' => ['sop distilasi', 'sop destilasi', 'astm d86', 'distillation'],
                'answer' => "🌡️ **SOP Pengujian Distilasi (ASTM D86)**\n\n1. Ukur tepat 100 mL sampel dengan gelas ukur dan masukkan ke labu distilasi.\n2. Pasang termometer distilasi tepat di tengah leher labu dan hubungkan ke pipa kondensor bersuhu 0–4 °C.\n3. Nyalakan pemanas listrik dan amati timbulnya tetesan kondensat pertama sebagai **Initial Boiling Point (IBP)**.\n4. Atur laju distilasi konstan 4–5 mL/menit.\n5. Catat suhu pada volume penguapan 10%, 50%, 90%, dan suhu tertinggi sebagai **Final Boiling Point (FBP)**.",
                'saran' => ['Batas spesifikasi Pertalite', 'Batas spesifikasi Solar', 'SOP Flash Point'],
            ],

            [
                'keys' => ['sop viskositas', 'sop kekentalan', 'astm d445', 'viscometer'],
                'answer' => "💧 **SOP Pengujian Viskositas Kinematik (ASTM D445)**\n\n1. Siapkan viskometer tabung kapiler (Cannon-Fenske / Ubbelohde) yang telah terkalibrasi.\n2. Masukkan sampel sesuai volume batas dan rendam tabung di dalam penangas air (water bath) bersuhu tepat **40,0 °C** selama 15 menit.\n3. Hisap sampel hingga melewati batas atas bola kapiler.\n4. Lepaskan hisapan dan nyalakan stopwatch tepat saat miniskus melewati garis batas atas, lalu hentikan stopwatch saat miniskus melewati garis batas bawah.\n5. Hitung Viskositas: ν = C × t (di mana C adalah konstanta viskometer dan t adalah waktu alir dalam detik).",
                'saran' => ['Batas spesifikasi Solar', 'Batas spesifikasi Dexlite', 'SOP Kadar Air'],
            ],

            [
                'keys' => ['sop kadar air', 'sop water content', 'karl fischer', 'astm d6304'],
                'answer' => "💦 **SOP Pengujian Kadar Air / Water Content (ASTM D6304 - Karl Fischer Coulometric)**\n\n1. Siapkan alat titrasi Karl Fischer Coulometric dan pastikan reagen aktif (drift stabil < 10 µg/min).\n2. Ambil sampel BBM menggunakan jarum suntik (syringe) kering dan timbang berat awal.\n3. Injeksikan sampel ke dalam sel titrasi melalui septum karet dan timbang kembali syringe untuk mencatat berat bersih sampel.\n4. Alat secara otomatis melakukan titrasi kulometrik hingga mencapai titik akhir elektrokimia.\n5. Baca hasil kadar air dalam satuan **mg/kg (ppm)** pada layar alat.",
                'saran' => ['Batas spesifikasi Biosolar', 'Batas spesifikasi Solar', 'SOP Viskositas'],
            ],

            [
                'keys' => ['sop ron', 'sop oktan', 'cfr engine', 'astm d2699'],
                'answer' => "⚡ **SOP Pengujian Bilangan Oktana / RON (ASTM D2699 - CFR Engine)**\n\n1. Hidupkan mesin CFR Engine standar satu silinder variabel kompresi dan biarkan mencapai suhu kerja stabil (minyak lumas 57 °C, jaket pendingin 100 °C).\n2. Masukkan sampel bensin ke tangki karburator.\n3. Atur rasio kompresi silinder dan sesuaikan rasio bahan bakar-udara hingga menghasilkan intensitas ketukan (knock intensity) standar pada knockmeter.\n4. Lakukan pembandingan (bracketing) dengan dua bahan bakar acuan primer (Primary Reference Fuels: campuran Iso-Oktana dan n-Heptana).\n5. Catat nilai interpolasi sebagai **Research Octane Number (RON)**.",
                'saran' => ['Batas spesifikasi Pertalite', 'Batas spesifikasi Pertamax', 'Batas spesifikasi Pertamax Turbo'],
            ],

            [
                'keys' => ['sop sulfur', 'sop belerang', 'astm d4294', 'xrf'],
                'answer' => "🧫 **SOP Pengujian Kandungan Sulfur (ASTM D4294 / D5453 - XRF Analyzer)**\n\n1. Siapkan sample cup khusus XRF dan pasang film tipis Mylar/Prolene bebas sulfur di bagian bawahnya tanpa ada lipatan.\n2. Tuang sampel BBM ke dalam cup hingga ketinggian kurang lebih 3/4 volume.\n3. Tempatkan cup ke ruang analisis spektrometer sinar-X fluoresensi (EDXRF Analyzer).\n4. Pilih kurva kalibrasi matriks yang sesuai (Gasoline / Diesel) dan jalankan pengukuran selama waktu eksposur standar (100–300 detik).\n5. Catat kadar sulfur terukur dalam satuan **% m/m** atau **mg/kg (ppm)**.",
                'saran' => ['Batas spesifikasi Pertamax', 'Batas spesifikasi Solar', 'Batas spesifikasi Pertamina Dex'],
            ],

            // 6. PANDUAN PENGGUNAAN SISTEM WEBSITE & FITUR
            [
                'keys' => ['cara input', 'cara isi data', 'tambah data', 'input hasil uji', 'simpan data'],
                'answer' => "✍️ **Panduan Cara Input Data Pengujian di Sistem:**\n\n• **Input Retain Sampel MT**:\n  1. Buka menu **Retain Sampel**.\n  2. Pilih tanggal laporan.\n  3. Klik tombol **'Edit Data Tabel'** pada slide atau tombol **'+ Isi Data'** pada tabel pivot.\n  4. Masukkan MT Nopol, Density Obs, Suhu, dan Nomor Tangki. Density'15 akan otomatis terhitung.\n  5. Klik **Simpan**.\n\n• **Input Uji Kesesuaian Spesifikasi BBM**:\n  1. Buka menu **Pengujian BBM**.\n  2. Pilih jenis sampel BBM dan isi nomor KKW/Tangki.\n  3. Lengkapi form parameter hasil uji lab.\n  4. Klik **Simpan & Evaluasi Mutu**.",
                'saran' => ['Retain Sampel MT', 'Cara Cetak Laporan', 'Export Excel'],
            ],

            [
                'keys' => ['cetak', 'print', 'unduh png', 'download png', 'cetak pdf', 'cara cetak laporan', 'download gambar', 'banner'],
                'answer' => "🖨️ **Panduan Cetak & Download Laporan:**\n\n1. **Cetak Slide Retain Sampel (Format Banner Pertamina Patra Niaga)**:\n   • Buka menu **Retain Sampel** -> klik tombol **'Cetak / Unduh Banner'**.\n   • Pilih mode tampilan: *Rekap Lengkap Harian (1 Lembar)*, *Sesi 06.00*, *Sesi 12.00*, atau *Sesi 18.00*.\n   • Klik tombol **'Download PNG'** untuk menyimpan gambar resolusi tinggi (high-res), atau klik **'Cetak / Simpan PDF'** untuk cetak A4 landscape 1 halaman tanpa duplikasi.\n\n2. **Cetak Sertifikat Mutu (CoQ)**:\n   • Buka menu **Riwayat Hasil Uji** -> klik ikon printer pada baris pengujian yang diinginkan.",
                'saran' => ['Retain Sampel MT', 'Export Excel', 'Cara input hasil uji'],
            ],

            [
                'keys' => ['excel', 'export excel', 'unduh excel', 'download excel', 'riwayat excel'],
                'answer' => "📊 **Panduan Export Data ke Microsoft Excel (.xls):**\n\n• **Excel Retain Sampel Harian**: Pada halaman Retain Sampel, klik tombol hijau **'Unduh Excel'** untuk mengunduh rekap pivot tanggal aktif.\n• **Excel Riwayat Seluruh Tanggal**: Klik tombol **'Excel Riwayat'** untuk mengekspor seluruh database retain sampel dari hari pertama hingga sekarang.\n• **Excel Checklist MT**: Pada modul Checklist MT, klik tombol **'Export Semua'** untuk mendapatkan rekap checklist armada transportir.",
                'saran' => ['Cara Cetak Laporan', 'Retain Sampel MT', 'Riwayat hasil uji'],
            ],

            [
                'keys' => ['akun', 'user', 'role', 'login', 'admin', 'petugas', 'hak akses'],
                'answer' => "🔐 **Hak Akses & Manajemen Akun Sistem:**\n\nSistem membedakan hak akses berdasarkan peran:\n\n1. **Admin / Supervisor Lab**:\n   • Memiliki akses penuh untuk membuat, mengedit, dan menghapus data pengujian lab, retain sampel, dan checklist MT.\n   • Otorisasi persetujuan Sertifikat Mutu (CoQ) dan keputusan eskalasi off-spec.\n\n2. **Petugas Lab / Analis**:\n   • Dapat menginput hasil uji, mengunggah foto sampel botol/tangki, melihat riwayat, dan mengunduh laporan PDF/Excel.\n\n3. **Transportir / SPBU**:\n   • Memiliki hak akses khusus untuk melihat status checklist armada Mobil Tangki miliknya.",
                'saran' => ['Cara input hasil uji', 'Retain Sampel MT', 'Bantuan'],
            ],
        ];
    }
}
