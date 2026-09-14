<!DOCTYPE html>
<html lang="id">
@php
    // ---- Nama file: NomorKKW_JenisBahanBakar_TanggalPengujian ----
    $bagianKkw = $hasilUji->nomor_kkw ? preg_replace('/[^A-Za-z0-9]+/', '', $hasilUji->nomor_kkw) : 'TanpaKKW';
    $bagianJenis = preg_replace('/[^A-Za-z0-9]+/', '', $hasilUji->nama_sampel);
    $bagianTanggal = $hasilUji->waktu_uji->format('d-m-Y');
    $namaFileDasar = "{$bagianKkw}_{$bagianJenis}_{$bagianTanggal}";
@endphp
<head>
    <meta charset="UTF-8">
    <title>{{ $namaFileDasar }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        @page { size: A4; margin: 14mm; }
        body { font-family: 'Segoe UI', Arial, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            #area-sertifikat { box-shadow: none !important; }
        }
        /* Foto bukti: tegak, proporsional, tidak diregangkan/dipepetkan */
        .foto-bukti-wrap {
            display: flex;
            justify-content: center;
            background: #f8fafc;
            border-radius: 0.5rem;
        }
        .foto-bukti-wrap img {
            max-width: 100%;
            max-height: 480px;
            width: auto;
            height: auto;
            object-fit: contain;
            image-orientation: from-image; /* fallback kalau EXIF belum terkoreksi */
        }
        .btn-unduh { transition: all .15s ease; }
        .btn-unduh:hover { transform: translateY(-1px); }
        .btn-unduh:active { transform: translateY(0); }
        .btn-unduh:disabled { opacity: .6; cursor: wait; transform: none; }
    </style>
</head>
<body class="bg-slate-200 py-8 text-slate-900">

<div class="no-print mx-auto mb-4 flex max-w-3xl flex-wrap items-center justify-end gap-2.5 px-6">
    <button id="btn-png" onclick="unduhGambar('image/png', '.png')"
            class="btn-unduh inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-900">
        <i data-lucide="image" class="h-4 w-4"></i> Unduh PNG
    </button>
    <button id="btn-jpg" onclick="unduhGambar('image/jpeg', '.jpg')"
            class="btn-unduh inline-flex items-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-700">
        <i data-lucide="file-image" class="h-4 w-4"></i> Unduh JPG
    </button>
    <button onclick="window.print()"
            class="btn-unduh inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-red-700">
        <i data-lucide="printer" class="h-4 w-4"></i> Cetak / Simpan PDF
    </button>
</div>

<div id="area-sertifikat" class="mx-auto max-w-3xl overflow-hidden rounded-lg bg-white shadow-2xl">
    <div class="flex items-center justify-between border-b-4 px-10 py-6" style="border-color:#DA251D;">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-white p-1.5 shadow-sm ring-1 ring-slate-200">
                <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-full w-full object-contain">
            </div>
            <div>
                <p class="text-lg font-extrabold uppercase leading-tight" style="color:#DA251D;">Pertamina Patra Niaga</p>
                <p class="text-xs font-semibold text-slate-500">Fuel Maos — Smart Fuel QC System</p>
            </div>
        </div>
        <div class="text-right text-xs leading-relaxed text-slate-500">
            <p class="font-bold text-slate-700">SERTIFIKAT HASIL UJI</p>
            <p>No. FM-{{ str_pad((string) $hasilUji->id, 5, '0', STR_PAD_LEFT) }}</p>
            <p>{{ now()->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <div class="px-10 pt-8 text-center">
        <h1 class="text-2xl font-extrabold uppercase tracking-wide text-slate-900">Certificate of Quality</h1>
        <p class="mt-1 text-sm text-slate-500">Sertifikat Mutu Bahan Bakar Minyak</p>
    </div>

    <div class="grid grid-cols-2 gap-x-8 gap-y-3 px-10 pt-8 text-sm">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Produk / Sampel</p>
            <p class="font-bold text-slate-900">{{ $hasilUji->nama_sampel }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Jenis Pengujian</p>
            <p class="font-bold text-slate-900">{{ $hasilUji->jenisUji->nama }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Nomor KKW / Kereta</p>
            <p class="font-bold text-slate-900">{{ $hasilUji->nomor_kkw ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Waktu Pengujian</p>
            <p class="font-bold text-slate-900">{{ $hasilUji->waktu_uji->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Petugas / Analis</p>
            <p class="font-bold text-slate-900">{{ $hasilUji->user->name }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Status Mutu</p>
            @php
                $badge = match ($evaluasi['verdict']) {
                    \App\Services\SpecEngine::PASS => 'bg-emerald-100 text-emerald-800',
                    \App\Services\SpecEngine::FAIL => 'bg-red-100 text-red-700',
                    \App\Services\SpecEngine::MARGINAL => 'bg-amber-100 text-amber-800',
                    default => 'bg-slate-100 text-slate-600',
                };
            @endphp
            <span class="inline-block rounded-full px-3 py-1 text-xs font-bold uppercase {{ $badge }}">{{ $evaluasi['verdict'] }}</span>
        </div>
    </div>

    <div class="px-10 pt-8">
        <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Hasil Pengukuran</p>
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="border-b-2 border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-2 pr-3">Parameter</th>
                    <th class="py-2 pr-3">Nilai</th>
                    <th class="py-2 pr-3">Batas Spesifikasi</th>
                    <th class="py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @if (count($evaluasi['detail']))
                    @foreach ($evaluasi['detail'] as $row)
                        <tr>
                            <td class="py-2.5 pr-3 font-medium text-slate-700">{{ $row['label'] }}</td>
                            <td class="py-2.5 pr-3 font-bold text-slate-900">{{ $row['nilai'] ?: '—' }} <span class="text-xs font-normal text-slate-400">{{ $row['satuan'] }}</span></td>
                            <td class="py-2.5 pr-3 text-slate-600">
                                @if ($row['min'] !== null && $row['max'] !== null)
                                    {{ $row['min'] }} – {{ $row['max'] }}
                                @elseif ($row['min'] !== null)
                                    Min {{ $row['min'] }}
                                @elseif ($row['max'] !== null)
                                    Maks {{ $row['max'] }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-2.5">
                                @php
                                    $st = match ($row['status']) {
                                        \App\Services\SpecEngine::PASS => 'text-emerald-700',
                                        \App\Services\SpecEngine::FAIL => 'text-red-700',
                                        \App\Services\SpecEngine::MARGINAL => 'text-amber-700',
                                        default => 'text-slate-400',
                                    };
                                @endphp
                                <span class="font-bold {{ $st }}">{{ $row['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="py-4 text-center text-slate-400">Tidak ada parameter yang dinilai terhadap spesifikasi.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($hasilUji->catatan)
        <div class="px-10 pt-6">
            <p class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400">Catatan</p>
            <p class="text-sm text-slate-700">{{ $hasilUji->catatan }}</p>
        </div>
    @endif

    @if ($hasilUji->foto_bukti)
        <div class="px-10 pt-8">
            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Foto Bukti Pengujian</p>
            @if ($hasilUji->fotoBuktiIsGambar())
                <div class="foto-bukti-wrap mx-auto max-w-xl rounded-lg border border-slate-200 p-2">
                    <img src="{{ $hasilUji->fotoBuktiUrl() }}" alt="Foto bukti pengujian" crossorigin="anonymous">
                </div>
            @else
                <p class="mx-auto block w-full max-w-xl rounded-lg border border-slate-200 px-4 py-3 text-center text-sm text-slate-600">
                    Lampiran: {{ $hasilUji->fotoBuktiNamaFile() }}
                </p>
            @endif
        </div>
    @endif

    <div class="mt-12 grid grid-cols-2 gap-8 px-10 pb-10 text-center text-sm">
        <div>
            <p class="mb-10 text-slate-500">Dianalisis oleh,</p>
            <p class="font-bold text-slate-800">{{ $hasilUji->user->name }}</p>
            <p class="text-xs text-slate-400">{{ $hasilUji->user->jabatan ?? 'Analis Lab' }}</p>
        </div>
        <div>
            <p class="mb-10 text-slate-500">Disetujui oleh,</p>
            <p class="font-bold text-slate-800">Supervisor Lab</p>
            <p class="text-xs text-slate-400">Quality Control — Pertamina Patra Niaga</p>
        </div>
    </div>

    <div class="border-t border-slate-100 bg-slate-50 px-10 py-4 text-center text-[11px] text-slate-400">
        Dokumen ini diterbitkan otomatis oleh <span class="font-semibold">Fuel Maos — Smart Fuel QC System</span> · Pertamina Patra Niaga. Hasil mengacu pada spesifikasi mutu BBM (SNI).
    </div>
</div>

<script>
    const namaFileDasar = @json($namaFileDasar);

    // Pastikan semua <img> di area sertifikat sudah benar-benar termuat
    // sebelum di-screenshot, supaya html2canvas tidak menangkap gambar
    // setengah-load (penyebab hasil unduhan tampak pecah/blur).
    function tungguSemuaGambarSiap(container) {
        const imgs = Array.from(container.querySelectorAll('img'));
        return Promise.all(imgs.map((img) => {
            if (img.complete && img.naturalWidth > 0) return Promise.resolve();
            return new Promise((resolve) => {
                img.addEventListener('load', resolve, { once: true });
                img.addEventListener('error', resolve, { once: true });
            });
        }));
    }

    function unduhGambar(mimeType, ekstensi) {
        const elArea = document.getElementById('area-sertifikat');
        const tombolPng = document.getElementById('btn-png');
        const tombolJpg = document.getElementById('btn-jpg');
        tombolPng.disabled = true;
        tombolJpg.disabled = true;
        const labelAsli = mimeType === 'image/png' ? tombolPng.innerHTML : tombolJpg.innerHTML;
        (mimeType === 'image/png' ? tombolPng : tombolJpg).innerHTML = '<span>⏳</span> Memproses...';

        tungguSemuaGambarSiap(elArea).then(() => html2canvas(elArea, {
            scale: 2.5,             // resolusi tinggi biar tajam saat di-zoom / dicetak ulang
            useCORS: true,
            allowTaint: false,
            imageTimeout: 15000,
            backgroundColor: '#ffffff',
            windowWidth: elArea.scrollWidth,
            windowHeight: elArea.scrollHeight,
        })).then((canvas) => {
            const kualitas = mimeType === 'image/jpeg' ? 0.95 : 1;
            const dataUrl = canvas.toDataURL(mimeType, kualitas);

            const link = document.createElement('a');
            link.href = dataUrl;
            link.download = namaFileDasar + ekstensi;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }).catch((err) => {
            console.error('Gagal membuat gambar sertifikat:', err);
            alert('Gagal mengunduh gambar. Coba lagi ya.');
        }).finally(() => {
            tombolPng.disabled = false;
            tombolJpg.disabled = false;
            tombolPng.innerHTML = '<i data-lucide="image" class="h-4 w-4"></i> Unduh PNG';
            tombolJpg.innerHTML = '<i data-lucide="file-image" class="h-4 w-4"></i> Unduh JPG';
            if (window.lucide) lucide.createIcons();
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>
</body>
</html>