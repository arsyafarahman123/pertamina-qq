@extends('layouts.app')
@section('title', $entry ? 'Edit Retain Sampel' : 'Input Retain Sampel Penyaluran MT — FT MAOS')

@section('content')
<div class="mx-auto max-w-6xl">
    {{-- Navigasi Atas --}}
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('retain-sampel.index', ['tanggal' => $entry ? $entry->tanggal->toDateString() : ($defaultTanggal ?? now()->toDateString())]) }}" 
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-600 hover:text-brand-blue bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-sm transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Laporan Retain
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-black text-emerald-800">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                ASTM Table 53B Otomatis
            </span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i data-lucide="alert-circle" class="h-4 w-4 text-red-600"></i> Mohon periksa data:
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
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#0f3861] px-3 py-1 text-[11px] font-bold text-white shadow-sm mb-2">
                    Edit Data Sampel
                </span>
                <h1 class="text-xl font-black text-slate-800">
                    Edit Retain Sampel — {{ $entry->produk }} (Pukul {{ str_replace(':', '.', $entry->jam_label) }} WIB)
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Isi Density Obs & Suhu — Density '15 dihitung otomatis oleh sistem sesuai ASTM Table 53B.
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
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Sesi Jam Observasi</label>
                        <select name="jam_label" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-bold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                            @foreach ($jamStandar as $j)
                                <option value="{{ $j }}" @selected(old('jam_label', $entry->jam_label) === $j)>
                                    Pukul {{ $j }} WIB {{ $j === '06:00' ? '(Awal Penyaluran)' : ($j === '12:00' ? '(Retain Siang)' : '(Retain Sore)') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Baris 2: Produk --}}
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Produk</label>
                    <select name="produk" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-bold text-[#0f3861] transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
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
                        <input type="text" name="tangki_timbun" value="{{ old('tangki_timbun', $entry->tangki_timbun) }}" placeholder="contoh: 09 atau T.09"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                </div>

                {{-- Baris 4: Density Obs & Temperatur --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Density Obs</label>
                        <input type="number" step="0.0001" id="single-density-obs" name="density_obs" required value="{{ old('density_obs', $entry->density_obs) }}" placeholder="contoh: 0.7420"
                               oninput="hitungSingleDensity15()"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-bold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Temperatur (°C)</label>
                        <input type="number" step="0.1" id="single-temp" name="temperatur" required value="{{ old('temperatur', $entry->temperatur) }}" placeholder="contoh: 29.5"
                               oninput="hitungSingleDensity15()"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-bold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>
                </div>

                {{-- Baris 5: Foto Botol Sampel --}}
                <div>
                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Foto Botol Sampel (Opsional)</label>
                    @if ($entry->fotoUrl())
                        <div class="mb-2 flex items-center gap-2">
                            <img src="{{ $entry->fotoUrl() }}" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                            <span class="text-xs text-slate-400">Foto tersimpan. Upload jika ingin mengganti.</span>
                        </div>
                    @endif
                    <input type="file" name="foto" accept="image/*,application/pdf" class="text-xs text-slate-600">
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
        {{-- MODE INPUT SEKALIGUS (MULTI-PRODUK DENGAN 1 TOMBOL SIMPAN DI PALING BAWAH) --}}
        {{-- ========================================================================= --}}
        <form method="POST" action="{{ route('retain-sampel.store') }}" enctype="multipart/form-data" id="bulk-retain-form" class="space-y-6">
            @csrf

            {{-- 1. KARTU HEADER SESI & TANGGAL --}}
            <div class="rounded-3xl border border-slate-200 bg-gradient-to-r from-[#0a233d] via-[#0f3861] to-[#1c558c] p-6 sm:p-7 text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-5">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-red px-3 py-1 text-[11px] font-black uppercase tracking-wider text-white shadow-sm">
                            <i data-lucide="layers" class="h-3.5 w-3.5"></i> Form Retain Multi-Produk
                        </span>
                        <h1 class="mt-2 text-xl sm:text-2xl font-black text-white tracking-tight">
                            Input Retain Sampel Penyaluran MT
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-200 mt-1">
                            Ketik data Density, Suhu, Nopol, dan Tangki untuk produk yang disalurkan. Tekan <b class="text-amber-300 underline">Simpan Semua Data Sampel</b> di paling bawah.
                        </p>
                    </div>

                    <div class="hidden sm:flex items-center gap-2 bg-white/10 rounded-2xl p-3 backdrop-blur border border-white/10">
                        <div class="text-right">
                            <span class="block text-[10px] uppercase font-bold text-slate-300">Format Resmi</span>
                            <span class="text-xs font-black text-emerald-300">FT MAOS — QC Lab</span>
                        </div>
                    </div>
                </div>

                {{-- Pemilih Tanggal & Sesi Jam --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-5">
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-200">
                            1. Tanggal Observasi
                        </label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal', $defaultTanggal ?? now()->toDateString()) }}"
                               class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-bold text-white backdrop-blur transition focus:border-brand-red focus:bg-white/20 focus:outline-none focus:ring-4 focus:ring-brand-red/20">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-slate-200">
                            2. Pilih Sesi Jam Penyaluran
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @php $currJam = old('jam_label', $defaultJamLabel ?? '06:00'); @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="jam_label" value="06:00" class="peer hidden" @checked($currJam === '06:00') onchange="updateSesiDisplay('06:00')">
                                <div class="peer-checked:bg-white peer-checked:text-[#0f3861] peer-checked:shadow-lg peer-checked:font-black rounded-xl border border-white/20 bg-white/10 p-2.5 text-center text-xs font-bold text-white transition hover:bg-white/20">
                                    06.00 WIB
                                    <span class="block text-[10px] opacity-80 font-normal">Awal Penyaluran</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="jam_label" value="12:00" class="peer hidden" @checked($currJam === '12:00') onchange="updateSesiDisplay('12:00')">
                                <div class="peer-checked:bg-white peer-checked:text-[#0f3861] peer-checked:shadow-lg peer-checked:font-black rounded-xl border border-white/20 bg-white/10 p-2.5 text-center text-xs font-bold text-white transition hover:bg-white/20">
                                    12.00 WIB
                                    <span class="block text-[10px] opacity-80 font-normal">Retain Siang</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="jam_label" value="18:00" class="peer hidden" @checked($currJam === '18:00') onchange="updateSesiDisplay('18:00')">
                                <div class="peer-checked:bg-white peer-checked:text-[#0f3861] peer-checked:shadow-lg peer-checked:font-black rounded-xl border border-white/20 bg-white/10 p-2.5 text-center text-xs font-bold text-white transition hover:bg-white/20">
                                    18.00 WIB
                                    <span class="block text-[10px] opacity-80 font-normal">Retain Sore</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TABEL SPREADSHEET MULTI-PRODUK (6 PRODUK RESMI) --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 shadow-card">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-slate-800 flex items-center gap-2">
                            <i data-lucide="flask-conical" class="h-4 w-4 text-brand-blue"></i>
                            Daftar Uji Retain Sampel Produk
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Isi baris produk yang diuji. Kosongkan produk yang tidak disalurkan.</p>
                    </div>
                    <span class="text-xs font-bold text-[#0f3861] bg-blue-50 border border-blue-200 px-3 py-1 rounded-full">
                        6 Produk Terdaftar
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-100/80 text-slate-600 uppercase tracking-wider text-[11px]">
                                <th class="px-3.5 py-3 text-left font-extrabold rounded-l-xl">Produk</th>
                                <th class="px-3 py-3 text-left font-extrabold">MT Nopol</th>
                                <th class="px-3 py-3 text-left font-extrabold">Tangki Timbun</th>
                                <th class="px-3 py-3 text-left font-extrabold">Density Obs</th>
                                <th class="px-3 py-3 text-left font-extrabold">Suhu (°C)</th>
                                <th class="px-3 py-3 text-center font-extrabold text-brand-blue">Density '15 (ASTM)</th>
                                <th class="px-3 py-3 text-center font-extrabold rounded-r-xl">Foto Botol</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="bulk-table-body">
                            @php
                                $productBadges = [
                                    'Pertalite' => 'bg-emerald-600 text-white',
                                    'Pertamax' => 'bg-blue-600 text-white',
                                    'Pertamax Turbo' => 'bg-rose-700 text-white',
                                    'Biosolar B50' => 'bg-amber-600 text-white',
                                    'Dexlite' => 'bg-teal-700 text-white',
                                    'Pertadex' => 'bg-purple-700 text-white',
                                ];
                            @endphp

                            @foreach ($produkList as $index => $prod)
                                @php
                                    $badge = $productBadges[$prod] ?? 'bg-slate-700 text-white';
                                @endphp
                                <tr class="hover:bg-blue-50/20 transition" data-row="{{ $index }}">
                                    {{-- Kolom 1: Nama Produk --}}
                                    <td class="px-3.5 py-3 whitespace-nowrap">
                                        <input type="hidden" name="items[{{ $index }}][produk]" value="{{ $prod }}">
                                        <span class="inline-flex items-center gap-1.5 rounded-xl {{ $badge }} px-3 py-1.5 text-xs font-black shadow-sm">
                                            {{ $prod }}
                                        </span>
                                    </td>

                                    {{-- Kolom 2: MT Nopol --}}
                                    <td class="px-2 py-2">
                                        <input type="text" name="items[{{ $index }}][mt_nopol]" 
                                               placeholder="cth: R 9675 B" 
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                                    </td>

                                    {{-- Kolom 3: Tangki Timbun --}}
                                    <td class="px-2 py-2">
                                        <input type="text" name="items[{{ $index }}][tangki_timbun]" 
                                               placeholder="cth: 09 / T.09" 
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                                    </td>

                                    {{-- Kolom 4: Density Obs --}}
                                    <td class="px-2 py-2">
                                        <input type="number" step="0.0001" name="items[{{ $index }}][density_obs]" 
                                               id="bulk_obs_{{ $index }}" 
                                               placeholder="0.7420" 
                                               oninput="calcBulkDensity15({{ $index }})"
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                                    </td>

                                    {{-- Kolom 5: Suhu --}}
                                    <td class="px-2 py-2">
                                        <input type="number" step="0.1" name="items[{{ $index }}][temperatur]" 
                                               id="bulk_temp_{{ $index }}" 
                                               placeholder="29.5" 
                                               oninput="calcBulkDensity15({{ $index }})"
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                                    </td>

                                    {{-- Kolom 6: Density'15 Live Preview --}}
                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center rounded-xl bg-blue-50 border border-blue-200 px-3.5 py-1.5 text-xs font-black text-brand-blue min-w-[80px]"
                                             id="bulk_d15_{{ $index }}">
                                            —
                                        </div>
                                    </td>

                                    {{-- Kolom 7: Foto Botol Opsional --}}
                                    <td class="px-2 py-2 text-center whitespace-nowrap">
                                        <label class="cursor-pointer inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 px-2.5 py-1.5 text-[11px] font-bold text-slate-600 transition">
                                            <i data-lucide="upload" class="h-3 w-3 text-slate-400"></i>
                                            <span id="foto-label-{{ $index }}">Upload</span>
                                            <input type="file" name="items[{{ $index }}][foto]" accept="image/*,application/pdf" class="hidden" onchange="previewFileLabel(this, {{ $index }})">
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tambah Baris Tambahan (Jika misal ada 2x Pertalite / produk lainnya) --}}
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="tambahBarisTabel()" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-blue hover:text-brand-blueDark">
                        <i data-lucide="plus-circle" class="h-4 w-4"></i> + Tambah Baris Produk Tambahan (misal 2x Pertalite)
                    </button>
                    <span class="text-[11px] text-slate-400 font-semibold">Density'15 dikalkulasi otomatis sesuai standar ASTM Table 53B</span>
                </div>
            </div>

            {{-- 3. STICKY BOTTOM BAR (TOMBOL SIMPAN PALING BAWAH) --}}
            <div class="sticky bottom-4 z-30 rounded-3xl border border-slate-200 bg-white/95 p-4 sm:p-5 shadow-2xl backdrop-blur">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#0f3861] text-white shadow-md">
                            <i data-lucide="check" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800">
                                Form Siap Disimpan
                            </p>
                            <p class="text-xs text-slate-500">
                                Seluruh baris yang diisi akan langsung tersimpan permanen dalam 1 kali klik.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('retain-sampel.index') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-2 rounded-2xl bg-brand-red hover:bg-brand-redDark px-8 py-3.5 text-sm sm:text-base font-extrabold text-white shadow-lg shadow-brand-red/30 transition hover:-translate-y-0.5 active:scale-95">
                            <i data-lucide="save" class="h-5 w-5"></i>
                            Simpan Semua Data Sampel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
// Live ASTM 53B Calculation for Bulk Table
function calcBulkDensity15(index) {
    var obsEl = document.getElementById('bulk_obs_' + index);
    var tempEl = document.getElementById('bulk_temp_' + index);
    var previewEl = document.getElementById('bulk_d15_' + index);
    if (!obsEl || !tempEl || !previewEl) return;

    var obs = parseFloat(obsEl.value);
    var temp = parseFloat(tempEl.value);

    if (isNaN(obs) || isNaN(temp) || obs <= 0) {
        previewEl.textContent = '—';
        return;
    }

    var rhoT = obs > 10.0 ? obs : (obs * 1000.0);
    if (rhoT < 100.0 || rhoT > 2000.0) {
        previewEl.textContent = (obs > 10.0 ? (obs / 1000.0).toFixed(4) : obs.toFixed(4));
        return;
    }

    var dT = temp - 15.0;
    var anchors = [
        [690.0, 0.8636], [700.0, 0.8500], [710.0, 0.8364], [720.0, 0.8182],
        [730.0, 0.8091], [733.0, 0.8000], [740.0, 0.7909], [743.0, 0.7909],
        [750.0, 0.7727], [759.0, 0.7636], [800.0, 0.7000], [810.0, 0.6909],
        [815.0, 0.6818], [820.0, 0.6818], [830.0, 0.6727], [839.0, 0.6636],
        [840.0, 0.6636], [850.0, 0.6545], [860.0, 0.6545], [869.0, 0.6455]
    ];

    var slope = 0.75;
    if (rhoT <= anchors[0][0]) slope = anchors[0][1];
    else if (rhoT >= anchors[anchors.length - 1][0]) slope = anchors[anchors.length - 1][1];
    else {
        for (var i = 0; i < anchors.length - 1; i++) {
            if (rhoT >= anchors[i][0] && rhoT <= anchors[i + 1][0]) {
                var frac = (rhoT - anchors[i][0]) / (anchors[i + 1][0] - anchors[i][0]);
                slope = anchors[i][1] + frac * (anchors[i + 1][1] - anchors[i][1]);
                break;
            }
        }
    }

    var rho15 = rhoT + (slope * dT);
    var d15 = (rho15 / 1000.0).toFixed(4);
    previewEl.textContent = d15;
}

function hitungSingleDensity15() {
    var obsEl = document.getElementById('single-density-obs');
    var tempEl = document.getElementById('single-temp');
    var previewEl = document.getElementById('single-preview-density-15');
    if (!obsEl || !tempEl || !previewEl) return;

    var obs = parseFloat(obsEl.value);
    var temp = parseFloat(tempEl.value);
    if (isNaN(obs) || isNaN(temp) || obs <= 0) return;

    var rhoT = obs > 10.0 ? obs : (obs * 1000.0);
    var dT = temp - 15.0;
    var slope = 0.75;
    var rho15 = rhoT + (slope * dT);
    previewEl.textContent = (rho15 / 1000.0).toFixed(4);
}

function previewFileLabel(input, index) {
    var label = document.getElementById('foto-label-' + index);
    if (input.files && input.files[0]) {
        label.textContent = '✓ ' + input.files[0].name.substring(0, 10) + '...';
        label.parentElement.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');
    }
}

var extraRowCounter = 100;
function tambahBarisTabel() {
    var tbody = document.getElementById('bulk-table-body');
    var idx = extraRowCounter++;
    var tr = document.createElement('tr');
    tr.className = 'hover:bg-blue-50/20 transition bg-amber-50/20';
    tr.innerHTML = `
        <td class="px-3.5 py-3 whitespace-nowrap">
            <select name="items[${idx}][produk]" class="rounded-xl border border-slate-200 bg-white px-2 py-1.5 text-xs font-bold text-[#0f3861]">
                <option value="Pertalite">Pertalite</option>
                <option value="Pertamax">Pertamax</option>
                <option value="Pertamax Turbo">Pertamax Turbo</option>
                <option value="Biosolar B50">Biosolar B50</option>
                <option value="Dexlite">Dexlite</option>
                <option value="Pertadex">Pertadex</option>
            </select>
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${idx}][mt_nopol]" placeholder="cth: R 9675 B" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-semibold">
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${idx}][tangki_timbun]" placeholder="cth: 09" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-semibold">
        </td>
        <td class="px-2 py-2">
            <input type="number" step="0.0001" name="items[${idx}][density_obs]" id="bulk_obs_${idx}" placeholder="0.7420" oninput="calcBulkDensity15(${idx})" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold">
        </td>
        <td class="px-2 py-2">
            <input type="number" step="0.1" name="items[${idx}][temperatur]" id="bulk_temp_${idx}" placeholder="29.5" oninput="calcBulkDensity15(${idx})" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold">
        </td>
        <td class="px-3 py-2 text-center whitespace-nowrap">
            <div class="inline-flex items-center justify-center rounded-xl bg-blue-50 border border-blue-200 px-3.5 py-1.5 text-xs font-black text-brand-blue min-w-[80px]" id="bulk_d15_${idx}">—</div>
        </td>
        <td class="px-2 py-2 text-center whitespace-nowrap">
            <button type="button" onclick="this.closest('tr').remove()" class="rounded-lg bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 text-xs font-bold">Hapus</button>
        </td>
    `;
    tbody.appendChild(tr);
    if (window.lucide) lucide.createIcons();
}
</script>
@endsection
