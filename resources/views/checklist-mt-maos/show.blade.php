@extends('layouts.app')
@section('title', 'Detail Checklist — ' . $checklist->nomor_polisi)

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('checklist-mt-maos.index') }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-card transition hover:bg-slate-50">
        <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
    </a>
    <div class="flex flex-wrap gap-2">
        <button id="export-btn"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-card transition hover:bg-slate-50">
            <i data-lucide="sheet" class="h-4 w-4"></i> Excel
        </button>
        <a href="{{ route('checklist-mt-maos.cetak', $checklist) }}" target="_blank"
           class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition hover:bg-brand-blueDark">
            <i data-lucide="printer" class="h-4 w-4"></i> Cetak / PDF / PNG
        </a>
        @if (!auth()->user()->isSpbu())
            <a href="{{ route('checklist-mt-maos.edit', $checklist) }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-brand-red px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:bg-brand-redDark">
                <i data-lucide="pencil" class="h-4 w-4"></i> Edit
            </a>
        @endif
    </div>
</div>

<!-- ===== Ringkasan identitas ===== -->
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
    <div class="mb-4 flex flex-wrap items-center justify-between border-b border-slate-100 pb-3 gap-2">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm">
                <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-full w-full object-contain">
            </div>
            <div class="leading-tight">
                <p class="text-sm font-black text-slate-800 tracking-tight">Fuel Maos</p>
                <p class="text-[10px] font-bold text-brand-gold tracking-tight">QC Lab &amp; Checklist Armada</p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Pemeriksaan Mobil Tangki</span>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Nomor Polisi</p>
        <p class="mt-1 text-lg font-extrabold text-slate-800">{{ $checklist->nomor_polisi }}</p>
    </div>
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Pemilik / SPBU</p>
        <p class="mt-1 font-bold text-slate-700">{{ $checklist->pemilik ?: '-' }}</p>
    </div>
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Tanggal Pemeriksaan</p>
        <p class="mt-1 font-bold text-slate-700">{{ $checklist->tanggal_periksa->translatedFormat('d F Y') }}</p>
    </div>
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Exp. Sertifikat Tera</p>
        <p class="mt-1 font-bold text-slate-700">{{ $checklist->tanggal_exp ?: '-' }}</p>
    </div>
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Diperiksa Oleh</p>
        <p class="mt-1 font-bold text-slate-700">{{ $checklist->created_by ?: '-' }}</p>
    </div>
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Status</p>
        @if ($checklist->isFlagged())
            <span class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-brand-red ring-1 ring-inset ring-red-200">
                <i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i> {{ $checklist->flagCount() }} Temuan
            </span>
        @else
            <span class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">
                <i data-lucide="circle-check" class="h-3.5 w-3.5"></i> Sesuai Standar
            </span>
        @endif
    </div>
    </div>
</div>

<!-- ===== Masa Sertifikat Tera ===== -->
<p class="mb-2 mt-2 px-1 text-xs font-extrabold uppercase tracking-wider text-brand-blueDark">1–2 · Masa Sertifikat Tera</p>
<div class="mb-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-card">
    <table class="w-full min-w-[760px] text-left text-xs">
        <thead class="bg-slate-50/80 text-[10px] font-bold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">Komp</th><th class="px-4 py-3">T2 Tera</th><th class="px-4 py-3">T2 Act</th>
                <th class="px-4 py-3">Selisih</th><th class="px-4 py-3">Dudukan</th><th class="px-4 py-3">Volume</th>
                <th class="px-4 py-3">Ijk Baut</th><th class="px-4 py-3">Hasil a–g</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($checklist->tera as $t)
            <tr>
                <td class="px-4 py-2.5 font-bold text-slate-700">Komp. {{ $t['komp'] }}</td>
                <td class="px-4 py-2.5">{{ $t['tinggiTera'] }}</td><td class="px-4 py-2.5">{{ $t['tinggiAct'] }}</td>
                <td class="px-4 py-2.5">{{ $t['selisih'] }}</td><td class="px-4 py-2.5">{{ $t['duduk'] }}</td>
                <td class="px-4 py-2.5">{{ $t['volume'] }}</td><td class="px-4 py-2.5">{{ $t['ijkBaut'] }}</td>
                <td class="px-4 py-2.5 text-slate-500">{{ $t['a'] }}/{{ $t['b'] }}/{{ $t['c'] }}/{{ $t['d'] }}/{{ $t['e'] }}/{{ $t['f'] }}/{{ $t['g'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ===== Item 3-17 ===== -->
<p class="mb-2 px-1 text-xs font-extrabold uppercase tracking-wider text-brand-blueDark">3–17 · Kondisi Fisik &amp; Perlengkapan</p>
<div class="mb-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-card">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="bg-slate-50/80 text-[10px] font-bold uppercase tracking-wide text-slate-500">
            <tr><th class="px-4 py-3">No</th><th class="px-4 py-3">Item</th><th class="px-4 py-3">Temuan</th><th class="px-4 py-3">Dispensasi</th><th class="px-4 py-3">Hasil</th><th class="px-4 py-3">Keterangan</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($grouped as $sec)
                @if(!empty($sec['group']))
                    <tr class="bg-slate-100">
                        <td class="px-4 py-2.5 text-center font-bold text-slate-500">{{ $sec['no'] }}</td>
                        <td colspan="5" class="px-4 py-2.5 font-bold text-slate-700">{{ $sec['item'] }} :</td>
                    </tr>
                    @foreach($sec['sub'] as $i => $s)
                        @php
                            $key = $sec['no'] . '-' . $i;
                            $res = $checklist->results[$key] ?? null;
                            $note = trim($checklist->notes[$key] ?? '');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-center text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 pl-6 font-medium text-slate-700">{{ $s['label'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $s['temuan'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $s['disp'] }}</td>
                            <td class="px-4 py-3">
                                @if($res === 'ok') <span class="inline-flex items-center gap-1 font-bold text-emerald-600"><i data-lucide="check" class="h-4 w-4"></i> Sesuai</span>
                                @elseif($res === 'bad') <span class="inline-flex items-center gap-1 font-bold text-brand-red"><i data-lucide="x" class="h-4 w-4"></i> Temuan</span>
                                @else <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 leading-relaxed text-slate-600">
                                {{ $s['ket'] }}
                                @if($note)
                                    <br><span class="italic text-amber-700">Catatan: {{ $note }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    @php
                        $key = $sec['no'];
                        $res = $checklist->results[$key] ?? null;
                        $note = trim($checklist->notes[$key] ?? '');
                    @endphp
                    <tr>
                        <td class="px-4 py-3 text-center font-semibold text-slate-500">{{ $sec['no'] }}</td>
                        <td class="px-4 py-3 font-medium text-slate-700">{{ $sec['item'] }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $sec['temuan'] }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $sec['disp'] }}</td>
                        <td class="px-4 py-3">
                            @if($res === 'ok') <span class="inline-flex items-center gap-1 font-bold text-emerald-600"><i data-lucide="check" class="h-4 w-4"></i> Sesuai</span>
                            @elseif($res === 'bad') <span class="inline-flex items-center gap-1 font-bold text-brand-red"><i data-lucide="x" class="h-4 w-4"></i> Temuan</span>
                            @else <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 leading-relaxed text-slate-600">
                            {{ $sec['ket'] }}
                            @if($note)
                                <br><span class="italic text-amber-700">Catatan: {{ $note }}</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

@if($checklist->ket_tambahan)
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
        <p class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400">Keterangan Tambahan</p>
        <p class="text-sm text-slate-700">{{ $checklist->ket_tambahan }}</p>
    </div>
@endif

<script>
document.getElementById('export-btn').onclick = async () => {
    const res = await fetch("{{ route('checklist-mt-maos.export', $checklist) }}");
    const data = await res.json();
    const wb = XLSX.utils.book_new();
    data.sheets.forEach((s, i) => {
        const ws = XLSX.utils.aoa_to_sheet(s.rows);
        ws['!cols'] = [{wch:14},{wch:32},{wch:10},{wch:12},{wch:10},{wch:40}];
        XLSX.utils.book_append_sheet(wb, ws, s.name || ('Checklist' + i));
    });
    XLSX.writeFile(wb, data.filename + '.xlsx');
};
</script>
@endsection
