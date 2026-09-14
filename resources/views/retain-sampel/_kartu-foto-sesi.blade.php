{{--
    Kartu foto 1 slot (Sampel Retain ATAU Sampel Penyaluran per jam).
    Vars: $judul, $jamLabelFoto, $url, $isPdf, $inputId, $tanggal
    Kalau $judul kosong, dipakai di dalam kartu yang udah punya header sendiri
    (label upload tetap tampil, cuma gak dobel judul).
--}}
<div class="{{ $judul ? 'overflow-hidden rounded-xl border border-slate-200 bg-white' : 'bg-white' }}">
    <div class="flex items-center justify-end gap-2 border-b border-slate-100 px-3.5 py-1.5">
        @if ($judul)
            <p class="mr-auto text-xs font-bold uppercase tracking-wide text-slate-500">{{ $judul }}</p>
        @endif
        <label for="{{ $inputId }}" class="inline-flex cursor-pointer items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 hover:bg-slate-200">
            <i data-lucide="upload" class="h-3.5 w-3.5"></i> {{ $url ? 'Ganti Foto' : 'Upload Foto' }}
        </label>
        <form method="POST" action="{{ route('retain-sampel.foto-sesi.store') }}" enctype="multipart/form-data" class="hidden">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="jam_label" value="{{ $jamLabelFoto }}">
            <input type="file" id="{{ $inputId }}" name="foto" accept="image/*,application/pdf" onchange="this.form.submit()">
        </form>
    </div>
    <div class="flex h-36 items-center justify-center bg-slate-50">
        @if ($url && $isPdf)
            <a href="{{ $url }}" target="_blank" class="flex flex-col items-center gap-1 text-brand-red">
                <i data-lucide="file-text" class="h-8 w-8"></i>
                <span class="text-xs font-semibold">Lihat PDF</span>
            </a>
        @elseif ($url)
            <a href="{{ $url }}" target="_blank" class="block h-full w-full">
                <img src="{{ $url }}" class="h-full w-full object-contain">
            </a>
        @else
            <p class="text-xs text-slate-300">Belum ada foto</p>
        @endif
    </div>
</div>
