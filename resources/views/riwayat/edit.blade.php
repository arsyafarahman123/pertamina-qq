@extends('layouts.app')
@section('title', 'Edit Hasil Uji')

@section('content')
<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('riwayat.index') }}" class="inline-flex items-center gap-1 font-medium hover:text-brand-blue">
        <i data-lucide="arrow-left" class="h-4 w-4"></i>
        Riwayat Hasil Uji
    </a>
    <span class="text-slate-300">/</span>
    <a href="{{ route('riwayat.show', $hasilUji) }}" class="font-medium hover:text-brand-blue">#{{ $hasilUji->id }}</a>
    <span class="text-slate-300">/</span>
    <span class="font-semibold text-slate-700">Edit</span>
</div>

<div class="mx-auto max-w-2xl">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-card">
        <div class="flex items-center gap-3 border-b border-slate-100 bg-gradient-to-r from-brand-red/5 to-brand-blue/5 px-6 py-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-blue text-white">
                <i data-lucide="pencil" class="h-5 w-5"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Edit Hasil Uji</h2>
                <p class="text-xs text-slate-400">{{ $hasilUji->jenisUji->nama }} · diuji {{ $hasilUji->waktu_uji->translatedFormat('d/m/Y H:i') }} WIB</p>
            </div>
        </div>

        <form method="POST" action="{{ route('riwayat.update', $hasilUji) }}" class="space-y-4 px-6 py-5" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Sampel</label>
                <select name="nama_sampel" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">
                    @foreach ($daftarSampel as $sampel)
                        <option value="{{ $sampel }}" @selected(old('nama_sampel', $hasilUji->nama_sampel) === $sampel)>{{ $sampel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor KKW / Kereta</label>
                <input type="text" name="nomor_kkw" value="{{ old('nomor_kkw', $hasilUji->nomor_kkw) }}" placeholder="Contoh: KKW 325"
                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">
            </div>

            @if (count($fieldConfig))
                <div class="border-t border-slate-100 pt-4">
                    <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Hasil Pengukuran</p>
                    <div class="space-y-4">
                        @foreach ($fieldConfig as $key => $field)
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $field['label'] }}</label>
                                <input type="{{ $field['type'] === 'number' ? 'number' : 'text' }}"
                                       step="any"
                                       name="data_hasil[{{ $key }}]"
                                       value="{{ old('data_hasil.' . $key, $hasilUji->data_hasil[$key] ?? '') }}"
                                       class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-brand-blue/50 focus:outline-none focus:ring-4 focus:ring-brand-blue/20">{{ old('catatan', $hasilUji->catatan) }}</textarea>
            </div>

            <x-foto-bukti-field
                name="foto_bukti"
                :existing-url="$hasilUji->fotoBuktiUrl()"
                :existing-is-gambar="$hasilUji->fotoBuktiIsGambar()"
                :existing-file-name="$hasilUji->fotoBuktiNamaFile()"
                help-text="Kosongkan jika tidak ingin mengganti foto bukti. Bisa unggah file apa saja atau ambil foto langsung dari kamera." />

            <div class="flex items-center gap-3 pt-1">
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-red to-brand-blue px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-blue/20 transition hover:opacity-90">
                    <i data-lucide="check" class="h-4 w-4"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('riwayat.show', $hasilUji) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
