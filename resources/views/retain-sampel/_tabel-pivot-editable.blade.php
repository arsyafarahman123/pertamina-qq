{{--
    Tabel rekap per-sesi multi-input langsung (Sekali Simpan di Bawah).
    Petugas cukup mengisi kolom Nopol, Tangki, Density Obs, dan Suhu untuk produk yang disalurkan,
    lalu menekan SATU tombol Simpan di bagian paling bawah.

    Vars: $produkList (array produk standar), $entries (array [produk => entry]),
          $entriesList (Collection seluruh record RetainSampelMt di sesi ini),
          $tanggal, $jamLabel ('06:00' / '12:00' / '18:00')
--}}
@php
    $actualEntries = isset($entriesList) && $entriesList->isNotEmpty() ? $entriesList : collect(array_values($entries ?? []));
    $existingByProduct = [];
    foreach ($actualEntries as $e) {
        $existingByProduct[$e->produk] = $e;
    }
    $formSesiId = 'form-bulk-sesi-' . str_replace([':', ' '], '', $jamLabel);
@endphp

<form method="POST" action="{{ route('retain-sampel.store') }}" id="{{ $formSesiId }}" class="space-y-4">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
    <input type="hidden" name="jam_label" value="{{ $jamLabel }}">

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full min-w-[760px] border-collapse text-xs sm:text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-[#0f3861] to-[#1e5083] text-white text-left uppercase tracking-wider text-[11px]">
                    <th class="px-3.5 py-3 font-bold rounded-tl-2xl">Produk Penyaluran</th>
                    <th class="px-3 py-3 font-bold">MT Nopol</th>
                    <th class="px-3 py-3 font-bold">Tangki Timbun</th>
                    <th class="px-3 py-3 font-bold">Density Obs</th>
                    <th class="px-3 py-3 font-bold">Suhu (°C)</th>
                    <th class="px-3 py-3 font-bold">Density '15 <span class="normal-case text-[9px] text-emerald-300 font-normal">(ASTM Otomatis)</span></th>
                    <th class="px-3 py-3 font-bold text-center rounded-tr-2xl">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $idx = 0; @endphp
                @foreach ($produkList as $p)
                    @php
                        $e = $existingByProduct[$p] ?? null;
                        $hasData = ($e && $e->density_obs !== null);
                        $rowIdx = $idx++;
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition {{ $hasData ? 'bg-emerald-50/20' : '' }}">
                        {{-- Nama Produk --}}
                        <td class="px-3.5 py-3 font-black text-slate-800 whitespace-nowrap">
                            <input type="hidden" name="items[{{ $rowIdx }}][produk]" value="{{ $p }}">
                            @if ($e)
                                <input type="hidden" name="items[{{ $rowIdx }}][id]" value="{{ $e->id }}">
                            @endif
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2.5 w-2.5 rounded-full {{ $hasData ? 'bg-emerald-500 ring-2 ring-emerald-200' : 'bg-slate-300' }}"></span>
                                <span class="font-bold text-[#0f3861]">{{ $p }}</span>
                            </div>
                        </td>

                        {{-- MT Nopol --}}
                        <td class="px-2 py-2">
                            <input type="text" name="items[{{ $rowIdx }}][mt_nopol]" 
                                   value="{{ old("items.$rowIdx.mt_nopol", $e?->mt_nopol) }}" 
                                   placeholder="cth: R 9675 B" 
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-1.5 text-xs font-semibold text-slate-700 transition focus:border-[#0f3861] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0f3861]/10">
                        </td>

                        {{-- Tangki Timbun --}}
                        <td class="px-2 py-2">
                            <input type="text" name="items[{{ $rowIdx }}][tangki_timbun]" 
                                   value="{{ old("items.$rowIdx.tangki_timbun", $e?->tangki_timbun) }}" 
                                   placeholder="cth: 09 / T.09" 
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-1.5 text-xs font-semibold text-slate-700 transition focus:border-[#0f3861] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0f3861]/10">
                        </td>

                        {{-- Density Obs --}}
                        <td class="px-2 py-2">
                            <input type="number" step="0.0001" name="items[{{ $rowIdx }}][density_obs]" 
                                   id="inline_obs_{{ $jamLabel }}_{{ $rowIdx }}"
                                   value="{{ old("items.$rowIdx.density_obs", $e?->density_obs) }}" 
                                   placeholder="0.7420" 
                                   oninput="hitungInlineDensity15('{{ $jamLabel }}', {{ $rowIdx }})"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-1.5 text-xs font-bold text-slate-800 transition focus:border-[#0f3861] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0f3861]/10">
                        </td>

                        {{-- Suhu --}}
                        <td class="px-2 py-2">
                            <input type="number" step="0.1" name="items[{{ $rowIdx }}][temperatur]" 
                                   id="inline_temp_{{ $jamLabel }}_{{ $rowIdx }}"
                                   value="{{ old("items.$rowIdx.temperatur", $e?->temperatur) }}" 
                                   placeholder="29.5" 
                                   oninput="hitungInlineDensity15('{{ $jamLabel }}', {{ $rowIdx }})"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-1.5 text-xs font-bold text-slate-800 transition focus:border-[#0f3861] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0f3861]/10">
                        </td>

                        {{-- Density 15 Otomatis --}}
                        <td class="px-3 py-2 whitespace-nowrap">
                            <div class="inline-flex items-center justify-center rounded-xl bg-blue-50 border border-blue-200/80 px-3 py-1 text-xs font-black text-[#0f3861] min-w-[76px]"
                                 id="inline_d15_{{ $jamLabel }}_{{ $rowIdx }}">
                                @if ($e && $e->density_15)
                                    {{ number_format($e->density_15, 4) }}
                                @else
                                    —
                                @endif
                            </div>
                        </td>

                        {{-- Status & Reset --}}
                        <td class="px-2 py-2 text-center whitespace-nowrap">
                            @if ($hasData)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 px-2 py-0.5 text-[10px] font-extrabold">
                                    ✓ Terisi
                                </span>
                            @else
                                <span class="text-[10px] font-semibold text-slate-400">
                                    Belum diisi
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- BAR TOMBOL SIMPAN SEKALIGUS DI PALING BAWAH --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-[#0f3861] font-bold text-[10px]">i</span>
            <span>Cukup isi baris produk yang disalurkan. Baris kosong akan otomatis dilewati.</span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('retain-sampel.create', ['tanggal' => $tanggal, 'jam_label' => $jamLabel]) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 hover:text-[#0f3861] transition shadow-sm">
                <i data-lucide="maximize-2" class="h-3.5 w-3.5 text-slate-400"></i> Form Layar Penuh
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-red hover:bg-brand-redDark px-5 py-2 text-xs sm:text-sm font-extrabold text-white shadow-md shadow-brand-red/25 transition hover:-translate-y-0.5 active:scale-95">
                <i data-lucide="save" class="h-4 w-4"></i>
                Simpan Semua Data (Pukul {{ str_replace(':', '.', $jamLabel) }} WIB)
            </button>
        </div>
    </div>
</form>

<script>
// Fungsi kalkulasi ASTM 53B realtime pada inline table
if (typeof window.hitungInlineDensity15 !== 'function') {
    window.hitungInlineDensity15 = function(jam, rowIdx) {
        var obsEl = document.getElementById('inline_obs_' + jam + '_' + rowIdx);
        var tempEl = document.getElementById('inline_temp_' + jam + '_' + rowIdx);
        var previewEl = document.getElementById('inline_d15_' + jam + '_' + rowIdx);
        if (!obsEl || !tempEl || !previewEl) return;

        var obs = parseFloat(obsEl.value);
        var temp = parseFloat(tempEl.value);

        if (isNaN(obs) || isNaN(temp) || obs <= 0) {
            previewEl.textContent = '—';
            return;
        }

        // Normalisasi basis kg/m3
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
    };
}
</script>