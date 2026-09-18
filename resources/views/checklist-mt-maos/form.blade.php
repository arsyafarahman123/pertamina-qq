@extends('layouts.app')
@section('title', ($isEdit ? 'Edit Checklist' : 'Checklist Baru') . ' — Mobil Tangki')

@section('content')
<div x-data="checklistForm()">

    <!-- Banner Pemulihan Draft (Auto-Save Recovery) -->
    <div id="draft-notice" class="mb-5 hidden items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50/90 px-5 py-3.5 text-amber-900 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm">
                <i data-lucide="history" class="h-5 w-5"></i>
            </div>
            <div>
                <p class="text-sm font-bold">Draft Isian Dipulihkan Otomatis</p>
                <p class="text-xs text-amber-700" id="draft-time-label">Data yang sebelumnya Anda ketik telah dipulihkan agar tidak hilang saat halaman di-refresh.</p>
            </div>
        </div>
        <button type="button" onclick="resetDraftForm()" class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 bg-white px-3 py-1.5 text-xs font-bold text-amber-800 shadow-sm transition hover:bg-amber-100">
            <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i> Reset Form
        </button>
    </div>

    <form id="checklist-form" method="POST" action="{{ $isEdit ? route('checklist-mt-maos.update', $checklist) : route('checklist-mt-maos.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
            <div class="bg-gradient-to-r from-brand-dark to-brand-blueDark px-6 py-5 text-center">
                <p class="text-lg font-extrabold uppercase tracking-wide text-white">Form Pemeriksaan Mobil Tangki</p>
                <div class="mt-1 flex items-center justify-center gap-2">
                    <p class="text-xs font-semibold uppercase tracking-widest text-brand-gold">Fuel Terminal Maos</p>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-[10px] font-bold text-emerald-300 backdrop-blur" id="auto-save-status">
                        <i data-lucide="cloud-check" class="h-3 w-3"></i> Auto-Save Aktif
                    </span>
                </div>
            </div>
        </div>

        @if($isEdit && ($checklist->isFlagged() || $checklist->hasPerbaikan()))
            <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-teal-200 bg-teal-50/80 p-4 shadow-card">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-teal-600 text-white shadow-sm">
                        <i data-lucide="wrench" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-teal-900">Pembaruan Riwayat Perbaikan (Langsung Hijau)</p>
                        <p class="text-xs text-teal-700">Tandai temuan yang sudah diperbaiki agar status di riwayat langsung berubah menjadi <b>HIJAU (Selesai Perbaikan)</b>.</p>
                    </div>
                </div>
                <button type="button" onclick="tandaiSemuaDiperbaiki()" class="inline-flex items-center gap-1.5 rounded-xl bg-teal-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-teal-700 transition shrink-0">
                    <i data-lucide="check-check" class="h-4 w-4"></i> Tandai Semua Selesai Diperbaiki
                </button>
            </div>
        @endif

        <!-- Banner Peringatan Kelengkapan Isian Real-Time -->
        <div id="incomplete-banner" class="mb-5 flex items-center justify-between gap-3 rounded-2xl border border-amber-300 bg-amber-50/95 px-5 py-3.5 text-amber-900 shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm">
                    <i data-lucide="alert-triangle" class="h-5 w-5"></i>
                </div>
                <div>
                    <p class="text-sm font-extrabold" id="incomplete-banner-title">Peringatan: Form Belum Selesai Diisi (<span id="incomplete-count-text">23</span> Item Belum Dipilih)</p>
                    <p class="text-xs text-amber-700" id="incomplete-banner-desc">Harap tentukan status setiap item (Sesuai, Temuan, atau Diperbaiki) sebelum menyimpan checklist.</p>
                </div>
            </div>
            <button type="button" onclick="lengkapiItemPertama()" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 px-3.5 py-2 text-xs font-black text-white shadow-sm transition">
                <i data-lucide="arrow-down" class="h-4 w-4"></i> Lengkapi
            </button>
        </div>

        @if ($errors->any())
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-card">
                <i data-lucide="circle-alert" class="mt-0.5 h-5 w-5 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- ===== Identitas kendaraan ===== -->
        <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
            <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50/70 px-5 py-3.5">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-red text-white"><i data-lucide="truck" class="h-4 w-4"></i></div>
                <p class="text-sm font-bold text-slate-700">Identitas Kendaraan</p>
            </div>
            <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Nomor Polisi</label>
                    <input type="text" name="nomor_polisi" value="{{ old('nomor_polisi', $checklist->nomor_polisi) }}" placeholder="AB 0000 XX" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Pemilik / SPBU</label>
                    <input type="text" name="pemilik" value="{{ old('pemilik', $checklist->pemilik) }}" placeholder="PT ..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Exp. Sertifikat Tera</label>
                    <input type="text" name="tanggal_exp" value="{{ old('tanggal_exp', $checklist->tanggal_exp) }}" placeholder="cth. Januari 2026"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
                <div class="sm:col-span-1">
                    <label class="mb-1.5 block text-xs font-semibold text-slate-500">Tanggal Pemeriksaan</label>
                    <input type="date" name="tanggal_periksa" value="{{ old('tanggal_periksa', optional($checklist->tanggal_periksa)->format('Y-m-d') ?? $checklist->tanggal_periksa) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
            </div>
        </div>

        <!-- ===== Masa Sertifikat Tera & Pengukuran Kompartemen ===== -->
        <p class="mb-2 mt-6 px-1 text-xs font-extrabold uppercase tracking-wider text-brand-blueDark">1–2 · Pengukuran Kompartemen Tangki &amp; Masa Tera</p>
        <div class="mb-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
            {{-- Datalists untuk Dropdown / Pilihan Cepat (Bisa Pilih atau Ketik Manual) --}}
            <datalist id="dudukan-list">
                <option value="Baik / Sesuai"></option>
                <option value="Baik"></option>
                <option value="Sesuai"></option>
                <option value="Kurang Baik"></option>
                <option value="Perlu Perbaikan"></option>
                <option value="Tidak Sesuai"></option>
            </datalist>

            <datalist id="volume-list">
                <option value="4.000 L"></option>
                <option value="5.000 L"></option>
                <option value="8.000 L"></option>
                <option value="16.000 L"></option>
                <option value="24.000 L"></option>
                <option value="32.000 L"></option>
                <option value="4000"></option>
                <option value="5000"></option>
                <option value="8000"></option>
            </datalist>

            <datalist id="ijk-list">
                <option value="Tersegel Baik"></option>
                <option value="Sesuai"></option>
                <option value="Segel Utuh"></option>
                <option value="Perlu Segel Ulang"></option>
            </datalist>

            @foreach($checklist->tera as $i => $t)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card transition hover:border-slate-300">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="text-xs font-black uppercase tracking-wide text-brand-red flex items-center gap-1.5">
                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-brand-red/10 text-[10px] text-brand-red font-bold">{{ $t['komp'] }}</span>
                        Kompartemen {{ $t['komp'] }}
                    </h4>
                    <span class="text-[10px] font-semibold text-slate-400">Titik Ukur &amp; Kondisi MT</span>
                </div>
                <input type="hidden" name="tera[{{ $i }}][komp]" value="{{ $t['komp'] }}">
                
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">a. Tinggi T2 Tera</label>
                        <input id="tera-{{ $i }}-tinggiTera" oninput="hitungSelisihTera({{ $i }})" name="tera[{{ $i }}][tinggiTera]" value="{{ $t['tinggiTera'] ?: ($t['a'] ?? '') }}" placeholder="cth. 1200"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">b. Tinggi T2 Act</label>
                        <input id="tera-{{ $i }}-tinggiAct" oninput="hitungSelisihTera({{ $i }})" name="tera[{{ $i }}][tinggiAct]" value="{{ $t['tinggiAct'] ?: ($t['b'] ?? '') }}" placeholder="cth. 1201"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-brand-blue flex items-center justify-between">
                            <span>c. Selisih T2</span>
                            <span class="text-[9px] font-normal text-slate-400">Auto / Manual</span>
                        </label>
                        <input id="tera-{{ $i }}-selisih" name="tera[{{ $i }}][selisih]" value="{{ $t['selisih'] ?: ($t['c'] ?? '') }}" placeholder="cth. +1 mm"
                               class="w-full rounded-xl border border-blue-200 bg-blue-50/40 px-3 py-2 text-xs font-bold text-brand-blue transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">d. Dudukan Tangki</label>
                        <input list="dudukan-list" name="tera[{{ $i }}][duduk]" value="{{ $t['duduk'] ?: ($t['d'] ?? '') }}" placeholder="Pilih / ketik manual"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">e. Volume Tangki</label>
                        <input list="volume-list" name="tera[{{ $i }}][volume]" value="{{ $t['volume'] ?: ($t['e'] ?? '') }}" placeholder="cth. 8.000 L / ketik"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">f. Ijk Baut &amp; Segel</label>
                        <input list="ijk-list" name="tera[{{ $i }}][ijkBaut]" value="{{ $t['ijkBaut'] ?: ($t['f'] ?? '') }}" placeholder="Pilih / ketik manual"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- ===== Item 3-17 ===== -->
        <div class="mb-3 mt-6 flex flex-wrap items-center justify-between gap-2 px-1">
            <p class="text-xs font-extrabold uppercase tracking-wider text-brand-blueDark">3–17 · Kondisi Fisik &amp; Perlengkapan</p>
            <div class="flex items-center gap-2">
                <button type="button" onclick="tandaiSemuaSesuai()" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 px-3.5 py-1.5 text-xs font-extrabold text-emerald-800 shadow-xs transition">
                    <i data-lucide="check-check" class="h-4 w-4 text-emerald-600"></i> Tandai Semua Sesuai
                </button>
            </div>
        </div>

        @foreach($items as $sec)
            @if(!empty($sec['group']))
                <div class="mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
                    <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50/70 px-5 py-3.5">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-red text-[11px] font-bold text-white">{{ $sec['no'] }}</span>
                        <p class="text-sm font-bold text-slate-700">{{ $sec['item'] }}</p>
                    </div>
                    <div class="divide-y divide-slate-100 px-5">
                        @foreach($sec['sub'] as $i => $s)
                            @php $key = $sec['no'].'-'.$i; @endphp
                            @include('checklist-mt-maos.partials.item-row', [
                                'idxLabel' => $sec['no'].'.'.($i+1),
                                'label' => $s['label'], 'temuan' => $s['temuan'], 'disp' => $s['disp'], 'ket' => $s['ket'],
                                'key' => $key, 'checklist' => $checklist,
                            ])
                        @endforeach
                    </div>
                </div>
            @else
                @php $key = $sec['no']; @endphp
                <div class="mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white px-5 shadow-card">
                    @include('checklist-mt-maos.partials.item-row', [
                        'idxLabel' => $sec['no'],
                        'label' => $sec['item'], 'temuan' => $sec['temuan'], 'disp' => $sec['disp'], 'ket' => $sec['ket'],
                        'key' => $key, 'checklist' => $checklist,
                    ])
                </div>
            @endif
        @endforeach

        <!-- ===== Keterangan tambahan ===== -->
        <div class="mb-24 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
            <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50/70 px-5 py-3.5">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-blue text-white"><i data-lucide="notebook-pen" class="h-4 w-4"></i></div>
                <p class="text-sm font-bold text-slate-700">Keterangan Tambahan</p>
            </div>
            <div class="p-5">
                <textarea name="ket_tambahan" rows="3" placeholder="Catatan umum pemeriksa..."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">{{ old('ket_tambahan', $checklist->ket_tambahan) }}</textarea>
            </div>
        </div>

        <!-- ===== Aksi sticky ===== -->
        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-6px_16px_rgba(15,23,42,0.06)] backdrop-blur lg:pl-[19rem]">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-2.5 px-0 lg:px-4">
                <div class="flex items-center gap-3">
                    <span id="checklist-progress-badge" class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-3 py-1 text-xs font-black text-amber-800 transition-all">
                        <i data-lucide="list-checks" class="h-3.5 w-3.5"></i>
                        <span id="checklist-progress-text">0 / 23 Item Terisi</span>
                    </span>
                    <div class="hidden md:flex items-center gap-2 text-xs text-slate-500">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Auto-Save Aktif</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="{{ $isEdit ? route('checklist-mt-maos.show', $checklist) : route('checklist-mt-maos.index') }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" id="btn-submit-checklist"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-brand-red px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:-translate-y-0.5 hover:bg-brand-redDark active:scale-95">
                        <i data-lucide="save" class="h-4 w-4"></i> Simpan Checklist
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- MODAL NOTIFIKASI ITEM BELUM SELESAI DIISI --}}
<div id="modal-unfilled" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl space-y-4">
        <div class="flex items-start gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                <i data-lucide="alert-triangle" class="h-6 w-6"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-800">Pemeriksaan Belum Selesai Diisi!</h3>
                <p class="text-xs text-slate-500 mt-0.5">Ada <span id="unfilled-count-badge" class="font-bold text-amber-700">0</span> item pemeriksaan fisik yang belum Anda tentukan statusnya (Sesuai / Temuan / Diperbaiki).</p>
            </div>
        </div>

        <div class="rounded-2xl bg-amber-50/90 border border-amber-200 p-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-900 mb-2">
                Daftar Item yang Belum Dipilih:
            </p>
            <div id="unfilled-list-container" class="max-h-48 overflow-y-auto space-y-1.5 text-xs text-slate-800 pr-1">
                <!-- Diisi via JavaScript -->
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-2 pt-2 border-t border-slate-100">
            <button type="button" onclick="tutupModalUnfilled()" class="w-full sm:w-auto rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100">
                Tutup
            </button>
            <button type="button" onclick="lengkapiItemPertama()" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-brand-blueDark">
                <i data-lucide="arrow-down" class="h-4 w-4"></i> Lengkapi Sekarang
            </button>
            <button type="button" onclick="submitPaksa()" class="w-full sm:w-auto rounded-xl border border-amber-300 bg-amber-100 hover:bg-amber-200 px-4 py-2.5 text-xs font-bold text-amber-900">
                Tetap Simpan
            </button>
        </div>
    </div>
</div>

<style>
/* Tombol Status Checklist MT */
.tbtn {
    cursor: pointer;
    user-select: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Sesuai (HIJAU SOLID MENYALA) */
.tbtn.tbtn-ok-active,
.tbtn-ok-active {
    background-color: #10b981 !important;
    border-color: #059669 !important;
    color: #ffffff !important;
    font-weight: 900 !important;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4) !important;
    transform: translateY(-1px);
}
.tbtn.tbtn-ok-active svg,
.tbtn-ok-active svg,
.tbtn.tbtn-ok-active i,
.tbtn-ok-active i {
    stroke: #ffffff !important;
    color: #ffffff !important;
}

/* Temuan (MERAH SOLID MENYALA) */
.tbtn.tbtn-bad-active,
.tbtn-bad-active {
    background-color: #ef4444 !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
    font-weight: 900 !important;
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4) !important;
    transform: translateY(-1px);
}
.tbtn.tbtn-bad-active svg,
.tbtn-bad-active svg,
.tbtn.tbtn-bad-active i,
.tbtn-bad-active i {
    stroke: #ffffff !important;
    color: #ffffff !important;
}

/* Diperbaiki (TEAL SOLID MENYALA) */
.tbtn.tbtn-repaired-active,
.tbtn-repaired-active {
    background-color: #0d9488 !important;
    border-color: #0f766e !important;
    color: #ffffff !important;
    font-weight: 900 !important;
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4) !important;
    transform: translateY(-1px);
}
.tbtn.tbtn-repaired-active svg,
.tbtn-repaired-active svg,
.tbtn.tbtn-repaired-active i,
.tbtn-repaired-active i {
    stroke: #ffffff !important;
    color: #ffffff !important;
}
</style>

<script>
function checklistForm() { return {}; }

const isEditMode = @json($isEdit);
const checklistId = @json($checklist->id ?? 0);
const DRAFT_KEY = isEditMode ? ('draft_checklist_mt_maos_edit_' + checklistId) : 'draft_checklist_mt_maos_create';

// Toggle Sesuai/Temuan/Diperbaiki: mengupdate input hidden dan warna tombol
function applyToggleState(target, val) {
    if (!target) return;
    const row = target.classList && target.classList.contains('item-check-row') 
        ? target 
        : (target.closest ? target.closest('.item-check-row') : null);
    if (!row) return;

    const hidden = row.querySelector('.row-result-input') || row.querySelector('input[type=hidden]');
    if (hidden) {
        hidden.value = val || '';
    }

    const allButtons = row.querySelectorAll('.tbtn');
    allButtons.forEach(btn => {
        btn.classList.remove(
            'tbtn-ok-active', 'tbtn-bad-active', 'tbtn-repaired-active',
            'bg-emerald-500', 'border-emerald-500', 'bg-emerald-600', 'border-emerald-600',
            'bg-brand-red', 'border-brand-red', 'bg-red-500', 'border-red-500',
            'bg-teal-600', 'border-teal-600', 'text-white'
        );
        
        const bVal = btn.dataset.val;
        if (bVal === 'repaired') {
            btn.classList.add('border-teal-200', 'text-teal-700', 'bg-teal-50');
            btn.classList.remove('border-slate-200', 'text-slate-600', 'bg-white');
        } else {
            btn.classList.add('border-slate-200', 'text-slate-600', 'bg-white');
            btn.classList.remove('border-teal-200', 'text-teal-700', 'bg-teal-50');
        }
    });

    if (val === 'ok') {
        row.querySelectorAll('.tbtn[data-val="ok"]').forEach(btn => {
            btn.classList.remove('border-slate-200', 'text-slate-600', 'bg-white');
            btn.classList.add('tbtn-ok-active');
        });
    } else if (val === 'bad') {
        row.querySelectorAll('.tbtn[data-val="bad"]').forEach(btn => {
            btn.classList.remove('border-slate-200', 'text-slate-600', 'bg-white');
            btn.classList.add('tbtn-bad-active');
        });
    } else if (val === 'repaired') {
        row.querySelectorAll('.tbtn[data-val="repaired"]').forEach(btn => {
            btn.classList.remove('border-teal-200', 'text-teal-700', 'bg-teal-50');
            btn.classList.add('tbtn-repaired-active');
        });
    }
}

// Pintasan isi catatan perbaikan otomatis
function isiCatatanPerbaikan(noteId, text, btn) {
    const textarea = document.getElementById(noteId);
    if (!textarea) return;
    const curVal = textarea.value.trim();
    if (curVal === '') {
        textarea.value = text;
    } else if (!curVal.includes(text)) {
        textarea.value = curVal + ' | ' + text;
    }
    
    // Otomatis ubah tombol status baris ini menjadi "Diperbaiki" (repaired) jika sebelumnya Temuan/kosong
    const row = textarea.closest('.item-check-row') || textarea.closest('.flex-col');
    if (row) {
        applyToggleState(row, 'repaired');
    }
    saveDraft();
    updateProgressChecklist();
}

// Tindakan cepat: tandai semua item sebagai Sesuai
function tandaiSemuaSesuai() {
    document.querySelectorAll('.item-check-row').forEach(row => {
        applyToggleState(row, 'ok');
    });
    updateProgressChecklist();
    saveDraft();
}

// Tindakan cepat: tandai semua temuan sebagai selesai diperbaiki
function tandaiSemuaDiperbaiki() {
    document.querySelectorAll('.item-check-row').forEach(row => {
        const hidden = row.querySelector('.row-result-input') || row.querySelector('input[type=hidden]');
        if (hidden && (hidden.value === 'bad' || hidden.value === '')) {
            applyToggleState(row, 'repaired');
            const txt = row.querySelector('textarea');
            if (txt && txt.value.trim() === '') {
                txt.value = 'Telah diperbaiki & normal saat pemeriksaan ulang.';
            }
        }
    });
    updateProgressChecklist();
    saveDraft();
    alert('Seluruh item temuan telah diubah menjadi status "Diperbaiki". Silakan klik "Simpan Checklist" untuk memperbarui status menjadi HIJAU di riwayat.');
}

// Hitung Otomatis Selisih T2 Kompartemen (bisa juga diedit manual)
function hitungSelisihTera(i) {
    const teraEl = document.getElementById(`tera-${i}-tinggiTera`);
    const actEl = document.getElementById(`tera-${i}-tinggiAct`);
    const selisihEl = document.getElementById(`tera-${i}-selisih`);
    if (!teraEl || !actEl || !selisihEl) return;

    const rawTera = teraEl.value.replace(/[^0-9.-]/g, '');
    const rawAct = actEl.value.replace(/[^0-9.-]/g, '');

    if (rawTera !== '' && rawAct !== '') {
        const vTera = parseFloat(rawTera);
        const vAct = parseFloat(rawAct);
        if (!isNaN(vTera) && !isNaN(vAct)) {
            const diff = vAct - vTera;
            const sign = diff > 0 ? '+' : '';
            selisihEl.value = `${sign}${diff} mm`;
            saveDraft();
        }
    }
}

let forceSubmitAllowed = false;

// Update Live Progress Checklist
function updateProgressChecklist() {
    const rows = document.querySelectorAll('.item-check-row');
    const total = rows.length || 23;
    let filled = 0;

    rows.forEach(r => {
        const hidden = r.querySelector('.row-result-input') || r.querySelector('input[type=hidden]');
        if (hidden && (hidden.value === 'ok' || hidden.value === 'bad' || hidden.value === 'repaired')) {
            filled++;
            r.classList.remove('ring-2', 'ring-amber-400', 'bg-amber-50/50', 'rounded-2xl', 'p-2');
        }
    });

    const unfilledCount = total - filled;

    const badgeEl = document.getElementById('checklist-progress-badge');
    const textEl = document.getElementById('checklist-progress-text');
    if (textEl && badgeEl) {
        textEl.innerText = `${filled} / ${total} Item Terisi`;
        if (filled === total) {
            badgeEl.className = 'inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-300 px-3 py-1 text-xs font-black text-emerald-800 transition-all';
            textEl.innerHTML = `✓ ${filled} / ${total} Lengkap`;
        } else {
            badgeEl.className = 'inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-300 px-3 py-1 text-xs font-black text-amber-800 transition-all animate-pulse';
        }
    }

    // Update real-time banner notifikasi di atas form
    const banner = document.getElementById('incomplete-banner');
    const countText = document.getElementById('incomplete-count-text');
    if (banner && countText) {
        if (filled === total) {
            banner.className = 'mb-5 hidden items-center justify-between gap-3 rounded-2xl border border-emerald-300 bg-emerald-50/95 px-5 py-3.5 text-emerald-900 shadow-sm transition-all';
        } else {
            banner.className = 'mb-5 flex items-center justify-between gap-3 rounded-2xl border border-amber-300 bg-amber-50/95 px-5 py-3.5 text-amber-900 shadow-sm transition-all';
            countText.textContent = unfilledCount;
        }
    }
}

// Cari seluruh item yang belum dipilih
function getUnfilledItems() {
    const unfilled = [];
    document.querySelectorAll('.item-check-row').forEach(row => {
        const hidden = row.querySelector('.row-result-input') || row.querySelector('input[type=hidden]');
        if (!hidden || !hidden.value || (hidden.value !== 'ok' && hidden.value !== 'bad' && hidden.value !== 'repaired')) {
            const idx = row.dataset.itemIdx || '';
            const label = row.dataset.itemLabel || '';
            unfilled.push({ row, idx, label, id: row.id });
            row.classList.add('ring-2', 'ring-amber-400', 'bg-amber-50/50', 'rounded-2xl', 'p-2');
        } else {
            row.classList.remove('ring-2', 'ring-amber-400', 'bg-amber-50/50', 'rounded-2xl', 'p-2');
        }
    });
    return unfilled;
}

// Buka Modal Notifikasi Belum Lengkap
function bukaModalUnfilled(unfilled) {
    const modal = document.getElementById('modal-unfilled');
    const countBadge = document.getElementById('unfilled-count-badge');
    const container = document.getElementById('unfilled-list-container');
    if (!modal) return;

    if (countBadge) countBadge.innerText = unfilled.length;
    if (container) {
        container.innerHTML = unfilled.map(u => `
            <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-white border border-amber-200/80 shadow-xs">
                <div class="flex items-center gap-2 truncate">
                    <span class="rounded-md bg-amber-200/60 px-1.5 py-0.5 text-[10px] font-black text-amber-900">${u.idx}</span>
                    <span class="font-bold text-slate-800 truncate">${u.label}</span>
                </div>
                <button type="button" onclick="scrollToItem('${u.id}')"
                        class="shrink-0 inline-flex items-center gap-1 rounded-lg bg-brand-blue/10 hover:bg-brand-blue hover:text-white px-2.5 py-1 text-[11px] font-bold text-brand-blue transition">
                    Isi <i data-lucide="arrow-right" class="h-3 w-3"></i>
                </button>
            </div>
        `).join('');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (window.lucide) lucide.createIcons();
}

function tutupModalUnfilled() {
    const modal = document.getElementById('modal-unfilled');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function scrollToItem(rowId) {
    tutupModalUnfilled();
    const el = document.getElementById(rowId);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.classList.add('ring-4', 'ring-brand-blue', 'bg-blue-50/60', 'rounded-2xl', 'p-2');
        setTimeout(() => {
            el.classList.remove('ring-4', 'ring-brand-blue', 'bg-blue-50/60');
        }, 3000);
    }
}

function lengkapiItemPertama() {
    const unfilled = getUnfilledItems();
    if (unfilled.length > 0) {
        scrollToItem(unfilled[0].id);
    } else {
        tutupModalUnfilled();
    }
}

function submitPaksa() {
    forceSubmitAllowed = true;
    tutupModalUnfilled();
    const form = document.getElementById('checklist-form');
    if (form) form.submit();
}

// Auto-save form draft to localStorage with Debounce
let autoSaveTimeout = null;

function saveDraftNow() {
    try {
        const form = document.getElementById('checklist-form');
        if (!form) return;
        const formData = new FormData(form);
        const dataObj = {};
        for (let [key, val] of formData.entries()) {
            if (key === '_token' || key === '_method') continue;
            dataObj[key] = val;
        }
        dataObj._savedAt = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        localStorage.setItem(DRAFT_KEY, JSON.stringify(dataObj));
        
        const statusEl = document.getElementById('auto-save-status');
        if (statusEl) {
            statusEl.innerHTML = '<i data-lucide="check" class="h-3 w-3"></i> Tersimpan ' + dataObj._savedAt;
            if (window.lucide) lucide.createIcons();
        }
    } catch (e) {
        console.warn('Gagal menyimpan draft ke localStorage:', e);
    }
}

function saveDraft() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(saveDraftNow, 200);
}

// Restore form draft from localStorage
function restoreDraft() {
    try {
        const saved = localStorage.getItem(DRAFT_KEY);
        if (!saved) return;
        const data = JSON.parse(saved);
        if (!data || typeof data !== 'object') return;

        let hasRestoredValues = false;
        const form = document.getElementById('checklist-form');
        if (!form) return;

        // Cek dan isi setiap field dari draft
        for (let key in data) {
            if (key.startsWith('_')) continue;
            const val = data[key];
            if (val !== undefined && val !== null && val !== '') {
                let el = null;
                try {
                    el = form.querySelector(`[name="${CSS.escape(key)}"]`);
                } catch(e) {
                    el = form.elements[key];
                }
                if (el) {
                    hasRestoredValues = true;
                    el.value = val;
                    // Jika hidden input hasil row pemeriksaan, update tampilan tombol
                    const row = el.closest('.item-check-row');
                    if (row) {
                        applyToggleState(row, val);
                    }
                }
            }
        }

        if (hasRestoredValues) {
            const notice = document.getElementById('draft-notice');
            const timeLabel = document.getElementById('draft-time-label');
            if (notice) {
                notice.classList.remove('hidden');
                notice.classList.add('flex');
            }
            if (timeLabel && data._savedAt) {
                timeLabel.textContent = 'Data terakhir disimpan otomatis pada ' + data._savedAt + '. Anda dapat langsung melanjutkan pengisian tanpa takut hilang.';
            }
            if (window.lucide) lucide.createIcons();
        }
    } catch (e) {
        console.warn('Gagal memulihkan draft:', e);
    }
}

function resetDraftForm() {
    if (confirm('Apakah Anda yakin ingin mengosongkan draft ini dan mulai dari awal?')) {
        localStorage.removeItem(DRAFT_KEY);
        window.location.reload();
    }
}

// Event listeners for real-time auto save & submission validation
document.addEventListener('DOMContentLoaded', () => {
    // Pasang click handler ke seluruh baris pemeriksaan
    document.querySelectorAll('.item-check-row').forEach(row => {
        const hidden = row.querySelector('.row-result-input') || row.querySelector('input[type=hidden]');
        
        // Sync tampilan awal saat page load (dari data existing/database)
        if (hidden && hidden.value) {
            applyToggleState(row, hidden.value);
        }

        row.querySelectorAll('.tbtn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const val = btn.dataset.val;
                const curVal = hidden ? hidden.value : '';
                const isSame = (curVal === val);
                const newVal = isSame ? '' : val;
                applyToggleState(row, newVal);
                updateProgressChecklist();
                saveDraft();
            });
        });
    });

    restoreDraft();
    updateProgressChecklist();

    const form = document.getElementById('checklist-form');
    if (form) {
        form.addEventListener('input', () => {
            saveDraft();
            updateProgressChecklist();
        });
        form.addEventListener('change', () => {
            saveDraft();
            updateProgressChecklist();
        });

        form.addEventListener('submit', (e) => {
            if (!forceSubmitAllowed) {
                const unfilled = getUnfilledItems();
                if (unfilled.length > 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    bukaModalUnfilled(unfilled);
                    return false;
                }
            }

            // Bersihkan draft saat berhasil submit
            try {
                localStorage.removeItem(DRAFT_KEY);
            } catch (err) {}
        });

        const btnSubmit = document.getElementById('btn-submit-checklist');
        if (btnSubmit) {
            btnSubmit.addEventListener('click', (e) => {
                if (!forceSubmitAllowed) {
                    const unfilled = getUnfilledItems();
                    if (unfilled.length > 0) {
                        e.preventDefault();
                        e.stopPropagation();
                        bukaModalUnfilled(unfilled);
                        return false;
                    }
                }
            });
        }
    }

    window.addEventListener('beforeunload', () => {
        saveDraftNow();
    });

    if (window.lucide) lucide.createIcons();
});
</script>
@endsection
