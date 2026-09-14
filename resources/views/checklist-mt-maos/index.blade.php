@extends('layouts.app')
@section('title', 'Checklist Mobil Tangki')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>

<!-- ===== Header aksi ===== -->
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <p class="text-sm text-slate-500">Digitalisasi Form Pemeriksaan Mobil Tangki — Fuel Terminal Maos</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <button id="export-all-btn"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-card transition hover:bg-slate-50">
            <i data-lucide="sheet" class="h-4 w-4"></i> Export Excel
        </button>
        @if (!auth()->user()->isSpbu())
            <a href="{{ route('checklist-mt-maos.create') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-brand-red px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:-translate-y-0.5 hover:bg-brand-redDark">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Data Checklist
            </a>
        @endif
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
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3.5">Nomor Polisi</th>
                    <th class="px-5 py-3.5">Pemilik</th>
                    <th class="px-5 py-3.5">Tgl Periksa</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Diperiksa Oleh</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($checklists as $c)
                    <tr class="group transition hover:bg-brand-blue/[0.03]">
                        <td class="px-5 py-4">
                            <span class="rounded-lg bg-brand-dark px-2.5 py-1 font-mono text-xs font-extrabold tracking-wide text-white">{{ $c->nomor_polisi }}</span>
                        </td>
                        <td class="px-5 py-4 font-medium text-slate-700">{{ $c->pemilik ?: '-' }}</td>
                        <td class="px-5 py-4 text-slate-500">{{ $c->tanggal_periksa->translatedFormat('d F Y') }}</td>
                        <td class="px-5 py-4">
                            @if ($c->isFlagged())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-brand-red ring-1 ring-inset ring-red-200">
                                    <i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i> {{ $c->flagCount() }} Temuan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <i data-lucide="circle-check" class="h-3.5 w-3.5"></i> Sesuai
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-500">{{ $c->created_by ?: '-' }}</td>
                        <td class="px-5 py-4">
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
                        <td colspan="6" class="px-5 py-20 text-center">
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
document.getElementById('export-all-btn').onclick = async () => {
    const btn = document.getElementById('export-all-btn');
    const labelAwal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span>⏳</span> Menyiapkan rekap...';

    try {
        const res = await fetch("{{ route('checklist-mt-maos.export-all') }}");
        const data = await res.json();
        if (!data.armada.length) { alert('Belum ada data checklist untuk direkap.'); return; }

        const BIRU = 'FF2F6FA6';
        const MERAH = 'FFE04B3E';
        const KUNING = 'FFF2D02B';
        const HIJAU = 'FF3CB878';
        const PUTIH = 'FFFFFFFF';

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet('Rekap Pemeriksaan');
        ws.columns = [
            { width: 5 }, { width: 16 }, { width: 24 }, { width: 16 },
            { width: 42 }, { width: 15 }, { width: 14 }, { width: 26 }, { width: 14 },
        ];

        // ---- Judul ----
        ws.mergeCells('A1:I1');
        ws.getCell('A1').value = data.judul;
        ws.getCell('A1').font = { bold: true, size: 14 };
        ws.getCell('A1').alignment = { horizontal: 'center' };

        ws.mergeCells('A2:I2');
        ws.getCell('A2').value = data.subjudul;
        ws.getCell('A2').font = { bold: true, size: 11 };
        ws.getCell('A2').alignment = { horizontal: 'center' };

        // ---- Header tabel ----
        const headerRow = ws.getRow(4);
        const headerLabel = ['No.', 'No.Pol', 'Pemilik', 'Tgl. Pemeriksaan', 'Temuan Pemeriksaan', 'Status', 'Tindak Lanjut', 'Catatan', 'Tanggal TL'];
        headerLabel.forEach((label, i) => {
            const cell = headerRow.getCell(i + 1);
            cell.value = label;
            cell.font = { bold: true, color: { argb: PUTIH } };
            cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
        });
        headerRow.height = 22;

        const thinBorder = { style: 'thin', color: { argb: 'FFBFBFBF' } };
        const applyBorder = (cell) => { cell.border = { top: thinBorder, left: thinBorder, bottom: thinBorder, right: thinBorder }; };

        let r = 5;

        data.armada.forEach((a, idx) => {
            const rowStart = r;
            const temuanList = a.temuan.length ? a.temuan : [{ label: 'Tidak ada temuan', kategori: null, disp: '-', catatan: '-' }];

            temuanList.forEach((t) => {
                const row = ws.getRow(r);
                row.getCell(5).value = (t.kategori ? '- ' : '') + t.label;
                row.getCell(5).alignment = { vertical: 'middle', wrapText: true };

                const statusCell = row.getCell(6);
                if (t.kategori) {
                    statusCell.value = t.kategori;
                    statusCell.font = { bold: true, color: { argb: t.kategori === 'Mandatory' ? PUTIH : 'FF6B5B00' } };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: t.kategori === 'Mandatory' ? MERAH : KUNING } };
                } else {
                    statusCell.value = 'Sesuai Standar';
                    statusCell.font = { bold: true, color: { argb: 'FF166534' } };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD1FAE5' } };
                }
                statusCell.alignment = { horizontal: 'center', vertical: 'middle' };

                row.getCell(7).value = t.disp || '-';
                row.getCell(8).value = t.catatan || '-';
                row.getCell(9).value = '';
                [5, 6, 7, 8, 9].forEach((c) => applyBorder(row.getCell(c)));
                r++;
            });

            // ---- Baris banner "Pemeriksaan Kekedapan Mobil Tangki" ----
            ws.mergeCells(`E${r}:I${r}`);
            const bannerCell = ws.getCell(`E${r}`);
            bannerCell.value = 'Pemeriksaan Kekedapan Mobil Tangki';
            bannerCell.font = { bold: true, color: { argb: PUTIH } };
            bannerCell.alignment = { horizontal: 'left', vertical: 'middle' };
            bannerCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
            applyBorder(bannerCell);
            r++;

            // ---- Baris hasil kekedapan ----
            const kedapRow = ws.getRow(r);
            kedapRow.getCell(5).value = a.kedap ? '- Tidak terdapat kebocoran' : '- Terdapat kebocoran, perlu tindak lanjut';
            const kedapStatus = kedapRow.getCell(6);
            kedapStatus.value = a.kedap ? 'Kedap' : 'Bocor';
            kedapStatus.font = { bold: true, color: { argb: PUTIH } };
            kedapStatus.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: a.kedap ? HIJAU : MERAH } };
            kedapStatus.alignment = { horizontal: 'center', vertical: 'middle' };
            kedapRow.getCell(7).value = '';
            kedapRow.getCell(8).value = '-';
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
            cPol.font = { bold: true };
            const cPemilik = ws.getCell(`C${rowStart}`);
            cPemilik.value = a.pemilik;
            const cTgl = ws.getCell(`D${rowStart}`);
            cTgl.value = a.tanggal;

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
        const blob = new Blob([buffer], { type: 'application/octet-stream' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = data.filename + '.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (e) {
        console.error(e);
        alert('Gagal membuat rekap Excel. Coba lagi ya.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = labelAwal;
    }
};
</script>
@endsection
