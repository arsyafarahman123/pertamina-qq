<?php

namespace App\Services;

/**
 * Smart Spec Engine — menilai hasil uji terhadap spesifikasi mutu BBM.
 * Menghasilkan verdict PASS / FAIL / MARGINAL / N/A secara otomatis.
 */
class SpecEngine
{
    public const PASS = 'PASS';
    public const FAIL = 'FAIL';
    public const MARGINAL = 'MARGINAL';
    public const NA = 'N/A';

    /**
     * Evaluasi seluruh data hasil uji untuk satu produk.
     *
     * @param  array|null  $dataHasil  ['field_key' => nilai]
     * @param  string      $produk     nama sampel (Solar, Biosolar, dst)
     */
    public static function evaluate(?array $dataHasil, string $produk): array
    {
        // Hasil dari wizard "Uji Kesesuaian Spesifikasi BBM" (UjiBbmController) sudah
        // membawa hasil penilaian lengkapnya sendiri (per-parameter, sesuai dokumen
        // Spesifikasi Dirjen Migas) -- pakai langsung itu, jangan dinilai ulang lewat
        // config/spec.php yang cakupan parameternya lebih terbatas.
        if (isset($dataHasil['_parameters']) && is_array($dataHasil['_parameters'])) {
            return self::evaluateFromParameters($dataHasil);
        }

        $specs = config('spec.produk.' . $produk, []);
        $marginal = (float) config('spec.marginal', 0.05);

        $detail = [];
        $counts = ['PASS' => 0, 'FAIL' => 0, 'MARGINAL' => 0, 'N/A' => 0];
        $statuses = [];

        foreach ((array) $dataHasil as $key => $value) {
            $spec = $specs[$key] ?? null;

            if (! $spec) {
                continue; // field informatif, tidak dinilai
            }

            if (trim((string) $value) === '') {
                $status = self::NA;
                $note = 'Tidak ada nilai terukur';
            } elseif (isset($spec['expected'])) {
                // Spec bertipe teks (mis. Warna): dicocokkan sebagai teks, bukan angka.
                $status = self::classifyText((string) $value, $spec);
                $note = self::describeText((string) $value, $spec, $status);
            } elseif (! is_numeric($value)) {
                $status = self::NA;
                $note = 'Tidak ada nilai terukur';
            } else {
                $num = (float) $value;
                $status = self::classify($num, $spec, $marginal);
                $note = self::describe($num, $spec, $status);
            }

            $detail[] = [
                'key' => $key,
                'label' => $spec['label'] ?? $key,
                'satuan' => $spec['satuan'] ?? '',
                'min' => $spec['min'] ?? null,
                'max' => $spec['max'] ?? null,
                'nilai' => $value,
                'status' => $status,
                'catatan' => $note,
            ];

            $counts[$status]++;
            $statuses[] = $status;
        }

        $verdict = self::overall($statuses);

        return [
            'verdict' => $verdict,
            'detail' => $detail,
            'ringkasan' => $counts,
            'jumlah_dinilai' => count($detail),
        ];
    }

    /**
     * Bangun hasil evaluasi dari laporan yang sudah dihitung oleh UjiBbmController
     * (dataHasil['_parameters'] = daftar parameter + status PASS/FAIL/INVALID yang
     * sudah dibandingkan langsung terhadap batas resmi di config/spesifikasi_bbm.php).
     */
    protected static function evaluateFromParameters(array $dataHasil): array
    {
        $counts = ['PASS' => 0, 'FAIL' => 0, 'MARGINAL' => 0, 'N/A' => 0];
        $detail = [];

        foreach ($dataHasil['_parameters'] as $item) {
            $status = $item['status'] ?? 'N/A';
            $status = $status === 'INVALID' ? self::NA : $status;
            if (! isset($counts[$status])) {
                $status = self::NA;
            }

            $detail[] = [
                'key' => $item['parameter'] ?? '-',
                'label' => $item['parameter'] ?? '-',
                'satuan' => $item['unit'] ?? '',
                'min' => $item['min'] ?? null,
                'max' => $item['max'] ?? null,
                'nilai' => $item['value'] ?? '-',
                'status' => $status,
                'catatan' => $item['message'] ?? '',
            ];

            $counts[$status]++;
        }

        $verdict = $dataHasil['_verdict'] ?? self::overall(array_column($detail, 'status'));

        return [
            'verdict' => $verdict,
            'detail' => $detail,
            'ringkasan' => $counts,
            'jumlah_dinilai' => count($detail),
        ];
    }

    /**
     * Klasifikasi satu nilai terhadap batas min/max.
     */
    public static function classify(float $value, array $spec, float $marginal): string
    {
        $min = isset($spec['min']) ? (float) $spec['min'] : null;
        $max = isset($spec['max']) ? (float) $spec['max'] : null;

        $belowMin = $min !== null && $value < $min;
        $aboveMax = $max !== null && $value > $max;

        if (! $belowMin && ! $aboveMax) {
            return self::PASS;
        }

        // Mendekati batas → MARGINAL (peringatan dini)
        if ($belowMin && $min > 0) {
            $tolerance = $min * $marginal;
            if ($value >= ($min - $tolerance)) {
                return self::MARGINAL;
            }
        }

        if ($aboveMax && $max > 0) {
            $tolerance = $max * $marginal;
            if ($value <= ($max + $tolerance)) {
                return self::MARGINAL;
            }
        }

        return self::FAIL;
    }

    /**
     * Klasifikasi nilai teks (mis. Warna) terhadap nilai yang diharapkan.
     * Tidak ada status MARGINAL untuk teks -- cuma cocok (PASS) atau tidak (FAIL).
     */
    public static function classifyText(string $value, array $spec): string
    {
        $nilai = mb_strtolower(trim($value));
        $harapan = mb_strtolower(trim((string) $spec['expected']));

        if ($nilai === '') {
            return self::NA;
        }

        return $nilai === $harapan ? self::PASS : self::FAIL;
    }

    /**
     * Deskripsi singkat hasil klasifikasi teks.
     */
    public static function describeText(string $value, array $spec, string $status): string
    {
        return match ($status) {
            self::PASS => 'Sesuai spesifikasi warna (on-spec).',
            self::FAIL => 'Warna tidak sesuai spesifikasi, seharusnya ' . $spec['expected'] . '.',
            default => 'Tidak dapat dinilai.',
        };
    }

    /**
     * Deskripsi singkat hasil klasifikasi.
     */
    public static function describe(float $value, array $spec, string $status): string
    {
        $min = isset($spec['min']) ? (float) $spec['min'] : null;
        $max = isset($spec['max']) ? (float) $spec['max'] : null;

        return match ($status) {
            self::PASS => 'Sesuai spesifikasi (on-spec).',
            self::MARGINAL => 'Mendekati batas spesifikasi, perlu perhatian.',
            self::FAIL => $min !== null && $value < $min
                ? 'Di bawah batas minimum ' . self::formatNumber($min) . '.'
                : 'Di atas batas maksimum ' . self::formatNumber($max) . '.',
            default => 'Tidak dapat dinilai.',
        };
    }

    /**
     * Verdict gabungan: FAIL menang, lalu MARGINAL, lalu PASS, lalu N/A.
     */
    public static function overall(array $statuses): string
    {
        if (empty($statuses)) {
            return self::NA;
        }
        if (in_array(self::FAIL, $statuses, true)) {
            return self::FAIL;
        }
        if (in_array(self::MARGINAL, $statuses, true)) {
            return self::MARGINAL;
        }
        if (in_array(self::PASS, $statuses, true)) {
            return self::PASS;
        }

        return self::NA;
    }

    /**
     * Format angka untuk tampilan deskripsi.
     */
    public static function formatNumber(float $number): string
    {
        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}
