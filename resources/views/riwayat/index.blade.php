@extends('layouts.app')
@section('title', 'Riwayat Hasil Uji')

@section('content')

<!-- ===== Ringkasan singkat ===== -->
<div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Total Hasil</p>
        <p class="mt-1 text-2xl font-extrabold text-slate-800">{{ $riwayat->total() }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Lulus (Pass)</p>
        <p class="mt-1 text-2xl font-extrabold text-emerald-600">{{ $ringkasan['pass'] }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Marginal</p>
        <p class="mt-1 text-2xl font-extrabold text-amber-500">{{ $ringkasan['marginal'] }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Gagal (Fail)</p>
        <p class="mt-1 text-2xl font-extrabold text-brand-red">{{ $ringkasan['fail'] }}</p>
    </div>
</div>

<!-- ===== Filter pencarian ===== -->
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
    <div class="mb-4 flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-blue/10 text-brand-blue">
            <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
        </div>
        <p class="text-sm font-bold text-slate-700">Filter &amp; Pencarian</p>
    </div>

    <form method="GET" class="grid grid-cols-1 gap-3.5 lg:grid-cols-12 lg:items-end">
        <div class="lg:col-span-4">
            <label class="mb-1.5 block text-xs font-semibold text-slate-500">Sampel / No. KKW</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i data-lucide="search" class="h-[18px] w-[18px]"></i>
                </span>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama sampel / nomor KKW..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-11 pr-3.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
            </div>
        </div>

        <div class="lg:col-span-3">
            <label class="mb-1.5 block text-xs font-semibold text-slate-500">Jenis Uji</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i data-lucide="flask-conical" class="h-[18px] w-[18px]"></i>
                </span>
                <select name="jenis_uji_id"
                        class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-11 pr-9 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    <option value="">Semua Jenis Uji</option>
                    @foreach ($daftarJenisUji as $jenis)
                        <option value="{{ $jenis->id }}" @selected(request('jenis_uji_id') == $jenis->id)>{{ $jenis->nama }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                </span>
            </div>
        </div>

        <div class="lg:col-span-2">
            <label class="mb-1.5 block text-xs font-semibold text-slate-500">Dari Tanggal</label>
            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </div>

        <div class="lg:col-span-2">
            <label class="mb-1.5 block text-xs font-semibold text-slate-500">Sampai Tanggal</label>
            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </div>

        <div class="flex gap-2 lg:col-span-1">
            <button type="submit"
                    class="inline-flex w-full shrink-0 items-center justify-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition hover:-translate-y-0.5 hover:bg-brand-blueDark hover:shadow-lg hover:shadow-brand-blue/30 active:translate-y-0">
                <i data-lucide="search" class="h-4 w-4"></i>
                <span class="lg:hidden">Cari</span>
            </button>
        </div>
    </form>

    @if (request()->anyFilled(['cari', 'jenis_uji_id', 'dari_tanggal', 'sampai_tanggal']))
        <div class="mt-3.5 flex items-center gap-2 border-t border-slate-100 pt-3.5">
            <span class="text-xs font-medium text-slate-400">Filter aktif —</span>
            <a href="{{ route('riwayat.index') }}" class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500 transition hover:bg-slate-200">
                <i data-lucide="x" class="h-3 w-3"></i> Reset semua
            </a>
        </div>
    @endif
</div>

<!-- ===== Tabel hasil uji ===== -->
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
        <div>
            <p class="text-sm font-semibold text-slate-700">
                Ditemukan <span class="rounded-md bg-brand-blue/10 px-2 py-0.5 font-bold text-brand-blue">{{ $riwayat->total() }}</span> hasil uji
            </p>
            @if ($riwayat->total() > 0)
                <p class="text-xs text-slate-400 mt-0.5">Menampilkan {{ $riwayat->firstItem() }}–{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} data</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Tombol Export Excel Keseluruhan Data --}}
            <a href="{{ url('/riwayat/export-excel-semua') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-800 hover:shadow">
                <i data-lucide="file-spreadsheet" class="h-4 w-4"></i>
                <span>Unduh Excel Keseluruhan Data</span>
            </a>

            @if (request()->anyFilled(['cari', 'jenis_uji_id', 'dari_tanggal', 'sampai_tanggal']))
                {{-- Tombol Export Excel Sesuai Filter Aktif --}}
                <a href="{{ url('/riwayat/export-excel') . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-slate-900 hover:shadow">
                    <i data-lucide="filter" class="h-4 w-4"></i>
                    <span>Unduh Excel (Sesuai Filter)</span>
                </a>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3.5">Waktu</th>
                    <th class="px-5 py-3.5">Jenis Uji</th>
                    <th class="px-5 py-3.5">Sampel</th>
                    <th class="px-5 py-3.5">No. KKW</th>
                    <th class="px-5 py-3.5">Status Mutu</th>
                    <th class="px-5 py-3.5">Petugas</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($riwayat as $item)
                    <tr class="group transition hover:bg-brand-blue/[0.03]">
                        <td class="whitespace-nowrap px-5 py-4 text-slate-500">
                            <p class="font-medium text-slate-700">{{ $item->waktu_uji->format('d/m/Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $item->waktu_uji->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $iconMap = [
                                    'FLASHPOINT' => 'flame',
                                    'COLORIMETER' => 'palette',
                                    'VISKOSITAS' => 'waves',
                                    'WATER_CONTENT' => 'droplets',
                                    'TAN' => 'flask-conical',
                                    'DESTILASI' => 'thermometer',
                                    'DENSITY' => 'gauge',
                                    'RON' => 'zap',
                                    'SULFUR' => 'atom',
                                ];
                                $ujiIcon = $iconMap[$item->jenisUji->kode] ?? ($item->jenisUji->icon ?: 'flask-conical');
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-brand-blue/10 px-2.5 py-1 text-xs font-bold text-brand-blue">
                                <i data-lucide="{{ $ujiIcon }}" class="h-3.5 w-3.5"></i>
                                {{ $item->jenisUji->nama }}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->nama_sampel }}</td>
                        <td class="px-5 py-4 text-slate-500">
                            @if ($item->nomor_kkw)
                                <span class="rounded-md bg-slate-100 px-2 py-1 font-mono text-xs text-slate-600">{{ $item->nomor_kkw }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $badge = match ($item->verdict) {
                                    \App\Services\SpecEngine::PASS => ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'circle-check'],
                                    \App\Services\SpecEngine::FAIL => ['bg-red-50 text-brand-red ring-red-200', 'circle-x'],
                                    \App\Services\SpecEngine::MARGINAL => ['bg-amber-50 text-amber-600 ring-amber-200', 'triangle-alert'],
                                    default => ['bg-slate-100 text-slate-600 ring-slate-200', 'circle-dot'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $badge[0] }}">
                                <i data-lucide="{{ $badge[1] }}" class="h-3.5 w-3.5"></i>
                                {{ $item->verdict }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-500">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-blue/60 to-brand-dark text-[10px] font-bold text-white">
                                    {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                </span>
                                <span class="truncate">{{ $item->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1 opacity-80 transition group-hover:opacity-100">
                                <a href="{{ route('riwayat.show', $item) }}" title="Lihat detail"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-brand-blue transition hover:bg-brand-blue/10">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                <a href="{{ route('riwayat.cetak', $item) }}" target="_blank" title="Cetak hasil uji"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                                    <i data-lucide="printer" class="h-4 w-4"></i>
                                </a>
                                @if (!auth()->user()->isSpbu())
                                    <a href="{{ route('riwayat.edit', $item) }}" title="Edit hasil uji"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </a>
                                    <form id="form-hapus-{{ $item->id }}" method="POST" action="{{ route('riwayat.destroy', $item) }}" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" title="Hapus hasil uji"
                                                @click="window.__confirmDeleteModal.show(
                                                    document.getElementById('form-hapus-{{ $item->id }}'),
                                                    'Hapus Hasil Uji Ini?',
                                                    'Sampel {{ $item->nama_sampel }} ({{ $item->jenisUji->nama }}) akan dihapus permanen dari Riwayat.'
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
                                <i data-lucide="clock" class="h-8 w-8 text-slate-300"></i>
                            </div>
                            <p class="mt-4 text-sm font-semibold text-slate-600">Belum ada data hasil uji</p>
                            <p class="mt-1 text-xs text-slate-400">Hasil uji yang disimpan akan muncul di sini secara otomatis.</p>
                            <a href="{{ route('ujibbm.index') }}"
                               class="mt-5 inline-flex items-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-brand-blue/25 transition hover:bg-brand-blueDark">
                                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                                Mulai Uji Mutu BBM
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($riwayat->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $riwayat->links() }}
        </div>
    @endif
</div>
@endsection
