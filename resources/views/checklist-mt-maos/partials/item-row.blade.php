@php
    $current = old("results.$key", $checklist->results[$key] ?? null);
    $note = old("notes.$key", $checklist->notes[$key] ?? '');
@endphp
<div class="grid grid-cols-[28px_1fr_170px] items-start gap-3 py-4">
    <div class="pt-0.5 text-xs font-extrabold text-slate-400">{{ $idxLabel }}</div>
    <div class="min-w-0">
        <p class="text-sm font-bold text-slate-800">{{ $label }}</p>
        <div class="mb-2 mt-1 flex flex-wrap gap-1.5">
            <span class="rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $temuan === 'Mayor' ? 'border-red-200 bg-red-50 text-brand-red' : 'border-amber-200 bg-amber-50 text-amber-600' }}">{{ $temuan }}</span>
            <span class="rounded-full border border-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-500">Dispensasi: {{ $disp }}</span>
        </div>
        <p class="mb-2 text-xs leading-relaxed text-slate-400">{{ $ket }}</p>
        <textarea name="notes[{{ $key }}]" rows="1" placeholder="Tambah keterangan / catatan temuan..."
                  class="w-full resize-y rounded-lg border border-slate-200 px-3 py-1.5 text-xs transition focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/10">{{ $note }}</textarea>
    </div>
    <div class="flex justify-end gap-1.5" data-toggle-group>
        <input type="hidden" name="results[{{ $key }}]" value="{{ $current }}">
        <button type="button" title="Sesuai" data-val="ok"
                class="tbtn flex h-9 w-9 items-center justify-center rounded-lg border-2 text-base font-extrabold transition {{ $current === 'ok' ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-200 text-slate-400' }}"><i data-lucide="check" class="h-4 w-4"></i></button>
        <button type="button" title="Temuan" data-val="bad"
                class="tbtn flex h-9 w-9 items-center justify-center rounded-lg border-2 text-base font-extrabold transition {{ $current === 'bad' ? 'border-brand-red bg-brand-red text-white' : 'border-slate-200 text-slate-400' }}"><i data-lucide="x" class="h-4 w-4"></i></button>
    </div>
</div>
