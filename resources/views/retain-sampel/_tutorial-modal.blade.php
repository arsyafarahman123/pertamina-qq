<!-- =========================================================================
     MODAL PETUNJUK OPERASIONAL & CARA PENGGUNAAN WEB RETAIN SAMPEL
     DILENGKAPI:
     1. Musik Pengiring Latar (Web Audio Ambient Melodic Synthesizer)
     2. Narator Suara Bahasa Indonesia (Web Speech API)
     3. Video / Simulasi Kursor Berjalan Mengklik Tombol Web
     PT PERTAMINA PATRA NIAGA — FUEL TERMINAL MAOS (SOP QC-03)
     ========================================================================= -->
<div x-data="sopRetainSampel()" 
     x-show="open" 
     x-cloak 
     @keydown.escape.window="closeModal()"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
         @click="closeModal()"
         x-show="open"
         x-transition:enter="ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Modal Container -->
    <div class="relative w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-2xl transition-all my-auto"
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-3"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-3">

        <!-- Pertamina 3-Color Ribbon Accent -->
        <div class="flex h-1.5 w-full">
            <div class="h-full w-1/3 bg-[#ED1B2F]"></div>
            <div class="h-full w-1/3 bg-[#006CB8]"></div>
            <div class="h-full w-1/3 bg-[#ACC42A]"></div>
        </div>

        <!-- Corporate Header -->
        <div class="border-b border-slate-200 bg-gradient-to-r from-slate-900 via-[#0a2540] to-[#0f3861] px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white p-2 shadow-md">
                        <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-300">PT Pertamina Patra Niaga &bull; FT Maos</span>
                            <span class="rounded bg-brand-blue/40 px-2 py-0.5 text-[9.5px] font-bold text-blue-200 ring-1 ring-white/20">SOP QC-03</span>
                        </div>
                        <h2 class="font-display text-base sm:text-lg font-black text-white tracking-tight">
                            Tutorial Cara Penggunaan Web Retain Sampel
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Tombol Kontrol Suara & Musik -->
                    <button type="button" 
                            @click="toggleAudio()" 
                            :class="soundEnabled ? 'bg-emerald-500/90 text-white hover:bg-emerald-600' : 'bg-rose-500/20 text-rose-300 hover:bg-rose-500/30'"
                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition shadow-sm"
                            :title="soundEnabled ? 'Klik untuk matikan suara & musik' : 'Klik untuk aktifkan suara & musik'">
                        <i :data-lucide="soundEnabled ? 'volume-2' : 'volume-x'" class="h-4 w-4"></i>
                        <span class="hidden sm:inline" x-text="soundEnabled ? 'Suara + Musik Aktif' : 'Audio Mati'"></span>
                    </button>

                    <!-- Tombol Tutup -->
                    <button type="button" 
                            @click="closeModal()" 
                            class="rounded-xl p-1.5 text-white/70 hover:bg-white/10 hover:text-white transition">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>

            <!-- Stepper Progress Bar -->
            <div class="mt-4 grid grid-cols-5 gap-1.5 border-t border-white/10 pt-3">
                <template x-for="(s, index) in steps" :key="index">
                    <button type="button" 
                            @click="goToStep(index)" 
                            class="text-left group transition">
                        <div class="h-1.5 w-full rounded-full transition-all duration-300"
                             :class="step === index ? 'bg-amber-400 shadow-sm shadow-amber-300' : (step > index ? 'bg-emerald-400' : 'bg-white/20')"></div>
                        <p class="mt-1 text-[10px] font-bold truncate transition"
                           :class="step === index ? 'text-amber-300 font-black' : 'text-slate-300/70 group-hover:text-white'"
                           x-text="(index + 1) + '. ' + s.navTitle"></p>
                    </button>
                </template>
            </div>
        </div>

        <!-- Body Slide Content -->
        <div class="p-6 sm:p-8 min-h-[380px] flex flex-col justify-between bg-slate-50/40">

            <!-- ============================================================ -->
            <!-- SLIDE 1: LANGKAH 1 - MEMBUKA & FILTER TANGGAL                 -->
            <!-- ============================================================ -->
            <div x-show="step === 0" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">1</span>
                    <div>
                        <span class="rounded bg-blue-50 px-2 py-0.5 text-[11px] font-bold text-brand-blue border border-blue-200">
                            Langkah 1: Akses Menu &amp; Tanggal
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Buka Menu Retain Sampel &amp; Pilih Tanggal Operasional
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Cara memulai pencatatan dan pengecekan retain sampel harian:
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="rounded-2xl border border-blue-200 bg-white p-4 shadow-sm space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#0f3861] text-white font-bold text-xs">A</span>
                            <h4 class="text-xs font-black text-slate-900">Klik Menu Retain Sampel MT</h4>
                        </div>
                        <p class="text-[11.5px] text-slate-600 leading-relaxed">
                            Pada sidebar navigasi sebelah kiri, klik menu <b>Retain Sampel MT</b>. Halaman akan menampilkan ringkasan pengamatan retain hari ini.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white font-bold text-xs">B</span>
                            <h4 class="text-xs font-black text-slate-900">Pilih Tanggal &amp; Filter</h4>
                        </div>
                        <p class="text-[11.5px] text-slate-600 leading-relaxed">
                            Gunakan kolom <b>Filter Tanggal</b> di bagian atas untuk melihat arsip retain hari sebelumnya atau klik tombol <span class="text-brand-blue font-bold">Input Data Retain Baru</span>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 2: LANGKAH 2 - INPUT 3 PILAR PAGI (06.00 WIB)          -->
            <!-- ============================================================ -->
            <div x-show="step === 1" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">2</span>
                    <div>
                        <span class="rounded bg-indigo-50 px-2 py-0.5 text-[11px] font-bold text-indigo-700 border border-indigo-200">
                            Langkah 2: Sesi 06.00 Pagi (Baseline)
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Input Data &amp; Unggah 3 Pilar Foto Sesi Pagi
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Saat mengklik <b>Input Retain Sampel</b> dan memilih Sesi 06.00 WIB:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3 text-xs text-slate-700">
                    <div class="flex items-start gap-3 rounded-xl bg-blue-50/60 p-3 border border-blue-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#0f3861] text-white font-black text-xs">A</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Unggah Foto Botol Retain 06.00 WIB</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Klik tombol <b>Pilih Foto Botol</b> untuk mengunggah foto jajaran botol bening sampel produk awal (Pertalite, Pertamax, Biosolar).
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-xl bg-amber-50/60 p-3 border border-amber-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#e86a17] text-white font-black text-xs">B</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Unggah Visual 3 Mobil Tangki (MT) Pertama</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Masukkan nomor polisi armada pertama dan unggah foto kompartemen 3 MT awal penerima produk di filling shed.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-xl bg-emerald-50/60 p-3 border border-emerald-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-700 text-white font-black text-xs">C</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Unggah Visual Tangki Timbun Aktif</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Pilih kode tangki timbun (misal T.09 Pertamax / T.04 Biosolar) dan unggah foto fisik tangki timbun penyaluran.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 3: LANGKAH 3 - INPUT 12.00 & 18.00 (PEMBANDING)        -->
            <!-- ============================================================ -->
            <div x-show="step === 2" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">3</span>
                    <div>
                        <span class="rounded bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-800 border border-amber-200">
                            Langkah 3: Sesi Siang &amp; Sore (Side-by-Side)
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Input Sesi 12.00 &amp; 18.00 untuk Pembanding Otomatis
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Sistem secara otomatis menghubungkan data sesi siang dan sore dengan acuan pagi:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-2xl border-2 border-blue-300 bg-blue-50/50 p-4">
                            <span class="inline-block rounded-full bg-brand-blue px-2.5 py-0.5 text-[10px] font-black text-white uppercase tracking-wider">
                                Kotak Kiri (Otomatis Terisi)
                            </span>
                            <h4 class="text-xs font-black text-slate-900 mt-1.5">Baseline 06.00 WIB Pagi</h4>
                            <p class="text-[11px] text-slate-600 mt-1">Foto botol &amp; hasil uji jam 6 pagi otomatis muncul sebagai pembanding patokan.</p>
                        </div>

                        <div class="rounded-2xl border-2 border-amber-400 bg-amber-50/50 p-4">
                            <span class="inline-block rounded-full bg-[#e86a17] px-2.5 py-0.5 text-[10px] font-black text-white uppercase tracking-wider">
                                Kotak Kanan (Input Petugas)
                            </span>
                            <h4 class="text-xs font-black text-slate-900 mt-1.5">Retain 12.00 / 18.00 WIB</h4>
                            <p class="text-[11px] text-slate-600 mt-1">Unggah foto botol siang/sore &amp; masukkan hasil uji terkini.</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-100 p-3 text-[11.5px] text-slate-600 flex items-center gap-2.5 border border-slate-200/80">
                        <i data-lucide="info" class="h-4 w-4 text-brand-blue shrink-0"></i>
                        <span>Petugas tidak perlu mengunggah ulang data jam 6 pagi saat input jam 12 atau 18, web langsung menggabungkannya secara berdampingan.</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 4: LANGKAH 4 - ASTM DENSITY 15 OTOMATIS                -->
            <!-- ============================================================ -->
            <div x-show="step === 3" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">4</span>
                    <div>
                        <span class="rounded bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-800 border border-emerald-200">
                            Langkah 4: Hitung Otomatis Density'15
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Ketik Density Observed &amp; Suhu (°C)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Cukup ketik nilai mentah pengujian hidrometer di laboratorium lapangan:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">1. Yang Diinput Petugas:</span>
                            <ul class="mt-2 space-y-1.5 text-slate-700 font-semibold">
                                <li class="flex items-center gap-1.5"><i data-lucide="edit-3" class="h-3.5 w-3.5 text-brand-blue"></i> <b>Density Observed</b>: Ketik misal <code>0.7380</code></li>
                                <li class="flex items-center gap-1.5"><i data-lucide="thermometer" class="h-3.5 w-3.5 text-brand-blue"></i> <b>Suhu Uji</b>: Ketik misal <code>29.5</code> °C</li>
                            </ul>
                        </div>
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">2. Hasil Instan Otomatis:</span>
                            <ul class="mt-2 space-y-1.5 text-emerald-900 font-semibold">
                                <li class="flex items-center gap-1.5"><i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-emerald-600"></i> <b>Density'15</b>: <code>0.7482</code> (4 desimal)</li>
                                <li class="flex items-center gap-1.5"><i data-lucide="shield-check" class="h-3.5 w-3.5 text-emerald-600"></i> Terverifikasi formula ASTM Table 53B</li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic">
                        *Lalu klik tombol simpan <span class="font-bold text-brand-blue">"Simpan Data Retain"</span> untuk memperbarui laporan slide.
                    </p>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 5: LANGKAH 5 - PREVIEW SLIDE, VIDEO DEMO & UNDUH BANNER -->
            <!-- ============================================================ -->
            <div x-show="step === 4" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">5</span>
                    <div>
                        <span class="rounded bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-brand-red border border-rose-200">
                            Langkah 5: Simulasi Video Berjalan &amp; Unduh Banner
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Klik Tab Slide Laporan &amp; Download Banner PNG HD
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Simulasi alur klik tombol web saat digunakan secara langsung:
                        </p>
                    </div>
                </div>

                <!-- Video Walkthrough Simulation Player -->
                <div class="relative overflow-hidden rounded-2xl border border-slate-300 bg-slate-900 shadow-md text-white">
                    <!-- Top Player Bar -->
                    <div class="flex items-center justify-between bg-slate-800/90 px-4 py-2 text-xs border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="font-bold text-slate-200 text-[11px]">Simulasi Interaktif: PT Pertamina Patra Niaga FT Maos</span>
                        </div>
                        <span class="rounded bg-brand-blue px-2 py-0.5 text-[10px] font-bold text-white uppercase">Live Running Video</span>
                    </div>

                    <!-- Screen Demo Container -->
                    <div class="p-4 bg-gradient-to-br from-[#07172b] to-[#0f3861] min-h-[180px] flex flex-col justify-between relative">
                        <!-- Simulated Top Tabs -->
                        <div class="flex items-center justify-between border-b border-white/10 pb-2.5">
                            <div class="flex items-center gap-1.5">
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold transition duration-300 cursor-pointer"
                                      :class="demoTab === 0 ? 'bg-brand-red text-white ring-2 ring-white/50 scale-105' : 'bg-white/10 text-slate-300'">06.00 (Pagi)</span>
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold transition duration-300 cursor-pointer"
                                      :class="demoTab === 1 ? 'bg-brand-blue text-white ring-2 ring-white/50 scale-105' : 'bg-white/10 text-slate-300'">12.00 (Siang)</span>
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold transition duration-300 cursor-pointer"
                                      :class="demoTab === 2 ? 'bg-brand-blue text-white ring-2 ring-white/50 scale-105' : 'bg-white/10 text-slate-300'">18.00 (Sore)</span>
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold transition duration-300 cursor-pointer"
                                      :class="demoTab === 3 ? 'bg-emerald-600 text-white ring-2 ring-white/50 scale-105' : 'bg-white/10 text-slate-300'">Rekap Semua</span>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded bg-amber-400 text-slate-900 px-2.5 py-1 text-[10px] font-extrabold shadow-sm transition"
                                  :class="demoTab === 3 ? 'scale-110 ring-2 ring-amber-200' : ''">
                                <i data-lucide="download" class="h-3.5 w-3.5"></i> Unduh Banner PNG
                            </span>
                        </div>

                        <!-- Dynamic Demo Card Preview -->
                        <div class="my-3 rounded-xl bg-white/10 backdrop-blur-sm p-3.5 border border-white/10 relative">
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="mouse-pointer-click" class="h-3.5 w-3.5 text-amber-300 animate-bounce"></i>
                                        <p class="font-extrabold text-amber-300 text-xs" x-text="demoSteps[demoTab].action"></p>
                                    </div>
                                    <p class="text-[10.5px] text-slate-300 mt-1" x-text="demoSteps[demoTab].desc"></p>
                                </div>
                                <span class="rounded bg-white/20 px-2 py-1 text-[9.5px] font-mono text-white font-bold" x-text="demoSteps[demoTab].badge"></span>
                            </div>
                        </div>

                        <!-- Bottom Controls of Video Player -->
                        <div class="flex items-center justify-between text-xs pt-1">
                            <button type="button" @click="toggleDemoPlay()" 
                                    class="inline-flex items-center gap-1 rounded-lg bg-white/20 hover:bg-white/30 px-2.5 py-1 text-[10.5px] font-bold text-white transition">
                                <i :data-lucide="demoPlaying ? 'pause' : 'play'" class="h-3 w-3"></i>
                                <span x-text="demoPlaying ? 'Jeda Simulasi' : 'Putar Ulang Video'"></span>
                            </button>
                            <span class="text-[10px] text-slate-300 font-mono" x-text="'Langkah ' + (demoTab + 1) + ' dari 4: Klik ' + demoSteps[demoTab].tabName"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Navigation Controls -->
            <div class="mt-6 pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" 
                        @click="prevStep()" 
                        :disabled="step === 0"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i> Sebelumnya
                </button>

                <!-- Voice Replay Button -->
                <button type="button" 
                        @click="speakNarration()" 
                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 hover:bg-slate-200 px-3 py-1 text-[11px] font-bold text-slate-700 transition">
                    <i data-lucide="rotate-ccw" class="h-3 w-3 text-brand-blue"></i>
                    <span>Ulangi Suara &amp; Musik</span>
                </button>

                <button type="button" 
                        @click="nextStep()" 
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue hover:bg-brand-blueDark px-4 py-2 text-xs font-bold text-white shadow-sm transition">
                    <span x-text="step === steps.length - 1 ? 'Selesai &amp; Siap Operasional' : 'Langkah Selanjutnya'"></span>
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
            soundEnabled: true,
            voicePlaying: false,
            demoTab: 0,
            demoPlaying: true,
            demoInterval: null,
            speechSynth: window.speechSynthesis || null,
            currentUtterance: null,
            audioCtx: null,
            bgmGainNode: null,
            bgmInterval: null,
            bgmChordIndex: 0,
            steps: [
                { 
                    navTitle: 'Akses & Filter',
                    narration: 'Langkah pertama: Buka menu Retain Sampel MT di sebelah kiri. Anda dapat memilih filter tanggal untuk melihat arsip atau klik tombol Input Retain Sampel untuk memasukkan data baru.'
                },
                { 
                    navTitle: 'Input 06.00 Pagi',
                    narration: 'Langkah kedua: Pada sesi jam enam pagi, masukkan data awal dan unggah tiga foto pilar mutu: yaitu Foto Botol Retain, visual kompartemen tiga mobil tangki pertama, dan visual tangki timbun yang aktif beroperasi.'
                },
                { 
                    navTitle: 'Input Sesi 12 & 18',
                    narration: 'Langkah ketiga: Untuk sesi jam dua belas siang dan delapan belas sore, Anda cukup mengunggah foto sampel dan data terkini. Sistem secara otomatis menampilkan kotak acuan jam enam pagi di sebelah kiri sebagai pembanding.'
                },
                { 
                    navTitle: 'Hitung ASTM 53B',
                    narration: 'Langkah keempat: Masukkan nilai density observed dan suhu pengujian. Sistem secara otomatis menghitung nilai density lima belas derajat celcius sesuai tabel lima puluh tiga B ASTM dengan presisi empat angka desimal resmi migas.'
                },
                { 
                    navTitle: 'Simulasi & Unduh',
                    narration: 'Langkah kelima: Klik tab sesi yang ingin dilihat, lalu klik tombol Unduh Banner PNG HD. Banner laporan resmi resolusi tinggi siap langsung dikirimkan ke grup koordinasi regional Pertamina.'
                },
            ],
            demoSteps: [
                { tabName: 'Tab 06.00', action: 'Klik Tab Sesi 06.00 WIB', desc: 'Menampilkan Slide Pagi 3 Pilar: Botol Retain + 3 MT Pertama + Tangki Timbun', badge: '1. Baseline 06.00' },
                { tabName: 'Tab 12.00', action: 'Klik Tab Sesi 12.00 WIB', desc: 'Menampilkan Perbandingan Berdampingan: Acuan 06.00 (Kiri) vs Retain 12.00 (Kanan)', badge: '2. Komparasi 12.00' },
                { tabName: 'Tab 18.00', action: 'Klik Tab Sesi 18.00 WIB', desc: 'Menampilkan Evaluasi Sore Berdampingan: Acuan 06.00 (Kiri) vs Retain 18.00 (Kanan)', badge: '3. Komparasi 18.00' },
                { tabName: 'Unduh Banner', action: 'Klik Tombol Unduh Banner PNG', desc: 'Menghasilkan gambar resolusi tinggi (PNG HD) siap kirim WhatsApp Regional', badge: '4. Ekspor HD Selesai' },
            ],
            init() {
                window.addEventListener('open-retain-tutorial', () => {
                    this.step = 0;
                    this.open = true;
                    this.initAudioContext();
                    this.startBgm();
                    this.startDemoLoop();
                    this.$nextTick(() => {
                        lucide.createIcons();
                        this.speakNarration();
                    });
                });

                this.$watch('step', () => {
                    this.$nextTick(() => lucide.createIcons());
                    this.speakNarration();
                });
            },
            initAudioContext() {
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!this.audioCtx && AudioContext) {
                        this.audioCtx = new AudioContext();
                        this.bgmGainNode = this.audioCtx.createGain();
                        this.bgmGainNode.gain.setValueAtTime(0.04, this.audioCtx.currentTime); // Lembut sebagai ambient background
                        this.bgmGainNode.connect(this.audioCtx.destination);
                    }
                    if (this.audioCtx && this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume();
                    }
                } catch (e) {
                    console.log('Web Audio not initialized:', e);
                }
            },
            startBgm() {
                if (!this.soundEnabled || !this.audioCtx) return;
                this.stopBgm();

                // Pola melodi ambient korporat lembut (C major 7th / F major 9th progression)
                const chords = [
                    [261.63, 329.63, 392.00, 493.88], // Cmaj7 (C4, E4, G4, B4)
                    [220.00, 261.63, 329.63, 392.00], // Am7   (A3, C4, E4, G4)
                    [174.61, 220.00, 261.63, 329.63], // Fmaj7 (F3, A3, C4, E4)
                    [196.00, 246.94, 293.66, 392.00]  // G     (G3, B3, D4, G4)
                ];

                const playChord = () => {
                    if (!this.soundEnabled || !this.audioCtx || this.audioCtx.state !== 'running') return;
                    const chord = chords[this.bgmChordIndex % chords.length];
                    this.bgmChordIndex++;

                    chord.forEach((freq, i) => {
                        try {
                            const osc = this.audioCtx.createOscillator();
                            const gain = this.audioCtx.createGain();
                            
                            osc.type = 'sine'; // Suara lembut dan elegan
                            osc.frequency.setValueAtTime(freq, this.audioCtx.currentTime);

                            // Ducking volume: saat voicePlaying volume musik mengecil lembut
                            const targetVol = this.voicePlaying ? 0.015 : 0.035;
                            gain.gain.setValueAtTime(0, this.audioCtx.currentTime);
                            gain.gain.linearRampToValueAtTime(targetVol, this.audioCtx.currentTime + 0.8 + (i * 0.15));
                            gain.gain.exponentialRampToValueAtTime(0.0001, this.audioCtx.currentTime + 3.2);

                            osc.connect(gain);
                            gain.connect(this.bgmGainNode);

                            osc.start(this.audioCtx.currentTime + (i * 0.1));
                            osc.stop(this.audioCtx.currentTime + 3.4);
                        } catch (err) {}
                    });
                };

                playChord();
                this.bgmInterval = setInterval(playChord, 3000);
            },
            stopBgm() {
                if (this.bgmInterval) {
                    clearInterval(this.bgmInterval);
                    this.bgmInterval = null;
                }
            },
            closeModal() {
                this.open = false;
                this.stopSpeech();
                this.stopBgm();
                this.stopDemoLoop();
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
            },
            toggleAudio() {
                this.soundEnabled = !this.soundEnabled;
                if (!this.soundEnabled) {
                    this.stopSpeech();
                    this.stopBgm();
                } else {
                    this.initAudioContext();
                    this.startBgm();
                    this.speakNarration();
                }
            },
            stopSpeech() {
                if (this.speechSynth) {
                    this.speechSynth.cancel();
                }
                this.voicePlaying = false;
            },
            speakNarration() {
                if (!this.soundEnabled || !this.speechSynth) return;
                this.stopSpeech();
                this.initAudioContext();

                try {
                    const text = this.steps[this.step].narration;
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95; // Kecepatan jelas & mudah dipahami
                    utterance.pitch = 1.0;

                    utterance.onstart = () => {
                        this.voicePlaying = true;
                    };
                    utterance.onend = () => {
                        this.voicePlaying = false;
                    };
                    utterance.onerror = () => {
                        this.voicePlaying = false;
                    };

                    this.currentUtterance = utterance;
                    this.speechSynth.speak(utterance);
                } catch (e) {
                    this.voicePlaying = false;
                }
            },
            startDemoLoop() {
                this.stopDemoLoop();
                this.demoPlaying = true;
                this.demoInterval = setInterval(() => {
                    if (this.demoPlaying) {
                        this.demoTab = (this.demoTab + 1) % this.demoSteps.length;
                        this.$nextTick(() => lucide.createIcons());
                    }
                }, 2800);
            },
            stopDemoLoop() {
                if (this.demoInterval) {
                    clearInterval(this.demoInterval);
                    this.demoInterval = null;
                }
            },
            toggleDemoPlay() {
                this.demoPlaying = !this.demoPlaying;
            }
        };
    }
</script>
