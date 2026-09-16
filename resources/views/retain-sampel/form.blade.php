@extends('layouts.app')
@section('title', $entry ? 'Edit Rekap Retain Sampel' : 'Input Retain Sampel Penyaluran MT')

@section('content')
<div class="mx-auto max-w-2xl">
    {{-- Top Navigation --}}
    <div class="mb-4">
        <a href="{{ route('retain-sampel.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-blue transition">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke rekap
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i data-lucide="alert-circle" class="h-4 w-4 text-red-600"></i> Ada beberapa kesalahan input:
            </div>
            <ul class="list-inside list-disc pl-2 text-xs space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KARTU FORM INPUT UTAMA (TAMPILAN ASLI COMPACT) --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-card">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h1 class="text-xl font-extrabold text-slate-800">
                {{ $entry ? 'Edit Retain Sampel Penyaluran MT' : 'Input Retain Sampel Penyaluran MT' }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Isi Density Obs & Suhu — Density '15 dihitung otomatis oleh sistem (rumus koreksi ASTM Tabel 53/54), tidak perlu buka tabel manual.
            </p>
            @if($entry)
                <p class="text-[11px] text-slate-400 mt-1">
                    Data pertama kali diinput: <b class="text-slate-600">{{ $entry->created_at->translatedFormat('d M Y, H:i') }} WIB</b>@if($entry->user) oleh <b class="text-slate-600">{{ $entry->user->name }}</b>@endif.
                </p>
            @endif
        </div>

        <form method="POST" action="{{ $entry ? route('retain-sampel.update', $entry) : route('retain-sampel.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if($entry)
                @method('PUT')
            @endif

            {{-- Baris 1: Tanggal & Jam Observasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ old('tanggal', $entry ? $entry->tanggal->toDateString() : now()->toDateString()) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Jam Observasi</label>
                    <input list="jam-list" name="jam_label" required value="{{ old('jam_label', $entry ? $entry->jam_label : '06:00') }}" placeholder="06:00"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    <datalist id="jam-list">
                        @foreach ($jamStandar as $j)
                            <option value="{{ $j }}"></option>
                        @endforeach
                    </datalist>
                    <span class="mt-1 block text-[10.5px] text-slate-400">Pilih jam sesi observasi 06:00 (bisa awal), 12:00 (retain siang), atau 18:00 (retain sore).</span>
                </div>
            </div>

            {{-- Baris 2: Produk --}}
            <div>
                <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Produk</label>
                <select name="produk" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    @foreach ($produkList as $p)
                        <option value="{{ $p }}" @selected(old('produk', $entry ? $entry->produk : 'Pertalite') === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Baris 3: MT Nopol & Tangki Timbun --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">MT Nopol</label>
                    <input type="text" name="mt_nopol" value="{{ old('mt_nopol', $entry ? $entry->mt_nopol : '') }}" placeholder="contoh: N 9435 UH"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Tangki Timbun</label>
                    <input type="text" name="tangki_timbun" value="{{ old('tangki_timbun', $entry ? $entry->tangki_timbun : '') }}" placeholder="contoh: T1"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
            </div>

            {{-- Baris 4: Density Obs & Temperatur --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Density Obs</label>
                    <input type="number" step="0.0001" id="density_obs" name="density_obs" required value="{{ old('density_obs', $entry ? $entry->density_obs : '') }}" placeholder="contoh: 735.0"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Temperatur (°C)</label>
                    <input type="number" step="0.1" id="temperatur" name="temperatur" required value="{{ old('temperatur', $entry ? $entry->temperatur : '') }}" placeholder="contoh: 29.5"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
            </div>

            {{-- Baris 5: Foto Botol Sampel --}}
            <div>
                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Foto (Dokumen / Botol Sampel) (Opsional)</label>
                
                @if ($entry && $entry->fotoUrl())
                    <div class="mb-2 flex items-center gap-2">
                        @if ($entry->fotoIsPdf())
                            <a href="{{ $entry->fotoUrl() }}" target="_blank" class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-50 ring-1 ring-slate-200">
                                <i data-lucide="file-text" class="h-6 w-6 text-brand-red"></i>
                            </a>
                        @else
                            <img src="{{ $entry->fotoUrl() }}" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                        @endif
                        <span class="text-xs text-slate-400">File sudah ada. Upload baru jika ingin mengganti.</span>
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-2">
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                        <i data-lucide="upload" class="h-3.5 w-3.5 text-slate-500"></i> Pilih File (JPG/PNG/PDF)
                        <input type="file" name="foto" id="input-foto" accept="image/*,application/pdf" class="hidden" onchange="previewFileNama(this)">
                    </label>
                    <button type="button" onclick="bukaKamera()"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                        <i data-lucide="camera" class="h-3.5 w-3.5 text-slate-500"></i> Jepret Foto
                    </button>
                    <span id="label-foto-terpilih" class="text-xs text-slate-500 italic hidden"></span>
                </div>
                <span class="mt-1 block text-[10.5px] text-slate-400">Foto/dokumen botol sampel khusus buat baris/produk ini tersimpan permanen bareng data ini. Format JPG, PNG, WEBP, atau PDF, maks 10 MB.</span>
            </div>

            {{-- Baris 6: Kotak Density'15 Otomatis --}}
            <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4 text-center">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-brand-blue">DENSITY '15 (OTOMATIS)</span>
                <span id="preview-density-15" class="my-1 block text-2xl font-black text-brand-blue">
                    {{ $entry ? number_format($entry->density_15, 4) : '—' }}
                </span>
                <span class="block text-[10.5px] text-slate-500">
                    Dihitung otomatis begitu Density Obs & Temperatur diisi — tersimpan otomatis saat form disubmit.
                </span>
            </div>

            {{-- Baris 7: Tombol Simpan --}}
            <div class="pt-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-brand-red py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-brand-red/25 transition hover:bg-brand-redDark">
                    <i data-lucide="save" class="h-4 w-4"></i> Simpan Rekap
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KAMERA INTERAKTIF --}}
<div id="modal-kamera" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-3xl bg-white p-5 shadow-2xl">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="camera" class="h-4 w-4 text-brand-blue"></i>
                Jepret Foto Sampel Langsung
            </h3>
            <button type="button" onclick="tutupKamera()" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-slate-950 aspect-video flex items-center justify-center">
            <video id="video-stream" autoplay playsinline class="h-full w-full object-cover"></video>
            <canvas id="canvas-capture" class="hidden"></canvas>
        </div>
        <div class="mt-4 flex items-center justify-end gap-2">
            <button type="button" onclick="tutupKamera()" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">
                Batal
            </button>
            <button type="button" onclick="ambilGambar()" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue px-4 py-2 text-xs font-bold text-white shadow hover:bg-blue-800">
                <i data-lucide="camera" class="h-3.5 w-3.5"></i> Ambil Foto
            </button>
        </div>
    </div>
</div>

<script>
// Live ASTM Table 53 Calculation on-the-fly
function hitungDensity15JS(obs, temp) {
    if (!obs || !temp || isNaN(obs) || isNaN(temp)) return null;
    const dObs = parseFloat(obs);
    const t = parseFloat(temp);
    if (dObs <= 0) return null;

    let K0 = 346.42278, K1 = 0.43884, K2 = 0.0;
    if (dObs >= 653.0 && dObs < 770.5) { // Gasoline
        K0 = 346.42278; K1 = 0.43884;
    } else if (dObs >= 770.5 && dObs < 838.5) { // Kerosine / Jet
        K0 = 594.5418; K1 = 0.0;
    } else if (dObs >= 838.5 && dObs < 900.0) { // Diesel
        K0 = 186.9696; K1 = 0.4862;
    }

    let d15 = dObs;
    const deltaT = t - 15.0;
    for (let i = 0; i < 5; i++) {
        const alpha15 = (K0 + K1 * d15) / (d15 * d15);
        const vcf = Math.exp(-alpha15 * deltaT * (1.0 + 0.8 * alpha15 * deltaT));
        const newD15 = dObs / vcf;
        if (Math.abs(newD15 - d15) < 0.0001) {
            d15 = newD15;
            break;
        }
        d15 = newD15;
    }
    return d15;
}

function updateDensityPreview() {
    const obsVal = document.getElementById('density_obs')?.value;
    const tempVal = document.getElementById('temperatur')?.value;
    const previewEl = document.getElementById('preview-density-15');
    if (!previewEl) return;

    const hasil = hitungDensity15JS(obsVal, tempVal);
    if (hasil !== null) {
        previewEl.innerText = hasil.toFixed(4);
    } else {
        previewEl.innerText = '—';
    }
}

document.getElementById('density_obs')?.addEventListener('input', updateDensityPreview);
document.getElementById('temperatur')?.addEventListener('input', updateDensityPreview);

// Preview File Nama
function previewFileNama(input) {
    const labelEl = document.getElementById('label-foto-terpilih');
    if (input.files && input.files[0]) {
        labelEl.innerText = input.files[0].name;
        labelEl.classList.remove('hidden');
    } else {
        labelEl.classList.add('hidden');
    }
}

// Camera stream handler
let mediaStream = null;
function bukaKamera() {
    const modal = document.getElementById('modal-kamera');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(stream => {
            mediaStream = stream;
            document.getElementById('video-stream').srcObject = stream;
        })
        .catch(err => {
            alert('Tidak dapat mengakses kamera: ' + err.message);
            tutupKamera();
        });
}

function tutupKamera() {
    const modal = document.getElementById('modal-kamera');
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }
}

function ambilGambar() {
    const video = document.getElementById('video-stream');
    const canvas = document.getElementById('canvas-capture');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        const file = new File([blob], "foto-sampel-" + Date.now() + ".jpg", { type: "image/jpeg" });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        const input = document.getElementById('input-foto');
        input.files = dataTransfer.files;
        previewFileNama(input);
        tutupKamera();
    }, 'image/jpeg', 0.9);
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
    updateDensityPreview();
});
</script>
@endsection
