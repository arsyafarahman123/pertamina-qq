@php
    $current = old("results.$key", $checklist->results[$key] ?? null);
    $note = old("notes.$key", $checklist->notes[$key] ?? '');
@endphp
<div id="item-row-{{ $key }}" data-item-key="{{ $key }}" data-item-label="{{ $label }}" data-item-idx="{{ $idxLabel }}" 
     class="item-check-row flex flex-col sm:grid sm:grid-cols-[28px_1fr_220px] lg:grid-cols-[28px_1fr_250px] items-start gap-2.5 sm:gap-3 py-4 border-b border-slate-100 last:border-b-0 transition-all">
    
    {{-- Single hidden input for this item row --}}
    <input type="hidden" name="results[{{ $key }}]" id="result-{{ str_replace(['.', '-'], '_', $key) }}" value="{{ $current }}" class="row-result-input">

    <div class="flex items-center justify-between sm:block w-full sm:w-auto">
        <div class="pt-0.5 text-xs font-extrabold text-slate-400">{{ $idxLabel }}</div>
        
        {{-- Tombol Mobile (3 Pilihan: Sesuai, Temuan, Diperbaiki) --}}
        <div class="flex sm:hidden justify-end gap-1.5">
            <button type="button" title="Sesuai" data-val="ok"
                    class="tbtn flex h-8 px-2.5 items-center justify-center gap-1 rounded-lg border-2 text-[11px] font-black transition {{ $current === 'ok' ? 'tbtn-ok-active' : 'border-slate-200 text-slate-600 bg-white hover:border-emerald-300' }}">
                <i data-lucide="check" class="h-3.5 w-3.5"></i> Sesuai
            </button>
            <button type="button" title="Temuan" data-val="bad"
                    class="tbtn flex h-8 px-2.5 items-center justify-center gap-1 rounded-lg border-2 text-[11px] font-black transition {{ $current === 'bad' ? 'tbtn-bad-active' : 'border-slate-200 text-slate-600 bg-white hover:border-red-300' }}">
                <i data-lucide="x" class="h-3.5 w-3.5"></i> Temuan
            </button>
            <button type="button" title="Diperbaiki" data-val="repaired"
                    class="tbtn flex h-8 px-2 items-center justify-center gap-1 rounded-lg border-2 text-[11px] font-black transition {{ $current === 'repaired' ? 'tbtn-repaired-active' : 'border-teal-200 text-teal-700 bg-teal-50/50 hover:border-teal-300' }}">
                <i data-lucide="wrench" class="h-3 w-3"></i> Diperbaiki
            </button>
        </div>
    </div>

    <div class="min-w-0 w-full">
        <p class="text-sm font-bold text-slate-800">{{ $label }}</p>
        <div class="mb-2 mt-1 flex flex-wrap gap-1.5">
            <span class="rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $temuan === 'Mayor' ? 'border-red-200 bg-red-50 text-brand-red' : 'border-amber-200 bg-amber-50 text-amber-600' }}">{{ $temuan }}</span>
            <span class="rounded-full border border-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-500">Dispensasi: {{ $disp }}</span>
        </div>
        <p class="mb-2 text-xs leading-relaxed text-slate-400">{{ $ket }}</p>

        {{-- Textarea Catatan & Quick chips --}}
        <div class="space-y-1.5">
            <textarea name="notes[{{ $key }}]" id="note-{{ str_replace(['.', '-'], '_', $key) }}" rows="1" placeholder="Catatan temuan / keterangan perbaikan..."
                      class="w-full resize-y rounded-lg border border-slate-200 px-3 py-1.5 text-xs transition focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/10">{{ $note }}</textarea>
            <div class="flex flex-wrap items-center gap-1">
                <span class="text-[10px] font-semibold text-slate-400">Pintasan perbaikan:</span>
                <button type="button" onclick="isiCatatanPerbaikan('note-{{ str_replace(['.', '-'], '_', $key) }}', 'Telah diperbaiki & normal', this)"
                        class="rounded-md border border-teal-200 bg-teal-50/70 hover:bg-teal-100 px-1.5 py-0.5 text-[10px] font-bold text-teal-700 transition">
                    + Sudah diperbaiki
                </button>
                <button type="button" onclick="isiCatatanPerbaikan('note-{{ str_replace(['.', '-'], '_', $key) }}', 'Telah diganti komponen baru', this)"
                        class="rounded-md border border-teal-200 bg-teal-50/70 hover:bg-teal-100 px-1.5 py-0.5 text-[10px] font-bold text-teal-700 transition">
                    + Telah diganti baru
                </button>
            </div>
        </div>
    </div>

    {{-- Tombol Desktop (3 Pilihan: Sesuai, Temuan, Diperbaiki) --}}
    <div class="hidden sm:flex justify-end items-center gap-1.5 w-full">
        <button type="button" title="Sesuai Standar" data-val="ok"
                class="tbtn flex h-9 px-3 items-center justify-center gap-1.5 rounded-xl border-2 text-xs font-black transition {{ $current === 'ok' ? 'tbtn-ok-active' : 'border-slate-200 text-slate-600 bg-white hover:border-emerald-400 hover:text-emerald-700 hover:bg-emerald-50/40' }}">
            <i data-lucide="check" class="h-4 w-4"></i> Sesuai
        </button>
        <button type="button" title="Ada Temuan" data-val="bad"
                class="tbtn flex h-9 px-3 items-center justify-center gap-1.5 rounded-xl border-2 text-xs font-black transition {{ $current === 'bad' ? 'tbtn-bad-active' : 'border-slate-200 text-slate-600 bg-white hover:border-red-400 hover:text-red-700 hover:bg-red-50/40' }}">
            <i data-lucide="x" class="h-4 w-4"></i> Temuan
        </button>
        <button type="button" title="Selesai Diperbaiki" data-val="repaired"
                class="tbtn flex h-9 px-2.5 items-center justify-center gap-1 rounded-xl border-2 text-xs font-black transition {{ $current === 'repaired' ? 'tbtn-repaired-active' : 'border-teal-200 text-teal-700 bg-teal-50 hover:border-teal-400 hover:bg-teal-100' }}">
            <i data-lucide="wrench" class="h-3.5 w-3.5"></i> Diperbaiki
        </button>
    </div>
</div>
