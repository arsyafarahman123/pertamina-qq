@extends('layouts.app')
@section('title', $jenisUji->nama)

@section('content')
<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('uji.index') }}" class="inline-flex items-center gap-1 font-medium hover:text-brand-blue">
        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Panduan Uji
    </a>
    <span class="text-slate-300">/</span>
    <span class="font-semibold text-slate-700">{{ $jenisUji->nama }}</span>
</div>

@php
    $metaUji = [
        'FLASHPOINT' => ['icon' => 'flame', 'astm' => 'ASTM D93', 'color' => 'bg-amber-50 text-amber-600 border-amber-200'],
        'COLORIMETER' => ['icon' => 'palette', 'astm' => 'ASTM D1500', 'color' => 'bg-purple-50 text-purple-600 border-purple-200'],
        'VISKOSITAS' => ['icon' => 'waves', 'astm' => 'ASTM D445', 'color' => 'bg-teal-50 text-teal-600 border-teal-200'],
        'WATER_CONTENT' => ['icon' => 'droplets', 'astm' => 'ASTM D6304', 'color' => 'bg-sky-50 text-sky-600 border-sky-200'],
        'TAN' => ['icon' => 'flask-conical', 'astm' => 'ASTM D664', 'color' => 'bg-rose-50 text-rose-600 border-rose-200'],
        'DESTILASI' => ['icon' => 'thermometer', 'astm' => 'ASTM D86', 'color' => 'bg-orange-50 text-orange-600 border-orange-200'],
        'DENSITY' => ['icon' => 'gauge', 'astm' => 'ASTM D1298', 'color' => 'bg-emerald-50 text-emerald-600 border-emerald-200'],
        'RON' => ['icon' => 'zap', 'astm' => 'ASTM D2699', 'color' => 'bg-blue-50 text-brand-blue border-blue-200'],
        'SULFUR' => ['icon' => 'atom', 'astm' => 'ASTM D4294', 'color' => 'bg-indigo-50 text-indigo-600 border-indigo-200'],
    ];
    $currentMeta = $metaUji[$jenisUji->kode] ?? [
        'icon' => $jenisUji->icon ?: 'flask-conical',
        'astm' => 'Standar Lab',
        'color' => 'bg-brand-blue/10 text-brand-blue border-brand-blue/20',
    ];
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

    <!-- Panduan SOP step-by-step -->
    <div class="lg:col-span-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border p-2.5 {{ $currentMeta['color'] }}">
                        <i data-lucide="{{ $currentMeta['icon'] }}" class="h-6 w-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">{{ $jenisUji->nama }}</h2>
                            <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 font-mono text-[11px] font-bold text-slate-600">
                                {{ $currentMeta['astm'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Panduan Langkah Prosedur Pengujian (SOP)</p>
                    </div>
                </div>
                <span class="rounded-full bg-brand-blue/5 px-3 py-1 text-xs font-semibold text-brand-blue">
                    {{ $jenisUji->langkahSop->count() }} langkah SOP
                </span>
            </div>
            <p class="mb-6 text-sm leading-relaxed text-slate-500">{{ $jenisUji->deskripsi }}</p>

            <div x-data="{ selesai: [] }" class="space-y-3">
                @foreach ($jenisUji->langkahSop as $langkah)
                    <div class="flex gap-4 rounded-xl border border-slate-100 p-4 transition"
                         :class="selesai.includes({{ $langkah->urutan }}) ? 'border-emerald-200 bg-emerald-50/50' : 'hover:border-slate-200 hover:bg-slate-50'">
                        <button type="button"
                                @click="selesai.includes({{ $langkah->urutan }}) ? selesai = selesai.filter(x => x !== {{ $langkah->urutan }}) : selesai.push({{ $langkah->urutan }})"
                                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition"
                                :class="selesai.includes({{ $langkah->urutan }}) ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 text-slate-500 hover:border-brand-blue/60 hover:text-brand-blue/50'">
                            <span x-show="!selesai.includes({{ $langkah->urutan }})">{{ $langkah->urutan }}</span>
                            <i x-show="selesai.includes({{ $langkah->urutan }})" data-lucide="check" class="h-4 w-4"></i>
                        </button>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800">{{ $langkah->judul_singkat }}</p>
                            <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $langkah->instruksi }}</p>
                            @if ($langkah->parameter_setting || $langkah->indikator_selesai)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if ($langkah->parameter_setting)
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                            <i data-lucide="settings-2" class="h-3.5 w-3.5"></i>
                                            {{ $langkah->parameter_setting }}
                                        </span>
                                    @endif
                                    @if ($langkah->indikator_selesai)
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                            {{ $langkah->indikator_selesai }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form input hasil uji -->
    <div class="lg:col-span-2">
        <div class="sticky top-20 rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-blue/5 text-brand-blue">
                    <i data-lucide="clipboard-edit" class="h-5 w-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Catat Hasil Uji</h2>
                    <p class="text-xs text-slate-400">Isi data pengukuran lalu simpan</p>
                </div>
            </div>

            <form method="POST" action="{{ route('uji.simpan', $jenisUji) }}" class="space-y-4" enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Sampel</label>
                    <select name="nama_sampel" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">
                        <option value="">— Pilih sampel —</option>
                        @foreach ($daftarSampel as $sampel)
                            <option value="{{ $sampel }}" @selected(old('nama_sampel') === $sampel)>{{ $sampel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor KKW / Kereta</label>
                    <input type="text" name="nomor_kkw" value="{{ old('nomor_kkw') }}" placeholder="Contoh: KKW 325"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">
                </div>

                @if (count($fieldConfig))
                    <div class="border-t border-slate-100 pt-4">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Hasil Pengukuran</p>
                        <div class="space-y-4">
                            @foreach ($fieldConfig as $key => $field)
                                <div>
                                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $field['label'] }}</label>
                                    <input type="{{ $field['type'] === 'number' ? 'number' : 'text' }}"
                                           step="any"
                                           name="data_hasil[{{ $key }}]"
                                           value="{{ old('data_hasil.' . $key, $field['default'] ?? '') }}"
                                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Catatan (opsional)</label>
                    <textarea name="catatan" rows="2" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">{{ old('catatan') }}</textarea>
                </div>

                <x-foto-bukti-field
                    name="foto_bukti"
                    help-text="Opsional. Unggah file apa saja (foto, PDF, dokumen) atau ambil foto langsung dari kamera — file gambar akan tampil lebih besar pada sertifikat cetak sebagai bukti pengujian." />

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-blue to-brand-dark px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-blue/50/30 transition hover:from-brand-blue hover:to-violet-700">
                    Simpan Hasil Uji
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
