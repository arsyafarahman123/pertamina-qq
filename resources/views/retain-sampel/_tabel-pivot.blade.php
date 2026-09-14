{{--
    Tabel pivot 1 sesi: kolom = Produk yang ada datanya, baris = MT Nopol,
    Density Obs, Density'15, Temperatur, Tangki Timbun — format PLEK sama
    kayak tabel broadcast WA "RETAIN SAMPEL PENYALURAN MT – FT MAOS".
    Vars: $produkAda (array produk => entry, urutan sesuai $produkList),
          $variant ('web' pakai style Tailwind biasa, 'cetak' pakai border tabel cetak)
--}}
@php
    $baris = [
        'MT Nopol' => fn ($e) => $e->mt_nopol ?: '-',
        'Density Obs' => fn ($e) => number_format($e->density_obs, 4),
        "Density'15" => fn ($e) => number_format($e->density_15, 4),
        'Temperatur' => fn ($e) => rtrim(rtrim(number_format($e->temperatur, 2), '0'), '.'),
        'Tangki Timbun' => fn ($e) => $e->tangki_timbun ?: '-',
    ];
@endphp

@if (empty($produkAda))
    <p class="px-4 py-6 text-center text-xs text-slate-300">Belum ada data.</p>
@else
    <table class="w-full border-collapse text-xs sm:text-sm">
        <thead>
            <tr class="{{ $variant === 'cetak' ? 'bg-slate-100 text-slate-500' : 'bg-slate-50 text-slate-500' }} text-left uppercase tracking-wide">
                <th class="{{ $variant === 'cetak' ? 'border border-slate-200' : 'border-b border-slate-100' }} px-3 py-2 font-semibold">Produk</th>
                @foreach ($produkAda as $produk => $e)
                    <th class="{{ $variant === 'cetak' ? 'border border-slate-200' : 'border-b border-slate-100' }} px-3 py-2 font-semibold">{{ $produk }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($baris as $labelBaris => $ambil)
                <tr>
                    <td class="{{ $variant === 'cetak' ? 'border border-slate-200' : '' }} px-3 py-2 font-bold text-slate-600">{{ $labelBaris }}</td>
                    @foreach ($produkAda as $e)
                        <td class="{{ $variant === 'cetak' ? 'border border-slate-200' : '' }} px-3 py-2 {{ $labelBaris === "Density'15" ? 'font-bold text-brand-blue' : 'text-slate-700' }}">
                            {{ $ambil($e) }}{{ $labelBaris === 'Temperatur' ? '°C' : '' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
