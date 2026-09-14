{{--
    Tabel rekap per-sesi yang bisa diisi/diedit LANGSUNG di halaman ini (gak perlu pindah halaman).
    Mendukung multiple sampel untuk produk yang sama (misal 2x Pertalite) & produk kustom.
    Semua icon menggunakan icon Lucide SVG profesional (bukan emoji).

    Vars: $produkList (array produk standar), $entries (array [produk => entry]),
          $entriesList (Collection seluruh record RetainSampelMt di sesi ini),
          $tanggal, $jamLabel ('06:00' / '12:00' / '18:00')
--}}
@php
    $actualEntries = isset($entriesList) && $entriesList->isNotEmpty() ? $entriesList : collect(array_values($entries ?? []));
    $existingProductNames = $actualEntries->pluck('produk')->all();
    $unfilledStandardProducts = array_diff($produkList, $existingProductNames);
    $newRowId = 'frm-new-' . str_replace([':', ' '], '', $jamLabel);
@endphp

<div class="space-y-3">
    <table class="w-full min-w-[700px] border-collapse text-xs sm:text-sm">
        <thead>
            <tr class="bg-slate-50 text-left uppercase tracking-wide text-slate-500">
                <th class="border-b border-slate-200 px-3 py-2.5 font-bold">Produk</th>
                <th class="border-b border-slate-200 px-3 py-2.5 font-bold">MT Nopol</th>
                <th class="border-b border-slate-200 px-3 py-2.5 font-bold">Density Obs</th>
                <th class="border-b border-slate-200 px-3 py-2.5 font-bold">Density'15 <span class="normal-case text-[10px] text-slate-400 font-normal">(otomatis)</span></th>
                <th class="border-b border-slate-200 px-3 py-2.5 font-bold">Suhu</th>
                <th class="border-b border-slate-200 px-3 py-2.5 font-bold">Tangki Timbun</th>
                <th class="border-b border-slate-200 px-3 py-2.5 text-right font-bold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            {{-- 1. DAFTAR SELURUH DATA YANG SUDAH TERINPUT (TERMASUK JIKA ADA 2X PERTALITE DLL) --}}
            @foreach ($actualEntries as $index => $e)
                @php
                    $rowId = 'frm-edit-' . $e->id;
                @endphp
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-3 py-2.5 font-bold text-slate-700 flex items-center gap-1.5">
                        <span class="inline-block h-2 w-2 rounded-full bg-brand-blue"></span>
                        <span>{{ $e->produk }}</span>
                        @if ($actualEntries->where('produk', $e->produk)->count() > 1)
                            <span class="rounded-full bg-amber-100 px-1.5 py-0.2 text-[10px] font-extrabold text-amber-800">#{{ $loop->iteration }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 font-semibold text-slate-700">{{ $e->mt_nopol ?: '-' }}</td>
                    <td class="px-3 py-2.5 text-slate-700 font-medium">{{ number_format($e->density_obs, 4) }}</td>
                    <td class="px-3 py-2.5 font-black text-brand-blue">{{ number_format($e->density_15, 4) }}</td>
                    <td class="px-3 py-2.5 text-slate-700">{{ rtrim(rtrim(number_format($e->temperatur, 2), '0'), '.') }}°C</td>
                    <td class="px-3 py-2.5 font-semibold text-slate-700">{{ $e->tangki_timbun ?: '-' }}</td>
                    <td class="px-3 py-2.5 text-right">
                        <div class="inline-flex items-center gap-1">
                            <button type="button" onclick="document.getElementById('{{ $rowId }}').classList.toggle('hidden')"
                                    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 shadow-sm transition">
                                <i data-lucide="pencil" class="h-3 w-3 text-slate-500"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('retain-sampel.destroy', $e) }}" onsubmit="return confirm('Hapus data sampel {{ $e->produk }} ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 px-2 py-1 text-[11px] font-bold text-red-600 transition" title="Hapus baris ini">
                                    <i data-lucide="trash-2" class="h-3 w-3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- FORM EDIT INLINE --}}
                <tr id="{{ $rowId }}" class="hidden">
                    <td colspan="7" class="bg-blue-50/40 p-4 border-y border-blue-100">
                        <form method="POST" action="{{ route('retain-sampel.update', $e) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <input type="hidden" name="jam_label" value="{{ $jamLabel }}">

                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Produk</label>
                                    <select name="produk" required class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-700">
                                        @foreach ($produkList as $p)
                                            <option value="{{ $p }}" @selected($e->produk === $p)>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">MT Nopol</label>
                                    <input type="text" name="mt_nopol" value="{{ $e->mt_nopol }}" placeholder="contoh: R 9675 B" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Tangki Timbun</label>
                                    <input type="text" name="tangki_timbun" value="{{ $e->tangki_timbun }}" placeholder="contoh: 09 atau T.09" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Density Obs</label>
                                    <input type="number" step="0.0001" name="density_obs" value="{{ $e->density_obs }}" required class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-800">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Suhu (°C)</label>
                                    <input type="number" step="0.1" name="temperatur" value="{{ $e->temperatur }}" required class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-800">
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-brand-blue px-3.5 py-1.5 text-xs font-bold text-white hover:bg-brand-blueDark shadow-sm">
                                    <i data-lucide="check" class="h-3.5 w-3.5"></i> Simpan Perubahan
                                </button>
                                <button type="button" onclick="document.getElementById('{{ $rowId }}').classList.add('hidden')" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-500 hover:bg-slate-50">
                                    Batal
                                </button>
                                <span class="text-[10px] text-slate-400">Density'15 dikalkulasi ulang otomatis oleh sistem.</span>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach

            {{-- 2. PRODUK STANDAR YANG BELUM DIINPUT DI SESI INI --}}
            @foreach ($unfilledStandardProducts as $prodUnfilled)
                @php
                    $rowUnfilledId = 'frm-unfilled-' . str_replace([':', ' '], '', $jamLabel) . '-' . \Illuminate\Support\Str::slug($prodUnfilled);
                @endphp
                <tr class="hover:bg-slate-50/50">
                    <td class="px-3 py-2.5 font-bold text-slate-400 flex items-center gap-1.5">
                        <span class="inline-block h-2 w-2 rounded-full bg-slate-200"></span>
                        <span>{{ $prodUnfilled }}</span>
                    </td>
                    <td colspan="5" class="px-3 py-2.5 italic text-slate-300">Belum ada data</td>
                    <td class="px-3 py-2.5 text-right">
                        <button type="button" onclick="document.getElementById('{{ $rowUnfilledId }}').classList.toggle('hidden')"
                                class="inline-flex items-center gap-1 rounded-lg bg-brand-red/10 px-2.5 py-1 text-[11px] font-bold text-brand-red hover:bg-brand-red/20 transition">
                            <i data-lucide="plus" class="h-3 w-3"></i> Isi Data
                        </button>
                    </td>
                </tr>

                {{-- FORM ISI CEPAT PRODUK BELUM ADA --}}
                <tr id="{{ $rowUnfilledId }}" class="hidden">
                    <td colspan="7" class="bg-slate-50 p-4 border-y border-slate-200">
                        <form method="POST" action="{{ route('retain-sampel.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <input type="hidden" name="jam_label" value="{{ $jamLabel }}">
                            <input type="hidden" name="produk" value="{{ $prodUnfilled }}">

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">MT Nopol</label>
                                    <input type="text" name="mt_nopol" placeholder="contoh: R 9675 B" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Tangki Timbun</label>
                                    <input type="text" name="tangki_timbun" placeholder="contoh: 09 atau T.09" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Density Obs</label>
                                    <input type="number" step="0.0001" name="density_obs" required placeholder="0.7420" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-800">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Suhu (°C)</label>
                                    <input type="number" step="0.1" name="temperatur" required placeholder="29.5" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-800">
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-brand-red px-3.5 py-1.5 text-xs font-bold text-white hover:bg-brand-redDark shadow-sm">
                                    <i data-lucide="save" class="h-3.5 w-3.5"></i> Simpan Data {{ $prodUnfilled }}
                                </button>
                                <button type="button" onclick="document.getElementById('{{ $rowUnfilledId }}').classList.add('hidden')" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-500 hover:bg-slate-50">
                                    Batal
                                </button>
                                <span class="text-[10px] text-slate-400">Density'15 dikalkulasi otomatis.</span>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 3. TOMBOL TAMBAH SAMPEL KUSTOM / DUPLIKAT BARU DI SESI INI --}}
    <div class="pt-2">
        <button type="button" onclick="document.getElementById('{{ $newRowId }}').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 hover:text-brand-blue hover:border-brand-blue shadow-sm transition">
            <i data-lucide="plus" class="h-3.5 w-3.5 text-slate-500"></i> Tambah Sampel Produk (Sesi {{ $jamLabel }} WIB)
        </button>

        {{-- FORM TAMBAH SAMPEL LAIN / DOUBLE PRODUK --}}
        <div id="{{ $newRowId }}" class="hidden mt-3 rounded-2xl bg-slate-50 p-4 border border-slate-200">
            <h4 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-1.5">
                <i data-lucide="plus" class="h-4 w-4 text-brand-blue"></i>
                Input Sampel Baru Sesi {{ $jamLabel }} WIB
            </h4>
            <form method="POST" action="{{ route('retain-sampel.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <input type="hidden" name="jam_label" value="{{ $jamLabel }}">

                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Produk</label>
                        <select name="produk" required class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-700">
                            @foreach ($produkList as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">MT Nopol</label>
                        <input type="text" name="mt_nopol" placeholder="contoh: R 9675 B" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Tangki Timbun</label>
                        <input type="text" name="tangki_timbun" placeholder="contoh: 09 atau T.09" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Density Obs</label>
                        <input type="number" step="0.0001" name="density_obs" required placeholder="0.7420" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Suhu (°C)</label>
                        <input type="number" step="0.1" name="temperatur" required placeholder="29.5" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-800">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-brand-red px-3.5 py-1.5 text-xs font-bold text-white hover:bg-brand-redDark shadow-sm">
                        <i data-lucide="save" class="h-3.5 w-3.5"></i> Simpan Sampel
                    </button>
                    <button type="button" onclick="document.getElementById('{{ $newRowId }}').classList.add('hidden')" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-500 hover:bg-slate-50">
                        Batal
                    </button>
                    <span class="text-[10px] text-slate-400">Density'15 dikalkulasi otomatis.</span>
                </div>
            </form>
        </div>
    </div>
</div>