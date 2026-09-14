@extends('layouts.app')
@section('title', 'Panduan Uji (SOP)')

@section('content')

@php
    $metaUji = [
        'FLASHPOINT' => [
            'icon' => 'flame',
            'astm' => 'ASTM D93',
            'color' => 'orange',
            'icon_class' => 'bg-amber-50 text-amber-600 border border-amber-200/80 group-hover:bg-gradient-to-br group-hover:from-amber-500 group-hover:to-orange-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-amber-500/25',
            'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200/70',
        ],
        'COLORIMETER' => [
            'icon' => 'palette',
            'astm' => 'ASTM D1500',
            'color' => 'purple',
            'icon_class' => 'bg-purple-50 text-purple-600 border border-purple-200/80 group-hover:bg-gradient-to-br group-hover:from-purple-500 group-hover:to-indigo-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-purple-500/25',
            'badge_class' => 'bg-purple-50 text-purple-700 border-purple-200/70',
        ],
        'VISKOSITAS' => [
            'icon' => 'waves',
            'astm' => 'ASTM D445',
            'color' => 'teal',
            'icon_class' => 'bg-teal-50 text-teal-600 border border-teal-200/80 group-hover:bg-gradient-to-br group-hover:from-teal-500 group-hover:to-cyan-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-teal-500/25',
            'badge_class' => 'bg-teal-50 text-teal-700 border-teal-200/70',
        ],
        'WATER_CONTENT' => [
            'icon' => 'droplets',
            'astm' => 'ASTM D6304',
            'color' => 'sky',
            'icon_class' => 'bg-sky-50 text-sky-600 border border-sky-200/80 group-hover:bg-gradient-to-br group-hover:from-sky-500 group-hover:to-blue-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-sky-500/25',
            'badge_class' => 'bg-sky-50 text-sky-700 border-sky-200/70',
        ],
        'TAN' => [
            'icon' => 'flask-conical',
            'astm' => 'ASTM D664',
            'color' => 'rose',
            'icon_class' => 'bg-rose-50 text-rose-600 border border-rose-200/80 group-hover:bg-gradient-to-br group-hover:from-rose-500 group-hover:to-pink-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-rose-500/25',
            'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200/70',
        ],
        'DESTILASI' => [
            'icon' => 'thermometer',
            'astm' => 'ASTM D86',
            'color' => 'amber',
            'icon_class' => 'bg-orange-50 text-orange-600 border border-orange-200/80 group-hover:bg-gradient-to-br group-hover:from-orange-500 group-hover:to-amber-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-orange-500/25',
            'badge_class' => 'bg-orange-50 text-orange-700 border-orange-200/70',
        ],
        'DENSITY' => [
            'icon' => 'gauge',
            'astm' => 'ASTM D1298',
            'color' => 'emerald',
            'icon_class' => 'bg-emerald-50 text-emerald-600 border border-emerald-200/80 group-hover:bg-gradient-to-br group-hover:from-emerald-500 group-hover:to-teal-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-emerald-500/25',
            'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
        ],
        'RON' => [
            'icon' => 'zap',
            'astm' => 'ASTM D2699',
            'color' => 'blue',
            'icon_class' => 'bg-blue-50 text-brand-blue border border-blue-200/80 group-hover:bg-gradient-to-br group-hover:from-brand-blue group-hover:to-blue-700 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-brand-blue/25',
            'badge_class' => 'bg-blue-50 text-brand-blue border-blue-200/70',
        ],
        'SULFUR' => [
            'icon' => 'atom',
            'astm' => 'ASTM D4294',
            'color' => 'indigo',
            'icon_class' => 'bg-indigo-50 text-indigo-600 border border-indigo-200/80 group-hover:bg-gradient-to-br group-hover:from-indigo-500 group-hover:to-violet-600 group-hover:text-white group-hover:border-transparent group-hover:shadow-lg group-hover:shadow-indigo-500/25',
            'badge_class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
        ],
    ];
@endphp

<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-sm text-slate-500">Pilih jenis pengujian untuk melihat panduan langkah-langkah (SOP) sekaligus mencatat hasil ujinya.</p>
    </div>
    <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-brand-blue/5 px-3 py-1 text-xs font-semibold text-brand-blue sm:self-auto">
        <i data-lucide="list-filter" class="h-3.5 w-3.5"></i>
        {{ $daftarUji->count() }} jenis pengujian
    </span>
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($daftarUji as $jenis)
        @php
            $meta = $metaUji[$jenis->kode] ?? [
                'icon' => $jenis->icon ?: 'flask-conical',
                'astm' => 'Standar Lab',
                'icon_class' => 'bg-brand-blue/5 text-brand-blue border border-brand-blue/20 group-hover:bg-brand-blue group-hover:text-white',
                'badge_class' => 'bg-slate-100 text-slate-600 border-slate-200',
            ];
        @endphp
        <a href="{{ route('uji.show', $jenis) }}"
           class="group relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-card transition duration-200 hover:-translate-y-1 hover:border-brand-blue/30 hover:shadow-xl">
            
            <div class="mb-4 flex items-center justify-between">
                <!-- Icon Khusus Setiap Jenis Uji -->
                <div class="flex h-13 w-13 items-center justify-center rounded-2xl transition-all duration-200 group-hover:scale-105 p-3 {{ $meta['icon_class'] }}">
                    <i data-lucide="{{ $meta['icon'] }}" class="h-6 w-6"></i>
                </div>

                <!-- Badge Standar ASTM / Metode -->
                <div class="flex items-center gap-2">
                    <span class="rounded-lg border px-2.5 py-1 font-mono text-[11px] font-bold tracking-tight {{ $meta['badge_class'] }}">
                        {{ $meta['astm'] }}
                    </span>
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50 text-slate-400 transition group-hover:bg-brand-blue/10 group-hover:text-brand-blue group-hover:translate-x-0.5">
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </div>
                </div>
            </div>

            <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-blue transition-colors duration-150">
                {{ $jenis->nama }}
            </h3>
            <p class="mt-1.5 flex-1 text-sm leading-relaxed text-slate-500 line-clamp-2">
                {{ $jenis->deskripsi }}
            </p>

            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs font-semibold text-slate-500">
                <span class="inline-flex items-center gap-1.5 text-brand-blue font-bold">
                    <i data-lucide="circle-check" class="h-4 w-4"></i>
                    {{ $jenis->langkah_sop_count }} langkah SOP
                </span>
                <span class="text-slate-400 group-hover:text-brand-blue transition font-medium text-[11px] inline-flex items-center gap-1">
                    Buka Panduan <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                </span>
            </div>
        </a>
    @endforeach
</div>
@endsection
