@props([
    'name' => 'foto_bukti',
    'existingUrl' => null,
    'existingIsGambar' => true,
    'existingFileName' => null,
    'label' => 'Foto Bukti (opsional)',
    'helpText' => null,
])

<div x-data="fotoBuktiField()" x-init="init()">
    <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label>

    {{-- File yang sudah tersimpan sebelumnya (mode edit) --}}
    @if ($existingUrl)
        <div class="mb-2">
            @if ($existingIsGambar)
                <img src="{{ $existingUrl }}" alt="Foto bukti pengujian" class="max-h-40 w-auto rounded-lg border border-slate-200 object-contain">
            @else
                <a href="{{ $existingUrl }}" target="_blank" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-100">
                    <i data-lucide="file-text" class="h-4 w-4 shrink-0"></i>
                    <span class="truncate">{{ $existingFileName ?? 'Lihat file tersimpan' }}</span>
                </a>
            @endif
        </div>
    @endif

    {{-- Preview file baru yang dipilih / difoto --}}
    <template x-if="previewUrl || fileName">
        <div class="mb-2 overflow-hidden rounded-lg border border-brand-blue/30 bg-brand-blue/5/40">
            <template x-if="isImage && previewUrl">
                <img :src="previewUrl" class="max-h-40 w-full object-contain bg-slate-50">
            </template>
            <div class="flex items-center gap-2 px-3 py-2 text-xs text-brand-blue">
                <i data-lucide="check-circle-2" class="h-3.5 w-3.5 shrink-0"></i>
                <span class="truncate" x-text="fileName"></span>
            </div>
        </div>
    </template>

    <div class="flex gap-2">
        <label class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-semibold text-brand-blue transition hover:bg-brand-blue/5">
            <i data-lucide="upload" class="h-4 w-4 shrink-0"></i>
            <span class="truncate">Pilih File</span>
            <input x-ref="fileInput" type="file" name="{{ $name }}"
                   class="hidden"
                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt"
                   @change="onFileChosen($event)">
        </label>
        <button type="button" @click="openCamera()"
                class="flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-semibold text-brand-blue transition hover:bg-brand-blue/5">
            <i data-lucide="camera" class="h-4 w-4 shrink-0"></i>
            Kamera
        </button>
    </div>

    <p class="mt-1.5 text-xs text-slate-400">{{ $helpText ?? 'Opsional. Unggah file apa saja (foto, PDF, dokumen) atau ambil foto langsung dari kamera.' }}</p>

    {{-- Modal kamera --}}
    <div x-show="cameraOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 p-4"
         style="display:none;">
        <div class="w-full max-w-md rounded-2xl bg-white p-4 shadow-lift" @click.outside="closeCamera()">
            <div class="mb-3 flex items-center justify-between">
                <p class="text-sm font-bold text-slate-900">Ambil Foto dari Kamera</p>
                <button type="button" @click="closeCamera()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div class="relative overflow-hidden rounded-xl bg-slate-900">
                <video x-ref="video" autoplay playsinline muted class="w-full" x-show="!capturedDataUrl"></video>
                <img x-show="capturedDataUrl" :src="capturedDataUrl" class="w-full" alt="Hasil jepretan">
            </div>

            <p x-show="cameraError" x-text="cameraError" class="mt-2 text-xs text-rose-600"></p>

            <div class="mt-3 flex gap-2">
                <template x-if="!capturedDataUrl && !cameraError">
                    <button type="button" @click="takeShot()" class="flex-1 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-blue">
                        Jepret
                    </button>
                </template>
                <template x-if="capturedDataUrl">
                    <button type="button" @click="retake()" class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Ulangi
                    </button>
                </template>
                <template x-if="capturedDataUrl">
                    <button type="button" @click="usePhoto()" class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        Gunakan Foto
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        function fotoBuktiField() {
            return {
                fileName: '',
                previewUrl: null,
                isImage: true,
                cameraOpen: false,
                cameraError: '',
                capturedDataUrl: null,
                stream: null,

                init() {
                    this.$watch('cameraOpen', (open) => {
                        if (!open) this.stopStream();
                    });
                },

                onFileChosen(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        this.fileName = '';
                        this.previewUrl = null;
                        return;
                    }
                    this.fileName = file.name;
                    this.isImage = file.type.startsWith('image/');
                    this.previewUrl = this.isImage ? URL.createObjectURL(file) : null;
                },

                async openCamera() {
                    this.cameraError = '';
                    this.capturedDataUrl = null;
                    this.cameraOpen = true;
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment' },
                            audio: false,
                        });
                        this.$nextTick(() => {
                            if (this.$refs.video) this.$refs.video.srcObject = this.stream;
                        });
                    } catch (err) {
                        this.cameraError = 'Tidak bisa mengakses kamera. Pastikan izin kamera diaktifkan di browser, lalu coba lagi.';
                    }
                },

                closeCamera() {
                    this.cameraOpen = false;
                    this.stopStream();
                    this.capturedDataUrl = null;
                },

                stopStream() {
                    if (this.stream) {
                        this.stream.getTracks().forEach((t) => t.stop());
                        this.stream = null;
                    }
                },

                takeShot() {
                    const video = this.$refs.video;
                    if (!video || !video.videoWidth) return;
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    this.capturedDataUrl = canvas.toDataURL('image/jpeg', 0.92);
                },

                retake() {
                    this.capturedDataUrl = null;
                },

                usePhoto() {
                    fetch(this.capturedDataUrl)
                        .then((res) => res.blob())
                        .then((blob) => {
                            const name = 'foto-kamera-' + Date.now() + '.jpg';
                            const file = new File([blob], name, { type: 'image/jpeg' });
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            this.$refs.fileInput.files = dt.files;
                            this.fileName = name;
                            this.isImage = true;
                            this.previewUrl = this.capturedDataUrl;
                            this.closeCamera();
                            if (window.lucide) lucide.createIcons();
                        });
                },
            };
        }
    </script>
@endonce
