<!DOCTYPE html>
<html lang="id">
@php
    $namaFileDasar = preg_replace('/[^A-Za-z0-9]+/', '', $checklist->nomor_polisi ?: 'Checklist') . '_' . $checklist->tanggal_periksa->format('d-m-Y');
@endphp
<head>
    <meta charset="UTF-8">
    <title>{{ $namaFileDasar }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        @page { size: A4 portrait; margin: 8mm 8mm 8mm 8mm; }
        body { font-family: 'Arial', 'Segoe UI', sans-serif; color: #000; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            #area-cetak { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
        }
        .btn-unduh { transition: all .15s ease; }
        .btn-unduh:hover { transform: translateY(-1px); }
        .btn-unduh:disabled { opacity: .6; cursor: wait; transform: none; }
        table.tbl-form, table.tbl-form th, table.tbl-form td {
            border: 1px solid #1e293b;
        }
        .sub-cell {
            border-bottom: 1px solid #94a3b8;
        }
        .sub-cell:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body class="bg-slate-200 py-6 text-slate-900">

{{-- ACTION TOP BAR (NO-PRINT) --}}
<div class="no-print mx-auto mb-4 flex max-w-4xl flex-wrap items-center justify-between gap-2.5 px-2">
    <a href="{{ route('checklist-mt-maos.show', $checklist) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
        <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Detail
    </a>

    <div class="flex items-center gap-2">
        <button id="btn-excel" onclick="unduhExcel()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white shadow hover:bg-emerald-800">
            <i data-lucide="file-spreadsheet" class="h-3.5 w-3.5"></i> Unduh Excel
        </button>
        <button id="btn-png" onclick="unduhGambar('image/png', '.png')"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-3.5 py-2 text-xs font-bold text-white shadow hover:bg-slate-900">
            <i data-lucide="image" class="h-3.5 w-3.5"></i> Unduh PNG
        </button>
        <button id="btn-pdf" onclick="unduhPdf()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-[#006CB8] px-3.5 py-2 text-xs font-bold text-white shadow hover:bg-blue-800">
            <i data-lucide="file-text" class="h-3.5 w-3.5"></i> Unduh PDF
        </button>
        <button onclick="window.print()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-[#DA251D] px-4 py-2 text-xs font-bold text-white shadow hover:bg-red-700">
            <i data-lucide="printer" class="h-3.5 w-3.5"></i> Cetak Dokumen
        </button>
    </div>
</div>

{{-- AREA CETAK FORM (PERSIS SESUAI LEMBAR ASLI PERTAMINA MAOS) --}}
<div id="area-cetak" class="mx-auto max-w-4xl bg-white p-6 sm:p-8 shadow-2xl rounded-sm text-[10.5px]">

    {{-- 1. HEADER JUDUL RESMI DENGAN LOGO FUEL MAOS --}}
    <div class="flex items-center justify-between border-b-2 border-black pb-2.5 mb-3 gap-2">
        <div class="flex items-center gap-2 shrink-0">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm">
                <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-full w-full object-contain">
            </div>
            <div class="leading-tight text-left">
                <p class="text-sm font-black text-slate-900 tracking-tight">Fuel Maos</p>
                <p class="text-[9px] font-black text-[#d97706] tracking-tight">QC Lab &amp; Checklist Armada</p>
            </div>
        </div>
        <div class="text-center font-bold px-2">
            <h1 class="text-sm sm:text-base font-black uppercase tracking-wider text-black leading-snug whitespace-nowrap">FORM PEMERIKSAAN MOBIL TANGKI</h1>
            <h2 class="text-xs sm:text-sm font-black uppercase tracking-widest text-black whitespace-nowrap">FUEL TERMINAL MAOS</h2>
        </div>
        <div class="shrink-0 hidden sm:flex justify-end items-center text-right text-[9px] font-semibold text-slate-700">
            <div>
                <div class="text-slate-500 uppercase tracking-wider text-[8px]">Quality & Quantity</div>
                <div class="font-bold text-black text-[9.5px]">Fuel Terminal Maos</div>
            </div>
        </div>
    </div>

    {{-- 2. METADATA KIRI --}}
    <div class="mb-2.5 text-[11px] font-semibold text-black leading-snug">
        <div class="flex items-center"><span class="w-36">NOMOR POLISI</span><span class="mr-2">:</span><span class="font-bold">{{ $checklist->nomor_polisi }}</span></div>
        <div class="flex items-center"><span class="w-36">PEMILIK</span><span class="mr-2">:</span><span>{{ $checklist->pemilik ?: '-' }}</span></div>
        <div class="flex items-center"><span class="w-36">Tanggal</span><span class="mr-2">:</span><span>{{ $checklist->tanggal_periksa->translatedFormat('d F Y') }}</span></div>
    </div>

    {{-- 3. TABEL PEMERIKSAAN TUNGGAL LENGKAP --}}
    <table class="tbl-form w-full border-collapse text-[10px] leading-tight">
        <thead>
            <tr style="background:#FFD400;" class="text-black font-black uppercase text-center">
                <th class="py-1.5 px-1 w-[4%]">NO</th>
                <th class="py-1.5 px-2 w-[40%] text-center">ITEM</th>
                <th class="py-1.5 px-1.5 w-[9%]">Temuan</th>
                <th class="py-1.5 px-1.5 w-[9%]">Dispensasi</th>
                <th class="py-1.5 px-2 w-[16%]">Hasil Pemeriksaan</th>
                <th class="py-1.5 px-2 w-[22%]">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            {{-- BARIS 1: Masa Sertifikat Tera --}}
            @php
                $res1 = $checklist->results['1'] ?? ($checklist->tanggal_exp ? 'ok' : null);
            @endphp
            <tr>
                <td class="text-center font-bold py-1">1</td>
                <td class="px-2 py-1 font-bold">Masa Sertifikat Tera</td>
                <td class="text-center py-1">Mayor</td>
                <td class="text-center py-1">-</td>
                <td class="text-center py-1 font-semibold">
                    @if($checklist->tanggal_exp)
                        Exp: {{ $checklist->tanggal_exp }}
                    @elseif($res1 === 'ok')
                        <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res1 === 'bad')
                        <span class="text-red-700 font-bold">Temuan</span>
                    @else
                        -
                    @endif
                </td>
                <td class="px-2 py-1">Pelaksanaan pengecekan masa tera MT</td>
            </tr>

            {{-- BARIS 2: 4 Kompartemen (a. Tinggi T2 Tera s/d f. Ijk Baut) --}}
            @php
                $teraList = $checklist->tera ?: \App\Models\ChecklistMtMaos::blankTera();
            @endphp
            @foreach($teraList as $idx => $t)
                <tr>
                    @if($loop->first)
                        <td rowspan="4" class="text-center font-bold align-middle">2</td>
                    @endif
                    {{-- Kolom ITEM: Kompartemen box + sub-items a-f --}}
                    <td class="p-0">
                        <div class="flex items-stretch">
                            <div class="w-12 shrink-0 flex items-center justify-center border-r border-slate-700 font-bold bg-slate-50 text-[9.5px] px-1 text-center">
                                Komp. {{ $t['komp'] ?? ($idx + 1) }}
                            </div>
                            <div class="flex-1 grid grid-cols-2 text-[9px] leading-relaxed p-1 gap-x-1">
                                <div>a. Tinggi T2 Tera</div>
                                <div>b. Tinggi T2 Act</div>
                                <div>c. Selisih T2</div>
                                <div>d. Dudukan</div>
                                <div>e. Volume</div>
                                <div>f. Ijk Baut</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center py-1">Mayor</td>
                    <td class="text-center py-1">-</td>
                    {{-- Kolom HASIL PEMERIKSAAN: Nilai a-f --}}
                    <td class="p-1">
                        <div class="grid grid-cols-2 text-[9px] leading-relaxed gap-x-1">
                            <div class="truncate">a. {{ $t['tinggiTera'] ?: '-' }}</div>
                            <div class="truncate">b. {{ $t['tinggiAct'] ?: '-' }}</div>
                            <div class="truncate">c. {{ $t['selisih'] ?: '-' }}</div>
                            <div class="truncate">d. {{ $t['duduk'] ?: '-' }}</div>
                            <div class="truncate">e. {{ $t['volume'] ?: '-' }}</div>
                            <div class="truncate">f. {{ $t['ijkBaut'] ?: '-' }}</div>
                        </div>
                    </td>
                    @if($loop->first)
                        <td rowspan="4" class="px-2 py-1 align-middle leading-snug">
                            Lakukan Pengecekan pada ketingian T2 tera dan Ijk Baut dan segel pada mobil Tangki
                        </td>
                    @endif
                </tr>
            @endforeach

            {{-- BARIS 3: Manhole :- --}}
            <tr style="background:#cbd5e1;">
                <td class="text-center font-bold py-0.5">3</td>
                <td colspan="5" class="px-2 py-0.5 font-bold">Manhole :-</td>
            </tr>
            @php
                $manholeSub = [
                    ['no' => '1', 'key' => '3-0', 'item' => '1. Packing', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak adanya rembesan dan kebocoran'],
                    ['no' => '2', 'key' => '3-1', 'item' => '2. Las Titik Flange', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                    ['no' => '3', 'key' => '3-2', 'item' => '3. Las Titik Engsel', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                    ['no' => '4', 'key' => '3-3', 'item' => '4. Palang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las palang dalam kondisi baik'],
                    ['no' => '5', 'key' => '3-4', 'item' => '5. Kebersihan', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan kebersihan area manhole (tidak hitam)'],
                ];
            @endphp
            @foreach($manholeSub as $m)
                @php
                    $res = $checklist->results[$m['key']] ?? ($checklist->results['3.' . $m['no']] ?? null);
                @endphp
                <tr>
                    <td class="text-center text-slate-400 py-0.5"></td>
                    <td class="px-2 py-0.5 pl-3">{{ $m['item'] }}</td>
                    <td class="text-center py-0.5">{{ $m['temuan'] }}</td>
                    <td class="text-center py-0.5">{{ $m['disp'] }}</td>
                    <td class="text-center py-0.5 font-semibold">
                        @if($res === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                        @elseif($res === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                        @else - @endif
                    </td>
                    <td class="px-2 py-0.5">{{ $m['ket'] }}</td>
                </tr>
            @endforeach

            {{-- BARIS 4: T2 Coaming --}}
            @php $res4 = $checklist->results['4'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">4</td>
                <td class="px-2 py-0.5 font-semibold">T2 Coaming</td>
                <td class="text-center py-0.5">Minor</td>
                <td class="text-center py-0.5">3 Day</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res4 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res4 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan angka T2 sesuai Tera</td>
            </tr>

            {{-- BARIS 5: Tanda Sah Lemping Volume Nominal --}}
            @php $res5 = $checklist->results['5'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">5</td>
                <td class="px-2 py-0.5 font-semibold">Tanda Sah Lemping Volume Nominal</td>
                <td class="text-center py-0.5">Mayor</td>
                <td class="text-center py-0.5">-</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res5 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res5 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan lemping sesuai dan kondisi segel</td>
            </tr>

            {{-- BARIS 6: Saluran Buangan Air --}}
            @php $res6 = $checklist->results['6'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">6</td>
                <td class="px-2 py-0.5 font-semibold">Saluran Buangan Air</td>
                <td class="text-center py-0.5">Minor</td>
                <td class="text-center py-0.5">3 Day</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res6 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res6 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan tidak tersumbat aliran air</td>
            </tr>

            {{-- BARIS 7: Pengecekan Kompartemen Dalam --}}
            @php $res7 = $checklist->results['7'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">7</td>
                <td class="px-2 py-0.5 font-semibold">Pengecekan Kompartemen Dalam</td>
                <td class="text-center py-0.5">Mayor</td>
                <td class="text-center py-0.5">-</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res7 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res7 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan kebersihan dalam tangki</td>
            </tr>

            {{-- BARIS 8: Bracket : --}}
            <tr style="background:#cbd5e1;">
                <td class="text-center font-bold py-0.5">8</td>
                <td class="px-2 py-0.5 font-bold">Bracket :</td>
                <td class="text-center py-0.5"></td>
                <td class="text-center py-0.5"></td>
                <td class="text-center py-0.5"></td>
                <td class="px-2 py-0.5 font-bold text-[9.5px]">Menahan Handle & Bracket ( Mayor )</td>
            </tr>
            @php
                $bracketSub = [
                    ['no' => '1', 'key' => '8-0', 'item' => '1. Keefektifan bracket', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan bracket berfungsi dengan baik (digoyangkan)'],
                    ['no' => '2', 'key' => '8-1', 'item' => '2. Las Titik Engsel', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                    ['no' => '3', 'key' => '8-2', 'item' => '3. Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak ada ganjalan pada Pen'],
                    ['no' => '4', 'key' => '8-3', 'item' => '4. Pengikat Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan pengikat tidak mudah lepas'],
                ];
            @endphp
            @foreach($bracketSub as $b)
                @php
                    $res = $checklist->results[$b['key']] ?? ($checklist->results['8.' . $b['no']] ?? null);
                @endphp
                <tr>
                    <td class="text-center text-slate-400 py-0.5"></td>
                    <td class="px-2 py-0.5 pl-3">{{ $b['item'] }}</td>
                    <td class="text-center py-0.5">{{ $b['temuan'] }}</td>
                    <td class="text-center py-0.5">{{ $b['disp'] }}</td>
                    <td class="text-center py-0.5 font-semibold">
                        @if($res === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                        @elseif($res === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                        @else - @endif
                    </td>
                    <td class="px-2 py-0.5">{{ $b['ket'] }}</td>
                </tr>
            @endforeach

            {{-- BARIS 9: Seal bottom Loader --}}
            @php $res9 = $checklist->results['9'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">9</td>
                <td class="px-2 py-0.5 font-semibold">Seal bottom Loader</td>
                <td class="text-center py-0.5">Mayor</td>
                <td class="text-center py-0.5">-</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res9 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res9 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan seal yang digunakan telah standard</td>
            </tr>

            {{-- BARIS 10: Sight Glass : --}}
            <tr style="background:#cbd5e1;">
                <td class="text-center font-bold py-0.5">10</td>
                <td colspan="5" class="px-2 py-0.5 font-bold">Sight Glass :</td>
            </tr>
            @php
                $sightSub = [
                    ['no' => '1', 'key' => '10-0', 'item' => '1. Kebersihan', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Memastikan fungsi dari sight glass'],
                    ['no' => '2', 'key' => '10-1', 'item' => '2. Las titik', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                ];
            @endphp
            @foreach($sightSub as $s)
                @php
                    $res = $checklist->results[$s['key']] ?? ($checklist->results['10.' . $s['no']] ?? null);
                @endphp
                <tr>
                    <td class="text-center text-slate-400 py-0.5"></td>
                    <td class="px-2 py-0.5 pl-3">{{ $s['item'] }}</td>
                    <td class="text-center py-0.5">{{ $s['temuan'] }}</td>
                    <td class="text-center py-0.5">{{ $s['disp'] }}</td>
                    <td class="text-center py-0.5 font-semibold">
                        @if($res === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                        @elseif($res === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                        @else - @endif
                    </td>
                    <td class="px-2 py-0.5">{{ $s['ket'] }}</td>
                </tr>
            @endforeach

            {{-- BARIS 11: Indikator Produk --}}
            @php $res11 = $checklist->results['11'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">11</td>
                <td class="px-2 py-0.5 font-semibold">Indikator Produk</td>
                <td class="text-center py-0.5">Minor</td>
                <td class="text-center py-0.5">2 Week</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res11 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res11 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan indikator produk tersedia</td>
            </tr>

            {{-- BARIS 12: Copy Sertifikat Tera Terpasang --}}
            @php $res12 = $checklist->results['12'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">12</td>
                <td class="px-2 py-0.5 font-semibold">Copy Sertifikat Tera Terpasang</td>
                <td class="text-center py-0.5">Minor</td>
                <td class="text-center py-0.5">3 Day</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res12 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res12 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan copy sertifikat tera terpasang pada box</td>
            </tr>

            {{-- BARIS 13: Kebersihan area bottom loading --}}
            @php $res13 = $checklist->results['13'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">13</td>
                <td class="px-2 py-0.5 font-semibold">Kebersihan area bottom loading</td>
                <td class="text-center py-0.5">Minor</td>
                <td class="text-center py-0.5">3 Day</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res13 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res13 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan kebersihan area bottom Loading</td>
            </tr>

            {{-- BARIS 14: Las Titik Foot Valve --}}
            @php $res14 = $checklist->results['14'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">14</td>
                <td class="px-2 py-0.5 font-semibold">Las Titik Foot Valve</td>
                <td class="text-center py-0.5">Mayor</td>
                <td class="text-center py-0.5">-</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res14 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res14 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan las titik dalam kondisi baik</td>
            </tr>

            {{-- BARIS 15: Selang Bongkar --}}
            @php $res15 = $checklist->results['15'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">15</td>
                <td class="px-2 py-0.5 font-semibold">Selang Bongkar</td>
                <td class="text-center py-0.5">Minor</td>
                <td class="text-center py-0.5">2 Week</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res15 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res15 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Tersedianya selang bongkar 3" & 4"</td>
            </tr>

            {{-- BARIS 16: Drainase Rumah Selang --}}
            @php $res16 = $checklist->results['16'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">16</td>
                <td class="px-2 py-0.5 font-semibold">Drainase Rumah Selang</td>
                <td class="text-center py-0.5">Minor</td>
                <td class="text-center py-0.5">3 Day</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res16 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res16 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan drainase rumah selang berfungsi</td>
            </tr>

            {{-- BARIS 17: Tanda Jaminan Pengikat TUM & Chassis --}}
            @php $res17 = $checklist->results['17'] ?? null; @endphp
            <tr>
                <td class="text-center font-bold py-0.5">17</td>
                <td class="px-2 py-0.5 font-semibold">Tanda Jaminan Pengikat TUM & Chassis</td>
                <td class="text-center py-0.5">Mayor</td>
                <td class="text-center py-0.5">-</td>
                <td class="text-center py-0.5 font-semibold">
                    @if($res17 === 'ok') <span class="text-emerald-700 font-bold">Sesuai</span>
                    @elseif($res17 === 'bad') <span class="text-red-700 font-bold">Temuan</span>
                    @else - @endif
                </td>
                <td class="px-2 py-0.5">Memastikan keadaan segel jaminan di chasis</td>
            </tr>
        </tbody>
    </table>

    {{-- 4. KETERANGAN TAMBAHAN --}}
    <div class="mt-2.5 text-[11px] leading-tight">
        <p class="font-bold">Keterangan tambahan :</p>
        <p class="text-slate-800 min-h-[16px]">{{ $checklist->ket_tambahan ?: '' }}</p>
    </div>

    {{-- 5. TANDA TANGAN (PEMERIKSA PT PERTAMINA PATRA NIAGA & PT PATRA LOGISTIK) --}}
    <div class="mt-8 flex items-start justify-between text-[11px] text-black px-2">
        <div class="w-64 text-left">
            <p class="font-normal leading-tight">Pemeriksa,</p>
            <p class="font-bold leading-tight">PT. Pertamina Patra Niaga</p>
            <div class="h-16"></div>
            <p class="whitespace-nowrap">(................................................)</p>
        </div>
        <div class="w-64 text-left">
            <p class="font-normal leading-tight">&nbsp;</p>
            <p class="font-bold leading-tight">PT. Patra Logistik</p>
            <div class="h-16"></div>
            <p class="whitespace-nowrap">(................................................)</p>
        </div>
    </div>
</div>

<script>
const namaFileDasar = @json($namaFileDasar);

function renderCanvas() {
    const el = document.getElementById('area-cetak');
    return html2canvas(el, {
        scale: 2.2,
        useCORS: true,
        backgroundColor: '#ffffff',
        windowWidth: el.scrollWidth,
        windowHeight: el.scrollHeight,
    });
}

function unduhGambar(mimeType, ekstensi) {
    const btn = document.getElementById('btn-png');
    btn.disabled = true;
    const label = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="h-3.5 w-3.5 animate-spin"></i> Memproses...';
    if (window.lucide) lucide.createIcons();

    renderCanvas().then((canvas) => {
        const link = document.createElement('a');
        link.href = canvas.toDataURL(mimeType, 1);
        link.download = namaFileDasar + ekstensi;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }).catch(() => alert('Gagal mengunduh gambar. Coba lagi ya.'))
      .finally(() => {
          btn.disabled = false;
          btn.innerHTML = label;
          if (window.lucide) lucide.createIcons();
      });
}

function unduhPdf() {
    const btn = document.getElementById('btn-pdf');
    btn.disabled = true;
    const label = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="h-3.5 w-3.5 animate-spin"></i> Memproses...';
    if (window.lucide) lucide.createIcons();

    renderCanvas().then((canvas) => {
        const { jsPDF } = window.jspdf;
        const imgW = 210, pageH = 297;
        const imgH = canvas.height * imgW / canvas.width;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgData = canvas.toDataURL('image/png', 1);
        let heightLeft = imgH, position = 0;
        pdf.addImage(imgData, 'PNG', 0, position, imgW, imgH);
        heightLeft -= pageH;
        while (heightLeft > 0) {
            position = heightLeft - imgH;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgW, imgH);
            heightLeft -= pageH;
        }
        pdf.save(namaFileDasar + '.pdf');
    }).catch(() => alert('Gagal membuat PDF. Coba lagi ya.'))
      .finally(() => {
          btn.disabled = false;
          btn.innerHTML = label;
          if (window.lucide) lucide.createIcons();
      });
}

function unduhExcel() {
    const rows = [];
    rows.push(['FORM PEMERIKSAAN MOBIL TANGKI - FUEL TERMINAL MAOS']);
    rows.push(['NOMOR POLISI', @json($checklist->nomor_polisi), 'PEMILIK', @json($checklist->pemilik), 'Tanggal', @json($checklist->tanggal_periksa->format('Y-m-d'))]);
    rows.push([]);
    rows.push(['NO', 'ITEM', 'Temuan', 'Dispensasi', 'Hasil Pemeriksaan', 'KETERANGAN']);

    // Row 1
    rows.push(['1', 'Masa Sertifikat Tera', 'Mayor', '-', @json($checklist->tanggal_exp ? ('Exp: ' . $checklist->tanggal_exp) : 'Sesuai'), 'Pelaksanaan pengecekan masa tera MT']);

    // Row 2 Komp 1-4
    @foreach($teraList as $idx => $t)
        rows.push([
            '2',
            'Komp. {{ $t['komp'] ?? ($idx + 1) }}: a.Tinggi T2 Tera, b.Tinggi T2 Act, c.Selisih T2, d.Dudukan, e.Volume, f.Ijk Baut',
            'Mayor',
            '-',
            'a.{{ $t['tinggiTera'] ?: '-' }}, b.{{ $t['tinggiAct'] ?: '-' }}, c.{{ $t['selisih'] ?: '-' }}, d.{{ $t['duduk'] ?: '-' }}, e.{{ $t['volume'] ?: '-' }}, f.{{ $t['ijkBaut'] ?: '-' }}',
            @json($loop->first ? 'Lakukan Pengecekan pada ketingian T2 tera dan Ijk Baut dan segel pada mobil Tangki' : '')
        ]);
    @endforeach

    // Row 3
    rows.push(['3', 'Manhole :-', '', '', '', '']);
    @foreach($manholeSub as $m)
        @php $res = $checklist->results[$m['key']] ?? ($checklist->results['3.' . $m['no']] ?? null); @endphp
        rows.push(['', @json($m['item']), @json($m['temuan']), @json($m['disp']), @json($res === 'ok' ? 'Sesuai' : ($res === 'bad' ? 'Temuan' : '-')), @json($m['ket'])]);
    @endforeach

    // Rows 4-7
    rows.push(['4', 'T2 Coaming', 'Minor', '3 Day', @json(($checklist->results['4'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['4'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan angka T2 sesuai Tera']);
    rows.push(['5', 'Tanda Sah Lemping Volume Nominal', 'Mayor', '-', @json(($checklist->results['5'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['5'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan lemping sesuai dan kondisi segel']);
    rows.push(['6', 'Saluran Buangan Air', 'Minor', '3 Day', @json(($checklist->results['6'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['6'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan tidak tersumbat aliran air']);
    rows.push(['7', 'Pengecekan Kompartemen Dalam', 'Mayor', '-', @json(($checklist->results['7'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['7'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan kebersihan dalam tangki']);

    // Row 8
    rows.push(['8', 'Bracket :', '', '', '', 'Menahan Handle & Bracket ( Mayor )']);
    @foreach($bracketSub as $b)
        @php $res = $checklist->results[$b['key']] ?? ($checklist->results['8.' . $b['no']] ?? null); @endphp
        rows.push(['', @json($b['item']), @json($b['temuan']), @json($b['disp']), @json($res === 'ok' ? 'Sesuai' : ($res === 'bad' ? 'Temuan' : '-')), @json($b['ket'])]);
    @endforeach

    // Rows 9-17
    rows.push(['9', 'Seal bottom Loader', 'Mayor', '-', @json(($checklist->results['9'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['9'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan seal yang digunakan telah standard']);
    rows.push(['10', 'Sight Glass :', '', '', '', '']);
    @foreach($sightSub as $s)
        @php $res = $checklist->results[$s['key']] ?? ($checklist->results['10.' . $s['no']] ?? null); @endphp
        rows.push(['', @json($s['item']), @json($s['temuan']), @json($s['disp']), @json($res === 'ok' ? 'Sesuai' : ($res === 'bad' ? 'Temuan' : '-')), @json($s['ket'])]);
    @endforeach
    rows.push(['11', 'Indikator Produk', 'Minor', '2 Week', @json(($checklist->results['11'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['11'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan indikator produk tersedia']);
    rows.push(['12', 'Copy Sertifikat Tera Terpasang', 'Minor', '3 Day', @json(($checklist->results['12'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['12'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan copy sertifikat tera terpasang pada box']);
    rows.push(['13', 'Kebersihan area bottom loading', 'Minor', '3 Day', @json(($checklist->results['13'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['13'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan kebersihan area bottom Loading']);
    rows.push(['14', 'Las Titik Foot Valve', 'Mayor', '-', @json(($checklist->results['14'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['14'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan las titik dalam kondisi baik']);
    rows.push(['15', 'Selang Bongkar', 'Minor', '2 Week', @json(($checklist->results['15'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['15'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Tersedianya selang bongkar 3" & 4"']);
    rows.push(['16', 'Drainase Rumah Selang', 'Minor', '3 Day', @json(($checklist->results['16'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['16'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan drainase rumah selang berfungsi']);
    rows.push(['17', 'Tanda Jaminan Pengikat TUM & Chassis', 'Mayor', '-', @json(($checklist->results['17'] ?? null) === 'ok' ? 'Sesuai' : ((($checklist->results['17'] ?? null) === 'bad') ? 'Temuan' : '-')), 'Memastikan keadaan segel jaminan di chasis']);

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(rows);
    ws['!cols'] = [{wch:6},{wch:38},{wch:10},{wch:12},{wch:20},{wch:42}];
    XLSX.utils.book_append_sheet(wb, ws, 'Checklist MT');
    XLSX.writeFile(wb, namaFileDasar + '.xlsx');
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
</body>
</html>
