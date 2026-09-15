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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #000;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            #area-cetak {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                border: none !important;
                min-height: auto !important;
            }
        }
        .btn-unduh { transition: all .15s ease; }
        .btn-unduh:hover { transform: translateY(-1px); }
        .btn-unduh:disabled { opacity: .6; cursor: wait; transform: none; }
        
        table.tbl-form {
            border-collapse: collapse !important;
            width: 100%;
        }
        table.tbl-form th, table.tbl-form td {
            border: 1px solid #000 !important;
        }
    </style>
</head>
<body class="bg-slate-200 py-4 sm:py-6 text-slate-900">

{{-- ACTION TOP BAR (NO-PRINT) --}}
<div class="no-print mx-auto mb-4 flex max-w-4xl flex-wrap items-center justify-between gap-2.5 px-3">
    <a href="{{ route('checklist-mt-maos.show', $checklist) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
        <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Detail
    </a>

    <div class="flex flex-wrap items-center gap-2">
        <button id="btn-excel" onclick="unduhExcel()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white shadow hover:bg-emerald-800">
            <i data-lucide="file-spreadsheet" class="h-3.5 w-3.5"></i> Unduh Excel
        </button>
        <button id="btn-png" onclick="unduhGambar('image/png', '.png')"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-3.5 py-2 text-xs font-bold text-white shadow hover:bg-slate-900">
            <i data-lucide="image" class="h-3.5 w-3.5"></i> Unduh PNG
        </button>
        <button id="btn-jpg" onclick="unduhGambar('image/jpeg', '.jpg')"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-indigo-800 px-3.5 py-2 text-xs font-bold text-white shadow hover:bg-indigo-900">
            <i data-lucide="camera" class="h-3.5 w-3.5"></i> Unduh JPG
        </button>
        <button id="btn-pdf" onclick="unduhPdf()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-[#006CB8] px-3.5 py-2 text-xs font-bold text-white shadow hover:bg-blue-800">
            <i data-lucide="file-text" class="h-3.5 w-3.5"></i> Unduh PDF (1 Hlm)
        </button>
        <button onclick="window.print()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-[#DA251D] px-4 py-2 text-xs font-bold text-white shadow hover:bg-red-700">
            <i data-lucide="printer" class="h-3.5 w-3.5"></i> Cetak Dokumen
        </button>
    </div>
</div>

{{-- WRAPPER AREA CETAK (RESPONSIF MOBILE DENGAN SCROLL HALUS) --}}
<div class="w-full overflow-x-auto pb-8 flex justify-center">
    <div id="area-cetak" class="w-[794px] min-h-[1090px] shrink-0 bg-white px-7 py-6 shadow-2xl rounded-sm text-[10px] leading-normal box-border flex flex-col justify-between">
        <div>
            {{-- 1. HEADER RESMI DENGAN LOGO PERTAMINA --}}
            <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-2.5">
                <div class="flex items-center gap-2.5">
                    {{-- Inline SVG Logo Pertamina (Crisp & High-Res) --}}
                    <svg class="h-8 w-11 shrink-0" viewBox="0 0 651 495" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path fill="#006cb8" d="m 10.4331,487.26912 c 0,0 129.1795,-205.48188 150.22912,-239.54621 21.04692,-34.07007 39.17911,-37.26994 97.32713,-37.26994 l 128.16389,0 c -7.55617,6.3402 -20.47901,19.57661 -30.1471,35.13293 l -130.35377,209.74331 c -13.03365,23.43632 -40.5952,31.93991 -73.66899,31.93991 z"/>
                            <path fill="#acc42a" d="m 520.72984,210.45317 c -58.14811,0 -76.38204,3.19876 -98.53432,37.26883 -22.15479,34.06443 -81.66563,125.85787 -81.66563,125.85787 l 149.03542,0 c 26.02025,0 51.34673,-11.42617 62.25148,-30.31375 l 88.05329,-132.81295 z"/>
                            <path fill="#ed1b2f" d="m 459.6697,8.54586 c 58.14811,0 76.38204,3.19876 98.53432,37.26883 22.15479,34.06443 81.66563,125.85787 81.66563,125.85787 l -149.03542,0 c -26.02025,0 -51.34673,-11.42617 -62.25148,-30.31375 l -88.05329,-132.81295 z"/>
                        </g>
                    </svg>
                    <div class="leading-none text-left">
                        <p class="text-xs font-black tracking-tight text-slate-900">PERTAMINA PATRA NIAGA</p>
                        <p class="text-[8.5px] font-bold text-[#d97706] tracking-tight mt-0.5">FUEL TERMINAL MAOS</p>
                    </div>
                </div>
                
                <div class="text-center font-bold px-2">
                    <h1 class="text-sm font-black uppercase tracking-wider text-black leading-snug">FORM PEMERIKSAAN MOBIL TANGKI</h1>
                    <h2 class="text-[11px] font-black uppercase tracking-widest text-slate-800">FUEL TERMINAL MAOS</h2>
                </div>
                
                <div class="text-right text-[8.5px] font-semibold text-slate-700 leading-tight">
                    <div class="text-slate-500 uppercase tracking-wider text-[7.5px]">QC &amp; QA Department</div>
                    <div class="font-bold text-black text-[9px]">Checklist MT Armada</div>
                </div>
            </div>

            {{-- 2. METADATA KIRI --}}
            <div class="mb-2 text-[10px] font-semibold text-black leading-tight space-y-0.5">
                <div class="flex items-center"><span class="w-28 font-bold">NOMOR POLISI</span><span class="mr-2">:</span><span class="font-black text-[11px] tracking-wide">{{ $checklist->nomor_polisi }}</span></div>
                <div class="flex items-center"><span class="w-28">PEMILIK</span><span class="mr-2">:</span><span>{{ $checklist->pemilik ?: '-' }}</span></div>
                <div class="flex items-center"><span class="w-28">Tanggal</span><span class="mr-2">:</span><span>{{ $checklist->tanggal_periksa->translatedFormat('d F Y') }}</span></div>
            </div>

            {{-- 3. TABEL PEMERIKSAAN LENGKAP --}}
            <table class="tbl-form w-full text-[9px]">
                <thead>
                    <tr class="text-black font-black uppercase text-center">
                        <th style="background:#FFD400;" class="py-1 px-1 w-[4%]">NO</th>
                        <th style="background:#FFD400;" class="py-1 px-2 w-[42%] text-center">ITEM</th>
                        <th style="background:#FFD400;" class="py-1 px-1 w-[8%]">Temuan</th>
                        <th style="background:#FFD400;" class="py-1 px-1 w-[8%]">Dispensasi</th>
                        <th style="background:#FFD400;" class="py-1 px-2 w-[16%]">Hasil Pemeriksaan</th>
                        <th style="background:#FFD400;" class="py-1 px-2 w-[22%]">KETERANGAN</th>
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
                        <td class="text-center py-1 font-bold">
                            @if($checklist->tanggal_exp)
                                Exp: {{ $checklist->tanggal_exp }}
                            @elseif($res1 === 'ok')
                                <span class="text-emerald-700">Sesuai</span>
                            @elseif($res1 === 'bad')
                                <span class="text-red-700">Temuan</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px]">Pelaksanaan pengecekan masa tera MT</td>
                    </tr>

                    {{-- BARIS 2: 4 Kompartemen --}}
                    @php
                        $teraList = $checklist->tera ?: \App\Models\ChecklistMtMaos::blankTera();
                    @endphp
                    <tr>
                        <td class="text-center font-bold py-1 align-middle bg-slate-50">2</td>
                        <td class="p-0 align-middle">
                            @foreach($teraList as $idx => $t)
                                <div class="flex items-center {{ !$loop->last ? 'border-b border-black' : '' }} py-2 px-2.5">
                                    <div class="w-16 shrink-0 font-bold bg-slate-100 text-[9px] text-center border-r border-black py-1 mr-2.5 flex items-center justify-center">
                                        Komp. {{ $t['komp'] ?? ($idx + 1) }}
                                    </div>
                                    <div class="flex-1 grid grid-cols-2 text-[8.5px] leading-snug gap-x-3 gap-y-1 font-medium text-slate-900">
                                        <div>a. Tinggi T2 Tera</div>
                                        <div>b. Tinggi T2 Act</div>
                                        <div>c. Selisih T2</div>
                                        <div>d. Dudukan</div>
                                        <div>e. Volume</div>
                                        <div>f. Ijk Baut &amp; Segel</div>
                                    </div>
                                </div>
                            @endforeach
                        </td>
                        <td class="text-center py-1 font-semibold align-middle">Mayor</td>
                        <td class="text-center py-1 font-semibold align-middle">-</td>
                        <td class="p-0 align-middle">
                            @foreach($teraList as $idx => $t)
                                <div class="flex items-center {{ !$loop->last ? 'border-b border-black' : '' }} py-2 px-3">
                                    <div class="w-full grid grid-cols-2 text-[8.5px] leading-snug gap-x-3 gap-y-1 font-bold text-slate-900">
                                        <div>a. {{ $t['tinggiTera'] !== '' && $t['tinggiTera'] !== null ? $t['tinggiTera'] : '-' }}</div>
                                        <div>b. {{ $t['tinggiAct'] !== '' && $t['tinggiAct'] !== null ? $t['tinggiAct'] : '-' }}</div>
                                        <div>c. {{ $t['selisih'] !== '' && $t['selisih'] !== null ? $t['selisih'] : '-' }}</div>
                                        <div>d. {{ $t['duduk'] !== '' && $t['duduk'] !== null ? $t['duduk'] : '-' }}</div>
                                        <div>e. {{ $t['volume'] !== '' && $t['volume'] !== null ? $t['volume'] : '-' }}</div>
                                        <div>f. {{ $t['ijkBaut'] !== '' && $t['ijkBaut'] !== null ? $t['ijkBaut'] : '-' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </td>
                        <td class="px-2 py-1 align-middle leading-snug text-[8.5px]">
                            Lakukan Pengecekan pada ketingian T2 tera dan Ijk Baut dan segel pada mobil Tangki
                        </td>
                    </tr>

                    {{-- BARIS 3: Manhole :- --}}
                    <tr>
                        <td class="text-center font-bold py-1 bg-slate-200">3</td>
                        <td colspan="5" class="px-2 py-1 font-bold bg-slate-200">Manhole :-</td>
                    </tr>
                    @php
                        $manholeSub = [
                            ['char' => 'a.', 'no' => '1', 'key' => '3-0', 'item' => 'Packing', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak adanya rembesan dan kebocoran'],
                            ['char' => 'b.', 'no' => '2', 'key' => '3-1', 'item' => 'Las Titik Flange', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                            ['char' => 'c.', 'no' => '3', 'key' => '3-2', 'item' => 'Las Titik Engsel', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                            ['char' => 'd.', 'no' => '4', 'key' => '3-3', 'item' => 'Palang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las palang dalam kondisi baik'],
                            ['char' => 'e.', 'no' => '5', 'key' => '3-4', 'item' => 'Kebersihan', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan kebersihan area manhole (tidak hitam)'],
                        ];
                    @endphp
                    @foreach($manholeSub as $m)
                        @php
                            $res = $checklist->results[$m['key']] ?? ($checklist->results['3.' . $m['no']] ?? null);
                        @endphp
                        <tr>
                            <td class="text-center font-bold text-slate-800 py-1">{{ $m['char'] }}</td>
                            <td class="px-2 py-1">{{ $m['item'] }}</td>
                            <td class="text-center py-1">{{ $m['temuan'] }}</td>
                            <td class="text-center py-1">{{ $m['disp'] }}</td>
                            <td class="text-center py-1 font-bold">
                                @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                @else <span class="text-slate-500 font-medium">-</span> @endif
                            </td>
                            <td class="px-2 py-1 text-[8.5px] leading-snug">{{ $m['ket'] }}</td>
                        </tr>
                    @endforeach

                    {{-- BARIS 4: T2 Coaming --}}
                    @php $res4 = $checklist->results['4'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">4</td>
                        <td class="px-2 py-1 font-semibold">T2 Coaming</td>
                        <td class="text-center py-1">Minor</td>
                        <td class="text-center py-1">3 Day</td>
                        <td class="text-center py-1 font-bold">
                            @if($res4 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res4 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan angka T2 sesuai Tera</td>
                    </tr>

                    {{-- BARIS 5: Tanda Sah Lemping Volume Nominal --}}
                    @php $res5 = $checklist->results['5'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">5</td>
                        <td class="px-2 py-1 font-semibold">Tanda Sah Lemping Volume Nominal</td>
                        <td class="text-center py-1">Mayor</td>
                        <td class="text-center py-1">-</td>
                        <td class="text-center py-1 font-bold">
                            @if($res5 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res5 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan lemping sesuai dan kondisi segel</td>
                    </tr>

                    {{-- BARIS 6: Saluran Buangan Air --}}
                    @php $res6 = $checklist->results['6'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">6</td>
                        <td class="px-2 py-1 font-semibold">Saluran Buangan Air</td>
                        <td class="text-center py-1">Minor</td>
                        <td class="text-center py-1">3 Day</td>
                        <td class="text-center py-1 font-bold">
                            @if($res6 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res6 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan tidak tersumbat aliran air</td>
                    </tr>

                    {{-- BARIS 7: Pengecekan Kompartemen Dalam --}}
                    @php $res7 = $checklist->results['7'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">7</td>
                        <td class="px-2 py-1 font-semibold">Pengecekan Kompartemen Dalam</td>
                        <td class="text-center py-1">Mayor</td>
                        <td class="text-center py-1">-</td>
                        <td class="text-center py-1 font-bold">
                            @if($res7 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res7 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan kebersihan dalam tangki</td>
                    </tr>

                    {{-- BARIS 8: Bracket : --}}
                    <tr>
                        <td class="text-center font-bold py-1 bg-slate-200">8</td>
                        <td class="px-2 py-1 font-bold bg-slate-200">Bracket :</td>
                        <td class="text-center py-1 bg-slate-200"></td>
                        <td class="text-center py-1 bg-slate-200"></td>
                        <td class="text-center py-1 bg-slate-200"></td>
                        <td class="px-2 py-1 font-bold text-[8.5px] bg-slate-200 leading-snug">Menahan Handle &amp; Bracket ( Mayor )</td>
                    </tr>
                    @php
                        $bracketSub = [
                            ['char' => 'a.', 'no' => '1', 'key' => '8-0', 'item' => 'Keefektifan bracket', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan bracket berfungsi dengan baik (digoyangkan)'],
                            ['char' => 'b.', 'no' => '2', 'key' => '8-1', 'item' => 'Las Titik Engsel', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                            ['char' => 'c.', 'no' => '3', 'key' => '8-2', 'item' => 'Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak ada ganjalan pada Pen'],
                            ['char' => 'd.', 'no' => '4', 'key' => '8-3', 'item' => 'Pengikat Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan pengikat tidak mudah lepas'],
                        ];
                    @endphp
                    @foreach($bracketSub as $b)
                        @php
                            $res = $checklist->results[$b['key']] ?? ($checklist->results['8.' . $b['no']] ?? null);
                        @endphp
                        <tr>
                            <td class="text-center font-bold text-slate-800 py-1">{{ $b['char'] }}</td>
                            <td class="px-2 py-1">{{ $b['item'] }}</td>
                            <td class="text-center py-1">{{ $b['temuan'] }}</td>
                            <td class="text-center py-1">{{ $b['disp'] }}</td>
                            <td class="text-center py-1 font-bold">
                                @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                @else <span class="text-slate-500 font-medium">-</span> @endif
                            </td>
                            <td class="px-2 py-1 text-[8.5px] leading-snug">{{ $b['ket'] }}</td>
                        </tr>
                    @endforeach

                    {{-- BARIS 9: Seal bottom Loader --}}
                    @php $res9 = $checklist->results['9'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">9</td>
                        <td class="px-2 py-1 font-semibold">Seal bottom Loader</td>
                        <td class="text-center py-1">Mayor</td>
                        <td class="text-center py-1">-</td>
                        <td class="text-center py-1 font-bold">
                            @if($res9 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res9 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan seal yang digunakan telah standard</td>
                    </tr>

                    {{-- BARIS 10: Sight Glass : --}}
                    <tr>
                        <td class="text-center font-bold py-1 bg-slate-200">10</td>
                        <td colspan="5" class="px-2 py-1 font-bold bg-slate-200">Sight Glass :</td>
                    </tr>
                    @php
                        $sightSub = [
                            ['char' => 'a.', 'no' => '1', 'key' => '10-0', 'item' => 'Kebersihan', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Memastikan fungsi dari sight glass'],
                            ['char' => 'b.', 'no' => '2', 'key' => '10-1', 'item' => 'Las titik', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                        ];
                    @endphp
                    @foreach($sightSub as $s)
                        @php
                            $res = $checklist->results[$s['key']] ?? ($checklist->results['10.' . $s['no']] ?? null);
                        @endphp
                        <tr>
                            <td class="text-center font-bold text-slate-800 py-1">{{ $s['char'] }}</td>
                            <td class="px-2 py-1">{{ $s['item'] }}</td>
                            <td class="text-center py-1">{{ $s['temuan'] }}</td>
                            <td class="text-center py-1">{{ $s['disp'] }}</td>
                            <td class="text-center py-1 font-bold">
                                @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                @else <span class="text-slate-500 font-medium">-</span> @endif
                            </td>
                            <td class="px-2 py-1 text-[8.5px] leading-snug">{{ $s['ket'] }}</td>
                        </tr>
                    @endforeach

                    {{-- BARIS 11: Indikator Produk --}}
                    @php $res11 = $checklist->results['11'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">11</td>
                        <td class="px-2 py-1 font-semibold">Indikator Produk</td>
                        <td class="text-center py-1">Minor</td>
                        <td class="text-center py-1">2 Week</td>
                        <td class="text-center py-1 font-bold">
                            @if($res11 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res11 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan indikator produk tersedia</td>
                    </tr>

                    {{-- BARIS 12: Copy Sertifikat Tera Terpasang --}}
                    @php $res12 = $checklist->results['12'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">12</td>
                        <td class="px-2 py-1 font-semibold">Copy Sertifikat Tera Terpasang</td>
                        <td class="text-center py-1">Minor</td>
                        <td class="text-center py-1">3 Day</td>
                        <td class="text-center py-1 font-bold">
                            @if($res12 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res12 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan copy sertifikat tera terpasang pada box</td>
                    </tr>

                    {{-- BARIS 13: Kebersihan area bottom loading --}}
                    @php $res13 = $checklist->results['13'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">13</td>
                        <td class="px-2 py-1 font-semibold">Kebersihan area bottom loading</td>
                        <td class="text-center py-1">Minor</td>
                        <td class="text-center py-1">3 Day</td>
                        <td class="text-center py-1 font-bold">
                            @if($res13 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res13 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan kebersihan area bottom Loading</td>
                    </tr>

                    {{-- BARIS 14: Las Titik Foot Valve --}}
                    @php $res14 = $checklist->results['14'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">14</td>
                        <td class="px-2 py-1 font-semibold">Las Titik Foot Valve</td>
                        <td class="text-center py-1">Mayor</td>
                        <td class="text-center py-1">-</td>
                        <td class="text-center py-1 font-bold">
                            @if($res14 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res14 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan las titik dalam kondisi baik</td>
                    </tr>

                    {{-- BARIS 15: Selang Bongkar --}}
                    @php $res15 = $checklist->results['15'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">15</td>
                        <td class="px-2 py-1 font-semibold">Selang Bongkar</td>
                        <td class="text-center py-1">Minor</td>
                        <td class="text-center py-1">2 Week</td>
                        <td class="text-center py-1 font-bold">
                            @if($res15 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res15 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Tersedianya selang bongkar 3" &amp; 4"</td>
                    </tr>

                    {{-- BARIS 16: Drainase Rumah Selang --}}
                    @php $res16 = $checklist->results['16'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">16</td>
                        <td class="px-2 py-1 font-semibold">Drainase Rumah Selang</td>
                        <td class="text-center py-1">Minor</td>
                        <td class="text-center py-1">3 Day</td>
                        <td class="text-center py-1 font-bold">
                            @if($res16 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res16 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan drainase rumah selang berfungsi</td>
                    </tr>

                    {{-- BARIS 17: Tanda Jaminan Pengikat TUM & Chassis --}}
                    @php $res17 = $checklist->results['17'] ?? null; @endphp
                    <tr>
                        <td class="text-center font-bold py-1">17</td>
                        <td class="px-2 py-1 font-semibold">Tanda Jaminan Pengikat TUM &amp; Chassis</td>
                        <td class="text-center py-1">Mayor</td>
                        <td class="text-center py-1">-</td>
                        <td class="text-center py-1 font-bold">
                            @if($res17 === 'ok') <span class="text-emerald-700">Sesuai</span>
                            @elseif($res17 === 'bad') <span class="text-red-700">Temuan</span>
                            @else <span class="text-slate-500 font-medium">-</span> @endif
                        </td>
                        <td class="px-2 py-1 text-[8.5px] leading-snug">Memastikan keadaan segel jaminan di chasis</td>
                    </tr>
                </tbody>
            </table>

            {{-- 4. KETERANGAN TAMBAHAN --}}
            <div class="mt-2 text-[10px] leading-tight">
                <span class="font-bold">Keterangan tambahan : </span>
                <span class="text-slate-800">{{ $checklist->ket_tambahan ?: '-' }}</span>
            </div>
        </div>

        {{-- 5. TANDA TANGAN (PEMERIKSA PT PERTAMINA PATRA NIAGA & PT PATRA LOGISTIK) --}}
        <div class="mt-6 flex items-start justify-between text-[10px] text-black px-4">
            <div class="w-60 text-left">
                <p class="font-normal leading-tight">Pemeriksa,</p>
                <p class="font-bold leading-tight">PT. Pertamina Patra Niaga</p>
                <div class="h-12"></div>
                <p class="font-bold whitespace-nowrap">( .................................................. )</p>
            </div>
            <div class="w-60 text-right">
                <p class="font-normal leading-tight">&nbsp;</p>
                <p class="font-bold leading-tight">PT. Patra Logistik / Transportir</p>
                <div class="h-12"></div>
                <p class="font-bold whitespace-nowrap">( .................................................. )</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pratinjau Gambar untuk Mobile (Long-Press Save / Bagikan) -->
<div id="modal-preview-gambar" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 p-3 backdrop-blur-sm no-print">
    <div class="relative max-h-[92vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-4 text-center shadow-2xl">
        <div class="mb-3 flex items-center justify-between border-b pb-2">
            <p class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                <i data-lucide="image" class="h-4 w-4 text-brand-blue"></i> Hasil Gambar Dokumen
            </p>
            <button onclick="tutupModalGambar()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <p class="mb-3 text-xs text-slate-600 bg-amber-50 border border-amber-200 rounded-xl p-2.5 leading-relaxed">
            📱 <strong>Tips HP:</strong> Jika unduhan otomatis tidak muncul, <strong>tekan &amp; tahan (long press) gambar di bawah</strong> lalu pilih <strong>"Simpan Gambar"</strong> atau <strong>"Download Image"</strong> ke galeri Anda.
        </p>
        <div class="mb-4 overflow-hidden rounded-xl border border-slate-200 shadow-inner">
            <img id="img-preview-target" src="" alt="Pratinjau Checklist MT" class="mx-auto w-full h-auto object-contain">
        </div>
        <div class="flex flex-wrap items-center justify-center gap-2">
            <a id="btn-tab-baru" href="#" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-4 py-2 text-xs font-bold text-white shadow hover:bg-slate-900">
                <i data-lucide="external-link" class="h-3.5 w-3.5"></i> Buka di Tab Baru
            </a>
            <button onclick="tutupModalGambar()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
const namaFileDasar = @json($namaFileDasar);

function renderCanvas() {
    window.scrollTo(0, 0);
    const el = document.getElementById('area-cetak');
    return html2canvas(el, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        backgroundColor: '#ffffff',
        scrollX: 0,
        scrollY: 0,
        windowWidth: 1024,
        logging: false
    });
}

function bukaModalGambar(dataUrl) {
    const modal = document.getElementById('modal-preview-gambar');
    const img = document.getElementById('img-preview-target');
    const btnTab = document.getElementById('btn-tab-baru');
    if (modal && img) {
        img.src = dataUrl;
        if (btnTab) btnTab.href = dataUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (window.lucide) lucide.createIcons();
    }
}

function tutupModalGambar() {
    const modal = document.getElementById('modal-preview-gambar');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function unduhGambar(mimeType, ekstensi) {
    const btnId = ekstensi === '.jpg' ? 'btn-jpg' : 'btn-png';
    const btn = document.getElementById(btnId);
    btn.disabled = true;
    const label = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="h-3.5 w-3.5 animate-spin"></i> Memproses...';
    if (window.lucide) lucide.createIcons();

    try {
        const canvas = await renderCanvas();
        const quality = mimeType === 'image/jpeg' ? 0.95 : 1.0;
        const filename = namaFileDasar + ekstensi;

        // Gunakan canvas.toBlob untuk efisiensi memory & kompatibilitas tinggi di Mobile
        canvas.toBlob(async (blob) => {
            if (!blob) {
                // Fallback jika toBlob tidak menghasilkan
                const dataUrl = canvas.toDataURL(mimeType, quality);
                bukaModalGambar(dataUrl);
                btn.disabled = false;
                btn.innerHTML = label;
                if (window.lucide) lucide.createIcons();
                return;
            }

            const file = new File([blob], filename, { type: mimeType });

            // Coba Web Share API jika di perangkat HP / Mobile yang mendukung sharing file
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            if (isMobile && navigator.canShare && navigator.canShare({ files: [file] })) {
                try {
                    await navigator.share({
                        files: [file],
                        title: filename,
                        text: 'Hasil Form Pemeriksaan Mobil Tangki — FT Maos'
                    });
                    btn.disabled = false;
                    btn.innerHTML = label;
                    if (window.lucide) lucide.createIcons();
                    return;
                } catch (shareErr) {
                    // Jika user cancel share, tetap lanjut ke download blob
                    if (shareErr.name !== 'AbortError') {
                        console.log('Web share fallback to download:', shareErr);
                    }
                }
            }

            // Download melalui Blob Object URL
            const blobUrl = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();

            // Jika di mobile Safari / WebView tertentu di mana klik a download diblokir, buka fallback modal
            if (isMobile) {
                setTimeout(() => {
                    bukaModalGambar(blobUrl);
                }, 300);
            }

            setTimeout(() => {
                document.body.removeChild(link);
                // Biarkan URL aktif jika dipakai di modal preview
            }, 1000);

            btn.disabled = false;
            btn.innerHTML = label;
            if (window.lucide) lucide.createIcons();
        }, mimeType, quality);

    } catch (err) {
        console.error(err);
        alert('Gagal membuat gambar. Coba lagi ya.');
        btn.disabled = false;
        btn.innerHTML = label;
        if (window.lucide) lucide.createIcons();
    }
}

function unduhPdf() {
    const btn = document.getElementById('btn-pdf');
    btn.disabled = true;
    const label = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="h-3.5 w-3.5 animate-spin"></i> Memproses...';
    if (window.lucide) lucide.createIcons();

    renderCanvas().then((canvas) => {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true
        });

        const pageWidth = 210;
        const pageHeight = 297;
        const margin = 5;
        const printableWidth = pageWidth - (margin * 2);
        const printableHeight = pageHeight - (margin * 2);

        const imgRatio = canvas.width / canvas.height;
        let imgWidth = printableWidth;
        let imgHeight = imgWidth / imgRatio;

        if (imgHeight > printableHeight) {
            imgHeight = printableHeight;
            imgWidth = imgHeight * imgRatio;
        }

        const posX = margin + (printableWidth - imgWidth) / 2;
        const posY = margin + (printableHeight - imgHeight) / 2;

        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        pdf.addImage(imgData, 'JPEG', posX, posY, imgWidth, imgHeight, undefined, 'FAST');
        pdf.save(namaFileDasar + '.pdf');
    }).catch((err) => {
        console.error(err);
        alert('Gagal membuat PDF. Coba lagi ya.');
    }).finally(() => {
        btn.disabled = false;
        btn.innerHTML = label;
        if (window.lucide) lucide.createIcons();
    });
}

async function unduhExcel() {
    const btn = document.getElementById('btn-excel');
    const labelAwal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="h-3.5 w-3.5 animate-spin"></i> Memproses...';
    if (window.lucide) lucide.createIcons();

    try {
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

        addMetaRow('Nomor Polisi', @json($checklist->nomor_polisi), 'Pemilik / SPBU', @json($checklist->pemilik ?: '-'));
        addMetaRow('Tgl Periksa', @json($checklist->tanggal_periksa->translatedFormat('d F Y')), 'Exp Sertifikat Tera', @json($checklist->tanggal_exp ?: '-'));
        addMetaRow('Pemeriksa', @json($checklist->created_by ?: '-'), 'Status Hasil', @json($checklist->isFlagged() ? ($checklist->flagCount() . ' Temuan') : 'Sesuai Standar'));
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
        const teraData = @json($checklist->tera ?: \App\Models\ChecklistMtMaos::blankTera());
        teraData.forEach((t, i) => {
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
        const itemsGrouped = @json(\App\Support\ChecklistMtMaosItems::all());
        const resultsMap = @json($checklist->results ?: []);
        const notesMap = @json($checklist->notes ?: []);

        itemsGrouped.forEach(sec => {
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
                    const res = resultsMap[key] || null;
                    const note = (notesMap[key] || '').trim();
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
                const res = resultsMap[key] || null;
                const note = (notesMap[key] || '').trim();
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
        const ketTambahan = @json($checklist->ket_tambahan ?: '');
        if (ketTambahan) {
            r++;
            ws.mergeCells(`A${r}:F${r}`);
            const kCell = ws.getCell(`A${r}`);
            kCell.value = 'KETERANGAN TAMBAHAN: ' + ketTambahan;
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
        link.download = namaFileDasar + '.xlsx';
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
        if (window.lucide) lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
</body>
</html>
