<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sampel Penyaluran MT {{ $tanggal }}</title>
</head>
<body>
    <table border="1">
        <tr>
            <td colspan="8" style="font-weight:bold;font-size:14px;">
                SAMPEL PENYALURAN MT — FT MAOS ({{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }})
            </td>
        </tr>

        @forelse ($rekap as $jam => $produkEntries)
            <tr></tr>
            <tr>
                <td colspan="8" style="font-weight:bold;background:#0f2a4a;color:#ffffff;">
                    REKAP TANGGAL {{ \Illuminate\Support\Carbon::parse($tanggal)->format('d-m-Y') }} — PUKUL {{ $jam }} WIB
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
            @foreach ($produkList as $produk)
                @if (isset($produkEntries[$produk]))
                    @php $e = $produkEntries[$produk]; @endphp
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $jam }}</td>
                        <td>{{ $produk }}</td>
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
                @endif
            @endforeach
        @empty
            <tr>
                <td colspan="11">Belum ada data rekap untuk tanggal ini.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
