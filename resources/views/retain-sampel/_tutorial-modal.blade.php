<!-- =========================================================================
     MODAL TUTORIAL & PANDUAN PENGGUNAAN INTERAKTIF RETAIN SAMPEL
     ========================================================================= -->
<div x-data="tutorialRetainSampel()" 
     x-show="open" 
     x-cloak 
     @keydown.escape.window="closeTutorial()"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity" 
         @click="closeTutorial()"
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Modal Box Container -->
    <div class="relative w-full max-w-3xl overflow-hidden rounded-3xl border border-white/20 bg-white shadow-2xl transition-all my-auto"
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">

        <!-- Top Gradient Header -->
        <div class="bg-gradient-to-r from-[#0f3861] via-[#006CB8] to-[#123e6b] px-6 py-4 text-white relative">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 text-amber-300 shadow-inner backdrop-blur-sm">
                        <i data-lucide="sparkles" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded bg-amber-400/20 px-2 py-0.5 text-[10px] font-extrabold text-amber-300 ring-1 ring-amber-400/30 uppercase tracking-wider">
                                Panduan Interaktif
                            </span>
                            <span class="text-[11px] text-blue-200 font-medium">FT Maos QC</span>
                        </div>
                        <h2 class="font-display text-base sm:text-lg font-black text-white tracking-tight">
                            Cara Membaca &amp; Menggunakan Laporan Retain Sampel
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Tombol Audio / Voice Toggle -->
                    <button type="button" 
                            @click="toggleAudio()" 
                            :title="soundEnabled ? 'Matikan Suara Narasi' : 'Nyalakan Suara Narasi'"
                            class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition backdrop-blur-md"
                            :class="soundEnabled ? 'bg-amber-400 text-slate-900 shadow-md shadow-amber-400/20' : 'bg-white/10 text-white/70 hover:bg-white/20'">
                        <i :data-lucide="soundEnabled ? 'volume-2' : 'volume-x'" class="h-4 w-4"></i>
                        <span class="hidden sm:inline" x-text="soundEnabled ? 'Suara Aktif' : 'Mute'"></span>
                    </button>

                    <!-- Tombol Close -->
                    <button type="button" 
                            @click="closeTutorial()" 
                            class="rounded-xl p-1.5 text-white/70 hover:bg-white/10 hover:text-white transition">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>

            <!-- Stepper Progress Bar -->
            <div class="mt-4 flex items-center justify-between gap-1.5 border-t border-white/10 pt-3">
                <template x-for="(s, index) in steps" :key="index">
                    <button type="button" 
                            @click="goToStep(index)" 
                            class="flex-1 text-left group">
                        <div class="h-1.5 w-full rounded-full transition-all duration-300"
                             :class="step === index ? 'bg-amber-400 shadow-sm shadow-amber-300' : (step > index ? 'bg-emerald-400' : 'bg-white/20')"></div>
                        <p class="mt-1 text-[10px] font-bold truncate transition"
                           :class="step === index ? 'text-amber-300 font-extrabold' : 'text-white/60 group-hover:text-white/90'"
                           x-text="(index + 1) + '. ' + s.shortTitle"></p>
                    </button>
                </template>
            </div>
        </div>

        <!-- Body Content Per Step (Animated Slide) -->
        <div class="p-6 sm:p-7 min-h-[340px] flex flex-col justify-between bg-slate-50/50">

            <!-- STEP 0: KONSEP UTAMA -->
            <div x-show="step === 0" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-brand-blue font-black text-xl shadow-sm">
                        1
                    </div>
                    <div>
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-brand-blue border border-blue-200">
                            Struktur Laporan Harian
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Tiga Sesi Observasi Retain Sampel (06.00, 12.00 &amp; 18.00 WIB)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                            Di Fuel Terminal Maos, pengawasan mutu fisik dan visual BBM dilakukan secara berkala 3 kali dalam sehari untuk memastikan tidak ada perubahan spesifikasi selama penyaluran armada Mobil Tangki (MT).
                        </p>
                    </div>
                </div>

                <!-- 3 Cards Layout -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-3.5 text-center">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-[#0f3861] text-white text-xs font-bold mb-1.5">06</span>
                        <h4 class="text-xs font-extrabold text-[#0f3861]">06.00 WIB</h4>
                        <p class="text-[11px] text-slate-600 mt-1">Awal Penyaluran MT (Sampel Baseline Acuan)</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-3.5 text-center">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-[#e86a17] text-white text-xs font-bold mb-1.5">12</span>
                        <h4 class="text-xs font-extrabold text-amber-900">12.00 WIB</h4>
                        <p class="text-[11px] text-slate-600 mt-1">Retain Siang (Diuji &amp; Dibandingkan ke 06.00)</p>
                    </div>
                    <div class="rounded-2xl border border-red-200 bg-red-50/60 p-3.5 text-center">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-[#DA251D] text-white text-xs font-bold mb-1.5">18</span>
                        <h4 class="text-xs font-extrabold text-red-900">18.00 WIB</h4>
                        <p class="text-[11px] text-slate-600 mt-1">Retain Sore (Diuji &amp; Dibandingkan ke 06.00)</p>
                    </div>
                </div>
            </div>

            <!-- STEP 1: SESI 06.00 (AWAL PENYALURAN) -->
            <div x-show="step === 1" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700 font-black text-xl shadow-sm">
                        2
                    </div>
                    <div>
                        <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700 border border-indigo-200">
                            Sesi Pagi
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Slide Pukul 06.00 WIB (Awal Penyaluran Mandiri)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                            Pada slide jam 06.00 WIB, tampilan berdiri sendiri sebagai standar awal hari. Berisi 3 pilar visual:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2 text-xs text-slate-700">
                    <div class="flex items-start gap-2">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">✓</span>
                        <p><b>Foto Botol Retain 06.00:</b> Sampel fisik produk BBM pada saat pipa pengisian pertama dibuka.</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">✓</span>
                        <p><b>Visual 3 MT Pertama:</b> Foto kompartemen 3 armada Mobil Tangki pertama yang mengisi.</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">✓</span>
                        <p><b>Visual Tangki Timbun:</b> Foto tangki aktif penyaluran FT Maos (misal T.09, T.04).</p>
                    </div>
                </div>
            </div>

            <!-- STEP 2: SESI 12.00 & 18.00 (SISTEM PEMBANDING SIDE-BY-SIDE) -->
            <div x-show="step === 2" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 font-black text-xl shadow-sm">
                        3
                    </div>
                    <div>
                        <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-800 border border-amber-200">
                            Fitur Pembanding Samping-Menyamping
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Slide Jam 12.00 &amp; 18.00: Kiri 06.00 vs Kanan Retain Sesi
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                            Untuk melihat apakah ada perubahan warna visual atau parameter berat jenis:
                        </p>
                    </div>
                </div>

                <!-- Mockup Ilustrasi Kotak Kiri & Kotak Kanan -->
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-xl border-2 border-dashed border-blue-300 bg-blue-50/50 p-3">
                            <span class="text-[10px] font-extrabold text-brand-blue uppercase">Kotak Kiri (Tetap)</span>
                            <p class="text-xs font-bold text-slate-800 mt-0.5">Sampel 06.00 WIB Pagi</p>
                            <p class="text-[10px] text-slate-500 mt-1">Sebagai acuan / *baseline* pembanding</p>
                        </div>
                        <div class="rounded-xl border-2 border-dashed border-amber-400 bg-amber-50/50 p-3">
                            <span class="text-[10px] font-extrabold text-amber-700 uppercase">Kotak Kanan (Pergantian)</span>
                            <p class="text-xs font-bold text-slate-800 mt-0.5">Retain 12.00 / 18.00 WIB</p>
                            <p class="text-[10px] text-slate-500 mt-1">Hasil sampel terkini siang/sore</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2.5 text-center italic">
                        "Dengan tampilan bersebelahan, tim Regional &amp; QC langsung tahu jika ada deviasi visual / densitas."
                    </p>
                </div>
            </div>

            <!-- STEP 3: DENSITY 15 OTOMATIS ASTM 53B -->
            <div x-show="step === 3" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 font-black text-xl shadow-sm">
                        4
                    </div>
                    <div>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-800 border border-emerald-200">
                            Standar Perhitungan Presisi
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Density'15 Otomatis Sesuai ASTM Table 53B
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                            Petugas tidak perlu lagi menghitung tabel ASTM secara manual:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2.5">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="rounded-lg bg-slate-100 px-2 py-1 font-mono font-bold text-slate-700">Input</span>
                        <span class="text-slate-600">&rarr; <b>Density Observed</b> (misal: 0.7380) + <b>Suhu °C</b> (misal: 29.5°C)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="rounded-lg bg-brand-blue px-2 py-1 font-mono font-bold text-white">Sistem</span>
                        <span class="text-slate-700">&rarr; Otomatis mengonversi ke <b>Density 15°C</b> standar <b>4 angka desimal</b> (contoh: <code>0.7482</code>).</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 pt-1 border-t border-slate-100">
                        <i data-lucide="shield-check" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                        <span>Formula akurat sesuai standar Petroleum Measurement Tables ASTM-IP.</span>
                    </div>
                </div>
            </div>

            <!-- STEP 4: DOWNLOAD & KIRIM REGION -->
            <div x-show="step === 4" x-transition.opacity class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-brand-red font-black text-xl shadow-sm">
                        5
                    </div>
                    <div>
                        <span class="rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-brand-red border border-rose-200">
                            Siap Kirim ke Regional
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">
                            Download Banner PNG / JPG Sekali Klik
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1.5 leading-relaxed">
                            Laporan langsung terformat rapi dalam resolusi HD siap diposting ke grup WhatsApp Region / RCBT:
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-[#0f3b66] px-3 py-1.5 text-xs font-bold text-white shadow-sm">
                            <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PNG
                        </span>
                        <span class="text-xs text-slate-600">Klik tombol ini di atas slide yang diinginkan.</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Banner memuat lengkap: Logo Pertamina, Judul Resmi FT Maos, Tanggal Laporan, Foto Sampel &amp; Mini Tabel Hasil Uji, serta Matriks Seluruh MT.
                    </p>
                </div>
            </div>

            <!-- Footer Navigation Controls -->
            <div class="mt-6 pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" 
                        @click="prevStep()" 
                        :disabled="step === 0"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs sm:text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i> Sebelumnya
                </button>

                <div class="flex items-center gap-1.5">
                    <template x-for="(s, index) in steps" :key="index">
                        <span class="h-2 rounded-full transition-all duration-300"
                              :class="step === index ? 'w-6 bg-brand-blue' : 'w-2 bg-slate-300'"></span>
                    </template>
                </div>

                <button type="button" 
                        @click="nextStep()" 
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue hover:bg-brand-blueDark px-5 py-2 text-xs sm:text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition">
                    <span x-text="step === steps.length - 1 ? 'Selesai &amp; Mengerti' : 'Lanjut'"></span>
                    <i :data-lucide="step === steps.length - 1 ? 'check' : 'arrow-right'" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    function tutorialRetainSampel() {
        return {
            open: false,
            step: 0,
            soundEnabled: true,
            audioCtx: null,
            speechSynth: window.speechSynthesis || null,
            steps: [
                { 
                    shortTitle: 'Struktur Sesi', 
                    narration: 'Selamat datang di panduan laporan Retain Sampel FT Maos. Pengawasan mutu BBM dilakukan dalam tiga sesi harian, yaitu jam enam pagi, dua belas siang, dan delapan belas sore.' 
                },
                { 
                    shortTitle: 'Sesi 06.00 Pagi', 
                    narration: 'Sesi jam enam pagi adalah acuan awal penyaluran. Berisi foto botol retain, visual kompartemen tiga mobil tangki pertama, dan foto tangki timbun aktif.' 
                },
                { 
                    shortTitle: 'Pembanding 12 & 18', 
                    narration: 'Pada slide jam dua belas dan delapan belas, tampilan dibagi dua berdampingan. Kotak kiri adalah acuan jam enam pagi, dan kotak kanan adalah sampel retain terkini untuk membandingkan perubahan visual dan berat jenis.' 
                },
                { 
                    shortTitle: 'Density 15 Otomatis', 
                    narration: 'Nilai density lima belas derajat celcius dihitung otomatis oleh sistem menggunakan rumus resmi tabel lima puluh tiga B ASTM standar empat desimal.' 
                },
                { 
                    shortTitle: 'Download & Kirim', 
                    narration: 'Setiap slide siap diunduh menjadi gambar PNG beresolusi tinggi sekali klik, siap dikirimkan ke grup WhatsApp Region tanpa perlu dipotong manual.' 
                },
            ],
            init() {
                window.addEventListener('open-retain-tutorial', () => this.openTutorial());

                this.$watch('open', value => {
                    if (value) {
                        this.$nextTick(() => lucide.createIcons());
                        this.playChime(520, 0.15);
                        this.speakNarration();
                    } else {
                        if (this.speechSynth) this.speechSynth.cancel();
                    }
                });
                this.$watch('step', () => {
                    this.$nextTick(() => lucide.createIcons());
                    this.playChime(640 + this.step * 60, 0.12);
                    this.speakNarration();
                });
            },
            openTutorial() {
                this.step = 0;
                this.open = true;
            },
            closeTutorial() {
                this.open = false;
                if (this.speechSynth) this.speechSynth.cancel();
            },
            nextStep() {
                if (this.step < this.steps.length - 1) {
                    this.step += 1;
                } else {
                    this.playSuccessChime();
                    this.closeTutorial();
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
                if (!this.soundEnabled && this.speechSynth) {
                    this.speechSynth.cancel();
                } else if (this.soundEnabled) {
                    this.playChime(600, 0.15);
                    this.speakNarration();
                }
            },
            playChime(freq = 520, duration = 0.15) {
                if (!this.soundEnabled) return;
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!this.audioCtx) {
                        this.audioCtx = new AudioContext();
                    }
                    if (this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume();
                    }
                    const osc = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, this.audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(freq * 1.5, this.audioCtx.currentTime + duration);
                    
                    gain.gain.setValueAtTime(0.08, this.audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.0001, this.audioCtx.currentTime + duration);
                    
                    osc.connect(gain);
                    gain.connect(this.audioCtx.destination);
                    osc.start();
                    osc.stop(this.audioCtx.currentTime + duration);
                } catch (e) {
                    // silent fallback
                }
            },
            playSuccessChime() {
                if (!this.soundEnabled) return;
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!this.audioCtx) this.audioCtx = new AudioContext();
                    const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
                    notes.forEach((freq, i) => {
                        setTimeout(() => this.playChime(freq, 0.2), i * 80);
                    });
                } catch (e) {}
            },
            speakNarration() {
                if (!this.soundEnabled || !this.speechSynth) return;
                try {
                    this.speechSynth.cancel();
                    const text = this.steps[this.step].narration;
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 1.0;
                    utterance.pitch = 1.05;
                    this.speechSynth.speak(utterance);
                } catch (e) {}
            }
        };
    }
</script>
