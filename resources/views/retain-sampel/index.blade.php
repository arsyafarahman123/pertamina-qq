@extends('layouts.app')
@section('title', 'Laporan Harian Retain Sampel Penyaluran MT')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-brand-blue text-white shadow-md shadow-brand-blue/25">
                <i data-lucide="flask-conical" class="h-4 w-4"></i>
            </span>
            Retain Sampel Penyaluran MT — FT MAOS
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Format resmi Pertamina Patra Niaga. Density'15 otomatis ASTM Table 53. Laporan harian 06.00, 12.00, dan 18.00 WIB.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('retain-sampel.semua') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:border-brand-blue hover:text-brand-blue">
            <i data-lucide="calendar-range" class="h-4 w-4 text-slate-400"></i> Lihat Semua Tanggal
        </a>
        <a href="{{ route('retain-sampel.export-excel-all') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-800 px-3.5 py-2 text-xs sm:text-sm font-bold text-white shadow-md transition hover:bg-emerald-900">
            <i data-lucide="file-spreadsheet" class="h-4 w-4"></i> Excel Riwayat
        </a>
        <a href="{{ route('retain-sampel.cetak', ['tanggal' => $tanggal]) }}" target="_blank"
           class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue px-3.5 py-2 text-xs sm:text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition hover:bg-brand-blueDark">
            <i data-lucide="printer" class="h-4 w-4"></i> Cetak / Unduh Banner
        </a>
        @if (!auth()->user()->isSpbu())
            <a href="{{ route('retain-sampel.create') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-brand-red px-3.5 py-2 text-xs sm:text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:bg-brand-redDark">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Data
            </a>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 shadow-sm flex items-center gap-2">
        <i data-lucide="check-circle" class="h-5 w-5 text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- FILTER TANGGAL --}}
<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Pilih Tanggal Laporan</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}"
                   class="rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2 text-sm font-semibold text-slate-700 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </div>
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition hover:bg-brand-blueDark">
            <i data-lucide="search" class="h-4 w-4"></i> Tampilkan
        </button>

        @if ($tanggalTersedia->isNotEmpty())
            <div class="ml-auto flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-bold text-slate-400 mr-1 hidden sm:inline">Riwayat:</span>
                @foreach ($tanggalTersedia->take(7) as $t)
                    <a href="{{ route('retain-sampel.index', ['tanggal' => $t->toDateString()]) }}"
                       class="rounded-lg border px-2.5 py-1 text-xs font-bold transition {{ $t->toDateString() === $tanggal ? 'border-brand-red bg-brand-red text-white shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                        {{ $t->translatedFormat('d M') }}
                    </a>
                @endforeach
            </div>
        @endif
    </form>
</div>

@php
    $jamKeys = ['06:00', '12:00', '18:00'];
@endphp

{{-- =========================================================================
     TAB NAVIGASI SESI LAPORAN (06.00 WIB | 12.00 WIB | 18.00 WIB | REKAP KESELURUHAN)
     ========================================================================= --}}
<div x-data="{ activeTab: '06:00' }" x-effect="if (window.lucide) $nextTick(() => lucide.createIcons())" class="space-y-6">
    {{-- Tabs Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-3">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 rounded-2xl bg-slate-200/80 p-1.5 text-xs sm:text-sm font-bold text-slate-600">
            <button type="button" @click="activeTab = '06:00'"
                    :class="activeTab === '06:00' ? 'bg-[#0f3861] text-white shadow-md' : 'text-slate-700 hover:text-slate-900 hover:bg-white/60'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 transition-all">
                <i data-lucide="clock" class="h-3.5 w-3.5"></i> Pukul 06.00 WIB <span class="text-[10px] opacity-80 font-normal hidden sm:inline">(Awal Penyaluran)</span>
            </button>
            <button type="button" @click="activeTab = '12:00'"
                    :class="activeTab === '12:00' ? 'bg-[#0f3861] text-white shadow-md' : 'text-slate-700 hover:text-slate-900 hover:bg-white/60'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 transition-all">
                <i data-lucide="sun" class="h-3.5 w-3.5"></i> Pukul 12.00 WIB <span class="text-[10px] opacity-80 font-normal hidden sm:inline">(Re-tank Siang)</span>
            </button>
            <button type="button" @click="activeTab = '18:00'"
                    :class="activeTab === '18:00' ? 'bg-[#0f3861] text-white shadow-md' : 'text-slate-700 hover:text-slate-900 hover:bg-white/60'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 transition-all">
                <i data-lucide="sunset" class="h-3.5 w-3.5"></i> Pukul 18.00 WIB <span class="text-[10px] opacity-80 font-normal hidden sm:inline">(Re-tank Sore)</span>
            </button>
            <button type="button" @click="activeTab = 'rekap-semua'"
                    :class="activeTab === 'rekap-semua' ? 'bg-[#0f3861] text-white shadow-md' : 'text-slate-700 hover:text-slate-900 hover:bg-white/60'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 transition-all">
                <i data-lucide="layers" class="h-3.5 w-3.5"></i> Rekap Keseluruhan (06.00 - 18.00)
            </button>
            <button type="button" @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-brand-red text-white shadow-md' : 'text-slate-700 hover:text-slate-900 hover:bg-white/60'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 transition-all">
                <i data-lucide="layout-grid" class="h-3.5 w-3.5"></i> Semua Slide Terpisah
            </button>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('retain-sampel.export-excel', ['tanggal' => $tanggal]) }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition">
                <i data-lucide="file-spreadsheet" class="h-3.5 w-3.5"></i> Unduh Excel
            </a>
            <a href="{{ route('retain-sampel.cetak', ['tanggal' => $tanggal]) }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition">
                <i data-lucide="printer" class="h-3.5 w-3.5"></i> Cetak / PDF
            </a>
        </div>
    </div>

    {{-- TAB 1: PUKUL 06.00 WIB --}}
    <div x-show="activeTab === '06:00'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Slide Format Laporan — Pukul 06.00 WIB</span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-0600-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-0600-{{ $tanggal }}.png')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] hover:bg-[#16518a] px-3.5 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PNG
                </button>
                <button onclick="unduhGambar('slide-0600-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Retain-0600-{{ $tanggal }}.jpg')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-700 hover:bg-slate-800 px-3 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        @include('retain-sampel._slide-laporan', [
            'tanggal' => $tanggal,
            'sesiJam' => '06:00',
            'rekap' => $rekap,
            'fotoSesi' => $fotoSesi,
            'produkList' => $produkList,
        ])
    </div>

    {{-- TAB 2: PUKUL 12.00 WIB --}}
    <div x-show="activeTab === '12:00'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Slide Format Laporan — Pukul 12.00 WIB (Sampel 06.00 Tetap + Re-Tank 12.00)</span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-1200-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-1200-{{ $tanggal }}.png')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] hover:bg-[#16518a] px-3.5 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PNG
                </button>
                <button onclick="unduhGambar('slide-1200-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Retain-1200-{{ $tanggal }}.jpg')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-700 hover:bg-slate-800 px-3 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        @include('retain-sampel._slide-laporan', [
            'tanggal' => $tanggal,
            'sesiJam' => '12:00',
            'rekap' => $rekap,
            'fotoSesi' => $fotoSesi,
            'produkList' => $produkList,
        ])
    </div>

    {{-- TAB 3: PUKUL 18.00 WIB --}}
    <div x-show="activeTab === '18:00'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Slide Format Laporan — Pukul 18.00 WIB (Sampel 06.00 Tetap + Re-Tank 18.00)</span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-1800-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-1800-{{ $tanggal }}.png')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] hover:bg-[#16518a] px-3.5 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PNG
                </button>
                <button onclick="unduhGambar('slide-1800-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Retain-1800-{{ $tanggal }}.jpg')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-700 hover:bg-slate-800 px-3 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        @include('retain-sampel._slide-laporan', [
            'tanggal' => $tanggal,
            'sesiJam' => '18:00',
            'rekap' => $rekap,
            'fotoSesi' => $fotoSesi,
            'produkList' => $produkList,
        ])
    </div>

    {{-- TAB 4: REKAP KESELURUHAN (06.00 - 18.00) --}}
    <div x-show="activeTab === 'rekap-semua'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Laporan Rekap Keseluruhan — Pukul 06.00 s/d 18.00 WIB</span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-rekap-semua-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Rekap-Keseluruhan-{{ $tanggal }}.png')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] hover:bg-[#16518a] px-3.5 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PNG
                </button>
                <button onclick="unduhGambar('slide-rekap-semua-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Rekap-Keseluruhan-{{ $tanggal }}.jpg')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-700 hover:bg-slate-800 px-3 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        @include('retain-sampel._slide-laporan', [
            'tanggal' => $tanggal,
            'sesiJam' => 'rekap-semua',
            'rekap' => $rekap,
            'fotoSesi' => $fotoSesi,
            'produkList' => $produkList,
        ])
    </div>

    {{-- TAB 5: SEMUA SLIDE TERPISAH BERURUTAN --}}
    <div x-show="activeTab === 'all'" x-cloak class="space-y-8">
        @foreach ($jamKeys as $jam)
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                        @if ($jam === '06:00') Sesi Pukul 06.00 WIB (Awal Penyaluran)
                        @elseif ($jam === '12:00') Sesi Pukul 12.00 WIB (Re-Tank Siang)
                        @else Sesi Pukul 18.00 WIB (Re-Tank Sore) @endif
                    </span>
                    <div class="flex items-center gap-2">
                        <button onclick="unduhGambar('slide-{{ str_replace(':', '', $jam) }}-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-{{ str_replace(':', '', $jam) }}-{{ $tanggal }}.png')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] hover:bg-[#16518a] px-3 py-1 text-xs font-bold text-white shadow transition">
                            <i data-lucide="download" class="h-3 w-3"></i> Download PNG
                        </button>
                    </div>
                </div>

                @include('retain-sampel._slide-laporan', [
                    'tanggal' => $tanggal,
                    'sesiJam' => $jam,
                    'rekap' => $rekap,
                    'fotoSesi' => $fotoSesi,
                    'produkList' => $produkList,
                ])
            </div>
        @endforeach
    </div>
</div>

@if (!auth()->user()->isSpbu())
{{-- =========================================================================
     BAGIAN EDIT & INPUT DATA CEPAT (INLINE EDITOR PER SESI)
     ========================================================================= --}}
<div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-card">
    <div class="bg-[#0f3b66] px-6 py-4 text-white flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-white">
                <i data-lucide="edit-3" class="h-4 w-4"></i>
            </span>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-white">Input & Edit Data Pengujian (06.00, 12.00, 18.00)</h3>
                <p class="text-[11px] text-white/70">Density'15 dihitung otomatis oleh sistem ASTM Table 53 saat data disimpan.</p>
            </div>
        </div>
    </div>

    @foreach ($jamKeys as $jam)
        @php
            $jamMeta = [
                '06:00' => ['title' => 'Pukul 06.00 WIB — Data Awal Penyaluran', 'bg' => 'bg-blue-50', 'text' => 'text-blue-900', 'border' => 'border-blue-200'],
                '12:00' => ['title' => 'Pukul 12.00 WIB — Re-tank / Pergantian Sampel', 'bg' => 'bg-amber-50', 'text' => 'text-amber-900', 'border' => 'border-amber-200'],
                '18:00' => ['title' => 'Pukul 18.00 WIB — Re-tank / Pergantian Sampel', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-900', 'border' => 'border-indigo-200'],
            ];
            $meta = $jamMeta[$jam];
            $entriesSesi = $rekap[$jam] ?? [];
            $entriesSesiList = $entriesByJam[$jam] ?? collect();
        @endphp

        <div class="border-t-2 {{ $meta['border'] }}">
            <div class="{{ $meta['bg'] }} px-6 py-3 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider {{ $meta['text'] }}">{{ $meta['title'] }}</span>
                <span class="text-xs font-bold {{ $meta['text'] }} bg-white/70 px-2.5 py-0.5 rounded-full border border-current/10">
                    {{ $entriesSesiList->count() }} Data Sampel
                </span>
            </div>
            <div class="overflow-x-auto p-2">
                @include('retain-sampel._tabel-pivot-editable', [
                    'produkList' => $produkList,
                    'entries' => $entriesSesi,
                    'entriesList' => $entriesSesiList,
                    'tanggal' => $tanggal,
                    'jamLabel' => $jam,
                ])
            </div>
        </div>
    @endforeach
</div>
@endif

<script>
function unduhGambar(elementId, mimeType, filename) {
    const el = document.getElementById(elementId);
    if (!el) {
        alert('Elemen tidak ditemukan');
        return;
    }

    const origCursor = document.body.style.cursor;
    document.body.style.cursor = 'wait';

    html2canvas(el, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        backgroundColor: '#ffffff',
        ignoreElements: function(element) {
            return element.classList.contains('no-print');
        },
        logging: false
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL(mimeType, 0.95);
        link.click();
        document.body.style.cursor = origCursor;
    }).catch(err => {
        console.error(err);
        alert('Gagal mengambil tangkapan layar: ' + err.message);
        document.body.style.cursor = origCursor;
    });
}
</script>
@endsection