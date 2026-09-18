@extends('layouts.app')
@section('title', 'Checklist Mobil Tangki')

@section('content')

<!-- ===== Header Aksi ===== -->
<div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <p class="text-xs sm:text-sm text-slate-500 font-medium">Digitalisasi Form Pemeriksaan Mobil Tangki — Fuel Terminal Maos</p>
    </div>
    
    <!-- Tombol Aksi (Responsif HP & Laptop) -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2">
            <button id="export-all-btn" onclick="triggerExport()"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-card transition hover:bg-slate-50 active:scale-[0.98]">
                <i data-lucide="sheet" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                <span id="export-btn-label" class="truncate">Export Excel</span>
            </button>
            <button id="export-pdf-all-btn" onclick="triggerPdfExport()"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-card transition hover:bg-slate-50 active:scale-[0.98]">
                <i data-lucide="file-text" class="h-4 w-4 text-[#006CB8] shrink-0"></i>
                <span id="export-pdf-btn-label" class="truncate">Export PDF</span>
            </button>
        </div>

        @if (!auth()->user()->isSpbu())
            <a href="{{ route('checklist-mt-maos.create') }}"
               class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-red px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:-translate-y-0.5 hover:bg-brand-redDark active:scale-[0.98]">
                <i data-lucide="plus" class="h-4 w-4 shrink-0"></i>
                <span>Tambah Data Checklist</span>
            </a>
        @endif
    </div>
</div>

<!-- ===== Bar Pilihan Export Terpilih (Muncul saat ada checkbox dicentang) ===== -->
<div id="selection-bar" class="mb-5 hidden flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-brand-blue/30 bg-gradient-to-r from-blue-50/90 via-white to-blue-50/80 p-4 shadow-card">
    <div class="flex items-center gap-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-blue text-white shadow-sm">
            <i data-lucide="check-square" class="h-5 w-5"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800">
                <span id="selected-count" class="rounded-md bg-brand-blue px-2 py-0.5 text-xs text-white">0</span> Checklist Dipilih
            </p>
            <p class="text-xs text-slate-500">Mengekspor rekap khusus untuk armada yang Anda centang.</p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="exportSelected()" class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700">
            <i data-lucide="sheet" class="h-4 w-4"></i> Excel
        </button>
        <button type="button" onclick="exportSelectedPdf()" class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-xl bg-[#006CB8] px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
            <i data-lucide="file-text" class="h-4 w-4"></i> PDF
        </button>
        <button type="button" onclick="uncheckAll()" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 shadow-sm hover:bg-slate-50">
            Batal
        </button>
    </div>
</div>

<!-- ===== Ringkasan Statistik (Responsif HP: Grid 2-3-5) ===== -->
<div class="mb-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3">
    <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'all'])) }}"
       class="rounded-2xl border transition p-3.5 sm:p-4 shadow-card {{ ($filter ?? 'all') === 'all' ? 'border-brand-blue bg-blue-50/50 ring-2 ring-brand-blue/20' : 'border-slate-200 bg-white hover:border-slate-300' }}">
        <p class="text-[10.5px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Checklist</p>
        <p class="mt-1 text-xl sm:text-2xl font-black text-slate-800">{{ $stats['total'] }}</p>
    </a>
    
    <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'today'])) }}"
       class="rounded-2xl border transition p-3.5 sm:p-4 shadow-card {{ ($filter ?? 'all') === 'today' ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/20' : 'border-emerald-200/80 bg-gradient-to-br from-white to-emerald-50/30 hover:border-emerald-300' }}">
        <div class="flex items-center justify-between">
            <p class="text-[10.5px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-700 truncate">Diupload Hari Ini</p>
            @if ($stats['today'] > 0)
                <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            @endif
        </div>
        <p class="mt-1 text-xl sm:text-2xl font-black text-emerald-600">{{ $stats['today'] }}</p>
    </a>

    <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'week'])) }}"
       class="rounded-2xl border transition p-3.5 sm:p-4 shadow-card {{ ($filter ?? 'all') === 'week' ? 'border-brand-blue bg-blue-50/50 ring-2 ring-brand-blue/20' : 'border-slate-200 bg-white hover:border-slate-300' }}">
        <p class="text-[10.5px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400">7 Hari Terakhir</p>
        <p class="mt-1 text-xl sm:text-2xl font-black text-brand-blue">{{ $stats['this_week'] }}</p>
    </a>

    <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'clean'])) }}"
       class="rounded-2xl border transition p-3.5 sm:p-4 shadow-card {{ ($filter ?? 'all') === 'clean' ? 'border-emerald-500 bg-emerald-50/50 ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white hover:border-slate-300' }}">
        <p class="text-[10.5px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400">Sesuai Standar</p>
        <p class="mt-1 text-xl sm:text-2xl font-black text-emerald-600">{{ $stats['clean'] }}</p>
    </a>

    <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'flagged'])) }}"
       class="col-span-2 sm:col-span-1 rounded-2xl border transition p-3.5 sm:p-4 shadow-card {{ ($filter ?? 'all') === 'flagged' ? 'border-rose-500 bg-rose-50/50 ring-2 ring-rose-500/20' : 'border-slate-200 bg-white hover:border-slate-300' }}">
        <p class="text-[10.5px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400">Ada Temuan</p>
        <p class="mt-1 text-xl sm:text-2xl font-black text-brand-red">{{ $stats['flagged'] }}</p>
    </a>
</div>

<!-- ===== Pencarian & Quick Filter Chips ===== -->
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-card space-y-3">
    <form method="GET" class="flex gap-2">
        @if (request('filter') && request('filter') !== 'all')
            <input type="hidden" name="filter" value="{{ request('filter') }}">
        @endif
        <div class="relative flex-1">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <i data-lucide="search" class="h-4 w-4"></i>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor polisi / pemilik / SPBU..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-10 pr-3.5 text-xs sm:text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </div>
        <button type="submit"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition hover:bg-brand-blueDark">
            <i data-lucide="search" class="h-4 w-4"></i>
            <span class="hidden sm:inline">Cari</span>
        </button>
    </form>

    <!-- Filter Cepat (Swipeable Horizontally di HP) -->
    <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar pt-2 border-t border-slate-100 text-xs whitespace-nowrap">
        <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1 hidden sm:inline">Filter:</span>
        <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'all'])) }}"
           class="inline-flex shrink-0 items-center gap-1 px-3 py-1.5 rounded-xl font-bold transition {{ ($filter ?? 'all') === 'all' ? 'bg-[#0f3861] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Semua ({{ $stats['total'] }})
        </a>
        <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'today'])) }}"
           class="inline-flex shrink-0 items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold transition {{ ($filter ?? 'all') === 'today' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
            <span class="h-1.5 w-1.5 rounded-full {{ ($filter ?? 'all') === 'today' ? 'bg-white' : 'bg-emerald-500 animate-pulse' }}"></span>
            Diupload Hari Ini ({{ $stats['today'] }})
        </a>
        <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'week'])) }}"
           class="inline-flex shrink-0 items-center gap-1 px-3 py-1.5 rounded-xl font-bold transition {{ ($filter ?? 'all') === 'week' ? 'bg-[#006CB8] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            7 Hari Terakhir ({{ $stats['this_week'] }})
        </a>
        <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'clean'])) }}"
           class="inline-flex shrink-0 items-center gap-1 px-3 py-1.5 rounded-xl font-bold transition {{ ($filter ?? 'all') === 'clean' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Sesuai Standar ({{ $stats['clean'] }})
        </a>
        <a href="{{ route('checklist-mt-maos.index', array_merge(request()->except('filter'), ['filter' => 'flagged'])) }}"
           class="inline-flex shrink-0 items-center gap-1 px-3 py-1.5 rounded-xl font-bold transition {{ ($filter ?? 'all') === 'flagged' ? 'bg-brand-red text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Ada Temuan ({{ $stats['flagged'] }})
        </a>
    </div>
</div>

<!-- ===== KONTEN DATA CHECKLIST ===== -->
<div class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
    <!-- Subheader Jumlah Data -->
    <div class="flex flex-wrap items-center justify-between border-b border-slate-200 px-4 sm:px-5 py-3.5 gap-2 bg-slate-50/50">
        <p class="text-xs sm:text-sm font-semibold text-slate-700">
            Ditemukan <span class="rounded-md bg-brand-blue/10 px-2 py-0.5 font-extrabold text-brand-blue">{{ $checklists->count() }}</span> checklist
            @if (($filter ?? 'all') === 'today')
                <span class="ml-1 text-[11px] font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded-md">• Filter Diupload Hari Ini</span>
            @endif
        </p>
        <span class="text-[11px] text-slate-400 hidden sm:inline">Centang checklist untuk mengekspor sebagian data</span>
    </div>

    <!-- ======================================================== -->
    <!-- 1. TAMPILAN LAPTOP / DESKTOP (Tabel Lebar & Rapi)        -->
    <!-- ======================================================== -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="w-12 px-4 py-3.5 text-center">
                        <input type="checkbox" id="check-all" title="Pilih Semua Checklist"
                               class="h-4 w-4 cursor-pointer rounded border-slate-300 text-brand-blue focus:ring-brand-blue/20">
                    </th>
                    <th class="px-4 py-3.5">Nomor Polisi</th>
                    <th class="px-4 py-3.5">Pemilik / SPBU</th>
                    <th class="px-4 py-3.5">Tgl Periksa &amp; Waktu Upload</th>
                    <th class="px-4 py-3.5">Status</th>
                    <th class="px-4 py-3.5">Diperiksa Oleh</th>
                    <th class="px-4 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($checklists as $c)
                    <tr id="row-{{ $c->id }}" class="group transition hover:bg-brand-blue/[0.03]">
                        <td class="w-12 px-4 py-4 text-center">
                            <input type="checkbox" value="{{ $c->id }}"
                                   class="row-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-brand-blue focus:ring-brand-blue/20"
                                   onchange="handleCheckboxChange(this)">
                        </td>
                        <td class="px-4 py-4">
                            <span class="rounded-lg bg-brand-dark px-2.5 py-1 font-mono text-xs font-extrabold tracking-wide text-white shadow-sm border border-slate-700">{{ $c->nomor_polisi }}</span>
                        </td>
                        <td class="px-4 py-4 font-medium text-slate-700">{{ $c->pemilik ?: '-' }}</td>
                        <td class="px-4 py-4">
                            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                <i data-lucide="calendar" class="h-3.5 w-3.5 text-[#006CB8]"></i>
                                <span>{{ $c->tanggal_periksa->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="mt-1">
                                @if ($c->created_at && $c->created_at->isToday())
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 px-2 py-0.5 text-[10.5px] font-extrabold text-emerald-800 border border-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Diupload Hari Ini ({{ $c->created_at->format('H:i') }} WIB)
                                    </span>
                                @elseif ($c->created_at)
                                    <span class="text-[11px] font-medium text-slate-400 flex items-center gap-1" title="Waktu di-add / diupload ke sistem">
                                        <i data-lucide="clock" class="h-3 w-3 text-slate-400"></i>
                                        Di-add: {{ $c->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400">{{ $c->tanggal_periksa->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if ($c->isIncomplete())
                                <div class="mb-1.5">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-[10.5px] font-black text-amber-800 shadow-xs" title="{{ implode(', ', $c->summaryIncomplete()) }}">
                                        <i data-lucide="alert-triangle" class="h-3 w-3 text-amber-600"></i> Belum Selesai Diisi ({{ $c->incompleteCount() }} Item)
                                    </span>
                                </div>
                            @endif

                            @if ($c->isFlagged())
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-brand-red ring-1 ring-inset ring-red-200">
                                        <i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i> {{ $c->flagCount() }} Temuan
                                    </span>
                                    <div class="mt-1 space-y-0.5 text-[11px] leading-tight">
                                        @foreach($c->summaryTemuan() as $st)
                                            <div class="flex items-start gap-1 text-slate-700">
                                                <span class="font-black text-brand-red">•</span>
                                                <span class="font-medium text-slate-700">{{ $st }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif ($c->hasPerbaikan())
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wide text-emerald-800 ring-1 ring-inset ring-emerald-300">
                                        <i data-lucide="check-check" class="h-3.5 w-3.5 text-emerald-600"></i> Selesai Perbaikan
                                    </span>
                                    <div class="mt-1 rounded-xl bg-emerald-50/90 border border-emerald-200 p-2 space-y-0.5 text-[11px] leading-tight">
                                        <p class="font-extrabold text-emerald-900 text-[10.5px] uppercase tracking-wider flex items-center gap-1">
                                            <i data-lucide="wrench" class="h-3 w-3 text-emerald-600"></i> Catatan Perbaikan:
                                        </p>
                                        @foreach($c->summaryPerbaikan() as $sp)
                                            <div class="flex items-start gap-1 text-emerald-950 font-medium">
                                                <span class="font-black text-emerald-600 shrink-0">✓</span>
                                                <span>{{ $sp }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <i data-lucide="circle-check" class="h-3.5 w-3.5"></i> Sesuai
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-slate-500">{{ $c->created_by ?: '-' }}</td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-80 transition group-hover:opacity-100">
                                <a href="{{ route('checklist-mt-maos.show', $c) }}" title="Lihat detail"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-brand-blue transition hover:bg-brand-blue/10">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                @if (!auth()->user()->isSpbu())
                                    <a href="{{ route('checklist-mt-maos.edit', $c) }}" title="Edit checklist"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </a>
                                    <form id="form-hapus-{{ $c->id }}" method="POST" action="{{ route('checklist-mt-maos.destroy', $c) }}" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" title="Hapus checklist"
                                                @click="window.__confirmDeleteModal.show(
                                                    document.getElementById('form-hapus-{{ $c->id }}'),
                                                    'Hapus Checklist Ini?',
                                                    'Checklist mobil tangki {{ $c->nomor_polisi }} akan dihapus permanen.'
                                                )"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-rose-500 transition hover:bg-rose-50">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-20 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50">
                                <i data-lucide="truck" class="h-8 w-8 text-slate-300"></i>
                            </div>
                            <p class="mt-4 text-sm font-semibold text-slate-600">Belum ada data checklist</p>
                            <p class="mt-1 text-xs text-slate-400">
                                @if (!auth()->user()->isSpbu())
                                    Mulai pemeriksaan dengan tombol "Tambah Data Checklist" di atas.
                                @else
                                    Hasil pemeriksaan mobil tangki akan muncul di sini.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ======================================================== -->
    <!-- 2. TAMPILAN HP / SMARTPHONE (Daftar Card Sentuh Rapi)    -->
    <!-- ======================================================== -->
    <div class="block md:hidden divide-y divide-slate-100">
        @forelse ($checklists as $c)
            <div id="card-{{ $c->id }}" class="p-4 transition space-y-3 {{ $c->hasPerbaikan() && !$c->isFlagged() ? 'bg-emerald-50/30 border-l-4 border-l-emerald-500' : 'hover:bg-slate-50/80' }}">
                <!-- Header Card: Checkbox + Plat + Status -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <input type="checkbox" value="{{ $c->id }}"
                               class="row-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-brand-blue focus:ring-brand-blue/20"
                               onchange="handleCheckboxChange(this)">
                        <span class="rounded-lg bg-brand-dark px-2.5 py-1 font-mono text-xs font-extrabold tracking-wide text-white border border-slate-700 shadow-sm">
                            {{ $c->nomor_polisi }}
                        </span>
                    </div>

                    <div class="flex flex-col items-end gap-1">
                        @if ($c->isIncomplete())
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-300 px-2 py-0.5 text-[10px] font-black text-amber-800">
                                <i data-lucide="alert-triangle" class="h-2.5 w-2.5 text-amber-600"></i> Belum Selesai ({{ $c->incompleteCount() }})
                            </span>
                        @endif

                        @if ($c->isFlagged())
                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[10.5px] font-bold uppercase tracking-wide text-brand-red ring-1 ring-inset ring-rose-200">
                                <i data-lucide="triangle-alert" class="h-3 w-3"></i> {{ $c->flagCount() }} Temuan
                            </span>
                        @elseif ($c->hasPerbaikan())
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10.5px] font-extrabold uppercase tracking-wide text-emerald-800 ring-1 ring-inset ring-emerald-300">
                                <i data-lucide="check-check" class="h-3 w-3 text-emerald-600"></i> Selesai Perbaikan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10.5px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                <i data-lucide="circle-check" class="h-3 w-3"></i> Sesuai
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Info Pemilik & Tanggal -->
                <div class="space-y-1.5 pl-6 text-xs">
                    <div class="font-bold text-slate-800 text-[13px] flex items-center gap-1.5">
                        <i data-lucide="building-2" class="h-3.5 w-3.5 text-slate-400 shrink-0"></i>
                        <span>{{ $c->pemilik ?: 'Pemilik Tidak Disebutkan' }}</span>
                    </div>

                    <div class="text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span class="flex items-center gap-1 font-medium">
                            <i data-lucide="calendar" class="h-3.5 w-3.5 text-[#006CB8]"></i>
                            {{ $c->tanggal_periksa->translatedFormat('d M Y') }}
                        </span>

                        @if ($c->created_at && $c->created_at->isToday())
                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 px-1.5 py-0.5 text-[10px] font-extrabold text-emerald-800 border border-emerald-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                Diupload Hari Ini ({{ $c->created_at->format('H:i') }} WIB)
                            </span>
                        @elseif ($c->created_at)
                            <span class="flex items-center gap-1 text-[11px] text-slate-400">
                                <i data-lucide="clock" class="h-3 w-3"></i>
                                Di-add: {{ $c->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Detail Temuan Mobile (Jika Ada) -->
                @if ($c->isFlagged() && count($c->summaryTemuan()))
                    <div class="ml-6 rounded-xl bg-rose-50/80 border border-rose-100 p-2.5 space-y-1 text-[11px]">
                        <p class="font-bold text-brand-red text-[10px] uppercase tracking-wider">Rincian Temuan:</p>
                        @foreach($c->summaryTemuan() as $st)
                            <div class="flex items-start gap-1.5 text-slate-700 leading-snug">
                                <span class="font-black text-brand-red shrink-0">•</span>
                                <span>{{ $st }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Detail Catatan Perbaikan Mobile (Jika Ada) -->
                @if ($c->hasPerbaikan() && count($c->summaryPerbaikan()))
                    <div class="ml-6 rounded-xl bg-emerald-50/90 border border-emerald-200 p-2.5 space-y-1 text-[11px]">
                        <p class="font-extrabold text-emerald-900 text-[10.5px] uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="wrench" class="h-3.5 w-3.5 text-emerald-600"></i> Catatan Perbaikan Selesai:
                        </p>
                        @foreach($c->summaryPerbaikan() as $sp)
                            <div class="flex items-start gap-1.5 text-emerald-950 leading-snug font-medium">
                                <span class="font-black text-emerald-600 shrink-0">✓</span>
                                <span>{{ $sp }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Footer Card Mobile: Inspector & Action Buttons -->
                <div class="flex items-center justify-between border-t border-slate-100 pt-2.5 pl-6">
                    <div class="text-[11px] text-slate-400 truncate max-w-[140px]">
                        Oleh: <span class="font-semibold text-slate-600">{{ $c->created_by ?: '-' }}</span>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ route('checklist-mt-maos.show', $c) }}"
                           class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-brand-blue transition hover:bg-blue-100">
                            <i data-lucide="eye" class="h-3.5 w-3.5"></i> Detail
                        </a>
                        @if (!auth()->user()->isSpbu())
                            <a href="{{ route('checklist-mt-maos.edit', $c) }}"
                               class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100">
                                <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                            </a>
                            <form id="form-hapus-m-{{ $c->id }}" method="POST" action="{{ route('checklist-mt-maos.destroy', $c) }}" class="contents">
                                @csrf
                                @method('DELETE')
                                <button type="button" title="Hapus checklist"
                                        @click="window.__confirmDeleteModal.show(
                                            document.getElementById('form-hapus-m-{{ $c->id }}'),
                                            'Hapus Checklist Ini?',
                                            'Checklist mobil tangki {{ $c->nomor_polisi }} akan dihapus permanen.'
                                        )"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-rose-200 text-rose-600 transition hover:bg-rose-50">
                                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50">
                    <i data-lucide="truck" class="h-7 w-7 text-slate-300"></i>
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-600">Belum ada data checklist</p>
                <p class="mt-1 text-xs text-slate-400">
                    @if (!auth()->user()->isSpbu())
                        Mulai pemeriksaan dengan tombol "Tambah Data Checklist".
                    @else
                        Hasil pemeriksaan mobil tangki akan muncul di sini.
                    @endif
                </p>
            </div>
        @endforelse
    </div>
</div>

<script>
function getSelectedIds() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const ids = Array.from(checked).map(cb => cb.value);
    return Array.from(new Set(ids));
}

function handleCheckboxChange(sourceCb) {
    const val = sourceCb.value;
    const isChecked = sourceCb.checked;
    
    // Sinkronkan checkbox versi desktop dan mobile untuk ID yang sama
    document.querySelectorAll('.row-checkbox[value="' + val + '"]').forEach(cb => {
        cb.checked = isChecked;
    });

    updateSelection();
}

function updateSelection() {
    const selected = getSelectedIds();
    const count = selected.length;
    const checkAll = document.getElementById('check-all');
    const selectionBar = document.getElementById('selection-bar');
    const countLabel = document.getElementById('selected-count');
    const exportBtnLabel = document.getElementById('export-btn-label');
    const exportPdfBtnLabel = document.getElementById('export-pdf-btn-label');

    // Total unique items
    const allUniqueIds = Array.from(new Set(Array.from(document.querySelectorAll('.row-checkbox')).map(cb => cb.value)));

    // Highlight row dan card terpilih
    allUniqueIds.forEach(id => {
        const isSel = selected.includes(id);
        const tr = document.getElementById('row-' + id);
        const card = document.getElementById('card-' + id);
        
        if (tr) {
            if (isSel) tr.classList.add('bg-blue-50/60');
            else tr.classList.remove('bg-blue-50/60');
        }
        if (card) {
            if (isSel) {
                card.classList.add('bg-blue-50/50', 'ring-2', 'ring-brand-blue/30');
            } else {
                card.classList.remove('bg-blue-50/50', 'ring-2', 'ring-brand-blue/30');
            }
        }
    });

    if (checkAll) {
        checkAll.checked = (allUniqueIds.length > 0 && count === allUniqueIds.length);
        checkAll.indeterminate = (count > 0 && count < allUniqueIds.length);
    }

    if (count > 0) {
        if (selectionBar) {
            selectionBar.classList.remove('hidden');
            selectionBar.classList.add('flex');
        }
        if (countLabel) countLabel.textContent = count;
        if (exportBtnLabel) exportBtnLabel.textContent = 'Export Excel (' + count + ')';
        if (exportPdfBtnLabel) exportPdfBtnLabel.textContent = 'Export PDF (' + count + ')';
    } else {
        if (selectionBar) {
            selectionBar.classList.add('hidden');
            selectionBar.classList.remove('flex');
        }
        if (exportBtnLabel) exportBtnLabel.textContent = 'Export Excel';
        if (exportPdfBtnLabel) exportPdfBtnLabel.textContent = 'Export PDF';
    }

    if (window.lucide) lucide.createIcons();
}

document.getElementById('check-all')?.addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateSelection();
});

function uncheckAll() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = false;
    });
    if (document.getElementById('check-all')) {
        document.getElementById('check-all').checked = false;
    }
    updateSelection();
}

function exportSelected() {
    const selected = getSelectedIds();
    if (!selected.length) {
        alert('Silakan centang minimal 1 checklist untuk diekspor.');
        return;
    }
    runExport(selected);
}

function triggerExport() {
    const selected = getSelectedIds();
    runExport(selected);
}

function exportSelectedPdf() {
    const selected = getSelectedIds();
    if (!selected.length) {
        alert('Silakan centang minimal 1 checklist untuk diekspor ke PDF.');
        return;
    }
    runPdfExport(selected);
}

function triggerPdfExport() {
    const selected = getSelectedIds();
    runPdfExport(selected);
}

function runPdfExport(ids = []) {
    let url = "{{ route('checklist-mt-maos.cetak-banyak') }}";
    if (ids && ids.length > 0) {
        url += '?ids=' + ids.join(',');
    }
    window.open(url, '_blank');
}

async function ensureExcelJs() {
    if (window.ExcelJS) return true;
    return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js';
        s.onload = () => resolve(true);
        s.onerror = () => reject(new Error('Gagal memuat pustaka Excel'));
        document.head.appendChild(s);
    });
}

async function runExport(ids = []) {
    const btn = document.getElementById('export-all-btn');
    const labelAwal = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> Menyiapkan rekap...';
    }

    try {
        await ensureExcelJs();

        let url = "{{ route('checklist-mt-maos.export-all') }}";
        if (ids && ids.length > 0) {
            url += '?ids=' + ids.join(',');
        }

        const res = await fetch(url);
        const data = await res.json();
        if (!data.armada || !data.armada.length) {
            alert('Tidak ada data checklist untuk direkap.');
            return;
        }

        const BIRU = 'FF2F6FA6';
        const MERAH = 'FFE04B3E';
        const KUNING = 'FFF2D02B';
        const HIJAU = 'FF3CB878';
        const BIRU_MUDA = 'FF0284C7';
        const PUTIH = 'FFFFFFFF';

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet('Rekap Pemeriksaan');
        ws.columns = [
            { width: 5 }, { width: 16 }, { width: 26 }, { width: 18 },
            { width: 46 }, { width: 16 }, { width: 15 }, { width: 38 }, { width: 14 },
        ];

        // ---- Judul ----
        ws.mergeCells('A1:I1');
        ws.getCell('A1').value = data.judul;
        ws.getCell('A1').font = { bold: true, size: 14, color: { argb: 'FF0F172A' } };
        ws.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };
        ws.getRow(1).height = 24;

        ws.mergeCells('A2:I2');
        ws.getCell('A2').value = data.subjudul;
        ws.getCell('A2').font = { bold: true, size: 11, color: { argb: 'FF475569' } };
        ws.getCell('A2').alignment = { horizontal: 'center', vertical: 'middle' };
        ws.getRow(2).height = 18;

        // ---- Header tabel ----
        const headerRow = ws.getRow(4);
        const headerLabel = ['No.', 'No.Pol', 'Pemilik', 'Tgl. Pemeriksaan', 'Temuan Pemeriksaan', 'Status', 'Tindak Lanjut', 'Catatan & Keterangan', 'Tanggal TL'];
        headerLabel.forEach((label, i) => {
            const cell = headerRow.getCell(i + 1);
            cell.value = label;
            cell.font = { bold: true, color: { argb: PUTIH }, size: 10 };
            cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
        });
        headerRow.height = 24;

        const thinBorder = { style: 'thin', color: { argb: 'FFBFBFBF' } };
        const applyBorder = (cell) => { cell.border = { top: thinBorder, left: thinBorder, bottom: thinBorder, right: thinBorder }; };

        let r = 5;

        data.armada.forEach((a, idx) => {
            const rowStart = r;
            const temuanList = a.temuan.length ? a.temuan : [{ label: 'Tidak ada temuan (Semua komponen sesuai standar)', kategori: null, disp: '-', catatan: '-' }];

            temuanList.forEach((t) => {
                const row = ws.getRow(r);
                row.getCell(5).value = (t.kategori ? '- ' : '') + t.label;
                row.getCell(5).alignment = { vertical: 'middle', wrapText: true };
                row.getCell(5).font = { size: 9.5 };

                const statusCell = row.getCell(6);
                if (t.kategori === 'Mandatory') {
                    statusCell.value = 'Mandatory';
                    statusCell.font = { bold: true, color: { argb: PUTIH }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: MERAH } };
                } else if (t.kategori === 'Non Mandatory') {
                    statusCell.value = 'Non Mandatory';
                    statusCell.font = { bold: true, color: { argb: 'FF5A4500' }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: KUNING } };
                } else if (t.kategori === 'Catatan') {
                    statusCell.value = 'Catatan';
                    statusCell.font = { bold: true, color: { argb: PUTIH }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU_MUDA } };
                } else {
                    statusCell.value = 'Sesuai Standar';
                    statusCell.font = { bold: true, color: { argb: 'FF166534' }, size: 9 };
                    statusCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD1FAE5' } };
                }
                statusCell.alignment = { horizontal: 'center', vertical: 'middle' };

                row.getCell(7).value = t.disp || '-';
                row.getCell(7).alignment = { horizontal: 'center', vertical: 'middle' };
                row.getCell(7).font = { size: 9.5 };

                row.getCell(8).value = t.catatan || '-';
                row.getCell(8).alignment = { vertical: 'middle', wrapText: true };
                row.getCell(8).font = { size: 9.5 };

                row.getCell(9).value = '';
                [5, 6, 7, 8, 9].forEach((c) => applyBorder(row.getCell(c)));
                r++;
            });

            // ---- Baris banner "Pemeriksaan Kekedapan Mobil Tangki" ----
            ws.mergeCells(`E${r}:I${r}`);
            const bannerCell = ws.getCell(`E${r}`);
            bannerCell.value = 'Pemeriksaan Kekedapan Mobil Tangki';
            bannerCell.font = { bold: true, color: { argb: PUTIH }, size: 9.5 };
            bannerCell.alignment = { horizontal: 'left', vertical: 'middle' };
            bannerCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: BIRU } };
            applyBorder(bannerCell);
            r++;

            // ---- Baris hasil kekedapan ----
            const kedapRow = ws.getRow(r);
            kedapRow.getCell(5).value = a.kedap ? '- Tidak terdapat kebocoran' : '- Terdapat kebocoran, perlu tindak lanjut';
            kedapRow.getCell(5).alignment = { vertical: 'middle', wrapText: true };
            kedapRow.getCell(5).font = { size: 9.5 };

            const kedapStatus = kedapRow.getCell(6);
            kedapStatus.value = a.kedap ? 'Kedap' : 'Bocor';
            kedapStatus.font = { bold: true, color: { argb: PUTIH }, size: 9 };
            kedapStatus.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: a.kedap ? HIJAU : MERAH } };
            kedapStatus.alignment = { horizontal: 'center', vertical: 'middle' };

            kedapRow.getCell(7).value = '-';
            kedapRow.getCell(7).alignment = { horizontal: 'center', vertical: 'middle' };

            kedapRow.getCell(8).value = '-';
            kedapRow.getCell(8).alignment = { horizontal: 'center', vertical: 'middle' };

            kedapRow.getCell(9).value = '';
            [5, 6, 7, 8, 9].forEach((c) => applyBorder(kedapRow.getCell(c)));
            r++;

            const rowEnd = r - 1;

            // ---- Kolom No / No.Pol / Pemilik / Tanggal digabung utk 1 blok armada ----
            ws.mergeCells(`A${rowStart}:A${rowEnd}`);
            ws.mergeCells(`B${rowStart}:B${rowEnd}`);
            ws.mergeCells(`C${rowStart}:C${rowEnd}`);
            ws.mergeCells(`D${rowStart}:D${rowEnd}`);

            const cNo = ws.getCell(`A${rowStart}`);
            cNo.value = idx + 1;
            const cPol = ws.getCell(`B${rowStart}`);
            cPol.value = a.nomor_polisi;
            cPol.font = { bold: true, size: 10 };
            const cPemilik = ws.getCell(`C${rowStart}`);
            cPemilik.value = a.pemilik;
            cPemilik.font = { size: 9.5 };
            const cTgl = ws.getCell(`D${rowStart}`);
            cTgl.value = a.tanggal;
            cTgl.font = { size: 9.5 };

            [cNo, cPol, cPemilik, cTgl].forEach((cell) => {
                cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                applyBorder(cell);
            });

            // Perapian border sel kosong hasil merge (baris demi baris supaya border tetap utuh saat dicetak)
            for (let rr = rowStart; rr <= rowEnd; rr++) {
                [1, 2, 3, 4].forEach((c) => applyBorder(ws.getRow(rr).getCell(c)));
            }
        });

        const buffer = await wb.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const blobUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = blobUrl;
        const filename = (ids && ids.length > 0) ? (data.filename + '_Pilihan_' + ids.length + '_Armada') : data.filename;
        link.download = filename + '.xlsx';
        document.body.appendChild(link);
        link.click();
        setTimeout(() => {
            document.body.removeChild(link);
            URL.revokeObjectURL(blobUrl);
        }, 1000);
    } catch (e) {
        console.error(e);
        alert('Gagal membuat rekap Excel. Coba lagi ya.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = labelAwal;
        }
    }
}
</script>
@endsection
