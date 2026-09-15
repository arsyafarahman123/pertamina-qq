<!DOCTYPE html>
<html lang="id">
@php
    $totalArmada = $checklists->count();
    $judulHalaman = 'Rekap Form Pemeriksaan Mobil Tangki (' . $totalArmada . ' Armada)';
@endphp
<head>
    <meta charset="UTF-8">
    <title>{{ $judulHalaman }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
            .lembar-cetak-item {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                border: none !important;
                min-height: auto !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .lembar-cetak-item:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
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
<div class="no-print sticky top-3 z-40 mx-auto mb-6 flex max-w-4xl flex-wrap items-center justify-between gap-3 rounded-2xl bg-white/95 px-4 py-3 shadow-lg backdrop-blur-md border border-slate-200">
    <div class="flex items-center gap-2">
        <a href="{{ route('checklist-mt-maos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Checklist MT
        </a>
        <span class="rounded-full bg-blue-50 border border-blue-200 px-2.5 py-1 text-[11px] font-extrabold text-[#006CB8]">
            {{ $totalArmada }} Form Terpilih
        </span>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <button id="btn-pdf-semua" onclick="unduhPdfSemua()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-[#006CB8] px-4 py-2 text-xs font-bold text-white shadow-md shadow-blue-700/20 hover:bg-blue-800">
            <i data-lucide="file-text" class="h-3.5 w-3.5"></i> <span id="label-btn-pdf">Unduh PDF Gabungan ({{ $totalArmada }} Hlm)</span>
        </button>
        <button onclick="window.print()"
                class="btn-unduh inline-flex items-center gap-1.5 rounded-xl bg-[#DA251D] px-4 py-2 text-xs font-bold text-white shadow-md shadow-red-700/20 hover:bg-red-700">
            <i data-lucide="printer" class="h-3.5 w-3.5"></i> Cetak / Print Semua
        </button>
    </div>
</div>

{{-- AREA CETAK SEMUA LEMBAR (TERSUSUN KE BAWAH) --}}
<div class="w-full flex flex-col items-center gap-8 pb-12">
    @foreach ($checklists as $index => $checklist)
        @php
            $namaFileDasar = preg_replace('/[^A-Za-z0-9]+/', '', $checklist->nomor_polisi ?: 'Checklist') . '_' . $checklist->tanggal_periksa->format('d-m-Y');
        @endphp

        <div class="w-full overflow-x-auto flex justify-center">
            <div id="lembar-{{ $checklist->id }}"
                 data-id="{{ $checklist->id }}"
                 data-nopol="{{ $checklist->nomor_polisi }}"
                 data-filename="{{ $namaFileDasar }}"
                 class="lembar-cetak-item w-[794px] min-h-[1090px] shrink-0 bg-white px-7 py-6 shadow-2xl rounded-sm text-[10px] leading-normal box-border relative flex flex-col justify-between">

                {{-- Indikator Urutan Halaman (Hanya Tampil di Layar) --}}
                <div class="no-print absolute -top-3 -right-3 rounded-full bg-slate-800 px-2.5 py-0.5 text-[10px] font-bold text-white shadow">
                    Hal {{ $index + 1 }} / {{ $totalArmada }}
                </div>

                <div>
                    {{-- 1. HEADER RESMI DENGAN LOGO PERTAMINA --}}
                    <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-2.5">
                        <div class="flex items-center gap-2.5">
                            {{-- Inline SVG Logo Pertamina (Crisp & High-Res) --}}
                            <svg class="h-8 w-11 shrink-0" viewBox="-888 -667 651 495" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path fill="#006cb8" d="m -877.5669,-179.73088 c 0,0 129.1795,-205.48188 150.22912,-239.54621 21.04692,-34.07007 39.17911,-37.26994 97.32713,-37.26994 l 128.16389,0 c -7.55617,6.3402 -20.47901,19.57661 -30.1471,35.13293 l -130.35377,209.74331 c -13.03365,23.43632 -40.5952,31.93991 -73.66899,31.93991 z"/>
                                    <path fill="#acc42a" d="m -367.27016,-456.54683 c -58.14811,0 -76.38204,3.19876 -98.53432,37.26883 -22.15479,34.06443 -81.66563,125.85787 -81.66563,125.85787 l 149.03542,0 c 26.02025,0 51.34673,-11.42617 62.25148,-30.31375 l 88.05329,-132.81295 z"/>
                                    <path fill="#ed1b2f" d="m -428.3303,-658.45414 c 58.14811,0 76.38204,3.19876 98.53432,37.26883 22.15479,34.06443 -81.66563,125.85787 -81.66563,125.85787 l -149.03542,0 c -26.02025,0 -51.34673,-11.42617 -62.25148,-30.31375 l -88.05329,-132.81295 z"/>
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
                                    <td class="text-center text-slate-400 py-1"></td>
                                    <td class="px-2 py-1 pl-3">{{ $m['item'] }}</td>
                                    <td class="text-center py-1">{{ $m['temuan'] }}</td>
                                    <td class="text-center py-1">{{ $m['disp'] }}</td>
                                    <td class="text-center py-1 font-bold">
                                        @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                        @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                        @else <span class="text-slate-500 font-medium">-</span> @endif
                                    </td>
                                    <td class="px-2 py-1 text-[8.5px]">{{ $m['ket'] }}</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan angka T2 sesuai Tera</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan lemping sesuai dan kondisi segel</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan tidak tersumbat aliran air</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan kebersihan dalam tangki</td>
                            </tr>

                            {{-- BARIS 8: Bracket : --}}
                            <tr>
                                <td class="text-center font-bold py-1 bg-slate-200">8</td>
                                <td class="px-2 py-1 font-bold bg-slate-200">Bracket :</td>
                                <td class="text-center py-1 bg-slate-200"></td>
                                <td class="text-center py-1 bg-slate-200"></td>
                                <td class="text-center py-1 bg-slate-200"></td>
                                <td class="px-2 py-1 font-bold text-[8.5px] bg-slate-200">Menahan Handle &amp; Bracket ( Mayor )</td>
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
                                    <td class="text-center text-slate-400 py-1"></td>
                                    <td class="px-2 py-1 pl-3">{{ $b['item'] }}</td>
                                    <td class="text-center py-1">{{ $b['temuan'] }}</td>
                                    <td class="text-center py-1">{{ $b['disp'] }}</td>
                                    <td class="text-center py-1 font-bold">
                                        @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                        @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                        @else <span class="text-slate-500 font-medium">-</span> @endif
                                    </td>
                                    <td class="px-2 py-1 text-[8.5px]">{{ $b['ket'] }}</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan seal yang digunakan telah standard</td>
                            </tr>

                            {{-- BARIS 10: Sight Glass : --}}
                            <tr>
                                <td class="text-center font-bold py-1 bg-slate-200">10</td>
                                <td colspan="5" class="px-2 py-1 font-bold bg-slate-200">Sight Glass :</td>
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
                                    <td class="text-center text-slate-400 py-1"></td>
                                    <td class="px-2 py-1 pl-3">{{ $s['item'] }}</td>
                                    <td class="text-center py-1">{{ $s['temuan'] }}</td>
                                    <td class="text-center py-1">{{ $s['disp'] }}</td>
                                    <td class="text-center py-1 font-bold">
                                        @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                        @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                        @else <span class="text-slate-500 font-medium">-</span> @endif
                                    </td>
                                    <td class="px-2 py-1 text-[8.5px]">{{ $s['ket'] }}</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan indikator produk tersedia</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan copy sertifikat tera terpasang pada box</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan kebersihan area bottom Loading</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan las titik dalam kondisi baik</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Tersedianya selang bongkar 3" &amp; 4"</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan drainase rumah selang berfungsi</td>
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
                                <td class="px-2 py-1 text-[8.5px]">Memastikan keadaan segel jaminan di chasis</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- 4. KETERANGAN TAMBAHAN --}}
                    <div class="mt-2 text-[10px] leading-tight">
                        <span class="font-bold">Keterangan tambahan : </span>
                        <span class="text-slate-800">{{ $checklist->ket_tambahan ?: '-' }}</span>
                    </div>
                </div>

                {{-- 5. TANDA TANGAN --}}
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
    @endforeach
</div>

<script>
async function unduhPdfSemua() {
    const btn = document.getElementById('btn-pdf-semua');
    const label = document.getElementById('label-btn-pdf');
    const labelAwal = label ? label.innerHTML : 'Unduh PDF Gabungan';
    
    if (btn) btn.disabled = true;
    if (label) label.innerHTML = '<i data-lucide="loader-2" class="h-3.5 w-3.5 inline animate-spin mr-1"></i> Menyusun PDF... (0%)';
    if (window.lucide) lucide.createIcons();

    try {
        const items = document.querySelectorAll('.lembar-cetak-item');
        if (!items.length) {
            alert('Tidak ada lembar form yang ditemukan.');
            return;
        }

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true
        });

        const pageWidth = 210;
        const pageHeight = 297;
        const margin = 6;
        const printableWidth = pageWidth - (margin * 2);
        const printableHeight = pageHeight - (margin * 2);

        window.scrollTo(0, 0);

        for (let i = 0; i < items.length; i++) {
            const el = items[i];
            const progress = Math.round(((i + 1) / items.length) * 100);
            if (label) {
                label.innerHTML = `<i data-lucide="loader-2" class="h-3.5 w-3.5 inline animate-spin mr-1"></i> Memproses Hlm ${i + 1}/${items.length} (${progress}%)`;
                if (window.lucide) lucide.createIcons();
            }

            const canvas = await html2canvas(el, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0,
                windowWidth: 1024,
                logging: false
            });

            const imgRatio = canvas.width / canvas.height;
            let imgWidth = printableWidth;
            let imgHeight = imgWidth / imgRatio;

            if (imgHeight > printableHeight) {
                imgHeight = printableHeight;
                imgWidth = imgHeight * imgRatio;
            }

            const posX = margin + (printableWidth - imgWidth) / 2;
            const posY = margin + (printableHeight - imgHeight) / 2;

            const imgData = canvas.toDataURL('image/jpeg', 0.98);

            if (i > 0) {
                pdf.addPage();
            }

            pdf.addImage(imgData, 'JPEG', posX, posY, imgWidth, imgHeight, undefined, 'FAST');
        }

        const tglStr = new Date().toISOString().slice(0, 10);
        const total = items.length;
        const filename = `Form_Pemeriksaan_MT_Rekap_${total}_Armada_${tglStr}.pdf`;
        
        pdf.save(filename);

    } catch (err) {
        console.error(err);
        alert('Gagal menyusun PDF gabungan. Silakan coba lagi.');
    } finally {
        if (btn) btn.disabled = false;
        if (label) label.innerHTML = labelAwal;
        if (window.lucide) lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_download') === '1') {
        setTimeout(() => {
            unduhPdfSemua();
        }, 600);
    }
});
</script>
</body>
</html>
