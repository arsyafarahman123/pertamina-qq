@extends('layouts.app')
@section('title', ($isEdit ? 'Edit Checklist' : 'Checklist Baru') . ' — Mobil Tangki')

@section('content')
<div x-data="checklistForm()">

    <form method="POST" action="{{ $isEdit ? route('checklist-mt-maos.update', $checklist) : route('checklist-mt-maos.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
            <div class="bg-gradient-to-r from-brand-dark to-brand-blueDark px-6 py-5 text-center">
                <p class="text-lg font-extrabold uppercase tracking-wide text-white">Form Pemeriksaan Mobil Tangki</p>
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-gold">Fuel Terminal Maos</p>
            </div>
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
                        <input name="tera[{{ $i }}][tinggiTera]" value="{{ $t['tinggiTera'] ?: ($t['a'] ?? '') }}" placeholder="cth. 1200 mm"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">b. Tinggi T2 Act</label>
                        <input name="tera[{{ $i }}][tinggiAct]" value="{{ $t['tinggiAct'] ?: ($t['b'] ?? '') }}" placeholder="cth. 1201 mm"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">c. Selisih T2</label>
                        <input name="tera[{{ $i }}][selisih]" value="{{ $t['selisih'] ?: ($t['c'] ?? '') }}" placeholder="cth. +1 mm"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">d. Dudukan Tangki</label>
                        <input name="tera[{{ $i }}][duduk]" value="{{ $t['duduk'] ?: ($t['d'] ?? '') }}" placeholder="cth. Baik / Sesuai"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">e. Volume Tangki</label>
                        <input name="tera[{{ $i }}][volume]" value="{{ $t['volume'] ?: ($t['e'] ?? '') }}" placeholder="cth. 8.000 L"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-extrabold uppercase text-slate-500">f. Ijk Baut &amp; Segel</label>
                        <input name="tera[{{ $i }}][ijkBaut]" value="{{ $t['ijkBaut'] ?: ($t['f'] ?? '') }}" placeholder="cth. Tersegel Baik"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-800 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/10">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- ===== Item 3-17 ===== -->
        <p class="mb-2 mt-6 px-1 text-xs font-extrabold uppercase tracking-wider text-brand-blueDark">3–17 · Kondisi Fisik &amp; Perlengkapan</p>

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
            <div class="mx-auto flex max-w-7xl justify-end gap-2.5 px-0 lg:px-4">
                <a href="{{ $isEdit ? route('checklist-mt-maos.show', $checklist) : route('checklist-mt-maos.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-red px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-red/25 transition hover:-translate-y-0.5 hover:bg-brand-redDark">
                    <i data-lucide="save" class="h-4 w-4"></i> Simpan Checklist
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function checklistForm() { return {}; }

// Toggle Sesuai/Temuan: tiap pasangan tombol berbagi satu hidden input results[key]
document.querySelectorAll('[data-toggle-group]').forEach(group => {
    const hidden = group.querySelector('input[type=hidden]');
    group.querySelectorAll('.tbtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.dataset.val;
            const isSame = hidden.value === val;
            hidden.value = isSame ? '' : val;
            group.querySelectorAll('.tbtn').forEach(b => {
                b.classList.remove('bg-emerald-500', 'border-emerald-500', 'text-white', 'bg-brand-red', 'border-brand-red');
                b.classList.add('text-slate-400', 'border-slate-200');
            });
            if (!isSame) {
                btn.classList.remove('text-slate-400', 'border-slate-200');
                btn.classList.add(val === 'ok' ? 'bg-emerald-500' : 'bg-brand-red', val === 'ok' ? 'border-emerald-500' : 'border-brand-red', 'text-white');
            }
        });
    });
});
lucide.createIcons();
</script>
@endsection
