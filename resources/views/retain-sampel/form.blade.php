@extends('layouts.app')
@section('title', $entry ? 'Edit Rekap Retain Sampel' : 'Input Retain Sampel Penyaluran MT (Multi-Produk)')

@section('content')
<div class="mx-auto max-w-5xl">
    {{-- Top Navigation --}}
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('retain-sampel.index', ['tanggal' => $entry ? $entry->tanggal->toDateString() : ($defaultTanggal ?? now()->toDateString())]) }}" 
           class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-500 hover:text-brand-blue transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Rekap Retain
        </a>
        <span class="text-xs font-semibold text-slate-400">
            {{ $entry ? 'Mode Edit Data' : 'Mode Input Sekaligus (Multi-Produk)' }}
        </span>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i data-lucide="alert-circle" class="h-4 w-4 text-red-600"></i> Ada beberapa kesalahan input:
            </div>
            <ul class="list-inside list-disc pl-2 text-xs space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($entry)
        {{-- ========================================================================= --}}
        {{-- MODE EDIT SINGLE ENTRY                                                    --}}
        {{-- ========================================================================= --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-card">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h1 class="text-xl font-extrabold text-slate-800">
                    Edit Retain Sampel Penyaluran MT — {{ $entry->produk }}
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Isi Density Obs & Suhu — Density '15 dihitung otomatis oleh sistem (rumus ASTM Tabel 53B).
                </p>
                <p class="text-[11px] text-slate-400 mt-1">
                    Data diinput: <b class="text-slate-600">{{ $entry->created_at->translatedFormat('d M Y, H:i') }} WIB</b>@if($entry->user) oleh <b class="text-slate-600">{{ $entry->user->name }}</b>@endif.
                </p>
            </div>

            <form method="POST" action="{{ route('retain-sampel.update', $entry) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Baris 1: Tanggal & Jam Observasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Tanggal</label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal', $entry->tanggal->toDateString()) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Jam Observasi</label>
                        <input list="jam-list" name="jam_label" required value="{{ old('jam_label', $entry->jam_label) }}" placeholder="06:00"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <datalist id="jam-list">
                            @foreach ($jamStandar as $j)
                                <option value="{{ $j }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                {{-- Baris 2: Produk --}}
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Produk</label>
                    <select name="produk" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        @foreach ($produkList as $p)
                            <option value="{{ $p }}" @selected(old('produk', $entry->produk) === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Baris 3: MT Nopol & Tangki Timbun --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">MT Nopol</label>
                        <input type="text" name="mt_nopol" value="{{ old('mt_nopol', $entry->mt_nopol) }}" placeholder="contoh: R 9675 B"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Tangki Timbun</label>
                        <input type="text" name="tangki_timbun" value="{{ old('tangki_timbun', $entry->tangki_timbun) }}" placeholder="contoh: T1 / 09"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                </div>

                {{-- Baris 4: Density Obs & Temperatur --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Density Obs</label>
                        <input type="number" step="0.0001" id="single-density-obs" name="density_obs" required value="{{ old('density_obs', $entry->density_obs) }}" placeholder="contoh: 735.0 atau 0.7350"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Temperatur (°C)</label>
                        <input type="number" step="0.1" id="single-temp" name="temperatur" required value="{{ old('temperatur', $entry->temperatur) }}" placeholder="contoh: 29.5"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                </div>

                {{-- Baris 5: Foto Botol Sampel --}}
                <div>
                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Foto Botol Sampel / Dokumen (Opsional)</label>
                    @if ($entry->fotoUrl())
                        <div class="mb-2 flex items-center gap-2">
                            <img src="{{ $entry->fotoUrl() }}" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                            <span class="text-xs text-slate-400">Foto tersimpan. Upload jika ingin mengganti.</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <input type="file" name="foto" accept="image/*,application/pdf" class="text-xs text-slate-600">
                    </div>
                </div>

                {{-- Baris 6: Kotak Density'15 Otomatis --}}
                <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4 text-center">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-brand-blue">DENSITY '15 (OTOMATIS ASTM 53B)</span>
                    <span id="single-preview-density-15" class="my-1 block text-2xl font-black text-brand-blue">
                        {{ number_format($entry->density_15, 4) }}
                    </span>
                    <span class="block text-[10.5px] text-slate-500">Dihitung otomatis realtime dari Density Obs & Suhu.</span>
                </div>

                {{-- Baris 7: Tombol Simpan --}}
                <div class="pt-3">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-brand-red py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-redDark">
                        <i data-lucide="save" class="h-4 w-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    @else
        {{-- ========================================================================= --}}
        {{-- MODE INPUT SEKALIGUS (MULTI-PRODUK DENGAN SIMPAN DI PALING BAWAH)          --}}
        {{-- ========================================================================= --}}
        <form method="POST" action="{{ route('retain-sampel.store') }}" enctype="multipart/form-data" id="bulk-retain-form" class="space-y-6">
            @csrf

            {{-- 1. KARTU HEADER SESI (TANGGAL & JAM) --}}
            <div class="rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-900 via-brand-dark to-brand-blueDark p-6 sm:p-7 text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-5">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-red px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-white shadow-sm">
                            <i data-lucide="layers" class="h-3.5 w-3.5"></i> Form Input Multi-Produk
                        </span>
                        <h1 class="mt-2 text-xl sm:text-2xl font-black text-white tracking-tight">
                            Input Retain Sampel Penyaluran MT
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1">
                            Isi semua produk yang diuji pada sesi ini, lalu klik <b class="text-amber-300">Simpan Semua Data Sampel</b> di paling bawah.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 bg-white/10 rounded-2xl p-2.5 backdrop-blur">
                        <div class="text-right px-2">
                            <span class="block text-[10px] uppercase font-bold text-slate-300 tracking-wider">Metode Hitung</span>
                            <span class="text-xs font-black text-emerald-300">ASTM Table 53B Otomatis</span>
                        </div>
                    </div>
                </div>

                {{-- Input Tanggal & Jam Sesi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-300">Tanggal Observasi</label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal', $defaultTanggal ?? now()->toDateString()) }}"
                               class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-bold text-white placeholder-white/40 backdrop-blur transition focus:border-brand-red focus:bg-white/20 focus:outline-none focus:ring-4 focus:ring-brand-red/20">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-300">Sesi Jam Observasi</label>
                        <div class="flex items-center gap-2">
                            <select name="jam_label" id="select-jam-label" required onchange="handleJamChange(this)"
                                    class="w-full rounded-2xl border border-white/20 bg-slate-800 text-white px-4 py-3 text-sm font-bold transition focus:border-brand-red focus:outline-none focus:ring-4 focus:ring-brand-red/20">
                                @foreach ($jamStandar as $j)
                                    <option value="{{ $j }}" @selected(old('jam_label', $defaultJamLabel ?? '06:00') === $j)>
                                        Pukul {{ $j }} WIB {{ $j === '06:00' ? '(Awal Penyaluran)' : ($j === '12:00' ? '(Retain Siang)' : '(Retain Sore)') }}
                                    </option>
                                @endforeach
                                <option value="custom">-- Jam Lainnya (Kustom) --</option>
                            </select>
                            <input type="text" id="custom-jam-input" placeholder="00:00" class="hidden w-32 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-bold text-white">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. DAFTAR KARTU PRODUK-PRODUK --}}
            <div class="space-y-4" id="produk-cards-container">
                @php
                    $colors = [
                        'Pertalite' => ['border' => 'border-emerald-200', 'badge' => 'bg-emerald-600 text-white', 'light' => 'bg-emerald-50/50'],
                        'Pertamax' => ['border' => 'border-blue-200', 'badge' => 'bg-blue-600 text-white', 'light' => 'bg-blue-50/50'],
                        'Pertamax Turbo' => ['border' => 'border-rose-200', 'badge' => 'bg-rose-700 text-white', 'light' => 'bg-rose-50/50'],
                        'Biosolar B50' => ['border' => 'border-amber-200', 'badge' => 'bg-amber-600 text-white', 'light' => 'bg-amber-50/50'],
                        'Dexlite' => ['border' => 'border-teal-200', 'badge' => 'bg-teal-700 text-white', 'light' => 'bg-teal-50/50'],
                        'Pertadex' => ['border' => 'border-purple-200', 'badge' => 'bg-purple-700 text-white', 'light' => 'bg-purple-50/50'],
                    ];
                @endphp

                @foreach ($produkList as $index => $prod)
                    @php
                        $style = $colors[$prod] ?? ['border' => 'border-slate-200', 'badge' => 'bg-slate-700 text-white', 'light' => 'bg-slate-50/50'];
                    @endphp
                    <div class="product-card rounded-3xl border {{ $style['border'] }} bg-white p-5 sm:p-6 shadow-card transition hover:shadow-md" data-index="{{ $index }}">
                        {{-- Header Baris Produk --}}
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="rounded-xl {{ $style['badge'] }} px-3 py-1 text-xs font-black shadow-sm">
                                    {{ $prod }}
                                </span>
                                <span class="text-xs font-semibold text-slate-400">Produk #{{ $index + 1 }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold text-slate-400">Kosongkan jika produk ini tidak disalurkan</span>
                            </div>
                        </div>

                        <input type="hidden" name="items[{{ $index }}][produk]" value="{{ $prod }}">

                        {{-- Input Grid Form --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-start">
                            <div>
                                <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">MT Nopol</label>
                                <input type="text" name="items[{{ $index }}][mt_nopol]" placeholder="cth: R 9675 B"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                            </div>

                            <div>
                                <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Tangki Timbun</label>
                                <input type="text" name="items[{{ $index }}][tangki_timbun]" placeholder="cth: 09 / T.09"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                            </div>

                            <div>
                                <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Density Obs</label>
                                <input type="number" step="0.0001" name="items[{{ $index }}][density_obs]" placeholder="cth: 735.0"
                                       data-type="obs" data-target="preview-{{ $index }}"
                                       class="bulk-density-input w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                            </div>

                            <div>
                                <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Temperatur (°C)</label>
                                <input type="number" step="0.1" name="items[{{ $index }}][temperatur]" placeholder="cth: 29.5"
                                       data-type="temp" data-target="preview-{{ $index }}"
                                       class="bulk-temp-input w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                            </div>

                            {{-- Live Calculated Density'15 Box --}}
                            <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-2.5 text-center">
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-blue">DENSITY '15</span>
                                <span id="preview-{{ $index }}" class="density-preview-val my-0.5 block text-lg font-black text-brand-blue">
                                    —
                                </span>
                                <span class="block text-[9.5px] font-medium text-slate-500">ASTM Table 53B</span>
                            </div>
                        </div>

                        {{-- Upload Foto Opsional --}}
                        <div class="mt-3.5 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3">
                            <div class="flex items-center gap-2">
                                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100">
                                    <i data-lucide="upload" class="h-3.5 w-3.5 text-slate-500"></i> Upload Foto Botol
                                    <input type="file" name="items[{{ $index }}][foto]" accept="image/*,application/pdf" class="hidden" onchange="previewItemFoto(this, {{ $index }})">
                                </label>
                                <button type="button" onclick="bukaKameraItem({{ $index }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100">
                                    <i data-lucide="camera" class="h-3.5 w-3.5 text-slate-500"></i> Kamera
                                </button>
                                <span id="label-foto-{{ $index }}" class="text-xs text-slate-500 italic hidden"></span>
                            </div>
                            <span class="text-[10.5px] text-slate-400">Opsional • Foto botol retain {{ $prod }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 3. TOMBOL TAMBAH PRODUK LAIN / DUPLIKAT --}}
            <div class="flex items-center justify-between">
                <button type="button" onclick="tambahBarisProduk()"
                        class="inline-flex items-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 bg-white hover:bg-slate-50 px-5 py-3 text-xs sm:text-sm font-bold text-slate-700 hover:border-brand-blue hover:text-brand-blue shadow-sm transition">
                    <i data-lucide="plus-circle" class="h-4 w-4 text-brand-blue"></i>
                    Tambah Produk Lain / Duplikat Sampel (misal 2x Pertalite)
                </button>
            </div>

            {{-- 4. STICKY BOTTOM BAR (TOMBOL SIMPAN PALING BAWAH) --}}
            <div class="sticky bottom-0 z-30 rounded-3xl border border-slate-200 bg-white/95 p-4 sm:p-5 shadow-2xl backdrop-blur">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brand-blue text-white shadow-md shadow-brand-blue/20">
                            <i data-lucide="check-circle-2" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-extrabold text-slate-800">
                                <span id="count-terisi" class="text-brand-blue font-black text-base">0</span> Produk Terisi
                            </p>
                            <p class="text-xs text-slate-500">Semua baris terisi akan disimpan permanen dalam sekali submit.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('retain-sampel.index') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-xs sm:text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-2 rounded-2xl bg-brand-red hover:bg-brand-redDark px-7 py-3.5 text-sm sm:text-base font-extrabold text-white shadow-lg shadow-brand-red/30 transition hover:-translate-y-0.5 active:scale-95">
                            <i data-lucide="save" class="h-5 w-5"></i>
                            Simpan Semua Data Sampel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

{{-- MODAL KAMERA --}}
<div id="modal-kamera" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-3xl bg-white p-5 shadow-2xl">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="camera" class="h-4 w-4 text-brand-blue"></i>
                Jepret Foto Sampel Langsung
            </h3>
            <button type="button" onclick="tutupKamera()" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-slate-950 aspect-video flex items-center justify-center">
            <video id="video-stream" autoplay playsinline class="h-full w-full object-cover"></video>
            <canvas id="canvas-capture" class="hidden"></canvas>
        </div>
        <div class="mt-4 flex items-center justify-end gap-2">
            <button type="button" onclick="tutupKamera()" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">
                Batal
            </button>
            <button type="button" onclick="ambilGambar()" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2 text-xs font-bold text-white shadow hover:bg-blue-800">
                <i data-lucide="camera" class="h-3.5 w-3.5"></i> Ambil Foto
            </button>
        </div>
    </div>
</div>

<script>
// ASTM 53B live calculation lookup table
const tabel53bAnchorsJS = [
    [690.0, 0.8636], [700.0, 0.8500], [710.0, 0.8364], [720.0, 0.8182],
    [730.0, 0.8091], [733.0, 0.8000], [740.0, 0.7909], [743.0, 0.7909],
    [750.0, 0.7727], [759.0, 0.7636], [800.0, 0.7000], [810.0, 0.6909],
    [815.0, 0.6818], [820.0, 0.6818], [830.0, 0.6727], [839.0, 0.6636],
    [840.0, 0.6636], [850.0, 0.6545], [860.0, 0.6545], [869.0, 0.6455]
];

function hitungSlope53BJS(rho) {
    const anchors = tabel53bAnchorsJS;
    const len = anchors.length;
    if (rho <= anchors[0][0]) return anchors[0][1];
    if (rho >= anchors[len - 1][0]) return anchors[len - 1][1];

    for (let i = 0; i < len - 1; i++) {
        const r1 = anchors[i][0];
        const s1 = anchors[i][1];
        const r2 = anchors[i + 1][0];
        const s2 = anchors[i + 1][1];
        if (rho >= r1 && rho <= r2) {
            const frac = (r2 > r1) ? ((rho - r1) / (r2 - r1)) : 0;
            return s1 + frac * (s2 - s1);
        }
    }
    return 0.75;
}

function hitungDensity15JS(obs, temp) {
    if (!obs || !temp || isNaN(obs) || isNaN(temp)) return null;
    const rawObs = parseFloat(obs);
    const t = parseFloat(temp);
    if (rawObs <= 0 || !isFinite(rawObs) || !isFinite(t)) return null;

    const rhoObs = rawObs > 10.0 ? rawObs : (rawObs * 1000.0);
    if (rhoObs < 100.0 || rhoObs > 2000.0) return rawObs > 10.0 ? (rawObs / 1000.0) : rawObs;

    const deltaT = t - 15.0;
    if (Math.abs(deltaT) < 0.00001) {
        return rhoObs / 1000.0;
    }

    const slope = hitungSlope53BJS(rhoObs);
    const rho15 = rhoObs + (slope * deltaT);
    return rho15 / 1000.0;
}

// Update single edit preview
function updateSinglePreview() {
    const obsEl = document.getElementById('single-density-obs');
    const tempEl = document.getElementById('single-temp');
    const prevEl = document.getElementById('single-preview-density-15');
    if (!obsEl || !tempEl || !prevEl) return;

    const res = hitungDensity15JS(obsEl.value, tempEl.value);
    prevEl.innerText = res !== null ? res.toFixed(4) : '—';
}
document.getElementById('single-density-obs')?.addEventListener('input', updateSinglePreview);
document.getElementById('single-temp')?.addEventListener('input', updateSinglePreview);

// Recalculate row & summary count
function recalculateAll() {
    let filledCount = 0;
    const cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        const obsInput = card.querySelector('input[data-type="obs"]');
        const tempInput = card.querySelector('input[data-type="temp"]');
        const previewEl = card.querySelector('.density-preview-val');

        if (obsInput && tempInput && previewEl) {
            const valObs = obsInput.value.trim();
            const valTemp = tempInput.value.trim();

            if (valObs !== '' && valTemp !== '') {
                const hasil = hitungDensity15JS(valObs, valTemp);
                if (hasil !== null) {
                    previewEl.innerText = hasil.toFixed(4);
                    filledCount++;
                } else {
                    previewEl.innerText = '—';
                }
            } else {
                previewEl.innerText = '—';
            }
        }
    });

    const countEl = document.getElementById('count-terisi');
    if (countEl) countEl.innerText = filledCount;
}

// Attach live event delegation
document.addEventListener('input', (e) => {
    if (e.target.classList.contains('bulk-density-input') || e.target.classList.contains('bulk-temp-input')) {
        recalculateAll();
    }
});

// Custom Jam Handler
function handleJamChange(select) {
    const customInput = document.getElementById('custom-jam-input');
    if (select.value === 'custom') {
        customInput.classList.remove('hidden');
        customInput.focus();
        customInput.oninput = () => { select.name = ''; customInput.name = 'jam_label'; };
    } else {
        customInput.classList.add('hidden');
        select.name = 'jam_label';
        customInput.name = '';
    }
}

// Tambah Baris Produk Dinamis
let nextCardIndex = 100;
const daftarPilihanProduk = @json($produkList);

function tambahBarisProduk() {
    const container = document.getElementById('produk-cards-container');
    const idx = nextCardIndex++;

    const optionsHtml = daftarPilihanProduk.map(p => `<option value="${p}">${p}</option>`).join('');

    const cardHtml = `
        <div class="product-card rounded-3xl border border-indigo-200 bg-indigo-50/20 p-5 sm:p-6 shadow-card transition" data-index="${idx}">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-indigo-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="rounded-xl bg-indigo-600 text-white px-3 py-1 text-xs font-black shadow-sm">
                        Produk Tambahan
                    </span>
                    <select name="items[${idx}][produk]" class="rounded-xl border border-slate-200 bg-white px-3 py-1 text-xs font-bold text-slate-700">
                        ${optionsHtml}
                    </select>
                </div>
                <button type="button" onclick="this.closest('.product-card').remove(); recalculateAll();"
                        class="inline-flex items-center gap-1 text-xs font-bold text-rose-500 hover:text-rose-700">
                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus Baris
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-start">
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">MT Nopol</label>
                    <input type="text" name="items[${idx}][mt_nopol]" placeholder="cth: R 9675 B"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-700">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Tangki Timbun</label>
                    <input type="text" name="items[${idx}][tangki_timbun]" placeholder="cth: 09 / T.09"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-700">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Density Obs</label>
                    <input type="number" step="0.0001" name="items[${idx}][density_obs]" placeholder="cth: 735.0"
                           data-type="obs" data-target="preview-${idx}"
                           class="bulk-density-input w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Temperatur (°C)</label>
                    <input type="number" step="0.1" name="items[${idx}][temperatur]" placeholder="cth: 29.5"
                           data-type="temp" data-target="preview-${idx}"
                           class="bulk-temp-input w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800">
                </div>
                <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-2.5 text-center">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-blue">DENSITY '15</span>
                    <span id="preview-${idx}" class="density-preview-val my-0.5 block text-lg font-black text-brand-blue">—</span>
                    <span class="block text-[9.5px] font-medium text-slate-500">ASTM Table 53B</span>
                </div>
            </div>

            <div class="mt-3.5 flex items-center justify-between border-t border-slate-100 pt-3">
                <div class="flex items-center gap-2">
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100">
                        <i data-lucide="upload" class="h-3.5 w-3.5 text-slate-500"></i> Upload Foto
                        <input type="file" name="items[${idx}][foto]" accept="image/*,application/pdf" class="hidden" onchange="previewItemFoto(this, ${idx})">
                    </label>
                    <span id="label-foto-${idx}" class="text-xs text-slate-500 italic hidden"></span>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', cardHtml);
    if (window.lucide) lucide.createIcons();
    recalculateAll();
}

// Foto preview label
function previewItemFoto(input, idx) {
    const labelEl = document.getElementById(`label-foto-${idx}`);
    if (labelEl && input.files && input.files[0]) {
        labelEl.innerText = input.files[0].name;
        labelEl.classList.remove('hidden');
    }
}

// Camera Handling
let currentCameraTargetIdx = null;
let mediaStream = null;

function bukaKameraItem(idx) {
    currentCameraTargetIdx = idx;
    const modal = document.getElementById('modal-kamera');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(stream => {
            mediaStream = stream;
            document.getElementById('video-stream').srcObject = stream;
        })
        .catch(err => {
            alert('Tidak dapat mengakses kamera: ' + err.message);
            tutupKamera();
        });
}

function tutupKamera() {
    const modal = document.getElementById('modal-kamera');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }
}

function ambilGambar() {
    const video = document.getElementById('video-stream');
    const canvas = document.getElementById('canvas-capture');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        const file = new File([blob], "foto-sampel-" + Date.now() + ".jpg", { type: "image/jpeg" });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        
        if (currentCameraTargetIdx !== null) {
            const input = document.querySelector(`input[name="items[${currentCameraTargetIdx}][foto]"]`);
            if (input) {
                input.files = dataTransfer.files;
                previewItemFoto(input, currentCameraTargetIdx);
            }
        }
        tutupKamera();
    }, 'image/jpeg', 0.9);
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
    recalculateAll();
    updateSinglePreview();
});
</script>
@endsection
