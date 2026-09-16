<!-- =========================================================================
     MODAL PETUNJUK TEKNIS & SOP LAPORAN RETAIN SAMPEL (ENTERPRISE)
     ========================================================================= -->
<div x-data="sopRetainSampel()" 
     x-show="open" 
     x-cloak 
     @keydown.escape.window="closeModal()"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
         @click="closeModal()"
         x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Modal Box Container (Professional Enterprise Design) -->
    <div class="relative w-full max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl transition-all my-auto"
         x-show="open"
         x-transition:enter="ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2">

        <!-- Pertamina 3-Color Ribbon Accent -->
        <div class="flex h-1.5 w-full">
            <div class="h-full w-1/3 bg-[#ED1B2F]"></div>
            <div class="h-full w-1/3 bg-[#006CB8]"></div>
            <div class="h-full w-1/3 bg-[#ACC42A]"></div>
        </div>

        <!-- Corporate Header -->
        <div class="border-b border-slate-200 bg-slate-50/90 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 p-2 shadow-sm">
                        <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-500">PT Pertamina Patra Niaga &bull; FT Maos</span>
                            <span class="rounded bg-blue-100 px-1.5 py-0.2 text-[9px] font-bold text-brand-blue">SOP QC-03</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">
                            Petunjuk Operasional &amp; Standar Laporan Retain Sampel
                        </h2>
                    </div>
                </div>

                <button type="button" 
                        @click="closeModal()" 
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <!-- Stepper Navigation -->
            <div class="mt-4 flex items-center justify-between gap-2 border-t border-slate-200/80 pt-3">
                <template x-for="(s, index) in steps" :key="index">
                    <button type="button" 
                            @click="goToStep(index)" 
                            class="flex-1 text-left">
                        <div class="h-1.5 w-full rounded-full transition-all duration-200"
                             :class="step === index ? 'bg-brand-blue' : (step > index ? 'bg-emerald-500' : 'bg-slate-200')"></div>
                        <p class="mt-1 text-[10.5px] font-bold truncate transition"
                           :class="step === index ? 'text-brand-blue font-extrabold' : 'text-slate-500 hover:text-slate-800'"
                           x-text="(index + 1) + '. ' + s.title"></p>
                    </button>
                </template>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6 sm:p-7 min-h-[330px] flex flex-col justify-between bg-white">

            <!-- LANGKAH 1: STRUKTUR SESI HARIAN -->
            <div x-show="step === 0" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-brand-blue font-bold text-sm border border-blue-200">1</span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Struktur Pengambilan Retain Sampel (3 Sesi Rutin)
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Berdasarkan SOP Pengawasan Kualitas Penyaluran BBM di Fuel Terminal Maos, pengujian dan dokumentasi sampel botol retain dilakukan tiga kali sehari:
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-black text-[#0f3861]">06.00 WIB</span>
                            <span class="rounded bg-blue-100 text-[10px] font-bold text-brand-blue px-1.5 py-0.5">Pagi</span>
                        </div>
                        <p class="text-xs font-bold text-slate-800">Awal Penyaluran MT</p>
                        <p class="text-[11px] text-slate-500 mt-1">Sampel fisik pertama saat penyaluran dimulai. Menjadi acuan standar mutu harian.</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-black text-[#e86a17]">12.00 WIB</span>
                            <span class="rounded bg-amber-100 text-[10px] font-bold text-amber-800 px-1.5 py-0.5">Siang</span>
                        </div>
                        <p class="text-xs font-bold text-slate-800">Retain Siang (Pergantian)</p>
                        <p class="text-[11px] text-slate-500 mt-1">Pengambilan sampel tengah hari untuk verifikasi stabilitas suhu dan density produk.</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-black text-[#DA251D]">18.00 WIB</span>
                            <span class="rounded bg-red-100 text-[10px] font-bold text-red-800 px-1.5 py-0.5">Sore</span>
                        </div>
                        <p class="text-xs font-bold text-slate-800">Retain Sore (Penutupan)</p>
                        <p class="text-[11px] text-slate-500 mt-1">Pengambilan sampel sesi sore untuk evaluasi penutupan penyaluran harian.</p>
                    </div>
                </div>
            </div>

            <!-- LANGKAH 2: SESI 06.00 PENGISIAN AWAL -->
            <div x-show="step === 1" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-brand-blue font-bold text-sm border border-blue-200">2</span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Format Slide Pukul 06.00 WIB (Baseline Acuan Mutu)
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Slide sesi pagi dirancang mandiri dengan dokumentasi 3 pilar operasional:
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-2.5 text-xs text-slate-700">
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-brand-blue text-white font-bold text-[10px]">A</span>
                        <div>
                            <p class="font-bold text-slate-900">Foto Botol Retain 06.00 WIB</p>
                            <p class="text-slate-500 text-[11px]">Foto jajaran botol sampel produk BBM (Pertalite, Pertamax, Biosolar) saat awal penyaluran.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-brand-blue text-white font-bold text-[10px]">B</span>
                        <div>
                            <p class="font-bold text-slate-900">Visual 3 Mobil Tangki (MT) Pertama</p>
                            <p class="text-slate-500 text-[11px]">Dokumentasi visual kompartemen 3 armada tangki pertama yang menerima produk.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-brand-blue text-white font-bold text-[10px]">C</span>
                        <div>
                            <p class="font-bold text-slate-900">Visual Tangki Timbun Aktif</p>
                            <p class="text-slate-500 text-[11px]">Foto tangki timbun penyaluran aktif di FT Maos (misal T.09 Pertamax, T.04 Biosolar).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LANGKAH 3: SISTEM PEMBANDING 12 & 18 -->
            <div x-show="step === 2" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-brand-blue font-bold text-sm border border-blue-200">3</span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Sistem Pembanding Samping-Menyamping (Side-by-Side) Jam 12 &amp; 18
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Pada slide jam 12.00 dan 18.00, tampilan dibagi menjadi dua kotak berdampingan untuk verifikasi visual dan fisik:
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="rounded-xl border border-blue-200 bg-blue-50/40 p-3.5">
                        <span class="inline-block rounded bg-brand-blue text-white px-2 py-0.5 text-[10px] font-bold uppercase mb-1">Kotak Kiri (Tetap)</span>
                        <h4 class="text-xs font-bold text-slate-900">Sampel 06.00 WIB Pagi</h4>
                        <p class="text-[11px] text-slate-600 mt-1">Berisi foto botol 06.00 dan tabel data pagi sebagai acuan pembanding (*baseline*).</p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-3.5">
                        <span class="inline-block rounded bg-[#e86a17] text-white px-2 py-0.5 text-[10px] font-bold uppercase mb-1">Kotak Kanan (Pergantian)</span>
                        <h4 class="text-xs font-bold text-slate-900">Retain 12.00 / 18.00 WIB</h4>
                        <p class="text-[11px] text-slate-600 mt-1">Berisi foto botol retain sesi terkini dan tabel hasil pengujian siang/sore.</p>
                    </div>
                </div>

                <div class="rounded-lg bg-slate-100 p-2.5 text-[11px] text-slate-600 flex items-center gap-2">
                    <i data-lucide="info" class="h-4 w-4 text-brand-blue shrink-0"></i>
                    <span>Tampilan berdampingan memudahkan tim Regional dan fungsi QC mendeteksi deviasi mutu secara cepat.</span>
                </div>
            </div>

            <!-- LANGKAH 4: OTOMASI ASTM DENSITY 15 -->
            <div x-show="step === 3" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-brand-blue font-bold text-sm border border-blue-200">4</span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Perhitungan Otomatis Density 15°C (ASTM Table 53B)
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Standar pengujian produk migas memerlukan konversi berat jenis terukur pada suhu uji ke kondisi standar 15°C:
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="rounded-lg bg-slate-50 border border-slate-200 p-3">
                            <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Data Input Petugas</span>
                            <ul class="mt-1 space-y-1 text-slate-700">
                                <li>&bull; <b>Density Observed</b> (contoh: <code>0.7410</code>)</li>
                                <li>&bull; <b>Temperatur / Suhu Uji</b> (contoh: <code>28.5 °C</code>)</li>
                            </ul>
                        </div>
                        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3">
                            <span class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">Hasil Otomatis Sistem</span>
                            <ul class="mt-1 space-y-1 text-emerald-900">
                                <li>&bull; <b>Density 15°C</b>: <code>0.7506</code> (ASTM 53B)</li>
                                <li>&bull; Format 4 desimal standar migas</li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        *Perhitungan mengeliminasi potensi kekeliruan pembacaan buku tabel cetak secara manual.
                    </p>
                </div>
            </div>

            <!-- LANGKAH 5: EKSPOR RESMI & PENGIRIMAN REGIONAL -->
            <div x-show="step === 4" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-brand-blue font-bold text-sm border border-blue-200">5</span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Unduh Banner HD &amp; Distribusi Laporan ke Region
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Setiap slide telah dilengkapi tombol ekspor instan untuk keperluan pelaporan:
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-2.5 text-xs text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#0f3b66] px-3 py-1 text-xs font-bold text-white shadow-sm">
                            <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PNG
                        </span>
                        <span class="text-slate-600">Unduh gambar banner resmi beresolusi tajam.</span>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Banner siap dibagikan ke grup monitoring Regional (RCBT) tanpa perlu proses pemotongan manual atau penyusunan ulang.
                    </p>
                </div>
            </div>

            <!-- Footer Controls -->
            <div class="mt-6 pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" 
                        @click="prevStep()" 
                        :disabled="step === 0"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i> Sebelumnya
                </button>

                <div class="flex items-center gap-1.5">
                    <template x-for="(s, index) in steps" :key="index">
                        <span class="h-1.5 rounded-full transition-all duration-200"
                              :class="step === index ? 'w-5 bg-brand-blue' : 'w-1.5 bg-slate-200'"></span>
                    </template>
                </div>

                <button type="button" 
                        @click="nextStep()" 
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue hover:bg-brand-blueDark px-4 py-2 text-xs font-bold text-white shadow-sm transition">
                    <span x-text="step === steps.length - 1 ? 'Selesai' : 'Selanjutnya'"></span>
                    <i :data-lucide="step === steps.length - 1 ? 'check' : 'arrow-right'" class="h-3.5 w-3.5"></i>
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    function sopRetainSampel() {
        return {
            open: false,
            step: 0,
            steps: [
                { title: 'Struktur Sesi' },
                { title: 'Sesi 06.00 Pagi' },
                { title: 'Sesi 12 & 18' },
                { title: 'ASTM Density 15' },
                { title: 'Ekspor Banner' },
            ],
            init() {
                window.addEventListener('open-retain-tutorial', () => {
                    this.step = 0;
                    this.open = true;
                    this.$nextTick(() => lucide.createIcons());
                });

                this.$watch('step', () => {
                    this.$nextTick(() => lucide.createIcons());
                });
            },
            closeModal() {
                this.open = false;
            },
            nextStep() {
                if (this.step < this.steps.length - 1) {
                    this.step += 1;
                } else {
                    this.closeModal();
                }
            },
            prevStep() {
                if (this.step > 0) {
                    this.step -= 1;
                }
            },
            goToStep(idx) {
                this.step = idx;
            }
        };
    }
</script>
