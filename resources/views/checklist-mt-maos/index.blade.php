@extends('layouts.app')
@section('title', 'Checklist Mobil Tangki')

@section('content')

<!-- ===== Header aksi ===== -->
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <p class="text-sm text-slate-500">Digitalisasi Form Pemeriksaan Mobil Tangki — Fuel Terminal Maos</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <button id="export-all-btn" onclick="triggerExport()"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-card transition hover:bg-slate-50">
            <i data-lucide="sheet" class="h-4 w-4 text-emerald-600"></i> <span id="export-btn-label">Export Excel Semua</span>
        </button>
        <button id="export-pdf-all-btn" onclick="triggerPdfExport()"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-card transition hover:bg-slate-50">
            <i data-lucide="file-text" class="h-4 w-4 text-[#006CB8]"></i> <span id="export-pdf-btn-label">Export PDF Semua</span>
        </button>
        @if (!auth()->user()->isSpbu())
            <a href="{{ route('checklist-mt-maos.create') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-brand-red px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:-translate-y-0.5 hover:bg-brand-redDark">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Data Checklist
            </a>
        @endif
    </div>
</div>

<!-- ===== Bar Pilihan Export Terpilih (Muncul saat ada checkbox dicentang) ===== -->
<div id="selection-bar" class="mb-5 hidden items-center justify-between gap-3 rounded-2xl border border-brand-blue/30 bg-gradient-to-r from-brand-blue/10 via-blue-50/80 to-brand-blue/5 p-4 shadow-card">
    <div class="flex items-center gap-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-blue text-white shadow-sm">
            <i data-lucide="check-square" class="h-5 w-5"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800"><span id="selected-count" class="rounded-md bg-brand-blue px-2 py-0.5 text-xs text-white">0</span> Checklist Dipilih</p>
            <p class="text-xs text-slate-500">Anda dapat mengekspor rekap hanya untuk armada yang Anda centang.</p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="exportSelected()" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition hover:bg-emerald-700">
            <i data-lucide="sheet" class="h-4 w-4"></i> Export Terpilih ke Excel
        </button>
        <button type="button" onclick="exportSelectedPdf()" class="inline-flex items-center gap-1.5 rounded-xl bg-[#006CB8] px-4 py-2 text-xs font-bold text-white shadow-md shadow-blue-600/20 transition hover:bg-blue-700">
            <i data-lucide="file-text" class="h-4 w-4"></i> Export Terpilih ke PDF
        </button>
        <button type="button" onclick="uncheckAll()" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 shadow-sm hover:bg-slate-50">
            Batal Pilih
        </button>
    </div>
</div>

<!-- ===== Ringkasan ===== -->
<div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Total Checklist</p>
        <p class="mt-1 text-2xl font-extrabold text-slate-800">{{ $stats['total'] }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">7 Hari Terakhir</p>
        <p class="mt-1 text-2xl font-extrabold text-brand-blue">{{ $stats['this_week'] }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Sesuai Standar</p>
        <p class="mt-1 text-2xl font-extrabold text-emerald-600">{{ $stats['clean'] }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Ada Temuan</p>
        <p class="mt-1 text-2xl font-extrabold text-brand-red">{{ $stats['flagged'] }}</p>
    </div>
</div>

<!-- ===== Pencarian ===== -->
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
    <form method="GET" class="flex gap-3">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <i data-lucide="search" class="h-[18px] w-[18px]"></i>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor polisi atau pemilik / SPBU..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-11 pr-3.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </div>
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition hover:bg-brand-blueDark">
            <i data-lucide="search" class="h-4 w-4"></i>
        </button>
    </form>
</div>

<!-- ===== Tabel ===== -->
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
        <p class="text-sm font-semibold text-slate-700">
            Ditemukan <span class="rounded-md bg-brand-blue/10 px-2 py-0.5 font-bold text-brand-blue">{{ $checklists->count() }}</span> checklist
        </p>
        <span class="text-xs text-slate-400">Centang baris untuk mengekspor sebagian data</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="w-12 px-4 py-3.5 text-center">
                        <input type="checkbox" id="check-all" title="Pilih Semua Checklist"
                               class="h-4 w-4 cursor-pointer rounded border-slate-300 text-brand-blue focus:ring-brand-blue/20">
                    </th>
                    <th class="px-4 py-3.5">Nomor Polisi</th>
                    <th class="px-4 py-3.5">Pemilik</th>
                    <th class="px-4 py-3.5">Tgl Periksa</th>
                    <th class="px-4 py-3.5">Status</th>
                    <th class="px-4 py-3.5">Diperiksa Oleh</th>
                    <th class="px-4 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($checklists as $c)
                    <tr id="row-{{ $c->id }}" class="group transition hover:bg-brand-blue/[0.03]">
                        <td class="w-12 px-4 py-4 text-center">
                            <input type="checkbox" value="{{ $c->id }}"
                                   class="row-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-brand-blue focus:ring-brand-blue/20"
                                   onchange="updateSelection()">
                        </td>
                        <td class="px-4 py-4">
                            <span class="rounded-lg bg-brand-dark px-2.5 py-1 font-mono text-xs font-extrabold tracking-wide text-white">{{ $c->nomor_polisi }}</span>
                        </td>
                        <td class="px-4 py-4 font-medium text-slate-700">{{ $c->pemilik ?: '-' }}</td>
                        <td class="px-4 py-4 text-slate-500">{{ $c->tanggal_periksa->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-4">
                            @if ($c->isFlagged())
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-brand-red ring-1 ring-inset ring-red-200">
                                        <i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i> {{ $c->flagCount() }} Temuan
                                    </span>
                                    <div class="mt-1 space-y-0.5 text-[11px] leading-tight">
                                        @foreach($c->summaryTemuan() as $st)
                                            <div class="flex items-start gap-1 text-slate-700">
                                                <span class="font-black text-brand-red">•</span>
                                                <span class="font-medium text-slate-700">{{ $st }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <i data-lucide="circle-check" class="h-3.5 w-3.5"></i> Sesuai
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-slate-500">{{ $c->created_by ?: '-' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-1 opacity-80 transition group-hover:opacity-100">
                                <a href="{{ route('checklist-mt-maos.show', $c) }}" title="Lihat detail"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-brand-blue transition hover:bg-brand-blue/10">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                @if (!auth()->user()->isSpbu())
                                    <a href="{{ route('checklist-mt-maos.edit', $c) }}" title="Edit checklist"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </a>
                                    <form id="form-hapus-{{ $c->id }}" method="POST" action="{{ route('checklist-mt-maos.destroy', $c) }}" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" title="Hapus checklist"
                                                @click="window.__confirmDeleteModal.show(
                                                    document.getElementById('form-hapus-{{ $c->id }}'),
                                                    'Hapus Checklist Ini?',
                                                    'Checklist mobil tangki {{ $c->nomor_polisi }} akan dihapus permanen.'
                                                )"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-rose-500 transition hover:bg-rose-50">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-20 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50">
                                <i data-lucide="truck" class="h-8 w-8 text-slate-300"></i>
                            </div>
                            <p class="mt-4 text-sm font-semibold text-slate-600">Belum ada data checklist</p>
                            <p class="mt-1 text-xs text-slate-400">
                                @if (!auth()->user()->isSpbu())
                                    Mulai pemeriksaan pertama dengan tombol "Tambah Data Checklist" di atas.
                                @else
                                    Hasil pemeriksaan mobil tangki akan muncul di sini.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
}

function updateSelection() {
    const selected = getSelectedIds();
    const count = selected.length;
    const allCbs = document.querySelectorAll('.row-checkbox');
    const checkAll = document.getElementById('check-all');
    const selectionBar = document.getElementById('selection-bar');
    const countLabel = document.getElementById('selected-count');
    const exportBtnLabel = document.getElementById('export-btn-label');
    const exportPdfBtnLabel = document.getElementById('export-pdf-btn-label');

    // Update baris terpilih
    allCbs.forEach(cb => {
        const tr = document.getElementById('row-' + cb.value);
        if (tr) {
            if (cb.checked) {
                tr.classList.add('bg-blue-50/60');
            } else {
                tr.classList.remove('bg-blue-50/60');
            }
        }
    });

    if (checkAll) {
        checkAll.checked = (allCbs.length > 0 && selected.length === allCbs.length);
        checkAll.indeterminate = (selected.length > 0 && selected.length < allCbs.length);
    }

    if (count > 0) {
        if (selectionBar) {
            selectionBar.classList.remove('hidden');
            selectionBar.classList.add('flex');
        }
        if (countLabel) countLabel.textContent = count;
        if (exportBtnLabel) exportBtnLabel.textContent = 'Export Excel (' + count + ' Terpilih)';
        if (exportPdfBtnLabel) exportPdfBtnLabel.textContent = 'Export PDF (' + count + ' Terpilih)';
    } else {
        if (selectionBar) {
            selectionBar.classList.add('hidden');
            selectionBar.classList.remove('flex');
        }
        if (exportBtnLabel) exportBtnLabel.textContent = 'Export Excel Semua';
        if (exportPdfBtnLabel) exportPdfBtnLabel.textContent = 'Export PDF Semua';
    }

    if (window.lucide) lucide.createIcons();
}

document.getElementById('check-all')?.addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateSelection();
});

function uncheckAll() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = false;
    });
    if (document.getElementById('check-all')) {
        document.getElementById('check-all').checked = false;
    }
    updateSelection();
}

function exportSelected() {
    const selected = getSelectedIds();
    if (!selected.length) {
        alert('Silakan centang minimal 1 checklist untuk diekspor.');
        return;
    }
    runExport(selected);
}

function triggerExport() {
    const selected = getSelectedIds();
    runExport(selected);
}

function exportSelectedPdf() {
    const selected = getSelectedIds();
    if (!selected.length) {
        alert('Silakan centang minimal 1 checklist untuk diekspor ke PDF.');
        return;
    }
    runPdfExport(selected);
}

function triggerPdfExport() {
    const selected = getSelectedIds();
    runPdfExport(selected);
}

function runPdfExport(ids = []) {
    let url = "{{ route('checklist-mt-maos.cetak-banyak') }}";
    if (ids && ids.length > 0) {
        url += '?ids=' + ids.join(',');
    }
    window.open(url, '_blank');
}

async function ensureExcelJs() {
    if (window.ExcelJS) return true;
    return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js';
        s.onload = () => resolve(true);
        s.onerror = () => reject(new Error('Gagal memuat pustaka Excel'));
        document.head.appendChild(s);
    });
}

async function runExport(ids = []) {
    const btn = document.getElementById('export-all-btn');
    const labelAwal = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> Menyiapkan rekap...';
    }

    try {
        await ensureExcelJs();

        let url = "{{ route('checklist-mt-maos.export-all') }}";
        if (ids && ids.length > 0) {
            url += '?ids=' + ids.join(',');
        }

        const res = await fetch(url);
        const data = await res.json();
        if (!data.armada || !data.armada.length) {
            alert('Tidak ada data checklist untuk direkap.');
            return;
        }

        const BIRU = 'FF2F6FA6';
        const MERAH = 'FFE04B3E';
        const KUNING = 'FFF2D02B';
        const HIJAU = 'FF3CB878';
        const BIRU_MUDA = 'FF0284C7';
        const PUTIH = 'FFFFFFFF';

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet('Rekap Pemeriksaan');
        ws.columns = [
            { width: 5 }, { width: 16 }, { width: 26 }, { width: 18 },
            { width: 46 }, { width: 16 }, { width: 15 }, { width: 38 }, { width: 14 },
        ];

        // ---- Judul ----
        ws.mergeCells('A1:I1');
        ws.getCell('A1').value = data.judul;
        ws.getCell('A1').font = { bold: true, size: 14, color: { argb: 'FF0F172A' } };
        ws.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };
        ws.getRow(1).height = 24;

        ws.mergeCells('A2:I2');
        ws.getCell('A2').value = data.subjudul;
        ws.getCell('A2').font = { bold: true, size: 11, color: { argb: 'FF475569' } };
        ws.getCell('A2').alignment = { horizontal: 'center', vertical: 'middle' };
        ws.getRow(2).height = 18;

        // ---- Header tabel ----
        const headerRow = ws.getRow(4);
        const headerLabel = ['No.', 'No.Pol', 'Pemilik', 'Tgl. Pemeriksaan', 'Temuan Pemeriksaan', 'Status', 'Tindak Lanjut', 'Catatan & Keterangan', 'Tanggal TL'];
        headerLabel.forEach((label, i) => {
            const cell = headerRow.getCell(i + 1);
            cell.value = label;
            cell.font = { bold: true, color: { argb: PUTIH }, size: 10 };
            cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
        });
        headerRow.height = 24;

        const thinBorder = { style: 'thin', color: { argb: 'FFBFBFBF' } };
        const applyBorder = (cell) => { cell.border = { top: thinBorder, left: thinBorder, bottom: thinBorder, right: thinBorder }; };

        let r = 5;

        data.armada.forEach((a, idx) => {
            const rowStart = r;
            const temuanList = a.temuan.length ? a.temuan : [{ label: 'Tidak ada temuan (Semua komponen sesuai standar)', kategori: null, disp: '-', catatan: '-' }];

            temuanList.forEach((t) => {
                const row = ws.getRow(r);
                row.getCell(5).value = (t.kategori ? '- ' : '') + t.label;
                row.getCell(5).alignment = { vertical: 'middle', wrapText: true };
                row.getCell(5).font = { size: 9.5 };

                const statusCell = row.getCell(6);
                if (t.kategori === 'Mandatory') {
                    statusCell.value = 'Mandatory';
                    statusCell.font = { bold: true, color: { argb: PUTIH }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: MERAH } };
                } else if (t.kategori === 'Non Mandatory') {
                    statusCell.value = 'Non Mandatory';
                    statusCell.font = { bold: true, color: { argb: 'FF5A4500' }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: KUNING } };
                } else if (t.kategori === 'Catatan') {
                    statusCell.value = 'Catatan';
                    statusCell.font = { bold: true, color: { argb: PUTIH }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU_MUDA } };
                } else {
                    statusCell.value = 'Sesuai Standar';
                    statusCell.font = { bold: true, color: { argb: 'FF166534' }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD1FAE5' } };
                }
                statusCell.alignment = { horizontal: 'center', vertical: 'middle' };

                row.getCell(7).value = t.disp || '-';
                row.getCell(7).alignment = { horizontal: 'center', vertical: 'middle' };
                row.getCell(7).font = { size: 9.5 };

                row.getCell(8).value = t.catatan || '-';
                row.getCell(8).alignment = { vertical: 'middle', wrapText: true };
                row.getCell(8).font = { size: 9.5 };

                row.getCell(9).value = '';
                [5, 6, 7, 8, 9].forEach((c) => applyBorder(row.getCell(c)));
                r++;
            });

            // ---- Baris banner "Pemeriksaan Kekedapan Mobil Tangki" ----
            ws.mergeCells(`E${r}:I${r}`);
            const bannerCell = ws.getCell(`E${r}`);
            bannerCell.value = 'Pemeriksaan Kekedapan Mobil Tangki';
            bannerCell.font = { bold: true, color: { argb: PUTIH }, size: 9.5 };
            bannerCell.alignment = { horizontal: 'left', vertical: 'middle' };
            bannerCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
            applyBorder(bannerCell);
            r++;

            // ---- Baris hasil kekedapan ----
            const kedapRow = ws.getRow(r);
            kedapRow.getCell(5).value = a.kedap ? '- Tidak terdapat kebocoran' : '- Terdapat kebocoran, perlu tindak lanjut';
            kedapRow.getCell(5).alignment = { vertical: 'middle', wrapText: true };
            kedapRow.getCell(5).font = { size: 9.5 };

            const kedapStatus = kedapRow.getCell(6);
            kedapStatus.value = a.kedap ? 'Kedap' : 'Bocor';
            kedapStatus.font = { bold: true, color: { argb: PUTIH }, size: 9 };
            kedapStatus.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: a.kedap ? HIJAU : MERAH } };
            kedapStatus.alignment = { horizontal: 'center', vertical: 'middle' };

            kedapRow.getCell(7).value = '-';
            kedapRow.getCell(7).alignment = { horizontal: 'center', vertical: 'middle' };

            kedapRow.getCell(8).value = '-';
            kedapRow.getCell(8).alignment = { horizontal: 'center', vertical: 'middle' };

            kedapRow.getCell(9).value = '';
            [5, 6, 7, 8, 9].forEach((c) => applyBorder(kedapRow.getCell(c)));
            r++;

            const rowEnd = r - 1;

            // ---- Kolom No / No.Pol / Pemilik / Tanggal digabung utk 1 blok armada ----
            ws.mergeCells(`A${rowStart}:A${rowEnd}`);
            ws.mergeCells(`B${rowStart}:B${rowEnd}`);
            ws.mergeCells(`C${rowStart}:C${rowEnd}`);
            ws.mergeCells(`D${rowStart}:D${rowEnd}`);

            const cNo = ws.getCell(`A${rowStart}`);
            cNo.value = idx + 1;
            const cPol = ws.getCell(`B${rowStart}`);
            cPol.value = a.nomor_polisi;
            cPol.font = { bold: true, size: 10 };
            const cPemilik = ws.getCell(`C${rowStart}`);
            cPemilik.value = a.pemilik;
            cPemilik.font = { size: 9.5 };
            const cTgl = ws.getCell(`D${rowStart}`);
            cTgl.value = a.tanggal;
            cTgl.font = { size: 9.5 };

            [cNo, cPol, cPemilik, cTgl].forEach((cell) => {
                cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                applyBorder(cell);
            });

            // Perapian border sel kosong hasil merge (baris demi baris supaya border tetap utuh saat dicetak)
            for (let rr = rowStart; rr <= rowEnd; rr++) {
                [1, 2, 3, 4].forEach((c) => applyBorder(ws.getRow(rr).getCell(c)));
            }
        });

        const buffer = await wb.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const blobUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = blobUrl;
        const filename = (ids && ids.length > 0) ? (data.filename + '_Pilihan_' + ids.length + '_Armada') : data.filename;
        link.download = filename + '.xlsx';
        document.body.appendChild(link);
        link.click();
        setTimeout(() => {
            document.body.removeChild(link);
            URL.revokeObjectURL(blobUrl);
        }, 1000);
    } catch (e) {
        console.error(e);
        alert('Gagal membuat rekap Excel. Coba lagi ya.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = labelAwal;
        }
    }
}
</script>
@endsection
