<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Lengkap Retain Sampel MT</title>
</head>
<body>
    <table border="1">
        <tr>
            <td colspan="11" style="font-weight:bold;font-size:14px;">
                RIWAYAT LENGKAP RETAIN SAMPEL PENYALURAN MT — FT MAOS
            </td>
        </tr>
        <tr>
            <td colspan="11">
                Diunduh: {{ now()->translatedFormat('l, d F Y H:i') }} WIB &nbsp;|&nbsp;
                Total seluruh data tersimpan: <b>{{ $totalData }}</b> baris
                (setiap kombinasi tanggal + jam dipisah jadi tabel sendiri di bawah ini)
            </td>
        </tr>

        @php $totalDitampilkan = 0; @endphp

        @forelse ($semua as $kunci => $entries)
            @php
                [$tgl, $jam] = explode('|', $kunci);
                $tglIndo = \Illuminate\Support\Carbon::parse($tgl)->format('d-m-Y');
                $totalDitampilkan += $entries->count();
            @endphp
            <tr></tr>
            <tr>
                <td colspan="11" style="font-weight:bold;background:#0f2a4a;color:#ffffff;">
                    REKAP TANGGAL {{ $tglIndo }} — PUKUL {{ $jam }} WIB ({{ $entries->count() }} data)
                </td>
            </tr>
            <tr style="font-weight:bold;background:#f1f5f9;">
                <td>Tanggal Observasi</td>
                <td>Jam Observasi</td>
                <td>Produk</td>
                <td>MT Nopol</td>
                <td>Density Obs</td>
                <td>Density'15</td>
                <td>Temperatur (°C)</td>
                <td>Tangki Timbun</td>
                <td>Foto</td>
                <td>Waktu Input (Sistem)</td>
                <td>Diinput Oleh</td>
            </tr>
            @foreach ($entries as $e)
                <tr>
                    <td>{{ $tglIndo }}</td>
                    <td>{{ $jam }}</td>
                    <td>{{ $e->produk }}</td>
                    <td>{{ $e->mt_nopol ?: '-' }}</td>
                    <td>{{ number_format($e->density_obs, 4) }}</td>
                    <td>{{ number_format($e->density_15, 4) }}</td>
                    <td>{{ number_format($e->temperatur, 2) }}</td>
                    <td>{{ $e->tangki_timbun ?: '-' }}</td>
                    <td>
                        @if ($e->fotoUrl())
                            <a href="{{ $e->fotoUrl() }}">Lihat Foto</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $e->created_at->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $e->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="11">Belum ada data sama sekali.</td>
            </tr>
        @endforelse

        <tr></tr>
        <tr>
            <td colspan="11" style="font-weight:bold;">
                TOTAL DATA DITAMPILKAN DI FILE INI: {{ $totalDitampilkan }} baris (harus sama dengan total di database: {{ $totalData }})
            </td>
        </tr>
    </table>
</body>
</html>
