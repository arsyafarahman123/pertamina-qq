@extends('layouts.app')
@section('title', 'Detail Hasil Uji')

@section('content')
<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('riwayat.index') }}" class="inline-flex items-center gap-1 font-medium hover:text-brand-blue">
        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Riwayat Hasil Uji
    </a>
    <span class="text-slate-300">/</span>
    <span class="font-semibold text-slate-700">#{{ $hasilUji->id }}</span>
</div>

<div class="max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
    <div class="border-b border-slate-100 bg-gradient-to-r from-brand-red/5 to-brand-blue/5 px-6 py-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $hasilUji->jenisUji->nama }}</h2>
                <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                    <i data-lucide="calendar-days" class="h-4 w-4"></i>
                    {{ $hasilUji->waktu_uji->translatedFormat('d F Y, H:i') }} WIB
                </p>
            </div>
            <div class="text-right">
                <span class="rounded-full bg-brand-blue px-3 py-1 text-xs font-semibold text-white shadow-sm">{{ $hasilUji->nama_sampel }}</span>
                @php
                    $badge = match ($evaluasi['verdict']) {
                        \App\Services\SpecEngine::PASS => 'bg-emerald-100 text-emerald-700',
                        \App\Services\SpecEngine::FAIL => 'bg-red-100 text-red-700',
                        \App\Services\SpecEngine::MARGINAL => 'bg-amber-100 text-amber-700',
                        default => 'bg-slate-100 text-slate-600',
                    };
                @endphp
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold uppercase {{ $badge }}">
                        <i data-lucide="circle-dot" class="h-3 w-3"></i>
                        {{ $evaluasi['verdict'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 bg-white px-6 py-3">
        <a href="{{ route('riwayat.cetak', $hasilUji) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-red px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-brand-blue">
            <i data-lucide="file-text" class="h-4 w-4"></i>
            Cetak Sertifikat Mutu
        </a>
        @if (!auth()->user()->isSpbu())
            <a href="{{ route('riwayat.edit', $hasilUji) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-blue/10 px-3 py-1.5 text-sm font-semibold text-brand-blue transition hover:bg-brand-blue/20">
                <i data-lucide="pencil" class="h-4 w-4"></i>
                Edit
            </a>
            <form id="form-hapus-{{ $hasilUji->id }}" method="POST" action="{{ route('riwayat.destroy', $hasilUji) }}" class="contents">
                @csrf
                @method('DELETE')
                <button type="button"
                        @click="window.__confirmDeleteModal.show(
                            document.getElementById('form-hapus-{{ $hasilUji->id }}'),
                            'Hapus Hasil Uji Ini?',
                            'Sampel {{ $hasilUji->nama_sampel }} ({{ $hasilUji->jenisUji->nama }}) akan dihapus permanen dari Riwayat.'
                        )"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-1.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                    Hapus
                </button>
            </form>
        @endif
    </div>

    <div class="px-6 py-5">
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-400">Nomor KKW / Kereta</dt>
                <dd class="mt-0.5 font-semibold text-slate-800">{{ $hasilUji->nomor_kkw ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-400">Petugas</dt>
                <dd class="mt-0.5 flex items-center gap-2 font-semibold text-slate-800">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-blue/10 text-[10px] font-bold text-brand-blue">{{ strtoupper(substr($hasilUji->user->name ?? 'U', 0, 1)) }}</span>
                    {{ $hasilUji->user->name }}
                </dd>
            </div>
        </dl>

        <div class="mt-5 border-t border-slate-100 pt-5">
            <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Hasil Pengukuran &amp; Penilaian Spesifikasi</p>
            <div class="divide-y divide-slate-50 rounded-xl border border-slate-100 bg-slate-50/50 text-sm">
                @forelse ($evaluasi['detail'] as $row)
                    @php
                        $st = match ($row['status']) {
                            \App\Services\SpecEngine::PASS => 'bg-emerald-100 text-emerald-700',
                            \App\Services\SpecEngine::FAIL => 'bg-red-100 text-red-700',
                            \App\Services\SpecEngine::MARGINAL => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <div class="flex items-center justify-between gap-4 px-4 py-2.5">
                        <div>
                            <dt class="text-slate-500">{{ $row['label'] }}</dt>
                            <dd class="text-xs text-slate-400">
                                @if ($row['min'] !== null && $row['max'] !== null)
                                    Spesifikasi: {{ $row['min'] }} – {{ $row['max'] }} {{ $row['satuan'] }}
                                @elseif ($row['min'] !== null)
                                    Spesifikasi: Min {{ $row['min'] }} {{ $row['satuan'] }}
                                @elseif ($row['max'] !== null)
                                    Spesifikasi: Maks {{ $row['max'] }} {{ $row['satuan'] }}
                                @else
                                    Tanpa batas spesifikasi
                                @endif
                            </dd>
                        </div>
                        <div class="text-right">
                            <dd class="font-semibold text-slate-800">{{ $row['nilai'] ?: '—' }} <span class="text-xs text-slate-400">{{ $row['satuan'] }}</span></dd>
                            <span class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $st }}">{{ $row['status'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-4 text-center text-slate-400">Tidak ada parameter yang dinilai terhadap spesifikasi.</div>
                @endforelse
            </div>
        </div>

        @if ($hasilUji->catatan)
            <div class="mt-5 border-t border-slate-100 pt-5">
                <p class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400">Catatan</p>
                <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm leading-relaxed text-amber-800">{{ $hasilUji->catatan }}</p>
            </div>
        @endif

        @if ($hasilUji->foto_bukti)
            <div class="mt-5 border-t border-slate-100 pt-5">
                <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Foto Bukti Pengujian</p>
                @if ($hasilUji->fotoBuktiIsGambar())
                    <a href="{{ $hasilUji->fotoBuktiUrl() }}" target="_blank">
                        <img src="{{ $hasilUji->fotoBuktiUrl() }}" alt="Foto bukti pengujian" class="w-full rounded-xl border border-slate-200 object-contain">
                    </a>
                @else
                    <a href="{{ $hasilUji->fotoBuktiUrl() }}" target="_blank"
                       class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-brand-blue hover:bg-slate-100">
                        <i data-lucide="file-text" class="h-5 w-5 shrink-0"></i>
                        <span class="truncate">{{ $hasilUji->fotoBuktiNamaFile() }}</span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
