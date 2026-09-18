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
                    <!-- Indikator Animasi Musik Berjalan -->
                    <template x-if="soundEnabled">
                        <div class="hidden sm:flex items-center gap-1 bg-white/10 px-2.5 py-1.5 rounded-xl border border-white/15 text-[11px] font-medium text-emerald-300">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Musik &amp; Suara Aktif</span>
                        </div>
                    </template>

                    <!-- Tombol Kontrol Suara & Musik -->
                    <button type="button" 
                            @click="toggleAudio()" 
                            :class="soundEnabled ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-rose-500/20 text-rose-300 hover:bg-rose-500/30'"
                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition shadow-sm"
                            :title="soundEnabled ? 'Klik untuk matikan suara & musik' : 'Klik untuk aktifkan suara & musik'">
                        <i :data-lucide="soundEnabled ? 'volume-2' : 'volume-x'" class="h-4 w-4"></i>
                        <span class="hidden sm:inline" x-text="soundEnabled ? 'Mute' : 'Bunyikan'"></span>
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
                            Buka Menu Retain Sampel MT di Bawah Dashboard
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
                            <h4 class="text-xs font-black text-slate-900">Menu Checklist Lapangan</h4>
                        </div>
                        <p class="text-[11.5px] text-slate-600 leading-relaxed">
                            Pada sidebar navigasi tepat di bawah Dashboard, klik menu <b>Retain Sampel MT</b>. Halaman akan menampilkan laporan visual retain harian.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white font-bold text-xs">B</span>
                            <h4 class="text-xs font-black text-slate-900">Pilih Tanggal &amp; Tambah Data</h4>
                        </div>
                        <p class="text-[11.5px] text-slate-600 leading-relaxed">
                            Gunakan pemilih tanggal untuk melihat arsip atau klik tombol merah <b>+ Tambah Data</b> untuk membuka formulir multi-produk sekaligus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 2: LANGKAH 2 - INPUT MULTI-PRODUK (1 TOMBOL SIMPAN)     -->
            <!-- ============================================================ -->
            <div x-show="step === 1" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">2</span>
                    <div>
                        <span class="rounded bg-indigo-50 px-2 py-0.5 text-[11px] font-bold text-indigo-700 border border-indigo-200">
                            Langkah 2: Form Multi-Produk Praktis
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Input Semua Produk Sekaligus (1 Tombol Simpan di Bawah)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Tidak perlu lagi menyimpan satu per satu per produk:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3 text-xs text-slate-700">
                    <div class="flex items-start gap-3 rounded-xl bg-blue-50/60 p-3 border border-blue-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#0f3861] text-white font-black text-xs">A</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">6 Produk Standar Terdaftar</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Form langsung menyediakan baris untuk: <b>Pertalite, Pertamax, Pertamax Turbo, Biosolar B50, Dexlite, dan Pertadex</b>.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-xl bg-amber-50/60 p-3 border border-amber-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#e86a17] text-white font-black text-xs">B</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Ketik Nopol, Tangki, Density &amp; Suhu</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Isi produk yang disalurkan pada sesi tersebut. Produk yang tidak disalurkan cukup dibiarkan kosong (otomatis dilewati).
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-xl bg-emerald-50/60 p-3 border border-emerald-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-700 text-white font-black text-xs">C</span>
                        <div>
                            <p class="font-extrabold text-slate-900 text-xs">Satu Tombol Simpan di Bawah</p>
                            <p class="text-[11.5px] text-slate-600 mt-0.5 leading-relaxed">
                                Cukup tekan tombol <b>"Simpan Semua Data Sampel"</b> di bagian paling bawah — semua data produk langsung tersimpan sekaligus!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 3: LANGKAH 3 - FORMAT SLIDE RESMI (06.00, 12.00, 18.00)-->
            <!-- ============================================================ -->
            <div x-show="step === 2" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">3</span>
                    <div>
                        <span class="rounded bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-800 border border-amber-200">
                            Langkah 3: Format Laporan Sesuai SOP FT MAOS
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Sesi Pagi 06.00 Tetap (Stay) &amp; Update Retain 12.00 / 18.00
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Struktur tampilan slide otomatis tersusun sejajar:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-2xl border-2 border-blue-300 bg-blue-50/50 p-3.5 text-left">
                            <span class="inline-block rounded-full bg-brand-blue px-2.5 py-0.5 text-[10px] font-black text-white uppercase tracking-wider">
                                Kotak Kiri (Stay / Tetap)
                            </span>
                            <h4 class="text-xs font-black text-slate-900 mt-1.5">Sampel Retain 06.00 WIB</h4>
                            <p class="text-[11px] text-slate-600 mt-1">Foto botol retain pagi &amp; tabel density tetap tampil dari pagi hingga selesai penyaluran sore.</p>
                        </div>

                        <div class="rounded-2xl border-2 border-amber-400 bg-amber-50/50 p-3.5 text-left">
                            <span class="inline-block rounded-full bg-[#e86a17] px-2.5 py-0.5 text-[10px] font-black text-white uppercase tracking-wider">
                                Kotak Kanan (Dinamis)
                            </span>
                            <h4 class="text-xs font-black text-slate-900 mt-1.5">Visual 3 MT (06.00) / Retain (12 &amp; 18)</h4>
                            <p class="text-[11px] text-slate-600 mt-1">Jam 06.00 menampilkan Visual Sales 3 MT &amp; Tangki. Jam 12.00 &amp; 18.00 menampilkan foto retain &amp; tabel pergantian produk.</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-100 p-2.5 text-[11.5px] text-slate-600 flex items-center gap-2 border border-slate-200/80">
                        <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>Analis cukup screenshot gambar slide yang sudah tergabung rapi untuk laporan broadcast WhatsApp.</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 4: LANGKAH 4 - ASTM DENSITY 15 OTOMATIS & EDIT CEPAT   -->
            <!-- ============================================================ -->
            <div x-show="step === 3" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">4</span>
                    <div>
                        <span class="rounded bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-800 border border-emerald-200">
                            Langkah 4: Hitung ASTM 53B &amp; Edit Cepat
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Density'15 Otomatis &amp; Tabel Input Langsung di Halaman
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Perhitungan instan dan kemudahan edit data:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">1. Realtime ASTM Tabel 53B:</span>
                            <ul class="mt-2 space-y-1.5 text-slate-700 font-semibold">
                                <li class="flex items-center gap-1.5"><i data-lucide="calculator" class="h-3.5 w-3.5 text-brand-blue"></i> Cukup ketik <b>Density Obs &amp; Suhu</b></li>
                                <li class="flex items-center gap-1.5"><i data-lucide="check" class="h-3.5 w-3.5 text-emerald-600"></i> <b>Density'15</b> langsung terhitung otomatis presisi 4 desimal</li>
                            </ul>
                        </div>
                        <div class="rounded-xl bg-blue-50 border border-blue-200 p-3.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue">2. Edit Cepat di Bawah Laporan:</span>
                            <ul class="mt-2 space-y-1.5 text-slate-800 font-semibold">
                                <li class="flex items-center gap-1.5"><i data-lucide="edit-3" class="h-3.5 w-3.5 text-brand-blue"></i> Tabel edit per sesi tersedia di bawah slide</li>
                                <li class="flex items-center gap-1.5"><i data-lucide="save" class="h-3.5 w-3.5 text-brand-red"></i> 1x klik <b>Simpan Semua Data</b> per sesi</li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic">
                        *Tidak ada lagi konfirmasi simpan berulang-ulang per produk.
                    </p>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SLIDE 5: LANGKAH 5 - SIMULASI & UNDUH BANNER HD              -->
            <!-- ============================================================ -->
            <div x-show="step === 4" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-3.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-brand-blue font-black text-base border border-blue-200">5</span>
                    <div>
                        <span class="rounded bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-brand-red border border-rose-200">
                            Langkah 5: Unduh Banner PNG HD &amp; Ekspor
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Pilih Tab Sesi &amp; Unduh Gambar Laporan Kualitas Tinggi
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            Simulasi alur tab laporan harian:
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
                        @click="playTutorialAudio()" 
                        class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 hover:bg-slate-200 px-3.5 py-1.5 text-[11px] font-bold text-slate-700 transition">
                    <i data-lucide="rotate-ccw" class="h-3 w-3 text-brand-blue"></i>
                    <span>Putar Ulang Suara &amp; Musik</span>
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
            audioCtx: null,
            bgmInterval: null,
            noteIndex: 0,
            speechPlayId: 0,
            currentAudio: null,
            steps: [
                { 
                    navTitle: 'Akses & Tanggal',
                    chunks: [
                        'Langkah pertama: Buka menu Retain Sampel MT pada navigasi sebelah kiri di bawah Dashboard.',
                        'Pilih tanggal operasional untuk melihat arsip, atau klik tombol Tambah Data untuk memasukkan data hari ini.'
                    ]
                },
                { 
                    navTitle: 'Form Multi-Produk',
                    chunks: [
                        'Langkah kedua: Formulir telah diperbarui menjadi format tabel multi produk sekaligus.',
                        'Tersedia enam produk resmi Pertamina: Pertalite, Pertamax, Pertamax Turbo, Biosolar B50, Dexlite, dan Pertadex.',
                        'Ketik nilai produk yang disalurkan, lalu cukup tekan satu tombol Simpan Semua Data di paling bawah.'
                    ]
                },
                { 
                    navTitle: 'Format Laporan SOP',
                    chunks: [
                        'Langkah ketiga: Laporan otomatis tersusun berdampingan.',
                        'Data jam enam pagi tetap bertahan di sisi kiri dari pagi sampai sore sebagai acuan baseline.',
                        'Sisi kanan menampilkan visual tiga mobil tangki pada jam enam pagi, serta update retain pada jam dua belas dan delapan belas sore.'
                    ]
                },
                { 
                    navTitle: 'ASTM 53B & Edit Cepat',
                    chunks: [
                        'Langkah keempat: Masukkan nilai density observed dan suhu pengujian.',
                        'Sistem otomatis mengkalkulasi density lima belas derajat celcius sesuai tabel lima puluh tiga B ASTM secara instan.',
                        'Anda juga bisa langsung mengedit data pada tabel di bawah slide dan menekan tombol simpan sesi.'
                    ]
                },
                { 
                    navTitle: 'Simulasi & Unduh',
                    chunks: [
                        'Langkah kelima: Klik tab sesi yang ingin dilihat, lalu klik tombol Unduh Banner PNG HD.',
                        'Gambar laporan resolusi tinggi siap langsung dikirimkan ke grup koordinasi WhatsApp.'
                    ]
                },
            ],
            demoSteps: [
                { tabName: 'Tab 06.00', action: 'Klik Tab Sesi 06.00 WIB', desc: 'Menampilkan Slide Pagi: Retain 06.00 (Kiri) + Visual 3 MT & Tangki Timbun (Kanan)', badge: '1. Baseline 06.00' },
                { tabName: 'Tab 12.00', action: 'Klik Tab Sesi 12.00 WIB', desc: 'Menampilkan Komparasi: Acuan 06.00 Stay (Kiri) vs Retain Siang 12.00 (Kanan)', badge: '2. Komparasi 12.00' },
                { tabName: 'Tab 18.00', action: 'Klik Tab Sesi 18.00 WIB', desc: 'Menampilkan Komparasi: Acuan 06.00 Stay (Kiri) vs Retain Sore 18.00 (Kanan)', badge: '3. Komparasi 18.00' },
                { tabName: 'Unduh Banner', action: 'Klik Tombol Unduh Banner PNG', desc: 'Menghasilkan gambar resolusi tinggi (PNG HD) siap kirim WhatsApp Regional', badge: '4. Ekspor HD Selesai' },
            ],
            init() {
                if (this.speechSynth && this.speechSynth.onvoiceschanged !== undefined) {
                    this.speechSynth.onvoiceschanged = () => {
                        this.speechSynth.getVoices();
                    };
                }

                window.addEventListener('open-retain-tutorial', () => {
                    this.step = 0;
                    this.open = true;
                    this.ensureAudioContext();
                    this.startDemoLoop();
                    this.playTutorialAudio();
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                });

                this.$watch('step', () => {
                    this.$nextTick(() => lucide.createIcons());
                    this.playTutorialAudio();
                });
            },
            ensureAudioContext() {
                try {
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    if (!this.audioCtx && AudioContextClass) {
                        this.audioCtx = new AudioContextClass();
                    }
                    if (this.audioCtx && this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume();
                    }
                } catch (e) {
                    console.error('AudioContext init error:', e);
                }
            },
            playBgmMelodyNote(freq, type = 'sine', gainVal = 0.08, dur = 0.8) {
                if (!this.soundEnabled || !this.audioCtx) return;
                try {
                    this.ensureAudioContext();
                    const now = this.audioCtx.currentTime;
                    const osc = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();

                    osc.type = type;
                    osc.frequency.setValueAtTime(freq, now);

                    gain.gain.setValueAtTime(0.001, now);
                    gain.gain.linearRampToValueAtTime(gainVal, now + 0.05);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + dur);

                    osc.connect(gain);
                    gain.connect(this.audioCtx.destination);

                    osc.start(now);
                    osc.stop(now + dur + 0.05);
                } catch (e) {}
            },
            startBgmEngine() {
                this.stopBgmEngine();
                if (!this.soundEnabled) return;
                this.ensureAudioContext();

                const melodyPattern = [
                    { note: 523.25, dur: 0.6, bass: 130.81 },
                    { note: 659.25, dur: 0.5, bass: null },
                    { note: 783.99, dur: 0.7, bass: 196.00 },
                    { note: 587.33, dur: 0.5, bass: null },
                    { note: 440.00, dur: 0.6, bass: 110.00 },
                    { note: 523.25, dur: 0.5, bass: null },
                    { note: 659.25, dur: 0.7, bass: 174.61 },
                    { note: 587.33, dur: 0.5, bass: null },
                ];

                this.noteIndex = 0;
                this.bgmInterval = setInterval(() => {
                    if (!this.soundEnabled) return;
                    const p = melodyPattern[this.noteIndex % melodyPattern.length];
                    this.noteIndex++;

                    const trebleVol = this.voicePlaying ? 0.025 : 0.08;
                    const bassVol = this.voicePlaying ? 0.02 : 0.06;

                    this.playBgmMelodyNote(p.note, 'sine', trebleVol, p.dur);
                    if (p.bass) {
                        this.playBgmMelodyNote(p.bass, 'triangle', bassVol, p.dur * 1.5);
                    }
                }, 480);
            },
            stopBgmEngine() {
                if (this.bgmInterval) {
                    clearInterval(this.bgmInterval);
                    this.bgmInterval = null;
                }
            },
            playTutorialAudio() {
                this.ensureAudioContext();
                this.startBgmEngine();
                this.speakNarration();
            },
            closeModal() {
                this.open = false;
                this.stopSpeech();
                this.stopBgmEngine();
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
                    this.stopBgmEngine();
                } else {
                    this.playTutorialAudio();
                }
            },
            stopSpeech() {
                this.speechPlayId++;
                if (this.currentAudio) {
                    try {
                        this.currentAudio.pause();
                        this.currentAudio.currentTime = 0;
                        this.currentAudio = null;
                    } catch (e) {}
                }
                if (this.speechSynth) {
                    try {
                        this.speechSynth.cancel();
                    } catch (e) {}
                }
                this.voicePlaying = false;
            },
            getIndonesianVoice() {
                if (!this.speechSynth) return null;
                const voices = this.speechSynth.getVoices() || [];
                if (!voices.length) return null;

                let idVoice = voices.find(v => {
                    const lang = (v.lang || '').toLowerCase().replace('_', '-');
                    const name = (v.name || '').toLowerCase();
                    return (lang === 'id-id' || lang === 'id' || lang.startsWith('id-') || lang.startsWith('in-')) &&
                           (name.includes('indonesia') || name.includes('damayanti') || name.includes('andika') || name.includes('gadis') || name.includes('natural') || name.includes('google'));
                });

                if (!idVoice) {
                    idVoice = voices.find(v => {
                        const lang = (v.lang || '').toLowerCase().replace('_', '-');
                        return lang === 'id-id' || lang === 'id' || lang.startsWith('id-') || lang.startsWith('in-');
                    });
                }

                if (!idVoice) {
                    idVoice = voices.find(v => (v.name || '').toLowerCase().includes('indonesia'));
                }

                return idVoice || null;
            },
            speakNarration() {
                if (!this.soundEnabled) return;
                this.stopSpeech();

                const curStep = this.steps[this.step];
                if (!curStep || !curStep.chunks || !curStep.chunks.length) return;

                const currentPlayId = ++this.speechPlayId;
                this.voicePlaying = true;

                let chunkIdx = 0;
                const playNextChunk = () => {
                    if (!this.soundEnabled || !this.voicePlaying || this.speechPlayId !== currentPlayId) {
                        this.voicePlaying = false;
                        return;
                    }
                    if (chunkIdx >= curStep.chunks.length) {
                        this.voicePlaying = false;
                        return;
                    }

                    const text = curStep.chunks[chunkIdx];
                    chunkIdx++;

                    const url = `https://translate.google.com/translate_tts?ie=UTF-8&tl=id&client=tw-ob&q=${encodeURIComponent(text)}`;
                    const audio = new Audio(url);
                    this.currentAudio = audio;

                    audio.onended = () => {
                        if (this.speechPlayId === currentPlayId) {
                            setTimeout(playNextChunk, 220);
                        }
                    };

                    audio.onerror = () => {
                        if (this.speechPlayId === currentPlayId) {
                            this.speakWebSpeechFallback(curStep.chunks.join(' '), currentPlayId);
                        }
                    };

                    audio.play().catch(e => {
                        if (this.speechPlayId === currentPlayId) {
                            this.speakWebSpeechFallback(curStep.chunks.join(' '), currentPlayId);
                        }
                    });
                };

                playNextChunk();
            },
            speakWebSpeechFallback(fullText, playId) {
                if (!this.speechSynth || !this.soundEnabled) {
                    this.voicePlaying = false;
                    return;
                }
                try {
                    const utterance = new SpeechSynthesisUtterance(fullText);
                    const idVoice = this.getIndonesianVoice();

                    if (idVoice) {
                        utterance.voice = idVoice;
                        utterance.lang = idVoice.lang || 'id-ID';
                    } else {
                        utterance.lang = 'id-ID';
                    }
                    utterance.rate = 0.95;
                    utterance.pitch = 1.0;

                    utterance.onstart = () => {
                        if (this.speechPlayId === playId) this.voicePlaying = true;
                    };
                    utterance.onend = () => {
                        if (this.speechPlayId === playId) this.voicePlaying = false;
                    };
                    utterance.onerror = () => {
                        if (this.speechPlayId === playId) this.voicePlaying = false;
                    };

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
