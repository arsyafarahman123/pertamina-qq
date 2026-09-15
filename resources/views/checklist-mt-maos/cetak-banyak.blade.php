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
            margin: 5mm 6mm;
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
            html, body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .lembar-cetak-item {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                height: 285mm !important;
                max-height: 285mm !important;
                padding: 0 !important;
                border: none !important;
                page-break-before: auto !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                break-before: auto !important;
                break-after: page !important;
                break-inside: avoid !important;
                overflow: hidden !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }
            .lembar-cetak-item:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            table.tbl-form {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
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
            padding: 1.5px 3px !important;
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
                 class="lembar-cetak-item w-[794px] max-w-[794px] min-h-[1050px] shrink-0 bg-white px-6 py-4 shadow-2xl rounded-sm text-[9px] leading-tight box-border relative flex flex-col justify-between">

                {{-- Indikator Urutan Halaman (Hanya Tampil di Layar) --}}
                <div class="no-print absolute -top-3 -right-3 rounded-full bg-slate-800 px-2.5 py-0.5 text-[10px] font-bold text-white shadow">
                    Hal {{ $index + 1 }} / {{ $totalArmada }}
                </div>

                <div>
                    {{-- 1. HEADER RESMI DENGAN LOGO PERTAMINA --}}
                    <div class="flex items-center justify-between border-b-2 border-black pb-1.5 mb-1.5">
                        <div class="flex items-center gap-2">
                            {{-- Inline SVG Logo Pertamina (Crisp & High-Res) --}}
                            <svg class="h-7 w-9 shrink-0" viewBox="0 0 651 495" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path fill="#006cb8" d="m 10.4331,487.26912 c 0,0 129.1795,-205.48188 150.22912,-239.54621 21.04692,-34.07007 39.17911,-37.26994 97.32713,-37.26994 l 128.16389,0 c -7.55617,6.3402 -20.47901,19.57661 -30.1471,35.13293 l -130.35377,209.74331 c -13.03365,23.43632 -40.5952,31.93991 -73.66899,31.93991 z"/>
                                    <path fill="#acc42a" d="m 520.72984,210.45317 c -58.14811,0 -76.38204,3.19876 -98.53432,37.26883 -22.15479,34.06443 -81.66563,125.85787 -81.66563,125.85787 l 149.03542,0 c 26.02025,0 51.34673,-11.42617 62.25148,-30.31375 l 88.05329,-132.81295 z"/>
                                    <path fill="#ed1b2f" d="m 459.6697,8.54586 c 58.14811,0 76.38204,3.19876 98.53432,37.26883 22.15479,34.06443 81.66563,125.85787 81.66563,125.85787 l -149.03542,0 c -26.02025,0 -51.34673,-11.42617 -62.25148,-30.31375 l -88.05329,-132.81295 z"/>
                                </g>
                            </svg>
                            <div class="leading-none text-left">
                                <p class="text-[11px] font-black tracking-tight text-slate-900">PERTAMINA PATRA NIAGA</p>
                                <p class="text-[8px] font-bold text-[#d97706] tracking-tight mt-0.5">FUEL TERMINAL MAOS</p>
                            </div>
                        </div>
                        
                        <div class="text-center font-bold px-2">
                            <h1 class="text-xs font-black uppercase tracking-wider text-black leading-tight">FORM PEMERIKSAAN MOBIL TANGKI</h1>
                            <h2 class="text-[9.5px] font-black uppercase tracking-widest text-slate-800">FUEL TERMINAL MAOS</h2>
                        </div>
                        
                        <div class="text-right text-[7.5px] font-semibold text-slate-700 leading-tight">
                            <div class="text-slate-500 uppercase tracking-wider text-[7px]">QC &amp; QA Department</div>
                            <div class="font-bold text-black text-[8px]">Checklist MT Armada</div>
                        </div>
                    </div>

                    {{-- 2. METADATA KIRI --}}
                    <div class="mb-1.5 text-[8.5px] font-semibold text-black leading-tight space-y-0.5">
                        <div class="flex items-center"><span class="w-24 font-bold">NOMOR POLISI</span><span class="mr-1.5">:</span><span class="font-black text-[9.5px] tracking-wide">{{ $checklist->nomor_polisi }}</span></div>
                        <div class="flex items-center"><span class="w-24">PEMILIK</span><span class="mr-1.5">:</span><span>{{ $checklist->pemilik ?: '-' }}</span></div>
                        <div class="flex items-center"><span class="w-24">Tanggal</span><span class="mr-1.5">:</span><span>{{ $checklist->tanggal_periksa->translatedFormat('d F Y') }}</span></div>
                    </div>

                    {{-- 3. TABEL PEMERIKSAAN LENGKAP --}}
                    <table class="tbl-form w-full text-[8px]">
                        <thead>
                            <tr class="text-black font-black uppercase text-center">
                                <th style="background:#FFD400;" class="py-0.5 px-1 w-[4%]">NO</th>
                                <th style="background:#FFD400;" class="py-0.5 px-1.5 w-[42%] text-center">ITEM</th>
                                <th style="background:#FFD400;" class="py-0.5 px-1 w-[8%]">Temuan</th>
                                <th style="background:#FFD400;" class="py-0.5 px-1 w-[8%]">Dispensasi</th>
                                <th style="background:#FFD400;" class="py-0.5 px-1.5 w-[16%]">Hasil Pemeriksaan</th>
                                <th style="background:#FFD400;" class="py-0.5 px-1.5 w-[22%]">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- BARIS 1: Masa Sertifikat Tera --}}
                            @php
                                $res1 = $checklist->results['1'] ?? ($checklist->tanggal_exp ? 'ok' : null);
                            @endphp
                            <tr>
                                <td class="text-center font-bold py-0.5">1</td>
                                <td class="px-1.5 py-0.5 font-bold">Masa Sertifikat Tera</td>
                                <td class="text-center py-0.5">Mayor</td>
                                <td class="text-center py-0.5">-</td>
                                <td class="text-center py-0.5 font-bold">
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
                                <td class="px-1.5 py-0.5 text-[7.5px]">Pelaksanaan pengecekan masa tera MT</td>
                            </tr>

                            {{-- BARIS 2: 4 Kompartemen --}}
                            @php
                                $teraList = $checklist->tera ?: \App\Models\ChecklistMtMaos::blankTera();
                            @endphp
                            <tr>
                                <td class="text-center font-bold py-0.5 align-middle bg-slate-50">2</td>
                                <td class="p-0 align-middle">
                                    @foreach($teraList as $idx => $t)
                                        <div class="flex items-center {{ !$loop->last ? 'border-b border-black' : '' }} py-0.5 px-1.5">
                                            <div class="w-12 shrink-0 font-bold bg-slate-100 text-[7.5px] text-center border-r border-black py-0.5 mr-1.5 flex items-center justify-center">
                                                Komp. {{ $t['komp'] ?? ($idx + 1) }}
                                            </div>
                                            <div class="flex-1 grid grid-cols-2 text-[7.5px] leading-tight gap-x-2 font-medium text-slate-900">
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
                                <td class="text-center py-0.5 font-semibold align-middle">Mayor</td>
                                <td class="text-center py-0.5 font-semibold align-middle">-</td>
                                <td class="p-0 align-middle">
                                    @foreach($teraList as $idx => $t)
                                        <div class="flex items-center {{ !$loop->last ? 'border-b border-black' : '' }} py-0.5 px-1.5">
                                            <div class="w-full grid grid-cols-2 text-[7.5px] leading-tight gap-x-2 font-bold text-slate-900">
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
                                <td class="px-1.5 py-0.5 align-middle leading-tight text-[7.5px]">
                                    Lakukan Pengecekan pada ketingian T2 tera dan Ijk Baut dan segel pada mobil Tangki
                                </td>
                            </tr>

                            {{-- BARIS 3: Manhole :- (a sampai i) --}}
                            <tr>
                                <td class="text-center font-bold py-0.5 bg-slate-200">3</td>
                                <td colspan="5" class="px-1.5 py-0.5 font-bold bg-slate-200">Manhole :-</td>
                            </tr>
                            @php
                                $sec3Sub = [
                                    ['char' => 'a.', 'key' => '3-0', 'alt_key' => '3.1', 'item' => 'Packing', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak adanya rembesan dan kebocoran'],
                                    ['char' => 'b.', 'key' => '3-1', 'alt_key' => '3.2', 'item' => 'Las Titik Flange', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                                    ['char' => 'c.', 'key' => '3-2', 'alt_key' => '3.3', 'item' => 'Las Titik Engsel', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                                    ['char' => 'd.', 'key' => '3-3', 'alt_key' => '3.4', 'item' => 'Palang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las palang dalam kondisi baik'],
                                    ['char' => 'e.', 'key' => '3-4', 'alt_key' => '3.5', 'item' => 'Kebersihan', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan kebersihan area manhole (tidak hitam)'],
                                    ['char' => 'f.', 'key' => '4',   'alt_key' => null,  'item' => 'T2 Coaming', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan angka T2 sesuai Tera'],
                                    ['char' => 'g.', 'key' => '5',   'alt_key' => null,  'item' => 'Tanda Sah Lemping Volume Nominal', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan lemping sesuai dan kondisi segel'],
                                    ['char' => 'h.', 'key' => '6',   'alt_key' => null,  'item' => 'Saluran Buangan Air', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan tidak tersumbat aliran air'],
                                    ['char' => 'i.', 'key' => '7',   'alt_key' => null,  'item' => 'Pengecekan Kompartemen Dalam', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan kebersihan dalam tangki'],
                                ];
                            @endphp
                            @foreach($sec3Sub as $m)
                                @php
                                    $res = $checklist->results[$m['key']] ?? ($m['alt_key'] ? ($checklist->results[$m['alt_key']] ?? null) : null);
                                @endphp
                                <tr>
                                    <td class="text-center font-bold text-slate-800 py-0.5">{{ $m['char'] }}</td>
                                    <td class="px-1.5 py-0.5">{{ $m['item'] }}</td>
                                    <td class="text-center py-0.5">{{ $m['temuan'] }}</td>
                                    <td class="text-center py-0.5">{{ $m['disp'] }}</td>
                                    <td class="text-center py-0.5 font-bold">
                                        @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                        @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                        @else <span class="text-slate-500 font-medium">-</span> @endif
                                    </td>
                                    <td class="px-1.5 py-0.5 text-[7.5px] leading-tight">{{ $m['ket'] }}</td>
                                </tr>
                            @endforeach

                            {{-- BARIS 4: Bracket : (a sampai e) --}}
                            <tr>
                                <td class="text-center font-bold py-0.5 bg-slate-200">4</td>
                                <td class="px-1.5 py-0.5 font-bold bg-slate-200">Bracket :</td>
                                <td class="text-center py-0.5 bg-slate-200"></td>
                                <td class="text-center py-0.5 bg-slate-200"></td>
                                <td class="text-center py-0.5 bg-slate-200"></td>
                                <td class="px-1.5 py-0.5 font-bold text-[7.5px] bg-slate-200 leading-tight">Menahan Handle &amp; Bracket ( Mayor )</td>
                            </tr>
                            @php
                                $sec4Sub = [
                                    ['char' => 'a.', 'key' => '8-0', 'alt_key' => '8.1', 'item' => 'Keefektifan bracket', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan bracket berfungsi dengan baik (digoyangkan)'],
                                    ['char' => 'b.', 'key' => '8-1', 'alt_key' => '8.2', 'item' => 'Las Titik Engsel', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                                    ['char' => 'c.', 'key' => '8-2', 'alt_key' => '8.3', 'item' => 'Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan tidak ada ganjalan pada Pen'],
                                    ['char' => 'd.', 'key' => '8-3', 'alt_key' => '8.4', 'item' => 'Pengikat Pen', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan pengikat tidak mudah lepas'],
                                    ['char' => 'e.', 'key' => '9',   'alt_key' => null,  'item' => 'Seal bottom Loader', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan seal yang digunakan telah standard'],
                                ];
                            @endphp
                            @foreach($sec4Sub as $b)
                                @php
                                    $res = $checklist->results[$b['key']] ?? ($b['alt_key'] ? ($checklist->results[$b['alt_key']] ?? null) : null);
                                @endphp
                                <tr>
                                    <td class="text-center font-bold text-slate-800 py-0.5">{{ $b['char'] }}</td>
                                    <td class="px-1.5 py-0.5">{{ $b['item'] }}</td>
                                    <td class="text-center py-0.5">{{ $b['temuan'] }}</td>
                                    <td class="text-center py-0.5">{{ $b['disp'] }}</td>
                                    <td class="text-center py-0.5 font-bold">
                                        @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                        @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                        @else <span class="text-slate-500 font-medium">-</span> @endif
                                    </td>
                                    <td class="px-1.5 py-0.5 text-[7.5px] leading-tight">{{ $b['ket'] }}</td>
                                </tr>
                            @endforeach

                            {{-- BARIS 5: Sight Glass : (a sampai i) --}}
                            <tr>
                                <td class="text-center font-bold py-0.5 bg-slate-200">5</td>
                                <td colspan="5" class="px-1.5 py-0.5 font-bold bg-slate-200">Sight Glass :</td>
                            </tr>
                            @php
                                $sec5Sub = [
                                    ['char' => 'a.', 'key' => '10-0', 'alt_key' => '10.1', 'item' => 'Kebersihan', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Memastikan fungsi dari sight glass'],
                                    ['char' => 'b.', 'key' => '10-1', 'alt_key' => '10.2', 'item' => 'Las titik', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                                    ['char' => 'c.', 'key' => '11',   'alt_key' => null,   'item' => 'Indikator Produk', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Memastikan indikator produk tersedia'],
                                    ['char' => 'd.', 'key' => '12',   'alt_key' => null,   'item' => 'Copy Sertifikat Tera Terpasang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan copy sertifikat tera terpasang pada box'],
                                    ['char' => 'e.', 'key' => '13',   'alt_key' => null,   'item' => 'Kebersihan area bottom loading', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan kebersihan area bottom Loading'],
                                    ['char' => 'f.', 'key' => '14',   'alt_key' => null,   'item' => 'Las Titik Foot Valve', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan las titik dalam kondisi baik'],
                                    ['char' => 'g.', 'key' => '15',   'alt_key' => null,   'item' => 'Selang Bongkar', 'temuan' => 'Minor', 'disp' => '2 Week', 'ket' => 'Tersedianya selang bongkar 3" & 4"'],
                                    ['char' => 'h.', 'key' => '16',   'alt_key' => null,   'item' => 'Drainase Rumah Selang', 'temuan' => 'Minor', 'disp' => '3 Day', 'ket' => 'Memastikan drainase rumah selang berfungsi'],
                                    ['char' => 'i.', 'key' => '17',   'alt_key' => null,   'item' => 'Tanda Jaminan Pengikat TUM & Chassis', 'temuan' => 'Mayor', 'disp' => '-', 'ket' => 'Memastikan keadaan segel jaminan di chasis'],
                                ];
                            @endphp
                            @foreach($sec5Sub as $s)
                                @php
                                    $res = $checklist->results[$s['key']] ?? ($s['alt_key'] ? ($checklist->results[$s['alt_key']] ?? null) : null);
                                @endphp
                                <tr>
                                    <td class="text-center font-bold text-slate-800 py-0.5">{{ $s['char'] }}</td>
                                    <td class="px-1.5 py-0.5">{{ $s['item'] }}</td>
                                    <td class="text-center py-0.5">{{ $s['temuan'] }}</td>
                                    <td class="text-center py-0.5">{{ $s['disp'] }}</td>
                                    <td class="text-center py-0.5 font-bold">
                                        @if($res === 'ok') <span class="text-emerald-700">Sesuai</span>
                                        @elseif($res === 'bad') <span class="text-red-700">Temuan</span>
                                        @else <span class="text-slate-500 font-medium">-</span> @endif
                                    </td>
                                    <td class="px-1.5 py-0.5 text-[7.5px] leading-tight">{{ $s['ket'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- 4. KETERANGAN TAMBAHAN --}}
                    <div class="mt-1 text-[8px] leading-tight">
                        <span class="font-bold">Keterangan tambahan : </span>
                        <span class="text-slate-800">{{ $checklist->ket_tambahan ?: '-' }}</span>
                    </div>
                </div>

                {{-- 5. TANDA TANGAN --}}
                <div class="mt-2.5 flex items-start justify-between text-[8px] text-black px-4">
                    <div class="w-52 text-left">
                        <p class="font-normal leading-tight">Pemeriksa,</p>
                        <p class="font-bold leading-tight">PT. Pertamina Patra Niaga</p>
                        <div class="h-8"></div>
                        <p class="font-bold whitespace-nowrap">( .................................................. )</p>
                    </div>
                    <div class="w-52 text-right">
                        <p class="font-normal leading-tight">&nbsp;</p>
                        <p class="font-bold leading-tight">PT. Patra Logistik / Transportir</p>
                        <div class="h-8"></div>
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
                scale: 2.5,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0,
                x: 0,
                y: 0,
                width: el.offsetWidth,
                height: el.offsetHeight,
                windowWidth: 1200,
                logging: false,
                onclone: (clonedDoc) => {
                    const clonedEl = clonedDoc.getElementById(el.id);
                    if (clonedEl) {
                        clonedEl.style.transform = 'none';
                        clonedEl.style.margin = '0 auto';
                        clonedEl.style.maxWidth = 'none';
                    }
                }
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
