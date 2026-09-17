@extends('layouts.app')
@section('title', 'Detail Checklist — ' . $checklist->nomor_polisi)

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <a href="{{ route('checklist-mt-maos.index') }}"
       class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-card transition hover:bg-slate-50">
        <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
    </a>
    <div class="flex flex-wrap items-center gap-2">
        <button id="export-btn"
                class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-card transition hover:bg-slate-50">
            <i data-lucide="sheet" class="h-4 w-4 text-emerald-600"></i> Excel
        </button>
        <a href="{{ route('checklist-mt-maos.cetak', $checklist) }}" target="_blank"
           class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition hover:bg-brand-blueDark">
            <i data-lucide="printer" class="h-4 w-4"></i> Cetak / PDF
        </a>
        @if (!auth()->user()->isSpbu())
            <a href="{{ route('checklist-mt-maos.edit', $checklist) }}"
               class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-xl bg-brand-red px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:bg-brand-redDark">
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
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Waktu Diupload / Di-add</p>
        <p class="mt-1 font-bold text-slate-700 flex items-center gap-1.5">
            <i data-lucide="clock" class="h-3.5 w-3.5 text-[#006CB8]"></i>
            {{ $checklist->created_at ? $checklist->created_at->translatedFormat('d F Y, H:i') . ' WIB' : $checklist->tanggal_periksa->translatedFormat('d F Y') }}
        </p>
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
                <th class="px-4 py-3">Kompartemen</th>
                <th class="px-4 py-3">a. T2 Tera</th>
                <th class="px-4 py-3">b. T2 Act</th>
                <th class="px-4 py-3">c. Selisih</th>
                <th class="px-4 py-3">d. Dudukan</th>
                <th class="px-4 py-3">e. Volume</th>
                <th class="px-4 py-3">f. Ijk Baut &amp; Segel</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($checklist->tera as $t)
            <tr>
                <td class="px-4 py-2.5 font-bold text-slate-700">Kompartemen {{ $t['komp'] }}</td>
                <td class="px-4 py-2.5 font-semibold text-slate-800">{{ $t['tinggiTera'] ?: ($t['a'] ?? '-') }}</td>
                <td class="px-4 py-2.5 font-semibold text-slate-800">{{ $t['tinggiAct'] ?: ($t['b'] ?? '-') }}</td>
                <td class="px-4 py-2.5 font-semibold text-slate-800">{{ $t['selisih'] ?: ($t['c'] ?? '-') }}</td>
                <td class="px-4 py-2.5">{{ $t['duduk'] ?: ($t['d'] ?? '-') }}</td>
                <td class="px-4 py-2.5">{{ $t['volume'] ?: ($t['e'] ?? '-') }}</td>
                <td class="px-4 py-2.5">{{ $t['ijkBaut'] ?: ($t['f'] ?? '-') }}</td>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script>
document.getElementById('export-btn').onclick = async () => {
    const btn = document.getElementById('export-btn');
    const labelAwal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span>⏳</span> Menyiapkan Excel...';

    try {
        const res = await fetch("{{ route('checklist-mt-maos.export', $checklist) }}");
        const data = await res.json();

        const BIRU = 'FF2F6FA6';
        const BIRU_TUA = 'FF1E3A8A';
        const KUNING = 'FFFFD400';
        const MERAH = 'FFE04B3E';
        const HIJAU = 'FF3CB878';
        const ABU_TERANG = 'FFF8FAFC';
        const ABU_HEADER = 'FFE2E8F0';
        const PUTIH = 'FFFFFFFF';
        const thinBorder = { style: 'thin', color: { argb: 'FFCBD5E1' } };
        const applyBorder = (cell) => { cell.border = { top: thinBorder, left: thinBorder, bottom: thinBorder, right: thinBorder }; };

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet('Checklist MT');
        ws.columns = [
            { width: 8 },   // A: No
            { width: 38 },  // B: Item
            { width: 12 },  // C: Temuan
            { width: 14 },  // D: Dispensasi
            { width: 18 },  // E: Hasil
            { width: 52 },  // F: Keterangan / Catatan
        ];

        // 1. JUDUL RESMI
        ws.mergeCells('A1:F1');
        const c1 = ws.getCell('A1');
        c1.value = 'PERTAMINA PATRA NIAGA — FUEL TERMINAL MAOS';
        c1.font = { bold: true, size: 12, color: { argb: PUTIH } };
        c1.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU_TUA } };
        c1.alignment = { horizontal: 'center', vertical: 'middle' };
        ws.getRow(1).height = 24;

        ws.mergeCells('A2:F2');
        const c2 = ws.getCell('A2');
        c2.value = 'FORM PEMERIKSAAN MOBIL TANGKI';
        c2.font = { bold: true, size: 11, color: { argb: 'FF0F172A' } };
        c2.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: KUNING } };
        c2.alignment = { horizontal: 'center', vertical: 'middle' };
        ws.getRow(2).height = 20;

        // 2. IDENTITAS KENDARAAN (KOTAK RAPI)
        let r = 4;
        const addMetaRow = (label1, val1, label2, val2) => {
            const row = ws.getRow(r);
            row.getCell(1).value = label1;
            row.getCell(1).font = { bold: true, size: 9.5, color: { argb: 'FF475569' } };
            row.getCell(1).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: ABU_TERANG } };
            
            row.getCell(2).value = val1;
            row.getCell(2).font = { bold: true, size: 10, color: { argb: 'FF0F172A' } };

            row.getCell(3).value = label2;
            row.getCell(3).font = { bold: true, size: 9.5, color: { argb: 'FF475569' } };
            row.getCell(3).fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: ABU_TERANG } };

            ws.mergeCells(`D${r}:F${r}`);
            const cellVal2 = row.getCell(4);
            cellVal2.value = val2;
            cellVal2.font = { bold: true, size: 9.5, color: { argb: 'FF0F172A' } };

            for (let c = 1; c <= 6; c++) applyBorder(row.getCell(c));
            row.height = 20;
            r++;
        };

        addMetaRow('Nomor Polisi', data.nomor_polisi, 'Pemilik / SPBU', data.pemilik);
        addMetaRow('Tgl Periksa', data.tanggal, 'Exp Sertifikat Tera', data.exp_tera);
        addMetaRow('Pemeriksa', data.created_by, 'Status Hasil', data.status_label);
        r++;

        // 3. SEKSI 1-2: PENGUKURAN KOMPARTEMEN & MASA TERA
        ws.mergeCells(`A${r}:F${r}`);
        const sec1 = ws.getCell(`A${r}`);
        sec1.value = '1–2 · PENGUKURAN KOMPARTEMEN TANGKI & MASA SERTIFIKAT TERA';
        sec1.font = { bold: true, size: 10, color: { argb: PUTIH } };
        sec1.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
        sec1.alignment = { vertical: 'middle' };
        applyBorder(sec1);
        ws.getRow(r).height = 22;
        r++;

        // Header Sub-tabel Kompartemen
        const kompHeader = ws.getRow(r);
        const kompLabels = ['Kompartemen', 'a. T2 Tera', 'b. T2 Act', 'c. Selisih', 'd. Dudukan Tangki', 'e. Volume & f. Segel'];
        kompLabels.forEach((lbl, idx) => {
            const cell = kompHeader.getCell(idx + 1);
            cell.value = lbl;
            cell.font = { bold: true, size: 9, color: { argb: 'FF334155' } };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: ABU_HEADER } };
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
            applyBorder(cell);
        });
        kompHeader.height = 20;
        r++;

        // Isi 4 Kompartemen
        data.tera.forEach((t, i) => {
            const row = ws.getRow(r);
            row.getCell(1).value = 'Kompartemen ' + (t.komp || (i + 1));
            row.getCell(1).font = { bold: true, size: 9 };
            row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };

            row.getCell(2).value = t.tinggiTera || t.a || '-';
            row.getCell(3).value = t.tinggiAct || t.b || '-';
            row.getCell(4).value = t.selisih || t.c || '-';
            row.getCell(5).value = t.duduk || t.d || '-';
            row.getCell(6).value = (t.volume || t.e ? ('Vol: ' + (t.volume || t.e)) : '') + (t.ijkBaut || t.f ? (' | Segel: ' + (t.ijkBaut || t.f)) : '-');

            for (let c = 1; c <= 6; c++) {
                if (c > 1 && c < 6) row.getCell(c).alignment = { horizontal: 'center', vertical: 'middle' };
                else if (c === 6) row.getCell(c).alignment = { vertical: 'middle', wrapText: true };
                row.getCell(c).font = { size: 9 };
                applyBorder(row.getCell(c));
            }
            row.height = 19;
            r++;
        });
        r++;

        // 4. SEKSI 3-17: KONDISI FISIK & PERLENGKAPAN
        ws.mergeCells(`A${r}:F${r}`);
        const sec2 = ws.getCell(`A${r}`);
        sec2.value = '3–17 · KONDISI FISIK & PERLENGKAPAN MOBIL TANGKI';
        sec2.font = { bold: true, size: 10, color: { argb: PUTIH } };
        sec2.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
        sec2.alignment = { vertical: 'middle' };
        applyBorder(sec2);
        ws.getRow(r).height = 22;
        r++;

        // Header Tabel Fisik
        const fisHeader = ws.getRow(r);
        const fisLabels = ['No', 'Item Pemeriksaan', 'Temuan', 'Dispensasi', 'Hasil', 'Catatan & Keterangan Lengkap'];
        fisLabels.forEach((lbl, idx) => {
            const cell = fisHeader.getCell(idx + 1);
            cell.value = lbl;
            cell.font = { bold: true, size: 9, color: { argb: 'FF334155' } };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: ABU_HEADER } };
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
            applyBorder(cell);
        });
        fisHeader.height = 20;
        r++;

        // Render Grup & Sub-item
        data.items_grouped.forEach(sec => {
            if (sec.group) {
                // Baris Group Title
                ws.mergeCells(`A${r}:F${r}`);
                const gCell = ws.getCell(`A${r}`);
                gCell.value = sec.no + '. ' + sec.item + ' :-';
                gCell.font = { bold: true, size: 9.5, color: { argb: 'FF1E293B' } };
                gCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF1F5F9' } };
                gCell.alignment = { vertical: 'middle' };
                applyBorder(gCell);
                ws.getRow(r).height = 20;
                r++;

                sec.sub.forEach((s, subIdx) => {
                    const key = sec.no + '-' + subIdx;
                    const res = data.results[key] || null;
                    const note = (data.notes[key] || '').trim();
                    const row = ws.getRow(r);

                    row.getCell(1).value = sec.no + '.' + (subIdx + 1);
                    row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };

                    row.getCell(2).value = s.label;
                    row.getCell(2).font = { size: 9.5 };

                    row.getCell(3).value = s.temuan;
                    row.getCell(3).alignment = { horizontal: 'center', vertical: 'middle' };

                    row.getCell(4).value = s.disp || '-';
                    row.getCell(4).alignment = { horizontal: 'center', vertical: 'middle' };

                    const hasilCell = row.getCell(5);
                    if (res === 'ok') {
                        hasilCell.value = 'Sesuai';
                        hasilCell.font = { bold: true, color: { argb: 'FF166534' }, size: 9 };
                        hasilCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD1FAE5' } };
                    } else if (res === 'bad') {
                        hasilCell.value = 'Temuan';
                        hasilCell.font = { bold: true, color: { argb: PUTIH }, size: 9 };
                        hasilCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: MERAH } };
                    } else {
                        hasilCell.value = '-';
                        hasilCell.font = { color: { argb: 'FF94A3B8' }, size: 9 };
                    }
                    hasilCell.alignment = { horizontal: 'center', vertical: 'middle' };

                    row.getCell(6).value = s.ket + (note ? (' | CATATAN: ' + note) : '');
                    row.getCell(6).alignment = { vertical: 'middle', wrapText: true };
                    row.getCell(6).font = { size: 9 };

                    for (let c = 1; c <= 6; c++) applyBorder(row.getCell(c));
                    r++;
                });
            } else {
                const key = sec.no;
                const res = data.results[key] || null;
                const note = (data.notes[key] || '').trim();
                const row = ws.getRow(r);

                row.getCell(1).value = sec.no;
                row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };

                row.getCell(2).value = sec.item;
                row.getCell(2).font = { size: 9.5, bold: true };

                row.getCell(3).value = sec.temuan;
                row.getCell(3).alignment = { horizontal: 'center', vertical: 'middle' };

                row.getCell(4).value = sec.disp || '-';
                row.getCell(4).alignment = { horizontal: 'center', vertical: 'middle' };

                const hasilCell = row.getCell(5);
                if (res === 'ok') {
                    hasilCell.value = 'Sesuai';
                    hasilCell.font = { bold: true, color: { argb: 'FF166534' }, size: 9 };
                    hasilCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD1FAE5' } };
                } else if (res === 'bad') {
                    hasilCell.value = 'Temuan';
                    hasilCell.font = { bold: true, color: { argb: PUTIH }, size: 9 };
                    hasilCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: MERAH } };
                } else {
                    hasilCell.value = '-';
                    hasilCell.font = { color: { argb: 'FF94A3B8' }, size: 9 };
                }
                hasilCell.alignment = { horizontal: 'center', vertical: 'middle' };

                row.getCell(6).value = sec.ket + (note ? (' | CATATAN: ' + note) : '');
                row.getCell(6).alignment = { vertical: 'middle', wrapText: true };
                row.getCell(6).font = { size: 9 };

                for (let c = 1; c <= 6; c++) applyBorder(row.getCell(c));
                r++;
            }
        });

        // 5. KETERANGAN TAMBAHAN
        if (data.ket_tambahan) {
            r++;
            ws.mergeCells(`A${r}:F${r}`);
            const kCell = ws.getCell(`A${r}`);
            kCell.value = 'KETERANGAN TAMBAHAN: ' + data.ket_tambahan;
            kCell.font = { bold: true, italic: true, size: 9.5, color: { argb: 'FF92400E' } };
            kCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFEF3C7' } };
            kCell.alignment = { vertical: 'middle', wrapText: true };
            applyBorder(kCell);
            ws.getRow(r).height = 24;
            r++;
        }

        // 6. BLOK TANDA TANGAN
        r += 2;
        const signRow1 = ws.getRow(r);
        signRow1.getCell(2).value = 'Pemeriksa,';
        signRow1.getCell(5).value = 'Transportir / Pemilik,';
        signRow1.getCell(2).font = { bold: true, size: 9.5 };
        signRow1.getCell(5).font = { bold: true, size: 9.5 };
        signRow1.getCell(2).alignment = { horizontal: 'center' };
        signRow1.getCell(5).alignment = { horizontal: 'center' };

        r++;
        const signRow2 = ws.getRow(r);
        signRow2.getCell(2).value = 'PT. PERTAMINA PATRA NIAGA';
        signRow2.getCell(5).value = 'PT. PATRA LOGISTIK / PEMILIK';
        signRow2.getCell(2).font = { bold: true, size: 9.5 };
        signRow2.getCell(5).font = { bold: true, size: 9.5 };
        signRow2.getCell(2).alignment = { horizontal: 'center' };
        signRow2.getCell(5).alignment = { horizontal: 'center' };

        r += 4;
        const signRow3 = ws.getRow(r);
        signRow3.getCell(2).value = '( ........................................ )';
        signRow3.getCell(5).value = '( ........................................ )';
        signRow3.getCell(2).alignment = { horizontal: 'center' };
        signRow3.getCell(5).alignment = { horizontal: 'center' };

        // Download file .xlsx
        const buffer = await wb.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const blobUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = data.filename + '.xlsx';
        document.body.appendChild(link);
        link.click();
        setTimeout(() => {
            document.body.removeChild(link);
            URL.revokeObjectURL(blobUrl);
        }, 1000);

    } catch (err) {
        console.error(err);
        alert('Gagal membuat Excel. Coba lagi ya.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = labelAwal;
    }
};
</script>
@endsection
