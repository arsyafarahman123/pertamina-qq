<!-- =========================================================================
     MODAL PETUNJUK OPERASIONAL & STANDAR LAPORAN RETAIN SAMPEL (DILENGKAPI SUARA & VIDEO)
     PT PERTAMINA PATRA NIAGA — FUEL TERMINAL MAOS (SOP QC-03)
     ========================================================================= -->
<div x-data="sopRetainSampel()" 
     x-show="open" 
     x-cloak 
     @keydown.escape.window="closeModal()"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity" 
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
                            Petunjuk Operasional &amp; Standar Laporan Retain Sampel
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Tombol Kontrol Suara Narator -->
                    <button type="button" 
                            @click="toggleAudio()" 
                            :class="voicePlaying ? 'bg-emerald-500 text-white animate-pulse' : (soundEnabled ? 'bg-white/20 text-white hover:bg-white/30' : 'bg-rose-500/20 text-rose-300 hover:bg-rose-500/30')"
                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition shadow-sm"
                            :title="soundEnabled ? 'Klik untuk jeda / matikan suara narator' : 'Klik untuk menyalakan suara narator'">
                        <i :data-lucide="voicePlaying ? 'volume-2' : (soundEnabled ? 'volume-1' : 'volume-x')" class="h-4 w-4"></i>
                        <span class="hidden sm:inline" x-text="voicePlaying ? 'Memutar Suara...' : (soundEnabled ? 'Suara Aktif' : 'Suara Mati')"></span>
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
            <!-- SLIDE 1: STRUKTUR SESI HARIAN                                -->
            <!-- ============================================================ -->
            <div x-show="step === 0" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">1</span>
                    <div>
                        <span class="rounded bg-blue-50 px-2 py-0.5 text-[11px] font-bold text-brand-blue border border-blue-200">
                            Struktur Sesi Harian
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Tiga Waktu Observasi Retain Sampel Penyaluran
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Di Fuel Terminal Maos, pengawasan kualitas retain sampel MT dilakukan dalam 3 sesi pengamatan berkala untuk menjamin mutu produk tetap stabil sepanjang operasional:
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <div class="rounded-2xl border border-blue-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-black text-[#0f3861]">06.00 WIB</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Pagi</span>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-900">Awal Penyaluran</h4>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            Sampel pertama saat pompa dan meter arus mulai mengalir. Berfungsi sebagai <b>acuan awal (baseline)</b> standar mutu.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-black text-[#e86a17]">12.00 WIB</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Siang</span>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-900">Retain Siang</h4>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            Pengambilan sampel siang untuk memantau perubahan temperatur lingkungan dan kestabilan density produk.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-red-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-black text-[#DA251D]">18.00 WIB</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Sore</span>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-900">Retain Sore</h4>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            Pengambilan sampel penutupan sore untuk evaluasi seluruh penyaluran harian sebelum rekap lengkap dibuat.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 2: SESI 06.00 (3 PILAR OPERASIONAL PAGI)               -->
            <!-- ============================================================ -->
            <div x-show="step === 1" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">2</span>
                    <div>
                        <span class="rounded bg-indigo-50 px-2 py-0.5 text-[11px] font-bold text-indigo-700 border border-indigo-200">
                            Sesi Pagi (Baseline Acuan Mutu)
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Format Slide Pukul 06.00 WIB: 3 Pilar Pengawasan
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Slide sesi pagi berdiri sendiri memuat 3 pilar dokumentasi mutu resmi Pertamina Patra Niaga:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3 text-xs text-slate-700">
                    <div class="flex items-start gap-3 rounded-xl bg-blue-50/60 p-3 border border-blue-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#0f3861] text-white font-black text-xs">A</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Foto Botol Retain 06.00 WIB</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Foto fisik jajaran botol sampel bening produk BBM (Pertalite, Pertamax, Biosolar) saat awal penyaluran untuk membuktikan kejernihan dan warna visual on-spec.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-xl bg-amber-50/60 p-3 border border-amber-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#e86a17] text-white font-black text-xs">B</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Visual 3 Mobil Tangki (MT) Pertama</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Dokumentasi visual pemeriksaan fisik kompartemen 3 armada tangki pertama yang menerima pengisian di filling shed Maos.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-xl bg-emerald-50/60 p-3 border border-emerald-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-700 text-white font-black text-xs">C</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Visual Tangki Timbun Aktif</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Foto tangki timbun yang sedang aktif beroperasi menyalurkan produk ke filling shed (misalnya Tangki T.09 Pertamax, Tangki T.04 Biosolar).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 3: SISTEM PEMBANDING SIDE-BY-SIDE 12.00 & 18.00         -->
            <!-- ============================================================ -->
            <div x-show="step === 2" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">3</span>
                    <div>
                        <span class="rounded bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-800 border border-amber-200">
                            Fitur Pembanding Samping-Menyamping
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Slide Jam 12.00 &amp; 18.00: Kotak Kiri 06.00 vs Kotak Kanan Sesi
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Untuk memudahkan evaluasi apakah terjadi perubahan mutu selama penyaluran siang dan sore:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-2xl border-2 border-blue-300 bg-blue-50/50 p-4">
                            <span class="inline-block rounded-full bg-brand-blue px-2.5 py-0.5 text-[10px] font-black text-white uppercase tracking-wider">
                                Kotak Kiri (Tetap)
                            </span>
                            <h4 class="text-xs font-black text-slate-900 mt-1.5">Sampel 06.00 WIB Pagi</h4>
                            <p class="text-[11px] text-slate-600 mt-1">Foto botol &amp; tabel hasil uji 06.00 sebagai patokan standar acuan.</p>
                        </div>

                        <div class="rounded-2xl border-2 border-amber-400 bg-amber-50/50 p-4">
                            <span class="inline-block rounded-full bg-[#e86a17] px-2.5 py-0.5 text-[10px] font-black text-white uppercase tracking-wider">
                                Kotak Kanan (Terkini)
                            </span>
                            <h4 class="text-xs font-black text-slate-900 mt-1.5">Retain 12.00 / 18.00 WIB</h4>
                            <p class="text-[11px] text-slate-600 mt-1">Foto botol retain terkini &amp; tabel hasil uji sesi siang atau sore.</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-100 p-3 text-[11.5px] text-slate-600 flex items-center gap-2.5 border border-slate-200/80">
                        <i data-lucide="arrow-left-right" class="h-4 w-4 text-brand-blue shrink-0"></i>
                        <span>Dengan tampilan berdampingan, tim QC dan Region dapat langsung membandingkan warna produk dan nilai density tanpa perlu membuka dua file terpisah.</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 4: ASTM DENSITY 15 OTOMATIS                            -->
            <!-- ============================================================ -->
            <div x-show="step === 3" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">4</span>
                    <div>
                        <span class="rounded bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-800 border border-emerald-200">
                            Perhitungan Standar Migas
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Density'15 Otomatis ASTM Table 53B (Presisi 4 Desimal)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Sistem menghitung berat jenis terstandarisasi 15°C secara otomatis dan akurat:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Petugas Memasukkan:</span>
                            <ul class="mt-2 space-y-1 text-slate-700 font-semibold">
                                <li class="flex items-center gap-1.5"><i data-lucide="check" class="h-3.5 w-3.5 text-brand-blue"></i> <b>Density Observed</b> (misal: <code>0.7380</code>)</li>
                                <li class="flex items-center gap-1.5"><i data-lucide="check" class="h-3.5 w-3.5 text-brand-blue"></i> <b>Suhu Pengujian</b> (misal: <code>29.5 °C</code>)</li>
                            </ul>
                        </div>
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Hasil Dihitung Otomatis:</span>
                            <ul class="mt-2 space-y-1 text-emerald-900 font-semibold">
                                <li class="flex items-center gap-1.5"><i data-lucide="calculator" class="h-3.5 w-3.5 text-emerald-600"></i> <b>Density'15</b>: <code>0.7482</code></li>
                                <li class="flex items-center gap-1.5"><i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-emerald-600"></i> Sesuai formula ASTM Table 53B</li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic">
                        *Menghilangkan risiko kesalahan membaca buku tabel fisik dan mempercepat pembuatan laporan QC.
                    </p>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 5: EKSPOR RESMI & VIDEO DEMO WALKTHROUGH              -->
            <!-- ============================================================ -->
            <div x-show="step === 4" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">5</span>
                    <div>
                        <span class="rounded bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-brand-red border border-rose-200">
                            Demo Video &amp; Ekspor Resmi
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Simulasi Alur Aplikasi &amp; Siap Kirim Regional (RCBT)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Simulasi interaktif cara kerja modul Retain Sampel FT Maos saat digunakan:
                        </p>
                    </div>
                </div>

                <!-- Video Walkthrough Simulation Player -->
                <div class="overflow-hidden rounded-2xl border border-slate-300 bg-slate-900 shadow-md text-white">
                    <!-- Top Player Bar -->
                    <div class="flex items-center justify-between bg-slate-800/90 px-4 py-2 text-xs border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="font-bold text-slate-200 text-[11px]">Demo Operasional: FT Maos QC Retain</span>
                        </div>
                        <span class="rounded bg-brand-blue px-2 py-0.5 text-[10px] font-bold text-white uppercase">Live Simulation</span>
                    </div>

                    <!-- Screen Demo Container -->
                    <div class="p-4 bg-gradient-to-br from-[#07172b] to-[#0f3861] min-h-[170px] flex flex-col justify-between">
                        <!-- Simulated Top Tabs -->
                        <div class="flex items-center justify-between border-b border-white/10 pb-2.5">
                            <div class="flex items-center gap-1.5">
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold"
                                      :class="demoTab === 0 ? 'bg-brand-red text-white' : 'bg-white/10 text-slate-300'">06.00 (Pagi)</span>
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold"
                                      :class="demoTab === 1 ? 'bg-brand-blue text-white' : 'bg-white/10 text-slate-300'">12.00 (Siang)</span>
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold"
                                      :class="demoTab === 2 ? 'bg-brand-blue text-white' : 'bg-white/10 text-slate-300'">18.00 (Sore)</span>
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold"
                                      :class="demoTab === 3 ? 'bg-emerald-600 text-white' : 'bg-white/10 text-slate-300'">Rekap Semua</span>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded bg-amber-400 text-slate-900 px-2 py-0.5 text-[10px] font-extrabold shadow-sm">
                                <i data-lucide="download" class="h-3 w-3"></i> PNG HD
                            </span>
                        </div>

                        <!-- Dynamic Demo Card Preview -->
                        <div class="my-3 rounded-xl bg-white/10 backdrop-blur-sm p-3 border border-white/10">
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-extrabold text-amber-300 text-[11px]" x-text="demoSteps[demoTab].title"></p>
                                    <p class="text-[10px] text-slate-300 mt-0.5" x-text="demoSteps[demoTab].desc"></p>
                                </div>
                                <span class="rounded bg-white/20 px-2 py-1 text-[9px] font-mono text-white" x-text="demoSteps[demoTab].badge"></span>
                            </div>
                        </div>

                        <!-- Bottom Controls of Video Player -->
                        <div class="flex items-center justify-between text-xs pt-1">
                            <button type="button" @click="toggleDemoPlay()" 
                                    class="inline-flex items-center gap-1 rounded-lg bg-white/20 hover:bg-white/30 px-2.5 py-1 text-[10.5px] font-bold text-white transition">
                                <i :data-lucide="demoPlaying ? 'pause' : 'play'" class="h-3 w-3"></i>
                                <span x-text="demoPlaying ? 'Jeda Simulasi' : 'Putar Ulang'"></span>
                            </button>
                            <span class="text-[10px] text-slate-400 font-mono" x-text="'Langkah ' + (demoTab + 1) + ' dari 4'"></span>
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
                    <span>Ulangi Suara</span>
                </button>

                <button type="button" 
                        @click="nextStep()" 
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue hover:bg-brand-blueDark px-4 py-2 text-xs font-bold text-white shadow-sm transition">
                    <span x-text="step === steps.length - 1 ? 'Selesai &amp; Paham' : 'Selanjutnya'"></span>
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
            steps: [
                { 
                    navTitle: 'Struktur Sesi',
                    narration: 'Selamat datang di panduan operasional Retain Sampel FT Maos. Pengawasan mutu dilakukan dalam tiga sesi rutin yaitu jam enam pagi, dua belas siang, dan delapan belas sore untuk memastikan mutu BBM selalu stabil.'
                },
                { 
                    navTitle: 'Sesi 06.00 Pagi',
                    narration: 'Sesi jam enam pagi adalah acuan standar mutu awal. Memuat tiga pilar operasional: Pilar A yaitu Foto Botol Retain pagi, Pilar B Dokumentasi Visual Tiga Mobil Tangki pertama, dan Pilar C Foto Tangki Timbun aktif.'
                },
                { 
                    navTitle: 'Sesi 12 & 18',
                    narration: 'Pada sesi jam dua belas siang dan delapan belas sore, tampilan dibagi dua berdampingan. Kotak kiri adalah acuan jam enam pagi, dan kotak kanan adalah sampel retain terkini untuk membandingkan stabilitas visual dan berat jenis.'
                },
                { 
                    navTitle: 'ASTM Density 15',
                    narration: 'Nilai density lima belas derajat celcius dihitung otomatis oleh sistem menggunakan formula standar tabel lima puluh tiga B ASTM dengan format presisi empat angka desimal resmi migas.'
                },
                { 
                    navTitle: 'Ekspor & Demo',
                    narration: 'Setiap slide siap diekspor sekali klik menjadi banner resolusi tinggi dan siap dibagikan langsung ke grup WhatsApp Regional tanpa perlu dipotong manual.'
                },
            ],
            demoSteps: [
                { title: 'Sesi 06.00 WIB (Awal Penyaluran)', desc: 'Botol Retain Pagi + Visual 3 MT + Visual Tangki Timbun', badge: 'Baseline 06.00' },
                { title: 'Sesi 12.00 WIB (Pembanding Siang)', desc: 'Kotak Kiri (06.00 Acuan) vs Kotak Kanan (Retain 12.00 Siang)', badge: 'Perbandingan 12.00' },
                { title: 'Sesi 18.00 WIB (Pembanding Sore)', desc: 'Kotak Kiri (06.00 Acuan) vs Kotak Kanan (Retain 18.00 Sore)', badge: 'Perbandingan 18.00' },
                { title: 'Rekap Keseluruhan & Unduh Banner', desc: 'Matriks seluruh produk + Nopol MT + Download PNG HD siap kirim', badge: 'Export Banner HD' },
            ],
            init() {
                window.addEventListener('open-retain-tutorial', () => {
                    this.step = 0;
                    this.open = true;
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
            closeModal() {
                this.open = false;
                this.stopSpeech();
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
                } else {
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

                try {
                    const text = this.steps[this.step].narration;
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95; // Kecepatan nyaman & jelas didengar
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
