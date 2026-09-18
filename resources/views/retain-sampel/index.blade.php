@extends('layouts.app')
@section('title', 'Laporan Harian Retain Sampel Penyaluran MT')

@section('content')
@php
    $carbonTgl = \Illuminate\Support\Carbon::parse($tanggal);
    $namaHariMap = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulanMap = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    
    $hariIndo = $namaHariMap[$carbonTgl->format('l')] ?? $carbonTgl->format('l');
    $bulanIndo = $namaBulanMap[(int)$carbonTgl->format('n')] ?? $carbonTgl->format('F');
    $tanggalIndo = $hariIndo . ', ' . $carbonTgl->format('d') . ' ' . $bulanIndo . ' ' . $carbonTgl->format('Y');
@endphp

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
        <button type="button" 
                onclick="(function(){ try { const AC = window.AudioContext || window.webkitAudioContext; if(AC){ window._userAudioCtx = window._userAudioCtx || new AC(); if(window._userAudioCtx.state === 'suspended') window._userAudioCtx.resume(); } } catch(e){} window.dispatchEvent(new CustomEvent('open-retain-tutorial')); })()"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:border-brand-blue hover:text-brand-blue">
            <i data-lucide="book-open" class="h-4 w-4 text-brand-blue"></i> Petunjuk &amp; SOP Retain
        </button>
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
            <a href="{{ route('retain-sampel.create', ['tanggal' => $tanggal]) }}"
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
                <i data-lucide="sun" class="h-3.5 w-3.5"></i> Pukul 12.00 WIB <span class="text-[10px] opacity-80 font-normal hidden sm:inline">(Retain Siang)</span>
            </button>
            <button type="button" @click="activeTab = '18:00'"
                    :class="activeTab === '18:00' ? 'bg-[#0f3861] text-white shadow-md' : 'text-slate-700 hover:text-slate-900 hover:bg-white/60'"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 transition-all">
                <i data-lucide="sunset" class="h-3.5 w-3.5"></i> Pukul 18.00 WIB <span class="text-[10px] opacity-80 font-normal hidden sm:inline">(Retain Sore)</span>
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

        <div class="w-full overflow-x-auto pb-4">
            <div class="min-w-[1060px] max-w-[1240px] mx-auto">
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

    {{-- TAB 2: PUKUL 12.00 WIB --}}
    <div x-show="activeTab === '12:00'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Slide Format Laporan — Pukul 12.00 WIB (Sampel 06.00 Tetap + Retain 12.00)</span>
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

        <div class="w-full overflow-x-auto pb-4">
            <div class="min-w-[1060px] max-w-[1240px] mx-auto">
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

    {{-- TAB 3: PUKUL 18.00 WIB --}}
    <div x-show="activeTab === '18:00'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Slide Format Laporan — Pukul 18.00 WIB (Sampel 06.00 Tetap + Retain 18.00)</span>
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

        <div class="w-full overflow-x-auto pb-4">
            <div class="min-w-[1060px] max-w-[1240px] mx-auto">
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

    {{-- TAB 4: REKAP KESELURUHAN (06.00 - 18.00) --}}
    <div x-show="activeTab === 'rekap-semua'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Slide Format Laporan — Rekap Lengkap (06.00, 12.00, 18.00)</span>
            <div class="flex items-center gap-2">
                <button onclick="unduhGambar('slide-rekap-semua-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-Rekap-Lengkap-{{ $tanggal }}.png')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] hover:bg-[#16518a] px-3.5 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PNG
                </button>
                <button onclick="unduhGambar('slide-rekap-semua-{{ str_replace('-', '', $tanggal) }}', 'image/jpeg', 'Laporan-Retain-Rekap-Lengkap-{{ $tanggal }}.jpg')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-700 hover:bg-slate-800 px-3 py-1.5 text-xs font-bold text-white shadow transition">
                    <i data-lucide="image" class="h-3.5 w-3.5"></i> JPG
                </button>
            </div>
        </div>

        <div class="w-full overflow-x-auto pb-4">
            <div class="min-w-[1060px] max-w-[1240px] mx-auto">
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

    {{-- TAB 5: SEMUA SLIDE TERPISAH BERURUTAN --}}
    <div x-show="activeTab === 'all'" x-cloak class="space-y-8">
        @foreach ($jamKeys as $jam)
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                        @if ($jam === '06:00') Sesi Pukul 06.00 WIB (Awal Penyaluran)
                        @elseif ($jam === '12:00') Sesi Pukul 12.00 WIB (Retain Siang)
                        @else Sesi Pukul 18.00 WIB (Retain Sore) @endif
                    </span>
                    <div class="flex items-center gap-2">
                        <button onclick="unduhGambar('slide-{{ str_replace(':', '', $jam) }}-{{ str_replace('-', '', $tanggal) }}', 'image/png', 'Laporan-Retain-{{ str_replace(':', '', $jam) }}-{{ $tanggal }}.png')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] hover:bg-[#16518a] px-3 py-1 text-xs font-bold text-white shadow transition">
                            <i data-lucide="download" class="h-3 w-3"></i> Download PNG
                        </button>
                    </div>
                </div>

                <div class="w-full overflow-x-auto pb-4">
                    <div class="min-w-[1060px] max-w-[1240px] mx-auto">
                        @include('retain-sampel._slide-laporan', [
                            'tanggal' => $tanggal,
                            'sesiJam' => $jam,
                            'rekap' => $rekap,
                            'fotoSesi' => $fotoSesi,
                            'produkList' => $produkList,
                        ])
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@if (!auth()->user()->isSpbu())
{{-- =========================================================================
     BAGIAN EDIT & INPUT DATA CEPAT (INLINE EDITOR PER SESI)
     ========================================================================= --}}
<div class="mt-8 border-t border-slate-200 pt-6">
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="edit-3" class="h-4 w-4 text-[#0f3861]"></i>
            Input / Edit Cepat Data Retain Sampel Hari Ini ({{ $tanggalIndo }})
        </h3>
        <span class="text-xs text-slate-400 font-semibold">Tersimpan otomatis ke database</span>
    </div>

    @foreach ($jamKeys as $jam)
        <div class="mb-6 rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <div class="mb-3 flex items-center justify-between border-b pb-2">
                <span class="text-xs font-bold text-[#0f3861] uppercase tracking-wide">
                    Sesi Pukul {{ str_replace(':', '.', $jam) }} WIB
                </span>
                <span class="text-[11px] text-slate-400 font-bold">Lengkap 8 Produk Penyaluran</span>
            </div>

            <div class="overflow-x-auto">
                @include('retain-sampel._tabel-pivot-editable', [
                    'tanggal' => $tanggal,
                    'jamLabel' => $jam,
                    'entries' => $rekap[$jam] ?? [],
                    'produkList' => $produkList
                ])
            </div>
        </div>
    @endforeach
</div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
async function ensureHtml2Canvas() {
    if (window.html2canvas) return true;
    return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
        s.onload = () => resolve(true);
        s.onerror = () => reject(new Error('Gagal memuat pustaka html2canvas'));
        document.head.appendChild(s);
    });
}

function triggerDownload(blobOrUrl, filename) {
    const link = document.createElement('a');
    link.download = filename;
    if (typeof blobOrUrl === 'string') {
        link.href = blobOrUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        const url = URL.createObjectURL(blobOrUrl);
        link.href = url;
        document.body.appendChild(link);
        link.click();
        setTimeout(() => {
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }, 2000);
    }
}

async function unduhGambar(elementId, mimeType, filename) {
    const el = document.getElementById(elementId);
    if (!el) {
        alert('Elemen slide tidak ditemukan: ' + elementId);
        return;
    }

    const origCursor = document.body.style.cursor;
    document.body.style.cursor = 'wait';

    try {
        await ensureHtml2Canvas();

        // Tunggu gambar di dalam slide selesai dimuat
        const images = el.querySelectorAll('img');
        const imagePromises = Array.from(images).map(img => {
            if (img.complete) return Promise.resolve();
            return new Promise(resolve => {
                img.onload = resolve;
                img.onerror = resolve;
            });
        });
        await Promise.all(imagePromises);

        const canvas = await html2canvas(el, {
            scale: 2,
            useCORS: true,
            allowTaint: false,
            backgroundColor: '#ffffff',
            windowWidth: 1280,
            scrollX: 0,
            scrollY: 0,
            ignoreElements: function(element) {
                return element.classList.contains('no-print');
            },
            logging: false
        });

        if (canvas.toBlob) {
            canvas.toBlob(function(blob) {
                if (blob) {
                    triggerDownload(blob, filename);
                } else {
                    triggerDownload(canvas.toDataURL(mimeType, 0.95), filename);
                }
            }, mimeType, 0.95);
        } else {
            triggerDownload(canvas.toDataURL(mimeType, 0.95), filename);
        }
    } catch (err) {
        console.error(err);
        alert('Gagal mengambil tangkapan layar slide: ' + err.message);
    } finally {
        document.body.style.cursor = origCursor;
    }
}
</script>

@include('retain-sampel._tutorial-modal')
@endsection