<!DOCTYPE html>
<html lang="id">
@php
    $carbonTgl = \Illuminate\Support\Carbon::parse($tanggal);
    $namaHariMap = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulanMap = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    
    $hariIndo = $namaHariMap[$carbonTgl->format('l')] ?? $carbonTgl->format('l');
    $bulanIndo = $namaBulanMap[(int)$carbonTgl->format('n')] ?? $carbonTgl->format('F');
    $tanggalIndo = $hariIndo . ', ' . $carbonTgl->format('d') . ' ' . $bulanIndo . ' ' . $carbonTgl->format('Y');

    $jamKeys = ['06:00', '12:00', '18:00'];
    $slugRekapSemua = 'slide-rekap-semua-' . str_replace('-', '', $tanggal);
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Retain Sampel Penyaluran MT FT MAOS - {{ $tanggal }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 4mm;
        }
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        [x-cloak] { display: none !important; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; margin: 0 !important; }
            .slide-scroll-wrapper { overflow: visible !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
            .slide-card-wrapper { min-width: 100% !important; max-width: 100% !important; width: 100% !important; margin: 0 !important; }
            .slide-container { box-shadow: none !important; border: 1px solid #cbd5e1 !important; page-break-inside: avoid !important; width: 100% !important; min-width: 100% !important; max-width: 100% !important; }
        }
        
        .btn-action { transition: all .15s ease; }
        .btn-action:hover { transform: translateY(-1px); }

        /* Custom scrollbar styling for slide preview */
        .slide-scroll-wrapper::-webkit-scrollbar {
            height: 8px;
        }
        .slide-scroll-wrapper::-webkit-scrollbar-track {
            background: #1e293b;
            border-radius: 4px;
        }
        .slide-scroll-wrapper::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }
        .slide-scroll-wrapper::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>
<body class="bg-slate-900 py-4 sm:py-6 px-2 sm:px-4 md:px-6 text-slate-800" x-data="{ modeTampil: '{{ request('sesi', '06:00') }}' }" x-effect="if (window.lucide) $nextTick(() => lucide.createIcons())">

{{-- NAVIGATION & ACTIONS TOP BAR (no-print) --}}
<div class="no-print mx-auto mb-6 flex max-w-7xl flex-wrap items-center justify-between gap-3 bg-slate-800/95 backdrop-blur p-4 rounded-2xl border border-slate-700 shadow-xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('retain-sampel.index', ['tanggal' => $tanggal]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-300 hover:text-white transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Dashboard
        </a>
        
        {{-- Pilihan Slide yang Ingin Dicetak (Tunggal / 1 Slide) --}}
        <div class="hidden md:flex items-center gap-1 bg-slate-900/80 p-1 rounded-xl border border-slate-700 text-xs font-bold text-slate-400">
            <button type="button" @click="modeTampil = 'rekap-semua'"
                    :class="modeTampil === 'rekap-semua' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition">
                <i data-lucide="layers" class="h-3.5 w-3.5"></i> Rekap Lengkap Harian
            </button>
            <button type="button" @click="modeTampil = '06:00'"
                    :class="modeTampil === '06:00' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition">
                <i data-lucide="clock" class="h-3.5 w-3.5"></i> Sesi 06.00 WIB
            </button>
            <button type="button" @click="modeTampil = '12:00'"
                    :class="modeTampil === '12:00' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition">
                <i data-lucide="sun" class="h-3.5 w-3.5"></i> Sesi 12.00 WIB
            </button>
            <button type="button" @click="modeTampil = '18:00'"
                    :class="modeTampil === '18:00' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition">
                <i data-lucide="sunset" class="h-3.5 w-3.5"></i> Sesi 18.00 WIB
            </button>
        </div>
    </div>
    
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('retain-sampel.export-excel', ['tanggal' => $tanggal]) }}"
           class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-emerald-500">
            <i data-lucide="file-spreadsheet" class="h-3.5 w-3.5"></i> Unduh Excel
        </a>
        <button onclick="window.print()"
                class="btn-action inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold text-white shadow hover:opacity-90 transition" style="background:#DA251D;">
            <i data-lucide="printer" class="h-3.5 w-3.5"></i> Cetak / Simpan PDF
        </button>
    </div>
</div>

{{-- MOBILE VIEWPORT SELECTOR (no-print) --}}
<div class="no-print mx-auto mb-4 flex md:hidden items-center justify-between gap-1 overflow-x-auto bg-slate-800 p-1.5 rounded-xl border border-slate-700 text-xs font-bold text-slate-400">
    <button type="button" @click="modeTampil = 'rekap-semua'"
            :class="modeTampil === 'rekap-semua' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
            class="whitespace-nowrap px-2.5 py-1.5 rounded-lg transition">
        Rekap Lengkap
    </button>
    <button type="button" @click="modeTampil = '06:00'"
            :class="modeTampil === '06:00' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
            class="whitespace-nowrap px-2.5 py-1.5 rounded-lg transition">
        06.00 WIB
    </button>
    <button type="button" @click="modeTampil = '12:00'"
            :class="modeTampil === '12:00' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
            class="whitespace-nowrap px-2.5 py-1.5 rounded-lg transition">
        12.00 WIB
    </button>
    <button type="button" @click="modeTampil = '18:00'"
            :class="modeTampil === '18:00' ? 'bg-[#0f3861] text-white shadow' : 'hover:text-white'"
            class="whitespace-nowrap px-2.5 py-1.5 rounded-lg transition">
        18.00 WIB
    </button>
</div>

<div class="mx-auto max-w-7xl">
    
    {{-- PILIHAN 1: REKAP LENGKAP HARIAN (1 HALAMAN TUNGGAL LENGKAP) --}}
    <div x-show="modeTampil === 'rekap-semua'" x-cloak class="space-y-3">
        <div class="no-print flex items-center justify-between px-2">
            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                Laporan Harian Retain Sampel Penyaluran MT (1 Lembar Lengkap)
            </span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('{{ $slugRekapSemua }}', 'image/png', 'Laporan-Retain-Rekap-Keseluruhan-{{ $tanggal }}.png')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] px-3.5 py-1.5 text-xs font-bold text-white shadow hover:bg-[#16518a]">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Unduh Gambar PNG
                </button>
                <button onclick="unduhGambar('{{ $slugRekapSemua }}', 'image/jpeg', 'Laporan-Retain-Rekap-Keseluruhan-{{ $tanggal }}.jpg')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-slate-700 px-3 py-1.5 text-xs font-bold text-white shadow hover:bg-slate-600">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        <div class="slide-scroll-wrapper w-full overflow-x-auto pb-4">
            <div class="slide-card-wrapper min-w-[1060px] max-w-[1240px] mx-auto">
                @include('retain-sampel._slide-laporan', [
                    'tanggal' => $tanggal,
                    'sesiJam' => 'rekap-semua',
                    'rekap' => $rekap,
                    'fotoSesi' => $fotoSesi,
                    'produkList' => $produkList,
                ])
            </div>
        </div>
    </div>

    {{-- PILIHAN 2: SESI 06.00 WIB SAJA --}}
    <div x-show="modeTampil === '06:00'" x-cloak class="space-y-3">
        <div class="no-print flex items-center justify-between px-2">
            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                Laporan Sesi Pukul 06.00 WIB (Awal Penyaluran)
            </span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-0600-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-0600-{{ $tanggal }}.png')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] px-3.5 py-1.5 text-xs font-bold text-white shadow hover:bg-[#16518a]">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Unduh PNG
                </button>
                <button onclick="unduhGambar('slide-0600-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Retain-0600-{{ $tanggal }}.jpg')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-slate-700 px-3 py-1.5 text-xs font-bold text-white shadow hover:bg-slate-600">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        <div class="slide-scroll-wrapper w-full overflow-x-auto pb-4">
            <div class="slide-card-wrapper min-w-[1060px] max-w-[1240px] mx-auto">
                @include('retain-sampel._slide-laporan', [
                    'tanggal' => $tanggal,
                    'sesiJam' => '06:00',
                    'rekap' => $rekap,
                    'fotoSesi' => $fotoSesi,
                    'produkList' => $produkList,
                ])
            </div>
        </div>
    </div>

    {{-- PILIHAN 3: SESI 12.00 WIB SAJA --}}
    <div x-show="modeTampil === '12:00'" x-cloak class="space-y-3">
        <div class="no-print flex items-center justify-between px-2">
            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                Laporan Sesi Pukul 12.00 WIB (Retain Siang)
            </span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-1200-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-1200-{{ $tanggal }}.png')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] px-3.5 py-1.5 text-xs font-bold text-white shadow hover:bg-[#16518a]">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Unduh PNG
                </button>
                <button onclick="unduhGambar('slide-1200-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Retain-1200-{{ $tanggal }}.jpg')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-slate-700 px-3 py-1.5 text-xs font-bold text-white shadow hover:bg-slate-600">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        <div class="slide-scroll-wrapper w-full overflow-x-auto pb-4">
            <div class="slide-card-wrapper min-w-[1060px] max-w-[1240px] mx-auto">
                @include('retain-sampel._slide-laporan', [
                    'tanggal' => $tanggal,
                    'sesiJam' => '12:00',
                    'rekap' => $rekap,
                    'fotoSesi' => $fotoSesi,
                    'produkList' => $produkList,
                ])
            </div>
        </div>
    </div>

    {{-- PILIHAN 4: SESI 18.00 WIB SAJA --}}
    <div x-show="modeTampil === '18:00'" x-cloak class="space-y-3">
        <div class="no-print flex items-center justify-between px-2">
            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                Laporan Sesi Pukul 18.00 WIB (Retain Sore)
            </span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-1800-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-1800-{{ $tanggal }}.png')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] px-3.5 py-1.5 text-xs font-bold text-white shadow hover:bg-[#16518a]">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Unduh PNG
                </button>
                <button onclick="unduhGambar('slide-1800-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Retain-1800-{{ $tanggal }}.jpg')"
                        class="btn-action inline-flex items-center gap-1.5 rounded-xl bg-slate-700 px-3 py-1.5 text-xs font-bold text-white shadow hover:bg-slate-600">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        <div class="slide-scroll-wrapper w-full overflow-x-auto pb-4">
            <div class="slide-card-wrapper min-w-[1060px] max-w-[1240px] mx-auto">
                @include('retain-sampel._slide-laporan', [
                    'tanggal' => $tanggal,
                    'sesiJam' => '18:00',
                    'rekap' => $rekap,
                    'fotoSesi' => $fotoSesi,
                    'produkList' => $produkList,
                ])
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        lucide.createIcons();
    }
});

function unduhGambar(elementId, mimeType, filename) {
    const el = document.getElementById(elementId);
    if (!el) {
        alert('Elemen slide tidak ditemukan');
        return;
    }

    const origCursor = document.body.style.cursor;
    document.body.style.cursor = 'wait';

    // Pastikan semua gambar dalam slide sudah loaded sempurna sebelum dicapture
    const images = el.querySelectorAll('img');
    const imagePromises = Array.from(images).map(img => {
        if (img.complete) return Promise.resolve();
        return new Promise(resolve => {
            img.onload = resolve;
            img.onerror = resolve;
        });
    });

    Promise.all(imagePromises).then(() => {
        return html2canvas(el, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            windowWidth: 1280,
            scrollX: 0,
            scrollY: 0,
            ignoreElements: function(element) {
                return element.classList.contains('no-print');
            },
            logging: false
        });
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL(mimeType, 0.95);
        link.click();
        document.body.style.cursor = origCursor;
    }).catch(err => {
        console.error(err);
        alert('Gagal mengunduh gambar: ' + err.message);
        document.body.style.cursor = origCursor;
    });
}
</script>
</body>
</html>