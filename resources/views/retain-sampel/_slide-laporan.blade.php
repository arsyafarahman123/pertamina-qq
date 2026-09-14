@php
    // Helper format tanggal Indonesia baku
    $carbonTgl = \Illuminate\Support\Carbon::parse($tanggal);
    $namaHariMap = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulanMap = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    
    $hariIndo = $namaHariMap[$carbonTgl->format('l')] ?? $carbonTgl->format('l');
    $bulanIndo = $namaBulanMap[(int)$carbonTgl->format('n')] ?? $carbonTgl->format('F');
    $tanggalIndo = $hariIndo . ', ' . $carbonTgl->format('d') . ' ' . $bulanIndo . ' ' . $carbonTgl->format('Y');

    $slugSesi = 'slide-' . str_replace([':', ' ', '-'], '', $sesiJam) . '-' . str_replace('-', '', $tanggal);

    // Data sesi per jam
    $produk06Entries = $rekap['06:00'] ?? [];
    $produk12Entries = $rekap['12:00'] ?? [];
    $produk18Entries = $rekap['18:00'] ?? [];

    $produk06Aktif = [];
    foreach ($produkList as $p) {
        if (isset($produk06Entries[$p])) {
            $produk06Aktif[] = $p;
        }
    }

    $produk12Aktif = [];
    foreach ($produkList as $p) {
        if (isset($produk12Entries[$p])) {
            $produk12Aktif[] = $p;
        }
    }

    $produk18Aktif = [];
    foreach ($produkList as $p) {
        if (isset($produk18Entries[$p])) {
            $produk18Aktif[] = $p;
        }
    }

    // Data sesi kanan untuk tab 12:00 atau 18:00
    $produkKananEntries = $rekap[$sesiJam] ?? [];
    $produkKananAktif = [];
    foreach ($produkList as $p) {
        if (isset($produkKananEntries[$p])) {
            $produkKananAktif[] = $p;
        }
    }

    // Semua produk yang ada data hari ini
    $semuaProdukHariIni = [];
    foreach ($produkList as $p) {
        foreach (['06:00', '12:00', '18:00'] as $j) {
            if (isset($rekap[$j][$p])) {
                $semuaProdukHariIni[] = $p;
                break;
            }
        }
    }
    if (empty($semuaProdukHariIni)) {
        $semuaProdukHariIni = $produk06Aktif ?: ['Pertalite', 'Pertamax', 'Biosolar B50'];
    }

    // Helper format nomor tangki
    $formatNomorTangki = function ($tangkiRaw) {
        if ($tangkiRaw === null || $tangkiRaw === '') return '-';
        $clean = trim(str_ireplace(['tangki', 't.'], '', (string)$tangkiRaw));
        if (is_numeric($clean)) {
            return 'T.' . str_pad($clean, 2, '0', STR_PAD_LEFT);
        }
        return (str_starts_with(strtoupper((string)$tangkiRaw), 'T.') ? '' : 'T.') . $tangkiRaw;
    };

    // Helper parsing nopol array
    $parseNopolArray = function ($rawNopol) {
        if (!$rawNopol) return [];
        $parts = preg_split('/[,\n\r;]+/', (string)$rawNopol);
        $result = [];
        foreach ($parts as $p) {
            $clean = trim($p);
            if ($clean !== '') {
                $result[] = $clean;
            }
        }
        return $result;
    };

    // Produk Utama untuk 3 MT (Pertamax atau produk pertama yang ada nopolnya)
    $produk3MtUtama = 'Pertamax';
    if (!isset($produk06Entries['Pertamax']) && !empty($produk06Aktif)) {
        $produk3MtUtama = $produk06Aktif[0];
    }
    
    // Nopol & Tangki untuk 3 MT produk utama
    $entry3Mt = $produk06Entries[$produk3MtUtama] ?? null;
    $nopols3Mt = $entry3Mt ? $parseNopolArray($entry3Mt->mt_nopol) : [];
    $tangki3MtFormatted = $entry3Mt ? $formatNomorTangki($entry3Mt->tangki_timbun) : '-';

    // Jika Pertamax kosong tapi ada produk 06 lain yang punya nopol
    if (empty($nopols3Mt)) {
        foreach ($produk06Aktif as $p) {
            if (!empty($produk06Entries[$p]->mt_nopol)) {
                $produk3MtUtama = $p;
                $entry3Mt = $produk06Entries[$p];
                $nopols3Mt = $parseNopolArray($entry3Mt->mt_nopol);
                $tangki3MtFormatted = $formatNomorTangki($entry3Mt->tangki_timbun);
                break;
            }
        }
    }

    // Rekap seluruh Nopol MT hari ini per produk & jam
    $nopolRekapLengkap = [];
    foreach ($produkList as $p) {
        foreach (['06:00', '12:00', '18:00'] as $j) {
            if (isset($rekap[$j][$p]) && !empty($rekap[$j][$p]->mt_nopol)) {
                $rawList = $parseNopolArray($rekap[$j][$p]->mt_nopol);
                foreach ($rawList as $clean) {
                    $nopolRekapLengkap[$p][] = [
                        'jam' => $j,
                        'nopol' => $clean
                    ];
                }
            }
        }
    }

    // FOTO PER SESI
    $foto06Retain = $fotoSesi['06:00'] ?? $fotoSesi['06:00_retain'] ?? null;
    $foto06VisualMt = $fotoSesi['06:00_visual_mt'] ?? null;
    $foto06VisualTangki = $fotoSesi['06:00_visual_tangki'] ?? null;
    $foto12Retank = $fotoSesi['12:00'] ?? $fotoSesi['12:00_retank'] ?? $fotoSesi['12:00_retain'] ?? null;
    $foto18Retank = $fotoSesi['18:00'] ?? $fotoSesi['18:00_retank'] ?? $fotoSesi['18:00_retain'] ?? null;

    if ($sesiJam === '06:00') {
        $fotoKiri = $foto06Retain;
        $labelFotoKiri = '06:00';
    } elseif ($sesiJam === '12:00') {
        $fotoKiri = $fotoSesi['12:00_retain'] ?? $foto06Retain;
        $labelFotoKiri = '12:00_retain';
    } elseif ($sesiJam === '18:00') {
        $fotoKiri = $fotoSesi['18:00_retain'] ?? $foto06Retain;
        $labelFotoKiri = '18:00_retain';
    } else {
        $fotoKiri = $foto06Retain;
        $labelFotoKiri = '06:00';
    }

    $fotoSesiKanan = $fotoSesi[$sesiJam] ?? $fotoSesi[$sesiJam . '_retank'] ?? null;

    $formatAngka = function ($val, $desimal = 4) {
        if ($val === null || $val === '') return '-';
        return str_replace('.', ',', number_format((float)$val, $desimal, '.', ''));
    };

    $formatSuhu = function ($val) {
        if ($val === null || $val === '') return '-';
        $num = (float)$val;
        return floor($num) == $num ? (string)(int)$num : str_replace('.', ',', (string)$num);
    };

    $tangkiListDefault = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
@endphp

<div id="{{ $slugSesi }}" class="relative overflow-hidden rounded-3xl bg-white p-6 sm:p-8 shadow-xl border border-slate-200 text-slate-800 transition-all font-sans">
    
    {{-- Pertamina Official Bottom-Left Curved Swoosh Accent --}}
    <div class="pointer-events-none absolute -bottom-5 -left-5 z-0 h-28 w-48 opacity-90">
        <svg viewBox="0 0 160 100" class="h-full w-full">
            <path d="M-10,100 Q40,55 120,82 Q150,92 160,100 L-10,100 Z" fill="#DA251D" />
            <path d="M-10,100 Q30,70 90,88 Q120,95 135,100 L-10,100 Z" fill="#ACC42A" />
        </svg>
    </div>

    {{-- ========================================================
         HEADER SLIDE RESMI PERTAMINA PATRA NIAGA
         ======================================================== --}}
    <div class="relative z-10 mb-6 flex flex-wrap items-center justify-between gap-4 border-b-2 border-slate-100 pb-4">
        {{-- Sisi Kiri: Badge Icon Resmi + Judul Laporan --}}
        <div class="flex items-center gap-3.5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#0e3b66] p-2.5 text-white shadow-md shadow-blue-950/20">
                <svg viewBox="0 0 24 24" class="h-full w-full" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5" stroke="#ACC42A"/>
                    <path d="M9 3v18" stroke="#ACC42A" stroke-width="1.5"/>
                    <path d="M14 8l3 3-3 3" stroke="#ffffff"/>
                    <path d="M6 14l2-4 2 2 3-5" stroke="#DA251D" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl md:text-2xl font-black uppercase tracking-tight text-[#0f3861]">
                    @if ($sesiJam === 'rekap-semua')
                        REKAP KESELURUHAN RETAIN SAMPEL PENYALURAN MT – FT MAOS
                    @else
                        RETAIN SAMPEL PENYALURAN MT – FT MAOS
                    @endif
                </h2>
                <p class="text-xs sm:text-sm font-bold text-[#1a4a75]">
                    {{ $tanggalIndo }} {{ $sesiJam === 'rekap-semua' ? '• (Hasil Gabungan 06.00, 12.00 & 18.00 WIB)' : '' }}
                </p>
            </div>
        </div>

        {{-- Sisi Kanan: Logo Resmi Favicon Pertamina + Wordmark Patra Niaga --}}
        <div class="flex items-center gap-2.5">
            <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-8 sm:h-10 w-auto object-contain">
            <div class="flex flex-col justify-center leading-none text-left">
                <span class="font-black tracking-wider text-[#071b33] text-sm sm:text-base leading-tight">PERTAMINA</span>
                <span class="font-black tracking-widest text-[#da251d] text-[10px] sm:text-[11px] leading-tight mt-0.5">PATRA NIAGA</span>
            </div>
        </div>
    </div>

    {{-- ========================================================
         KONTEN REKAP KESELURUHAN (SEMUA JAM DI HARI ITU GABUNGAN)
         ======================================================== --}}
    @if ($sesiJam === 'rekap-semua')
        <div class="relative z-10 space-y-6">
            
            {{-- 3 KOTAK SESI BERDAMPINGAN: 06.00, 12.00, 18.00 WIB --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-stretch">
                
                {{-- SESI 1: PUKUL 06.00 WIB (Awal Penyaluran) --}}
                <div class="flex flex-col justify-between rounded-3xl bg-[#f0f4f8]/90 p-4 sm:p-5 border border-slate-200/90 shadow-sm">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 rounded-full bg-[#123e6b] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm">
                                <span class="h-2 w-2 rounded-full bg-[#ACC42A]"></span>
                                06.00 WIB (Awal Penyaluran)
                            </span>
                            <button type="button" onclick="document.getElementById('form-edit-rekap-0600-{{ $slugSesi }}').classList.toggle('hidden')"
                                    class="no-print inline-flex items-center gap-1.5 text-[11px] font-bold text-[#0f3861] bg-white border border-slate-200 hover:border-brand-blue px-2.5 py-1 rounded-lg shadow-sm">
                                <i data-lucide="pencil" class="h-3 w-3 text-slate-500"></i> Edit 06.00
                            </button>
                        </div>

                        {{-- Gallery Foto 06.00: Retain + Visual 3 MT + Visual Tangki --}}
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <div class="rounded-xl border border-slate-200 bg-white p-1 text-center shadow-inner">
                                <div class="flex h-24 sm:h-28 items-center justify-center overflow-hidden rounded-lg bg-slate-50">
                                    @if ($foto06Retain)
                                        <img src="{{ $foto06Retain->url() }}" crossorigin="anonymous" alt="Retain 06.00" class="h-full w-full object-contain">
                                    @else
                                        <span class="text-[9px] text-slate-400 font-bold">Botol Retain</span>
                                    @endif
                                </div>
                                <span class="text-[9px] font-bold text-slate-600 block mt-1">Sampel Retain</span>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-1 text-center shadow-inner">
                                <div class="flex h-24 sm:h-28 items-center justify-center overflow-hidden rounded-lg bg-slate-50">
                                    @if ($foto06VisualMt)
                                        <img src="{{ $foto06VisualMt->url() }}" crossorigin="anonymous" alt="3 MT 06.00" class="h-full w-full object-contain">
                                    @else
                                        <span class="text-[9px] text-slate-400 font-bold">Visual 3 MT</span>
                                    @endif
                                </div>
                                <span class="text-[9px] font-bold text-slate-600 block mt-1">3 MT Pertama</span>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-1 text-center shadow-inner">
                                <div class="flex h-24 sm:h-28 items-center justify-center overflow-hidden rounded-lg bg-slate-50">
                                    @if ($foto06VisualTangki)
                                        <img src="{{ $foto06VisualTangki->url() }}" crossorigin="anonymous" alt="Tangki 06.00" class="h-full w-full object-contain">
                                    @else
                                        <span class="text-[9px] text-slate-400 font-bold">Tangki Timbun</span>
                                    @endif
                                </div>
                                <span class="text-[9px] font-bold text-slate-600 block mt-1">Gelas Tangki</span>
                            </div>
                        </div>

                        {{-- Ringkasan Nopol & Tangki 06.00 --}}
                        <div class="rounded-xl bg-white p-2.5 border border-slate-200 text-xs mb-3">
                            <div class="flex justify-between items-center border-b border-slate-100 pb-1 mb-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Tangki Timbun:</span>
                                <span class="font-black text-[#0f3861]">{{ $tangki3MtFormatted }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Visual Sales {{ $produk3MtUtama }} 3 MT:</span>
                                @if (!empty($nopols3Mt))
                                    <div class="font-bold text-slate-800 text-[11px]">
                                        {{ implode(', ', $nopols3Mt) }}
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Belum ada input</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Mini Table 06.00 --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                        <table class="w-full text-center text-[11px] border-collapse">
                            <thead>
                                <tr class="bg-[#6c8296] text-white">
                                    <th class="px-2 py-1 text-left font-bold text-[10px]">Produk</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">Dens'15</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">Suhu</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">Tangki</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                @forelse ($produk06Aktif as $p)
                                    @php $e = $produk06Entries[$p] ?? null; @endphp
                                    <tr>
                                        <td class="px-2 py-1 text-left font-bold text-slate-700">{{ $p }}</td>
                                        <td class="px-1 py-1 font-bold text-[#0f3b66]">{{ $e ? $formatAngka($e->density_15, 4) : '-' }}</td>
                                        <td class="px-1 py-1">{{ $e ? $formatSuhu($e->temperatur) : '-' }}°C</td>
                                        <td class="px-1 py-1">{{ $e?->tangki_timbun ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-2 text-center text-slate-400 italic text-[10px]">Belum ada data 06.00</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SESI 2: PUKUL 12.00 WIB (Re-Tank Siang) --}}
                <div class="flex flex-col justify-between rounded-3xl bg-[#f0f4f8]/90 p-4 sm:p-5 border border-slate-200/90 shadow-sm">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 rounded-full bg-[#123e6b] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm">
                                <span class="h-2 w-2 rounded-full bg-[#e86a17]"></span>
                                12.00 WIB (Re-Tank Siang)
                            </span>
                            <button type="button" onclick="document.getElementById('form-edit-rekap-1200-{{ $slugSesi }}').classList.toggle('hidden')"
                                    class="no-print inline-flex items-center gap-1.5 text-[11px] font-bold text-[#0f3861] bg-white border border-slate-200 hover:border-brand-blue px-2.5 py-1 rounded-lg shadow-sm">
                                <i data-lucide="pencil" class="h-3 w-3 text-slate-500"></i> Edit 12.00
                            </button>
                        </div>

                        {{-- Foto Re-Tank 12.00 --}}
                        <div class="rounded-2xl border border-slate-200 bg-white p-2 text-center shadow-inner mb-3">
                            <div class="flex h-36 sm:h-40 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                                @if ($foto12Retank)
                                    <img src="{{ $foto12Retank->url() }}" crossorigin="anonymous" alt="Re-Tank 12.00" class="h-full w-full object-contain">
                                @else
                                    <div class="p-3 text-center">
                                        <svg class="mx-auto h-7 w-7 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                        </svg>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1">Foto Re-Tank 12.00</p>
                                    </div>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold text-slate-600 block mt-1.5">Foto Sampel Re-Tank 12.00 WIB</span>
                        </div>
                    </div>

                    {{-- Mini Table 12.00 --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                        <table class="w-full text-center text-[11px] border-collapse">
                            <thead>
                                <tr class="bg-[#6c8296] text-white">
                                    <th class="px-2 py-1 text-left font-bold text-[10px]">Produk</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">MT Nopol</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">Dens'15</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">Suhu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                @forelse ($produk12Aktif as $p)
                                    @php $e = $produk12Entries[$p] ?? null; @endphp
                                    <tr>
                                        <td class="px-2 py-1 text-left font-bold text-slate-700">{{ $p }}</td>
                                        <td class="px-1 py-1 font-semibold">{{ $e?->mt_nopol ?: '-' }}</td>
                                        <td class="px-1 py-1 font-bold text-[#0f3b66]">{{ $e ? $formatAngka($e->density_15, 4) : '-' }}</td>
                                        <td class="px-1 py-1">{{ $e ? $formatSuhu($e->temperatur) : '-' }}°C</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-2 text-center text-slate-400 italic text-[10px]">Belum ada data 12.00</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SESI 3: PUKUL 18.00 WIB (Re-Tank Sore) --}}
                <div class="flex flex-col justify-between rounded-3xl bg-[#f0f4f8]/90 p-4 sm:p-5 border border-slate-200/90 shadow-sm">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 rounded-full bg-[#123e6b] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm">
                                <span class="h-2 w-2 rounded-full bg-[#DA251D]"></span>
                                18.00 WIB (Re-Tank Sore)
                            </span>
                            <button type="button" onclick="document.getElementById('form-edit-rekap-1800-{{ $slugSesi }}').classList.toggle('hidden')"
                                    class="no-print inline-flex items-center gap-1.5 text-[11px] font-bold text-[#0f3861] bg-white border border-slate-200 hover:border-brand-blue px-2.5 py-1 rounded-lg shadow-sm">
                                <i data-lucide="pencil" class="h-3 w-3 text-slate-500"></i> Edit 18.00
                            </button>
                        </div>

                        {{-- Foto Re-Tank 18.00 --}}
                        <div class="rounded-2xl border border-slate-200 bg-white p-2 text-center shadow-inner mb-3">
                            <div class="flex h-36 sm:h-40 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                                @if ($foto18Retank)
                                    <img src="{{ $foto18Retank->url() }}" crossorigin="anonymous" alt="Re-Tank 18.00" class="h-full w-full object-contain">
                                @else
                                    <div class="p-3 text-center">
                                        <svg class="mx-auto h-7 w-7 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                        </svg>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1">Foto Re-Tank 18.00</p>
                                    </div>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold text-slate-600 block mt-1.5">Foto Sampel Re-Tank 18.00 WIB</span>
                        </div>
                    </div>

                    {{-- Mini Table 18.00 --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                        <table class="w-full text-center text-[11px] border-collapse">
                            <thead>
                                <tr class="bg-[#6c8296] text-white">
                                    <th class="px-2 py-1 text-left font-bold text-[10px]">Produk</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">MT Nopol</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">Dens'15</th>
                                    <th class="px-1 py-1 font-bold text-[10px]">Suhu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-800">
                                @forelse ($produk18Aktif as $p)
                                    @php $e = $produk18Entries[$p] ?? null; @endphp
                                    <tr>
                                        <td class="px-2 py-1 text-left font-bold text-slate-700">{{ $p }}</td>
                                        <td class="px-1 py-1 font-semibold">{{ $e?->mt_nopol ?: '-' }}</td>
                                        <td class="px-1 py-1 font-bold text-[#0f3b66]">{{ $e ? $formatAngka($e->density_15, 4) : '-' }}</td>
                                        <td class="px-1 py-1">{{ $e ? $formatSuhu($e->temperatur) : '-' }}°C</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-2 text-center text-slate-400 italic text-[10px]">Belum ada data 18.00</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- DRAWER FORM EDIT CEPAT SESI 06.00, 12.00, 18.00 (Toggled) --}}
            <div id="form-edit-rekap-0600-{{ $slugSesi }}" class="hidden no-print rounded-2xl bg-white p-4 border border-blue-200 shadow-md">
                <div class="mb-3 flex items-center justify-between border-b pb-2">
                    <span class="text-xs font-bold text-[#0f3861] uppercase">Edit Data Penyaluran (Pukul 06.00 WIB)</span>
                    <button type="button" onclick="document.getElementById('form-edit-rekap-0600-{{ $slugSesi }}').classList.add('hidden')" class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 font-bold"><i data-lucide="x" class="h-3.5 w-3.5"></i> Tutup</button>
                </div>
                <div class="space-y-3">
                    @foreach ($produkList as $p)
                        @php $e = $produk06Entries[$p] ?? null; @endphp
                        <form method="POST" action="{{ $e ? route('retain-sampel.update', $e) : route('retain-sampel.store') }}" class="grid grid-cols-2 sm:grid-cols-5 gap-2 items-end border-b pb-2 last:border-b-0">
                            @csrf
                            @if ($e) @method('PUT') @endif
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <input type="hidden" name="jam_label" value="06:00">
                            <input type="hidden" name="produk" value="{{ $p }}">
                            <div>
                                <span class="text-[10px] font-bold text-[#0f3861] block mb-1">{{ $p }}</span>
                                <input type="text" name="mt_nopol" value="{{ $e?->mt_nopol }}" placeholder="Nopol MT" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Density Obs</span>
                                <input type="number" step="0.0001" name="density_obs" value="{{ $e?->density_obs }}" placeholder="0.7420" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Suhu (°C)</span>
                                <input type="number" step="0.1" name="temperatur" value="{{ $e?->temperatur }}" placeholder="24" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Tangki</span>
                                <input type="text" name="tangki_timbun" value="{{ $e?->tangki_timbun }}" placeholder="9" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <button type="submit" class="w-full rounded-lg bg-brand-blue hover:bg-brand-blueDark text-white px-2 py-1 text-xs font-bold transition">Simpan</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>

            <div id="form-edit-rekap-1200-{{ $slugSesi }}" class="hidden no-print rounded-2xl bg-white p-4 border border-blue-200 shadow-md">
                <div class="mb-3 flex items-center justify-between border-b pb-2">
                    <span class="text-xs font-bold text-[#0f3861] uppercase">Edit Data Re-Tank Siang (Pukul 12.00 WIB)</span>
                    <button type="button" onclick="document.getElementById('form-edit-rekap-1200-{{ $slugSesi }}').classList.add('hidden')" class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 font-bold"><i data-lucide="x" class="h-3.5 w-3.5"></i> Tutup</button>
                </div>
                <div class="space-y-3">
                    @foreach ($produkList as $p)
                        @php $e = $produk12Entries[$p] ?? null; @endphp
                        <form method="POST" action="{{ $e ? route('retain-sampel.update', $e) : route('retain-sampel.store') }}" class="grid grid-cols-2 sm:grid-cols-5 gap-2 items-end border-b pb-2 last:border-b-0">
                            @csrf
                            @if ($e) @method('PUT') @endif
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <input type="hidden" name="jam_label" value="12:00">
                            <input type="hidden" name="produk" value="{{ $p }}">
                            <div>
                                <span class="text-[10px] font-bold text-[#0f3861] block mb-1">{{ $p }}</span>
                                <input type="text" name="mt_nopol" value="{{ $e?->mt_nopol }}" placeholder="Nopol MT" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Density Obs</span>
                                <input type="number" step="0.0001" name="density_obs" value="{{ $e?->density_obs }}" placeholder="0.7420" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Suhu (°C)</span>
                                <input type="number" step="0.1" name="temperatur" value="{{ $e?->temperatur }}" placeholder="24" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Tangki</span>
                                <input type="text" name="tangki_timbun" value="{{ $e?->tangki_timbun }}" placeholder="9" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <button type="submit" class="w-full rounded-lg bg-brand-blue hover:bg-brand-blueDark text-white px-2 py-1 text-xs font-bold transition">Simpan</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>

            <div id="form-edit-rekap-1800-{{ $slugSesi }}" class="hidden no-print rounded-2xl bg-white p-4 border border-blue-200 shadow-md">
                <div class="mb-3 flex items-center justify-between border-b pb-2">
                    <span class="text-xs font-bold text-[#0f3861] uppercase">Edit Data Re-Tank Sore (Pukul 18.00 WIB)</span>
                    <button type="button" onclick="document.getElementById('form-edit-rekap-1800-{{ $slugSesi }}').classList.add('hidden')" class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 font-bold"><i data-lucide="x" class="h-3.5 w-3.5"></i> Tutup</button>
                </div>
                <div class="space-y-3">
                    @foreach ($produkList as $p)
                        @php $e = $produk18Entries[$p] ?? null; @endphp
                        <form method="POST" action="{{ $e ? route('retain-sampel.update', $e) : route('retain-sampel.store') }}" class="grid grid-cols-2 sm:grid-cols-5 gap-2 items-end border-b pb-2 last:border-b-0">
                            @csrf
                            @if ($e) @method('PUT') @endif
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <input type="hidden" name="jam_label" value="18:00">
                            <input type="hidden" name="produk" value="{{ $p }}">
                            <div>
                                <span class="text-[10px] font-bold text-[#0f3861] block mb-1">{{ $p }}</span>
                                <input type="text" name="mt_nopol" value="{{ $e?->mt_nopol }}" placeholder="Nopol MT" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Density Obs</span>
                                <input type="number" step="0.0001" name="density_obs" value="{{ $e?->density_obs }}" placeholder="0.7420" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Suhu (°C)</span>
                                <input type="number" step="0.1" name="temperatur" value="{{ $e?->temperatur }}" placeholder="24" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-1">Tangki</span>
                                <input type="text" name="tangki_timbun" value="{{ $e?->tangki_timbun }}" placeholder="9" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                            </div>
                            <div>
                                <button type="submit" class="w-full rounded-lg bg-brand-blue hover:bg-brand-blueDark text-white px-2 py-1 text-xs font-bold transition">Simpan</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>

        </div>
    @else
        {{-- ========================================================
             KONTEN SESI INDIVIDUAL (06.00, 12.00, 18.00 WIB)
             DENGAN DUA KOTAK SEJAJAR (KIRI & KANAN)
             ======================================================== --}}
        <div class="relative z-10 grid grid-cols-1 gap-6 lg:grid-cols-2 items-stretch">
            
            {{-- ========================================================
                 KOTAK KIRI: SAMPEL RETAIN (JAM 06.00 WIB — STAY TETAP)
                 ======================================================== --}}
            <div class="flex flex-col justify-between rounded-3xl bg-[#f0f4f8]/80 p-5 sm:p-6 border border-slate-200/90 shadow-sm">
                <div>
                    {{-- Header Pill Kiri + Tombol Edit Cepat --}}
                    <div class="mb-4 flex items-center justify-between">
                        <div class="inline-flex items-center gap-2.5 rounded-full bg-[#123e6b] px-5 py-2 text-white shadow-sm">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#e86a17] text-white">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                </svg>
                            </span>
                            <span class="text-xs sm:text-sm font-bold tracking-wide">Sampel Retain</span>
                        </div>

                        <button type="button" onclick="document.getElementById('form-edit-kiri-{{ $slugSesi }}').classList.toggle('hidden')"
                                class="no-print inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 hover:border-brand-blue hover:text-brand-blue px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm transition">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                            </svg>
                            <span>Edit Data Tabel</span>
                        </button>
                    </div>

                    {{-- Foto Sampel Retain --}}
                    <div class="group relative mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-inner">
                        <div class="flex h-56 sm:h-64 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                            @if ($fotoKiri && $fotoKiri->isPdf())
                                <a href="{{ $fotoKiri->url() }}" target="_blank" class="flex flex-col items-center gap-2 text-brand-red">
                                    <svg class="h-10 w-10 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    <span class="text-xs font-bold text-red-600">Dokumen PDF Sampel Retain</span>
                                </a>
                            @elseif ($fotoKiri)
                                <img src="{{ $fotoKiri->url() }}" crossorigin="anonymous" alt="Sampel Retain" class="h-full w-full object-contain">
                            @else
                                <div class="text-center p-4">
                                    <svg class="mx-auto h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                    </svg>
                                    <p class="text-xs font-bold text-slate-400 mt-2">Foto Sampel Retain</p>
                                    <p class="text-[10px] text-slate-400">Pertalite, Pertamax, Biosolar B50</p>
                                </div>
                            @endif
                        </div>

                        @if (!auth()->user()->isSpbu())
                        {{-- Form Upload Foto Retain (no-print) --}}
                        <div class="no-print mt-2 flex items-center justify-between border-t border-slate-100 pt-2 px-1">
                            <span class="text-[11px] font-semibold text-slate-500">Foto Botol Sampel Retain {{ $sesiJam === '06:00' ? '06.00' : str_replace(':', '.', $sesiJam) }}</span>
                            <form method="POST" action="{{ route('retain-sampel.foto-sesi.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <input type="hidden" name="jam_label" value="{{ $labelFotoKiri }}">
                                <label for="upload-retain-{{ $slugSesi }}" class="cursor-pointer inline-flex items-center gap-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 px-3 py-1 text-[11px] font-bold text-slate-700 transition">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span>{{ $fotoKiri ? 'Ganti Foto' : 'Upload Foto' }}</span>
                                </label>
                                <input type="file" id="upload-retain-{{ $slugSesi }}" name="foto" accept="image/*,application/pdf" class="hidden" onchange="this.form.submit()">
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Teks Keterangan --}}
                    <div class="mb-3 space-y-0.5 text-xs sm:text-sm font-semibold text-slate-800">
                        <p>Update hari ini <span class="font-bold">{{ $tanggalIndo }}</span></p>
                        <p>Pukul 06.00 WIB adalah sebagai berikut :</p>
                    </div>
                </div>

                {{-- Form Edit Inline (Toggled) --}}
                <div id="form-edit-kiri-{{ $slugSesi }}" class="hidden no-print mb-4 rounded-2xl bg-white p-4 border border-blue-200 shadow-md">
                    <div class="mb-3 flex items-center justify-between border-b pb-2">
                        <span class="text-xs font-bold text-[#0f3861] uppercase">Edit Data Sampel Retain (Jam 06.00)</span>
                        <button type="button" onclick="document.getElementById('form-edit-kiri-{{ $slugSesi }}').classList.add('hidden')" class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 font-bold"><i data-lucide="x" class="h-3.5 w-3.5"></i> Tutup</button>
                    </div>
                    <div class="space-y-4">
                        @foreach ($produkList as $p)
                            @php $e = $produk06Entries[$p] ?? null; @endphp
                            <form method="POST" action="{{ $e ? route('retain-sampel.update', $e) : route('retain-sampel.store') }}" class="grid grid-cols-2 sm:grid-cols-5 gap-2 items-end border-b pb-2 last:border-b-0">
                                @csrf
                                @if ($e) @method('PUT') @endif
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <input type="hidden" name="jam_label" value="06:00">
                                <input type="hidden" name="produk" value="{{ $p }}">
                                <div>
                                    <span class="text-[10px] font-bold text-[#0f3861] block mb-1">{{ $p }}</span>
                                    <input type="text" name="mt_nopol" value="{{ $e?->mt_nopol }}" placeholder="Nopol MT" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 block mb-1">Density Obs</span>
                                    <input type="number" step="0.0001" name="density_obs" value="{{ $e?->density_obs }}" placeholder="0.7420" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 block mb-1">Suhu (°C)</span>
                                    <input type="number" step="0.1" name="temperatur" value="{{ $e?->temperatur }}" placeholder="24" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 block mb-1">Tangki</span>
                                    <input type="text" name="tangki_timbun" value="{{ $e?->tangki_timbun }}" placeholder="9" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                </div>
                                <div>
                                    <button type="submit" class="w-full rounded-lg bg-brand-blue hover:bg-brand-blueDark text-white px-2 py-1 text-xs font-bold transition">Simpan</button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>

                {{-- Tabel Data Sampel Retain Jam 06.00 --}}
                <div class="overflow-x-auto rounded-xl border border-slate-300 bg-white shadow-sm">
                    @if (!empty($produk06Aktif))
                        <table class="w-full text-center text-xs border-collapse">
                            <thead>
                                <tr class="bg-[#6c8296] text-white">
                                    <th class="border-r border-slate-400 px-2.5 py-2 text-left font-bold text-[11px] uppercase tracking-wider">Produk</th>
                                    @foreach ($produk06Aktif as $p)
                                        <th class="border-r last:border-r-0 border-slate-400 px-2.5 py-2 font-bold text-[11px] uppercase tracking-wider">{{ $p }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-800">
                                {{-- MT Nopol --}}
                                <tr class="bg-slate-50/70 font-semibold">
                                    <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">MT Nopol</td>
                                    @foreach ($produk06Aktif as $p)
                                        @php $e = $produk06Entries[$p] ?? null; @endphp
                                        <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-bold">{{ $e?->mt_nopol ?: '-' }}</td>
                                    @endforeach
                                </tr>
                                {{-- Density Obs --}}
                                <tr>
                                    <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">Density Obs</td>
                                    @foreach ($produk06Aktif as $p)
                                        @php $e = $produk06Entries[$p] ?? null; @endphp
                                        <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-medium">{{ $e ? $formatAngka($e->density_obs, 3) : '-' }}</td>
                                    @endforeach
                                </tr>
                                {{-- Density'15 --}}
                                <tr class="bg-blue-50/40">
                                    <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-[#0f3b66]">Density'15</td>
                                    @foreach ($produk06Aktif as $p)
                                        @php $e = $produk06Entries[$p] ?? null; @endphp
                                        <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-bold text-[#0f3b66]">{{ $e ? $formatAngka($e->density_15, 4) : '-' }}</td>
                                    @endforeach
                                </tr>
                                {{-- Temperatur --}}
                                <tr>
                                    <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">Temperatur</td>
                                    @foreach ($produk06Aktif as $p)
                                        @php $e = $produk06Entries[$p] ?? null; @endphp
                                        <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-medium">{{ $e ? $formatSuhu($e->temperatur) : '-' }}</td>
                                    @endforeach
                                </tr>
                                {{-- Tangki Timbun --}}
                                <tr class="bg-slate-50/70">
                                    <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">Tangki Timbun</td>
                                    @foreach ($produk06Aktif as $p)
                                        @php $e = $produk06Entries[$p] ?? null; @endphp
                                        <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-medium">{{ $e?->tangki_timbun ?: '-' }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <div class="py-6 text-center text-xs text-slate-400 italic">
                            Belum ada data sampel yang diinput untuk pukul 06.00 WIB. Klik <b>Edit Data Tabel</b> di atas untuk mulai mengisi.
                        </div>
                    @endif
                </div>
            </div>

            {{-- ========================================================
                 KOTAK KANAN: KONDISIONAL (JAM 06.00 vs 12.00 vs 18.00)
                 ======================================================== --}}
            <div class="flex flex-col justify-between rounded-3xl bg-[#f0f4f8]/80 p-5 sm:p-6 border border-slate-200/90 shadow-sm">
                @if ($sesiJam === '06:00')
                    {{-- ----------------------------------------------------
                         KOTAK KANAN JAM 06.00: 3 MT PERTAMA vs TANGKI TIMBUN
                         ---------------------------------------------------- --}}
                    <div>
                        <div class="mb-4 flex items-center justify-between">
                            <div class="inline-flex items-center gap-2.5 rounded-full bg-[#123e6b] px-5 py-2 text-white shadow-sm">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#e86a17] text-white">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                    </svg>
                                </span>
                                <span class="text-xs sm:text-sm font-bold tracking-wide">Sampel 3 MT Pertama vs Sampel Tangki Timbun</span>
                            </div>

                            @if (!auth()->user()->isSpbu())
                            <button type="button" onclick="document.getElementById('form-edit-kanan-{{ $slugSesi }}').classList.toggle('hidden')"
                                    class="no-print inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 hover:border-brand-blue hover:text-brand-blue px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm transition">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                </svg>
                                <span>Sesuaikan Nopol 3 MT & Tangki</span>
                            </button>
                            @endif
                        </div>

                        {{-- Dua Foto Bersebelahan --}}
                        <div class="mb-4 grid grid-cols-2 gap-3.5">
                            {{-- Foto 1: Visual Sales Penyaluran 3 MT --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-inner">
                                <div class="flex h-56 sm:h-64 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                                    @if ($foto06VisualMt)
                                        <img src="{{ $foto06VisualMt->url() }}" crossorigin="anonymous" alt="Visual 3 MT Pertama" class="h-full w-full object-contain">
                                    @else
                                        <div class="text-center p-3">
                                            <svg class="mx-auto h-7 w-7 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                            </svg>
                                            <p class="text-[11px] font-bold text-slate-400 mt-1">Visual Sales 3 MT</p>
                                            <p class="text-[9px] text-slate-400">{{ $produk3MtUtama }} 3 MT Pertama</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="no-print mt-2 flex items-center justify-center border-t border-slate-100 pt-1.5">
                                    <form method="POST" action="{{ route('retain-sampel.foto-sesi.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                        <input type="hidden" name="jam_label" value="06:00_visual_mt">
                                        <label for="upload-visual-mt-{{ $slugSesi }}" class="cursor-pointer inline-flex items-center gap-1 rounded-lg bg-slate-100 hover:bg-slate-200 px-2.5 py-1 text-[10px] font-bold text-slate-700 transition">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                            <span>{{ $foto06VisualMt ? 'Ganti' : 'Upload' }} 3 MT</span>
                                        </label>
                                        <input type="file" id="upload-visual-mt-{{ $slugSesi }}" name="foto" accept="image/*,application/pdf" class="hidden" onchange="this.form.submit()">
                                    </form>
                                </div>
                            </div>

                            {{-- Foto 2: Visual Tangki Timbun --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-inner">
                                <div class="flex h-56 sm:h-64 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                                    @if ($foto06VisualTangki)
                                        <img src="{{ $foto06VisualTangki->url() }}" crossorigin="anonymous" alt="Visual Tangki Timbun" class="h-full w-full object-contain">
                                    @else
                                        <div class="text-center p-3">
                                            <svg class="mx-auto h-7 w-7 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="7" y="2" width="10" height="20" rx="2"></rect>
                                                <line x1="7" y1="8" x2="17" y2="8"></line>
                                                <line x1="7" y1="12" x2="17" y2="12"></line>
                                                <line x1="7" y1="16" x2="17" y2="16"></line>
                                            </svg>
                                            <p class="text-[11px] font-bold text-slate-400 mt-1">Visual Tangki Timbun</p>
                                            <p class="text-[9px] text-slate-400">Gelas Ukur Tangki</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="no-print mt-2 flex items-center justify-center border-t border-slate-100 pt-1.5">
                                    <form method="POST" action="{{ route('retain-sampel.foto-sesi.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                        <input type="hidden" name="jam_label" value="06:00_visual_tangki">
                                        <label for="upload-visual-tangki-{{ $slugSesi }}" class="cursor-pointer inline-flex items-center gap-1 rounded-lg bg-slate-100 hover:bg-slate-200 px-2.5 py-1 text-[10px] font-bold text-slate-700 transition">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                            <span>{{ $foto06VisualTangki ? 'Ganti' : 'Upload' }} Tangki</span>
                                        </label>
                                        <input type="file" id="upload-visual-tangki-{{ $slugSesi }}" name="foto" accept="image/*,application/pdf" class="hidden" onchange="this.form.submit()">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========================================================
                         FORM EDIT STRATEGIS: SESUAIKAN PRODUK, 3 MT & TANGKI
                         (Mendukung Semua Produk + Pemilihan Tangki Cepat)
                         ======================================================== --}}
                    <div id="form-edit-kanan-{{ $slugSesi }}" x-data="{
                            selectedProduct: '{{ $produk3MtUtama }}',
                            activeTangki: '{{ $entry3Mt?->tangki_timbun ?? '' }}',
                            mt1: '{{ $nopols3Mt[0] ?? '' }}',
                            mt2: '{{ $nopols3Mt[1] ?? '' }}',
                            mt3: '{{ $nopols3Mt[2] ?? '' }}',
                            combinedNopol() {
                                return [this.mt1, this.mt2, this.mt3].filter(v => v && v.trim() !== '').join(', ');
                            }
                         }" class="hidden no-print mb-4 rounded-3xl bg-white p-5 border-2 border-brand-blue/30 shadow-xl">
                        
                        <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#0f3861] text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-black text-[#0f3861] uppercase tracking-wide">
                                        Sesuaikan Nopol 3 MT & Nomor Tangki
                                    </h4>
                                    <p class="text-[10px] text-slate-400 font-semibold">Pilih produk, masukkan unit MT penyaluran, dan pilih nomor tangki</p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('form-edit-kanan-{{ $slugSesi }}').classList.add('hidden')" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 hover:bg-slate-200 px-2.5 py-1 text-xs text-slate-500 font-bold transition">
                                <i data-lucide="x" class="h-3 w-3"></i> Tutup
                            </button>
                        </div>

                        {{-- TAB PILIHAN PRODUK --}}
                        <div class="mb-4">
                            <label class="text-[11px] font-bold text-slate-600 block mb-1.5 uppercase tracking-wider">
                                1. Pilih Produk Penyaluran :
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($produkList as $p)
                                    <button type="button" @click="selectedProduct = '{{ $p }}'"
                                            :class="selectedProduct === '{{ $p }}' ? 'bg-[#0f3861] text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition">
                                        {{ $p }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- FORM EDIT PER PRODUK --}}
                        @foreach ($produkList as $p)
                            @php
                                $entryP = $produk06Entries[$p] ?? null;
                                $nopolsP = $entryP ? $parseNopolArray($entryP->mt_nopol) : [];
                            @endphp
                            <div x-show="selectedProduct === '{{ $p }}'" x-cloak class="space-y-4">
                                <form method="POST" action="{{ $entryP ? route('retain-sampel.update', $entryP) : route('retain-sampel.store') }}" class="space-y-4">
                                    @csrf
                                    @if ($entryP) @method('PUT') @endif
                                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                    <input type="hidden" name="jam_label" value="06:00">
                                    <input type="hidden" name="produk" value="{{ $p }}">

                                    {{-- 3 INPUT MOBIL TANGKI (MT 1, MT 2, MT 3) --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-[11px] font-bold text-[#0f3861] uppercase tracking-wide">
                                                2. Daftar Nopol Mobil Tangki (3 MT Pertama {{ $p }}) :
                                            </label>
                                            <span class="text-[10px] font-semibold text-slate-400">Otomatis berurutan 1, 2, 3</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                            <div>
                                                <span class="text-[10px] font-bold text-slate-500 block mb-0.5">MT Pertama (1)</span>
                                                <input type="text" name="mt_nopol_1" id="mt_1_{{ $p }}"
                                                       value="{{ $nopolsP[0] ?? '' }}"
                                                       placeholder="Contoh: N 3762 O"
                                                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold text-slate-800 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-bold text-slate-500 block mb-0.5">MT Kedua (2)</span>
                                                <input type="text" name="mt_nopol_2" id="mt_2_{{ $p }}"
                                                       value="{{ $nopolsP[1] ?? '' }}"
                                                       placeholder="Contoh: R 3762 O"
                                                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold text-slate-800 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-bold text-slate-500 block mb-0.5">MT Ketiga (3)</span>
                                                <input type="text" name="mt_nopol_3" id="mt_3_{{ $p }}"
                                                       value="{{ $nopolsP[2] ?? '' }}"
                                                       placeholder="Contoh: B 9067 SFW"
                                                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold text-slate-800 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                                            </div>
                                        </div>
                                        {{-- Hidden input mt_nopol yang dikirim ke backend --}}
                                        <input type="hidden" name="mt_nopol" id="combined_nopol_{{ $p }}" value="{{ $entryP?->mt_nopol }}">
                                    </div>

                                    {{-- PILIHAN TANGKI TIMBUN + CHIPS CEPAT --}}
                                    <div>
                                        <label class="text-[11px] font-bold text-[#0f3861] block mb-1.5 uppercase tracking-wide">
                                            3. Nomor Tangki Timbun :
                                        </label>
                                        <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                            @foreach ($tangkiListDefault as $tk)
                                                <button type="button" onclick="document.getElementById('input_tangki_{{ $p }}').value = '{{ $tk }}'"
                                                        class="px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 hover:bg-[#0f3861] hover:text-white text-xs font-bold text-slate-700 transition">
                                                    T.{{ $tk }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="input_tangki_{{ $p }}" name="tangki_timbun"
                                                   value="{{ $entryP?->tangki_timbun ?? '' }}"
                                                   placeholder="Ketik nomor tangki, contoh: 05, 06, 09"
                                                   class="w-full max-w-xs rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold text-slate-800 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                                            <span class="text-[11px] text-slate-400 font-semibold">(Format otomatis menambahkan 'T.')</span>
                                        </div>
                                    </div>

                                    {{-- DENSITY OBS & SUHU (Jika perlu disesuaikan sekalian) --}}
                                    <div class="grid grid-cols-2 gap-3 pt-1 border-t border-slate-100">
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-500 block mb-1">Density Obs :</label>
                                            <input type="number" step="0.0001" name="density_obs" value="{{ $entryP?->density_obs ?? 0.7420 }}"
                                                   class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-800">
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-500 block mb-1">Suhu (°C) :</label>
                                            <input type="number" step="0.1" name="temperatur" value="{{ $entryP?->temperatur ?? 24 }}"
                                                   class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-800">
                                        </div>
                                    </div>

                                    {{-- TOMBOL SUBMIT --}}
                                    <div class="flex items-center gap-2 pt-2">
                                        <button type="submit" onclick="
                                            var n1 = document.getElementById('mt_1_{{ $p }}').value.trim();
                                            var n2 = document.getElementById('mt_2_{{ $p }}').value.trim();
                                            var n3 = document.getElementById('mt_3_{{ $p }}').value.trim();
                                            var all = [n1, n2, n3].filter(Boolean).join(', ');
                                            document.getElementById('combined_nopol_{{ $p }}').value = all;
                                        " class="rounded-xl bg-[#0f3861] hover:bg-[#16518a] text-white px-5 py-2.5 text-xs font-bold shadow-md transition flex items-center gap-1.5">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Simpan Perubahan ({{ $p }})
                                        </button>
                                        <button type="button" onclick="document.getElementById('form-edit-kanan-{{ $slugSesi }}').classList.add('hidden')"
                                                class="text-xs font-bold text-slate-500 hover:text-slate-700 px-3 py-2">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    {{-- Keterangan Nopol 3 MT & Tangki Timbun (OTOMATIS DARI DATABASE & TAMPIL ELEGAN) --}}
                    <div class="mt-2 grid grid-cols-2 gap-4 rounded-2xl bg-white p-4 border border-slate-200/90 shadow-sm text-xs sm:text-sm">
                        <div>
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-slate-800">Visual Sales Penyaluran {{ $produk3MtUtama }} 3 MT :</p>
                            </div>
                            @if (!empty($nopols3Mt))
                                <ol class="mt-1.5 space-y-1 font-bold text-slate-800">
                                    @foreach ($nopols3Mt as $idx => $nopol)
                                        <li class="flex items-center gap-1.5">
                                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[#0f3861] text-[11px] font-black">
                                                {{ $idx + 1 }}
                                            </span>
                                            <span>{{ $nopol }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="mt-1 text-xs text-slate-400 italic">Belum ada input nopol MT</p>
                            @endif
                        </div>
                        <div class="flex flex-col justify-start">
                            <p class="font-bold text-slate-800">Visual Tangki Timbun :</p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="inline-flex items-center justify-center rounded-xl bg-blue-50 border border-blue-200 px-3 py-1 text-base sm:text-lg font-black text-[#0f3b66]">
                                    {{ $tangki3MtFormatted }}
                                </span>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- ----------------------------------------------------
                         KOTAK KANAN: RE-TANK INDIVIDUAL (12.00 ATAU 18.00 WIB)
                         ---------------------------------------------------- --}}
                    <div>
                        <div class="mb-4 flex items-center justify-between">
                            <div class="inline-flex items-center gap-2.5 rounded-full bg-[#123e6b] px-5 py-2 text-white shadow-sm">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#e86a17] text-white">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                    </svg>
                                </span>
                                <span class="text-xs sm:text-sm font-bold tracking-wide">
                                    Re-tank (Pukul {{ str_replace(':', '.', $sesiJam) }} WIB)
                                </span>
                            </div>

                            <button type="button" onclick="document.getElementById('form-edit-retank-{{ $slugSesi }}').classList.toggle('hidden')"
                                    class="no-print inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 hover:border-brand-blue hover:text-brand-blue px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm transition">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                </svg>
                                <span>Edit Data Re-Tank</span>
                            </button>
                        </div>

                        {{-- Foto Re-Tank --}}
                        <div class="group relative mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-inner">
                            <div class="flex h-56 sm:h-64 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                                @if ($fotoSesiKanan && $fotoSesiKanan->isPdf())
                                    <a href="{{ $fotoSesiKanan->url() }}" target="_blank" class="flex flex-col items-center gap-2 text-brand-red">
                                        <svg class="h-10 w-10 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        <span class="text-xs font-bold text-red-600">Dokumen PDF Re-tank</span>
                                    </a>
                                @elseif ($fotoSesiKanan)
                                    <img src="{{ $fotoSesiKanan->url() }}" crossorigin="anonymous" alt="Foto Re-Tank {{ $sesiJam }}" class="h-full w-full object-contain">
                                @else
                                    <div class="text-center p-4">
                                        <svg class="mx-auto h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M19 19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2l4-8V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7l4 8z"/>
                                        </svg>
                                        <p class="text-xs font-bold text-slate-400 mt-2">Foto Re-Tank Pukul {{ str_replace(':', '.', $sesiJam) }}</p>
                                        <p class="text-[10px] text-slate-400">Pergantian Sampel Produk</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Form Upload Foto Re-Tank (no-print) --}}
                            <div class="no-print mt-2 flex items-center justify-between border-t border-slate-100 pt-2 px-1">
                                <span class="text-[11px] font-semibold text-slate-500">Foto Botol Re-Tank {{ str_replace(':', '.', $sesiJam) }}</span>
                                <form method="POST" action="{{ route('retain-sampel.foto-sesi.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                    <input type="hidden" name="jam_label" value="{{ $sesiJam }}">
                                    <label for="upload-retank-{{ $slugSesi }}" class="cursor-pointer inline-flex items-center gap-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 px-3 py-1 text-[11px] font-bold text-slate-700 transition">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                        <span>{{ $fotoSesiKanan ? 'Ganti Foto' : 'Upload Foto' }}</span>
                                    </label>
                                    <input type="file" id="upload-retank-{{ $slugSesi }}" name="foto" accept="image/*,application/pdf" class="hidden" onchange="this.form.submit()">
                                </form>
                            </div>
                        </div>

                        {{-- Teks Keterangan Re-Tank --}}
                        <div class="mb-3 space-y-0.5 text-xs sm:text-sm font-semibold text-slate-800">
                            <p>Update Re-tank hari ini <span class="font-bold">{{ $tanggalIndo }}</span></p>
                            <p>Pukul {{ str_replace(':', '.', $sesiJam) }} WIB adalah sebagai berikut :</p>
                        </div>
                    </div>

                    {{-- Form Edit Cepat Re-Tank (Toggled) --}}
                    <div id="form-edit-retank-{{ $slugSesi }}" class="hidden no-print mb-4 rounded-2xl bg-white p-4 border border-blue-200 shadow-md">
                        <div class="mb-3 flex items-center justify-between border-b pb-2">
                            <span class="text-xs font-bold text-[#0f3861] uppercase">Edit Data Re-Tank (Pukul {{ str_replace(':', '.', $sesiJam) }} WIB)</span>
                            <button type="button" onclick="document.getElementById('form-edit-retank-{{ $slugSesi }}').classList.add('hidden')" class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 font-bold"><i data-lucide="x" class="h-3.5 w-3.5"></i> Tutup</button>
                        </div>
                        <div class="space-y-4">
                            @foreach ($produkList as $p)
                                @php $e = $produkKananEntries[$p] ?? null; @endphp
                                <form method="POST" action="{{ $e ? route('retain-sampel.update', $e) : route('retain-sampel.store') }}" class="grid grid-cols-2 sm:grid-cols-5 gap-2 items-end border-b pb-2 last:border-b-0">
                                    @csrf
                                    @if ($e) @method('PUT') @endif
                                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                    <input type="hidden" name="jam_label" value="{{ $sesiJam }}">
                                    <input type="hidden" name="produk" value="{{ $p }}">
                                    <div>
                                        <span class="text-[10px] font-bold text-[#0f3861] block mb-1">{{ $p }}</span>
                                        <input type="text" name="mt_nopol" value="{{ $e?->mt_nopol }}" placeholder="Nopol MT" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 block mb-1">Density Obs</span>
                                        <input type="number" step="0.0001" name="density_obs" value="{{ $e?->density_obs }}" placeholder="0.7420" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 block mb-1">Suhu (°C)</span>
                                        <input type="number" step="0.1" name="temperatur" value="{{ $e?->temperatur }}" placeholder="24" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 block mb-1">Tangki</span>
                                        <input type="text" name="tangki_timbun" value="{{ $e?->tangki_timbun }}" placeholder="9" class="w-full rounded-lg border border-slate-200 px-2 py-1 text-xs">
                                    </div>
                                    <div>
                                        <button type="submit" class="w-full rounded-lg bg-brand-blue hover:bg-brand-blueDark text-white px-2 py-1 text-xs font-bold transition">Simpan</button>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tabel Data Re-Tank --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-300 bg-white shadow-sm">
                        @if (!empty($produkKananAktif))
                            <table class="w-full text-center text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[#6c8296] text-white">
                                        <th class="border-r border-slate-400 px-2.5 py-2 text-left font-bold text-[11px] uppercase tracking-wider">Produk</th>
                                        @foreach ($produkKananAktif as $p)
                                            <th class="border-r last:border-r-0 border-slate-400 px-2.5 py-2 font-bold text-[11px] uppercase tracking-wider">{{ $p }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-slate-800">
                                    {{-- MT Nopol --}}
                                    <tr class="bg-slate-50/70 font-semibold">
                                        <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">MT Nopol</td>
                                        @foreach ($produkKananAktif as $p)
                                            @php $e = $produkKananEntries[$p] ?? null; @endphp
                                            <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-bold">{{ $e?->mt_nopol ?: '-' }}</td>
                                        @endforeach
                                    </tr>
                                    {{-- Density Obs --}}
                                    <tr>
                                        <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">Density Obs</td>
                                        @foreach ($produkKananAktif as $p)
                                            @php $e = $produkKananEntries[$p] ?? null; @endphp
                                            <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-medium">{{ $e ? $formatAngka($e->density_obs, 3) : '-' }}</td>
                                        @endforeach
                                    </tr>
                                    {{-- Density'15 --}}
                                    <tr class="bg-blue-50/40">
                                        <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-[#0f3b66]">Density'15</td>
                                        @foreach ($produkKananAktif as $p)
                                            @php $e = $produkKananEntries[$p] ?? null; @endphp
                                            <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-bold text-[#0f3b66]">{{ $e ? $formatAngka($e->density_15, 4) : '-' }}</td>
                                        @endforeach
                                    </tr>
                                    {{-- Temperatur --}}
                                    <tr>
                                        <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">Temperatur</td>
                                        @foreach ($produkKananAktif as $p)
                                            @php $e = $produkKananEntries[$p] ?? null; @endphp
                                            <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-medium">{{ $e ? $formatSuhu($e->temperatur) : '-' }}</td>
                                        @endforeach
                                    </tr>
                                    {{-- Tangki Timbun --}}
                                    <tr class="bg-slate-50/70">
                                        <td class="border-r border-slate-200 px-2.5 py-1.5 text-left font-bold text-slate-700">Tangki Timbun</td>
                                        @foreach ($produkKananAktif as $p)
                                            @php $e = $produkKananEntries[$p] ?? null; @endphp
                                            <td class="border-r last:border-r-0 border-slate-200 px-2.5 py-1.5 font-medium">{{ $e?->tangki_timbun ?: '-' }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <div class="py-6 text-center text-xs text-slate-400 italic">
                                Belum ada data Re-tank yang diinput untuk sesi pukul {{ str_replace(':', '.', $sesiJam) }} WIB.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ========================================================
         BAGIAN BAWAH: TABEL MATRIKS GABUNGAN SEMUA JAM (06.00, 12.00, 18.00)
         & REKAP SELURUH MOBIL TANGKI (MT) HARI INI
         ======================================================== --}}
    <div class="relative z-10 mt-8 border-t-2 border-slate-100 pt-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#0f3861] text-white">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </span>
                <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-[#0f3861]">
                    Hasil Gabungan Pengujian Seluruh Jam Hari Ini (06.00, 12.00 & 18.00 WIB)
                </h3>
            </div>
            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                Penyaluran 06.00 WIB + Re-Tank 12.00 WIB + Re-Tank 18.00 WIB
            </span>
        </div>

        {{-- Tabel Matriks Lengkap Produk × Sesi Jam (06.00, 12.00, 18.00) --}}
        <div class="overflow-x-auto rounded-2xl border border-slate-300 bg-white shadow-sm mb-5">
            <table class="w-full text-center text-xs border-collapse">
                <thead>
                    <tr class="bg-[#0f3861] text-white">
                        <th rowspan="2" class="border-r border-slate-400 px-3 py-2 text-left font-black uppercase text-[11px]">Produk</th>
                        @foreach (['06:00', '12:00', '18:00'] as $j)
                            <th colspan="5" class="border-r last:border-r-0 border-slate-400 px-3 py-2 font-black uppercase text-[11px]">
                                Pukul {{ str_replace(':', '.', $j) }} WIB {{ $j === '06:00' ? '(Awal Penyaluran)' : '(Re-Tank)' }}
                            </th>
                        @endforeach
                    </tr>
                    <tr class="bg-[#6c8296] text-white text-[10px] uppercase font-bold">
                        @foreach (['06:00', '12:00', '18:00'] as $j)
                            <th class="border-r border-slate-400 px-2 py-1.5">MT Nopol</th>
                            <th class="border-r border-slate-400 px-2 py-1.5">Density Obs</th>
                            <th class="border-r border-slate-400 px-2 py-1.5">Density'15</th>
                            <th class="border-r border-slate-400 px-2 py-1.5">Suhu</th>
                            <th class="border-r last:border-r-0 border-slate-400 px-2 py-1.5">Tangki</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-800">
                    @foreach ($semuaProdukHariIni as $p)
                        <tr class="hover:bg-slate-50 font-medium">
                            <td class="border-r border-slate-200 px-3 py-2 text-left font-black text-[#0f3861] bg-slate-50/70">{{ $p }}</td>
                            @foreach (['06:00', '12:00', '18:00'] as $j)
                                @php $e = $rekap[$j][$p] ?? null; @endphp
                                @if ($e)
                                    <td class="border-r border-slate-200 px-2 py-1.5 font-bold">{{ $e->mt_nopol ?: '-' }}</td>
                                    <td class="border-r border-slate-200 px-2 py-1.5">{{ $formatAngka($e->density_obs, 3) }}</td>
                                    <td class="border-r border-slate-200 px-2 py-1.5 font-bold text-brand-blue">{{ $formatAngka($e->density_15, 4) }}</td>
                                    <td class="border-r border-slate-200 px-2 py-1.5">{{ $formatSuhu($e->temperatur) }}°C</td>
                                    <td class="border-r last:border-r-0 border-slate-200 px-2 py-1.5 font-semibold">{{ $e->tangki_timbun ?: '-' }}</td>
                                @else
                                    <td colspan="5" class="border-r last:border-r-0 border-slate-200 px-2 py-1.5 text-center text-slate-300 italic">—</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Rekap Nopol MT Per Produk Hari Ini --}}
        @if (!empty($nopolRekapLengkap))
            <div class="rounded-2xl bg-[#f0f4f8]/90 p-4 border border-slate-200">
                <p class="text-[11px] font-black text-slate-700 uppercase tracking-wide mb-2.5">
                    Daftar Mobil Tangki (MT) Penyaluran & Re-Tank Hari Ini:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($nopolRekapLengkap as $prod => $items)
                        <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-1.5 mb-2">
                                <span class="text-xs font-black text-[#0f3861] uppercase">{{ $prod }}</span>
                                <span class="text-[10px] font-bold text-slate-400">{{ count($items) }} Unit MT</span>
                            </div>
                            <ol class="space-y-1 text-xs font-bold text-slate-800">
                                @foreach ($items as $idx => $it)
                                    <li class="flex items-center justify-between">
                                        <span>{{ $idx + 1 }}. &nbsp;{{ $it['nopol'] }}</span>
                                        <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ str_replace(':', '.', $it['jam']) }} WIB</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
