@extends('layouts.app')
@section('title', 'Laporan Penyaluran MT — Semua Tanggal')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <p class="text-sm text-slate-500">Sampel Penyaluran MT — FT MAOS. Menampilkan <b>seluruh tanggal</b> sekaligus, gak perlu ganti-ganti filter tanggal.</p>
        <p class="mt-1 text-xs text-slate-400">Total seluruh data tersimpan di sistem: <b class="text-slate-600">{{ $totalDataKeseluruhan }}</b> baris, {{ count($rekapSemua) }} tanggal berbeda.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('retain-sampel.index') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-blue hover:text-brand-blue">
            <i data-lucide="calendar" class="h-4 w-4"></i> Lihat Per Tanggal
        </a>
        <a href="{{ route('retain-sampel.export-excel-all') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-800 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-emerald-900">
            <i data-lucide="database" class="h-4 w-4"></i> Excel Riwayat Lengkap
        </a>
        <a href="{{ route('retain-sampel.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-brand-red px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:-translate-y-0.5 hover:bg-brand-redDark">
            <i data-lucide="plus" class="h-4 w-4"></i> Input Sampel Baru
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
        {{ session('success') }}
    </div>
@endif

{{-- Navigasi cepat lompat ke tanggal tertentu + toggle urutan --}}
@if (count($rekapSemua))
    <div class="mb-6 flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <a href="{{ route('retain-sampel.semua', ['urutan' => $urutan === 'asc' ? 'desc' : 'asc']) }}"
           class="mr-2 inline-flex items-center gap-1.5 rounded-lg bg-brand-blue px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-brand-blueDark">
            <i data-lucide="{{ $urutan === 'asc' ? 'arrow-down-wide-narrow' : 'arrow-up-narrow-wide' }}" class="h-3.5 w-3.5"></i>
            {{ $urutan === 'asc' ? 'Terlama → Terbaru' : 'Terbaru → Terlama' }}
        </a>
        <span class="mr-1 h-5 w-px bg-slate-200"></span>
        @foreach (array_keys($rekapSemua) as $tgl)
            <a href="#tgl-{{ $tgl }}" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-500 transition hover:border-brand-blue hover:text-brand-blue">
                {{ \Illuminate\Support\Carbon::parse($tgl)->translatedFormat('d M Y') }}
            </a>
        @endforeach
    </div>
@endif

@forelse ($rekapSemua as $tanggal => $rekapPerJam)
    <div id="tgl-{{ $tanggal }}" class="mb-3 scroll-mt-20">
        <h2 class="mb-3 flex items-center gap-2 text-base font-extrabold text-slate-800">
            <i data-lucide="calendar-days" class="h-5 w-5 text-brand-red"></i>
            {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
            <a href="{{ route('retain-sampel.cetak', ['tanggal' => $tanggal]) }}" target="_blank"
               class="ml-auto inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-500 hover:border-brand-blue hover:text-brand-blue">
                <i data-lucide="printer" class="h-3.5 w-3.5"></i> Cetak tanggal ini
            </a>
        </h2>

        @foreach ($rekapPerJam as $jam => $produkEntries)
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
                <div class="flex items-center justify-between gap-2 bg-brand-dark px-5 py-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="h-4 w-4 text-brand-gold"></i>
                        <p class="text-sm font-bold text-white">Pukul {{ $jam }} WIB</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-2.5">Produk</th>
                                <th class="px-4 py-2.5">Foto</th>
                                <th class="px-4 py-2.5">MT Nopol</th>
                                <th class="px-4 py-2.5">Density Obs</th>
                                <th class="px-4 py-2.5">Density'15 <span class="normal-case font-normal text-slate-400">(otomatis)</span></th>
                                <th class="px-4 py-2.5">Temperatur</th>
                                <th class="px-4 py-2.5">Tangki Timbun</th>
                                <th class="px-4 py-2.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($produkList as $produk)
                                @if (isset($produkEntries[$produk]))
                                    @php $e = $produkEntries[$produk]; @endphp
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-4 py-2.5 font-bold text-slate-700">{{ $produk }}</td>
                                        <td class="px-4 py-2.5">
                                            @if ($e->fotoUrl() && $e->fotoIsPdf())
                                                <a href="{{ $e->fotoUrl() }}" target="_blank" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 ring-1 ring-slate-200">
                                                    <i data-lucide="file-text" class="h-4 w-4 text-brand-red"></i>
                                                </a>
                                            @elseif ($e->fotoUrl())
                                                <a href="{{ $e->fotoUrl() }}" target="_blank">
                                                    <img src="{{ $e->fotoUrl() }}" class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-200">
                                                </a>
                                            @else
                                                <span class="text-slate-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5">{{ $e->mt_nopol ?: '-' }}</td>
                                        <td class="px-4 py-2.5">{{ number_format($e->density_obs, 4) }}</td>
                                        <td class="px-4 py-2.5 font-bold text-brand-blue">{{ number_format($e->density_15, 4) }}</td>
                                        <td class="px-4 py-2.5">{{ rtrim(rtrim(number_format($e->temperatur, 2), '0'), '.') }}°C</td>
                                        <td class="px-4 py-2.5">{{ $e->tangki_timbun ?: '-' }}</td>
                                        <td class="px-4 py-2.5 text-right">
                                            <a href="{{ route('retain-sampel.edit', $e) }}" class="text-slate-400 hover:text-brand-blue"><i data-lucide="pencil" class="h-4 w-4"></i></a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@empty
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-400">
        Belum ada data rekap sama sekali. Klik <b>Input Sampel Baru</b> untuk mulai mencatat.
    </div>
@endforelse
@endsection
