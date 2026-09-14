<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Hasil Uji BBM</title>
</head>
<body>
    <table border="1">
        {{-- 1. HEADER UTAMA RESMI --}}
        <tr>
            <td colspan="10" style="font-weight:bold;font-size:14px;background:#0F2744;color:#ffffff;text-align:center;height:32px;">
                REKAPITULASI HASIL PENGUJIAN MUTU BBM — FUEL TERMINAL MAOS
            </td>
        </tr>
        <tr>
            <td colspan="10" style="font-weight:bold;font-size:11px;background:#006CB8;color:#ffffff;text-align:center;height:24px;">
                PT PERTAMINA PATRA NIAGA · QUALITY &amp; QUANTITY DEPARTMENT
            </td>
        </tr>
        <tr>
            <td colspan="10" style="font-size:10px;background:#f8fafc;padding:6px;">
                <b>Waktu Ekspor:</b> {{ now()->translatedFormat('l, d F Y H:i:s') }} WIB &nbsp;|&nbsp;
                <b>Cakupan Data:</b> {{ $isSemua ? 'Seluruh Riwayat Keseluruhan' : 'Sesuai Filter Aktif' }} &nbsp;|&nbsp;
                <b>Total Data:</b> <b>{{ $totalData }}</b> baris hasil pengujian
            </td>
        </tr>
        <tr></tr>

        {{-- 2. HEADER TABEL --}}
        <tr style="font-weight:bold;background:#FFD400;color:#000000;text-align:center;height:28px;">
            <th style="width:50px;">NO</th>
            <th style="width:160px;">WAKTU PENGUJIAN</th>
            <th style="width:140px;">PRODUK / SAMPEL</th>
            <th style="width:140px;">NOMOR KKW / KERETA</th>
            <th style="width:220px;">JENIS PENGUJIAN</th>
            <th style="width:120px;">STATUS MUTU</th>
            <th style="width:360px;">DETAIL PARAMETER &amp; HASIL UJI</th>
            <th style="width:200px;">CATATAN</th>
            <th style="width:160px;">PETUGAS / ANALIS</th>
            <th style="width:160px;">WAKTU INPUT SISTEM</th>
        </tr>

        {{-- 3. ISI DATA --}}
        @forelse ($riwayat as $idx => $item)
            @php
                $verdict = strtoupper($item->verdict ?? 'PASS');
                $bgVerdict = match($verdict) {
                    'PASS' => '#dcfce7',
                    'FAIL' => '#fee2e2',
                    'MARGINAL' => '#fef3c7',
                    default => '#f1f5f9'
                };
                $colorVerdict = match($verdict) {
                    'PASS' => '#15803d',
                    'FAIL' => '#b91c1c',
                    'MARGINAL' => '#b45309',
                    default => '#334155'
                };

                // Susun detail parameter menjadi teks terbaca rapi
                $detailList = [];
                if (is_array($item->data_hasil)) {
                    foreach ($item->data_hasil as $key => $val) {
                        if (is_array($val)) {
                            $valStr = json_encode($val);
                        } else {
                            $valStr = (string)$val;
                        }
                        $label = ucwords(str_replace(['_', '-'], ' ', $key));
                        $detailList[] = "{$label}: {$valStr}";
                    }
                }
                $detailStr = implode(' | ', $detailList) ?: '-';
            @endphp
            <tr>
                <td style="text-align:center;">{{ $idx + 1 }}</td>
                <td style="text-align:center;">{{ $item->waktu_uji ? $item->waktu_uji->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
                <td style="font-weight:bold;text-align:left;">{{ $item->nama_sampel }}</td>
                <td style="text-align:center;">{{ $item->nomor_kkw ?: '-' }}</td>
                <td style="text-align:left;">{{ $item->jenisUji?->nama ?: 'Uji Kesesuaian Spesifikasi BBM' }}</td>
                <td style="text-align:center;font-weight:bold;background:{{ $bgVerdict }};color:{{ $colorVerdict }};">
                    {{ $verdict }}
                </td>
                <td style="text-align:left;">{{ $detailStr }}</td>
                <td style="text-align:left;">{{ $item->catatan ?: '-' }}</td>
                <td style="text-align:left;">{{ $item->user?->name ?: 'Admin Lab QQ' }}</td>
                <td style="text-align:center;font-size:9px;color:#64748b;">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align:center;color:#64748b;padding:12px;">
                    Tidak ada data hasil pengujian yang ditemukan.
                </td>
            </tr>
        @endforelse
    </table>
</body>
</html>
