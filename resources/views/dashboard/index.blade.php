@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $badgeClass = function ($verdict) {
        return match ($verdict) {
            \App\Services\SpecEngine::PASS => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            \App\Services\SpecEngine::FAIL => 'bg-red-100 text-red-700 ring-red-200',
            \App\Services\SpecEngine::MARGINAL => 'bg-amber-100 text-amber-700 ring-amber-200',
            default => 'bg-slate-100 text-slate-600 ring-slate-200',
        };
    };
@endphp

<!-- ===================== Hero Banner Pertamina ===================== -->
<div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-r from-brand-red via-red-600 to-brand-blue p-6 shadow-xl shadow-red-500/10 sm:p-8">
    <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-10 bottom-0 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-12 w-auto drop-shadow-lg sm:h-14">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/70">Fuel Maos &middot; Fuel Quality Control</p>
                <h1 class="text-xl font-extrabold text-white sm:text-2xl">Dashboard Mutu BBM</h1>
                <p class="mt-0.5 text-sm text-white/80">Ringkasan pengujian spesifikasi & SOP — {{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('ujibbm.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-brand-red shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">
                <i data-lucide="clipboard-check" class="h-4 w-4"></i>
                Uji Spesifikasi BBM
            </a>
            <a href="{{ route('uji.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-bold text-white ring-1 ring-inset ring-white/40 backdrop-blur transition hover:bg-white/25">
                <i data-lucide="book-open-check" class="h-4 w-4"></i>
                Panduan Uji SOP
            </a>
        </div>
    </div>
</div>

<!-- ===================== Modul Operasional Fuel Terminal (gaya S&D One) ===================== -->
<div class="mb-6">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-500">Modul Operasional Fuel Terminal Maos</h2>
        <span class="text-xs text-slate-400">{{ now()->translatedFormat('d F Y') }}</span>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
        {{-- Penerimaan & Retain Sampel — data ASLI dari Rekap Retain Sampel MT --}}
        <a href="{{ route('retain-sampel.index') }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card transition hover:-translate-y-0.5 hover:shadow-lift">
            <div class="bg-gradient-to-r from-brand-blue to-indigo-600 px-5 py-3">
                <p class="text-sm font-bold text-white">Penerimaan &amp; Retain Sampel</p>
            </div>
            <div class="p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Bulan Ini</p>
                <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ $modulPenerimaan['total'] }}</p>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-slate-500"><i data-lucide="image" class="h-3.5 w-3.5"></i> Ada Foto Bukti</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ $modulPenerimaan['ada_foto'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-slate-500"><i data-lucide="image-off" class="h-3.5 w-3.5"></i> Belum Ada Foto</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">{{ $modulPenerimaan['belum_foto'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2">
                        <span class="flex items-center gap-1.5 text-slate-400"><i data-lucide="database" class="h-3.5 w-3.5"></i> Total Seluruh Riwayat</span>
                        <span class="text-xs font-bold text-slate-500">{{ $modulPenerimaan['total_semua'] }}</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Penyaluran — data ASLI dari Checklist Mobil Tangki --}}
        <a href="{{ route('checklist-mt-maos.index') }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card transition hover:-translate-y-0.5 hover:shadow-lift">
            <div class="bg-gradient-to-r from-brand-red to-rose-600 px-5 py-3">
                <p class="text-sm font-bold text-white">Penyaluran &middot; Checklist Mobil Tangki</p>
            </div>
            <div class="p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Checklist</p>
                <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ $modulPenyaluran['total'] }}</p>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-slate-500"><i data-lucide="badge-check" class="h-3.5 w-3.5"></i> Sesuai Standar</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ $modulPenyaluran['sesuai'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-slate-500"><i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i> Ada Temuan</span>
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $modulPenyaluran['temuan'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2">
                        <span class="flex items-center gap-1.5 text-slate-400"><i data-lucide="calendar-clock" class="h-3.5 w-3.5"></i> 7 Hari Terakhir</span>
                        <span class="text-xs font-bold text-slate-500">{{ $modulPenyaluran['7_hari'] }}</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Uji Spesifikasi BBM — ringkas, arahkan ke KPI di bawah --}}
        <a href="{{ route('ujibbm.index') }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card transition hover:-translate-y-0.5 hover:shadow-lift">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3">
                <p class="text-sm font-bold text-white">Uji Spesifikasi BBM</p>
            </div>
            <div class="p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">On-Spec Bulan Ini</p>
                <p class="mt-1 text-3xl font-extrabold text-slate-900">{{ $persenBbmOnSpec }}%</p>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-slate-500"><i data-lucide="flask-conical" class="h-3.5 w-3.5"></i> Total Diuji</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">{{ $totalBbmBulanIni }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-slate-500"><i data-lucide="circle-check" class="h-3.5 w-3.5"></i> Lulus (PASS)</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ $totalBbmPass ?? '' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2">
                        <span class="flex items-center gap-1.5 text-slate-400"><i data-lucide="arrow-right" class="h-3.5 w-3.5"></i> Lihat detail</span>
                        <span class="text-xs font-bold text-brand-blue">Buka modul →</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- KPI Utama -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-card transition hover:shadow-lift">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-red to-brand-blue"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Hasil Uji Bulan Ini</p>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">{{ $totalHasilBulanIni }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-blue/10 text-brand-blue">
                <i data-lucide="flask-conical" class="h-6 w-6"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-card transition hover:shadow-lift">
        <div class="absolute inset-x-0 top-0 h-1 bg-emerald-500"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">On-Spec (PASS)</p>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-emerald-600">{{ $persenOnSpec }}%</p>
                <p class="mt-1 text-xs text-slate-400">{{ $verdictCounts['PASS'] }} dari {{ $totalHasilBulanIni }} hasil uji</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <i data-lucide="circle-check" class="h-6 w-6"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-card transition hover:shadow-lift">
        <div class="absolute inset-x-0 top-0 h-1 bg-amber-500"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Marginal</p>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-amber-500">{{ $verdictCounts['MARGINAL'] }}</p>
                <p class="mt-1 text-xs text-slate-400">Mendekati batas spesifikasi</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                <i data-lucide="triangle-alert" class="h-6 w-6"></i>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-card transition hover:shadow-lift">
        <div class="absolute inset-x-0 top-0 h-1 bg-red-500"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Off-Spec (FAIL)</p>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-red-600">{{ $verdictCounts['FAIL'] }}</p>
                <p class="mt-1 text-xs text-slate-400">Perlu tindak lanjut</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600">
                <i data-lucide="circle-alert" class="h-6 w-6"></i>
            </div>
        </div>
    </div>
</div>

<!-- Off-spec alert -->
@if (count($offSpec))
    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4">
        <div class="flex items-center gap-2 font-bold text-red-700">
            <i data-lucide="circle-alert" class="h-5 w-5"></i>
            Peringatan Off-Spec
        </div>
        <ul class="mt-2 space-y-1.5">
            @foreach ($offSpec as $item)
                <li class="flex flex-wrap items-center gap-2 text-sm text-red-800">
                    <span class="font-semibold">{{ $item->nama_sampel }}</span>
                    <span>· {{ $item->jenisUji->nama }}</span>
                    <span>· {{ $item->waktu_uji->translatedFormat('d M Y, H:i') }}</span>
                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold uppercase text-red-700 ring-1 ring-red-200">{{ $item->verdict }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<!-- ===================== Rekap Uji Spesifikasi BBM (Gasoline & Gasoil) ===================== -->
<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-red to-brand-blue text-white shadow-md">
                <i data-lucide="clipboard-check" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Uji Spesifikasi BBM</h2>
                <p class="text-xs text-slate-400">Kesesuaian mutu tiap produk terhadap batas spesifikasi Dirjen Migas — {{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-2xl font-extrabold text-slate-900">{{ $totalBbmBulanIni }}</p>
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Total Diuji</p>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div class="text-right">
                <p class="text-2xl font-extrabold text-emerald-600">{{ $persenBbmOnSpec }}%</p>
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">On-Spec</p>
            </div>
            <a href="{{ route('ujibbm.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-red px-3 py-2 text-sm font-semibold text-white transition hover:bg-brand-blue">
                Uji Sekarang
                <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 divide-y divide-slate-100 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
        @php
            $kategoriInfo = [
                'gasoline' => ['label' => 'Gasoline', 'sub' => 'Bensin', 'icon' => 'flame', 'from' => 'from-brand-red', 'to' => 'to-red-500', 'text' => 'text-brand-red', 'bg' => 'bg-brand-red/5'],
                'gasoil' => ['label' => 'Gasoil', 'sub' => 'Diesel', 'icon' => 'settings', 'from' => 'from-brand-blue', 'to' => 'to-brand-blue/50', 'text' => 'text-brand-blue', 'bg' => 'bg-brand-blue/5'],
            ];
        @endphp

        @foreach ($kategoriInfo as $katKey => $info)
            <div class="p-6">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br {{ $info['from'] }} {{ $info['to'] }} text-white shadow-sm">
                        <i data-lucide="{{ $info['icon'] }}" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $info['label'] }}</p>
                        <p class="text-xs text-slate-400">{{ $info['sub'] }} &middot; {{ collect($bbmRekap[$katKey])->sum('jumlah') }} sampel diuji bulan ini</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($bbmRekap[$katKey] as $produk)
                        <div class="flex items-center justify-between rounded-xl {{ $info['bg'] }} px-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $produk['nama'] }}</p>
                                <p class="text-[11px] text-slate-400">
                                    {{ $produk['jumlah'] }} uji
                                    @if ($produk['jumlah'] > 0)
                                        &middot; {{ $produk['pass'] }} PASS / {{ $produk['fail'] }} FAIL
                                    @endif
                                </p>
                            </div>
                            @if ($produk['terakhir'])
                                @php
                                    $b = match ($produk['terakhir']) {
                                        \App\Services\SpecEngine::PASS => 'bg-emerald-100 text-emerald-700',
                                        \App\Services\SpecEngine::FAIL => 'bg-red-100 text-red-700',
                                        \App\Services\SpecEngine::MARGINAL => 'bg-amber-100 text-amber-700',
                                        default => 'bg-slate-100 text-slate-500',
                                    };
                                @endphp
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $b }}">{{ $produk['terakhir'] }}</span>
                            @else
                                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Belum diuji</span>
                            @endif
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Grafik tren -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card lg:col-span-2">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Tren Status Mutu</h2>
                <p class="text-xs text-slate-400">Jumlah PASS / FAIL / MARGINAL per hari — {{ now()->translatedFormat('F Y') }}</p>
            </div>
            <a href="{{ route('uji.index') }}" class="inline-flex items-center gap-1 rounded-lg bg-brand-red px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-brand-blue">
                Mulai pengujian
                <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
        </div>

        <div class="h-72">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Rekap per parameter + aktivitas -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <h2 class="mb-1 text-base font-bold text-slate-900">Rekap per Parameter</h2>
        <p class="mb-5 text-xs text-slate-400">Jumlah pengujian per alat &mdash; Panduan Uji (SOP)</p>
        @php $max = $rekapPerJenis->max('jumlah_bulan_ini') ?: 1; @endphp
        <div class="space-y-4">
            @forelse ($rekapPerJenis as $jenis)
                <div>
                    <div class="mb-1.5 flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700">{{ $jenis->nama }}</span>
                        <span class="font-medium text-slate-500">{{ $jenis->jumlah_bulan_ini }} uji</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-red to-brand-blue transition-all" style="width: {{ max(0, $jenis->jumlah_bulan_ini / $max * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-slate-400">Belum ada data pengujian bulan ini.</p>
            @endforelse
        </div>

        <div class="mt-6 border-t border-slate-100 pt-5">
            <h2 class="mb-4 text-base font-bold text-slate-900">Aktivitas Terbaru</h2>
            <div class="space-y-4">
                @forelse ($aktivitasTerbaru as $item)
                    @php
                        $vBadge = match ($item->evaluasi['verdict'] ?? \App\Services\SpecEngine::evaluate($item->data_hasil, $item->nama_sampel)['verdict']) {
                            \App\Services\SpecEngine::PASS => 'bg-emerald-100 text-emerald-700',
                            \App\Services\SpecEngine::FAIL => 'bg-red-100 text-red-700',
                            \App\Services\SpecEngine::MARGINAL => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-500',
                        };
                        $vLabel = $item->evaluasi['verdict'] ?? \App\Services\SpecEngine::evaluate($item->data_hasil, $item->nama_sampel)['verdict'];
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-blue/10 text-xs font-bold text-brand-blue">
                            {{ strtoupper(substr($item->jenisUji->nama ?? 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $item->jenisUji->nama }} — {{ $item->nama_sampel }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $item->user->name }} · {{ $item->waktu_uji->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $vBadge }}">{{ $vLabel }}</span>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <i data-lucide="clock" class="mx-auto mb-3 h-10 w-10 text-slate-300"></i>
                        <p class="text-sm text-slate-400">Belum ada aktivitas pengujian.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('trendChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        label: 'PASS',
                        data: @json($trendPass),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,.12)',
                        fill: true,
                        tension: .35,
                        borderWidth: 2,
                    },
                    {
                        label: 'MARGINAL',
                        data: @json($trendMarginal),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,.10)',
                        fill: true,
                        tension: .35,
                        borderWidth: 2,
                    },
                    {
                        label: 'FAIL',
                        data: @json($trendFail),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,.10)',
                        fill: true,
                        tension: .35,
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    });
</script>
@endsection
