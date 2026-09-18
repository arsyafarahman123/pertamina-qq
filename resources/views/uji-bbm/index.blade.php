@extends('layouts.app')

@section('title', 'Uji Mutu BBM')

@section('content')
<div x-data="ujiBbmApp()" class="relative">
    <!-- Background Decor -->
    <div class="pointer-events-none absolute -inset-x-4 -top-8 -z-10 h-96 bg-gradient-to-b from-brand-blue/5 to-transparent"></div>
    <div class="pointer-events-none absolute right-0 top-0 -z-10 h-72 w-72 rounded-full bg-brand-red/5 blur-3xl"></div>
    <div class="pointer-events-none absolute left-0 top-20 -z-10 h-72 w-72 rounded-full bg-brand-blue/50/5 blur-3xl"></div>

    <!-- Hero Banner -->
    <div class="mb-10 overflow-hidden rounded-3xl bg-gradient-to-br from-[#0A1628] via-[#0F2744] to-[#1a3a5c] p-8 sm:p-10 shadow-2xl shadow-brand-blue/20 relative" x-show="step < 4" x-transition>
        <!-- Decorative bg elements -->
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-brand-red/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -left-10 bottom-0 h-48 w-48 rounded-full bg-brand-blue/20 blur-3xl"></div>
        <div class="pointer-events-none absolute right-1/3 top-0 h-32 w-32 rounded-full bg-white/5 blur-2xl"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-6">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10 shadow-xl">
                <i data-lucide="flask-conical" class="h-8 w-8 text-white"></i>
            </div>
            <div class="flex-1">
                <div class="mb-1 flex items-center gap-2">
                    <span class="rounded-full bg-brand-red/80 px-3 py-0.5 text-[10px] font-bold uppercase tracking-widest text-white">Pertamina Patra Niaga</span>
                    <span class="rounded-full bg-white/10 px-3 py-0.5 text-[10px] font-bold uppercase tracking-widest text-white/60">Fuel Quality Control</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Uji Mutu BBM</h1>
                <p class="mt-1 text-sm text-white/50">Langkah interaktif memastikan produk sesuai batas toleransi spesifikasi Dirjen Migas.</p>
            </div>
            <!-- Step Indicator inside hero -->
            <div class="flex items-center gap-2 shrink-0">
                <div class="flex items-center gap-1.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl font-bold text-sm transition-all duration-300"
                         :class="step >= 1 ? 'bg-brand-red text-white shadow-lg shadow-brand-red/40' : 'bg-white/10 text-white/40'">1</div>
                    <div class="h-0.5 w-5" :class="step > 1 ? 'bg-brand-red' : 'bg-white/20'"></div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl font-bold text-sm transition-all duration-300"
                         :class="step >= 2 ? 'bg-brand-red text-white shadow-lg shadow-brand-red/40' : 'bg-white/10 text-white/40'">2</div>
                    <div class="h-0.5 w-5" :class="step > 2 ? 'bg-brand-red' : 'bg-white/20'"></div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl font-bold text-sm transition-all duration-300"
                         :class="step >= 3 ? 'bg-brand-red text-white shadow-lg shadow-brand-red/40' : 'bg-white/10 text-white/40'">3</div>
                </div>
            </div>
        </div>
        <!-- Step label -->
        <div class="relative z-10 mt-5 border-t border-white/10 pt-4 flex items-center gap-3 text-xs text-white/50 font-medium">
            <span :class="step === 1 ? 'text-white font-bold' : ''">&bull; Pilih Kategori</span>
            <span :class="step === 2 ? 'text-white font-bold' : ''">&bull; Jenis BBM</span>
            <span :class="step === 3 ? 'text-white font-bold' : ''">&bull; Input Hasil Uji</span>
        </div>
    </div>

    <!-- STEP 1: PILIH KATEGORI -->
    <div x-show="step === 1" x-transition.opacity.duration.500ms class="mx-auto max-w-4xl">
        <p class="mb-5 text-xs font-bold uppercase tracking-widest text-slate-400">Langkah 1 — Pilih Kategori Produk</p>
        <div class="grid gap-5 sm:grid-cols-2">
            <!-- Card Gasoline -->
            <button type="button" @click="selectKategori('gasoline')"
                    class="group relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-0 text-left shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-brand-red/15 focus:outline-none">
                <!-- Top accent bar -->
                <div class="h-1.5 w-full bg-gradient-to-r from-brand-red via-orange-400 to-amber-400"></div>
                <div class="p-7">
                    <!-- Glowing orb -->
                    <div class="pointer-events-none absolute -right-12 -bottom-12 h-40 w-40 rounded-full bg-gradient-to-br from-brand-red/20 to-orange-400/10 blur-2xl group-hover:opacity-80 transition-opacity"></div>
                    <div class="relative z-10">
                        <div class="mb-5 flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-red to-orange-500 text-white shadow-lg shadow-brand-red/30">
                                <i data-lucide="flame" class="h-7 w-7"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Kategori</p>
                                <h3 class="text-2xl font-black text-slate-800">Gasoline</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-slate-500 mb-5">Bensin berkualitas tinggi (Pertalite, Pertamax, Pertamax Turbo). Pengujian RON, Sulfur, Distilasi &amp; Density.</p>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="rounded-lg bg-red-50 border border-red-100 px-2.5 py-1 text-[10px] font-bold text-brand-red uppercase tracking-wide">RON</span>
                            <span class="rounded-lg bg-red-50 border border-red-100 px-2.5 py-1 text-[10px] font-bold text-brand-red uppercase tracking-wide">Sulfur</span>
                            <span class="rounded-lg bg-red-50 border border-red-100 px-2.5 py-1 text-[10px] font-bold text-brand-red uppercase tracking-wide">Distilasi</span>
                            <span class="rounded-lg bg-red-50 border border-red-100 px-2.5 py-1 text-[10px] font-bold text-brand-red uppercase tracking-wide">Density</span>
                        </div>
                    </div>
                    <!-- Arrow -->
                    <div class="absolute bottom-6 right-6 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400 transition-all group-hover:bg-brand-red group-hover:text-white group-hover:translate-x-0.5">
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </div>
                </div>
            </button>

            <!-- Card Gasoil -->
            <button type="button" @click="selectKategori('gasoil')"
                    class="group relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-0 text-left shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-brand-blue/15 focus:outline-none">
                <div class="h-1.5 w-full bg-gradient-to-r from-brand-blue via-blue-500 to-cyan-400"></div>
                <div class="p-7">
                    <div class="pointer-events-none absolute -right-12 -bottom-12 h-40 w-40 rounded-full bg-gradient-to-br from-brand-blue/20 to-cyan-400/10 blur-2xl group-hover:opacity-80 transition-opacity"></div>
                    <div class="relative z-10">
                        <div class="mb-5 flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-blue to-blue-600 text-white shadow-lg shadow-brand-blue/30">
                                <i data-lucide="droplets" class="h-7 w-7"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Kategori</p>
                                <h3 class="text-2xl font-black text-slate-800">Gasoil</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-slate-500 mb-5">Solar industri &amp; transportasi (Biosolar, Dexlite, Pertamina Dex). Pengujian komprehensif 8 parameter kritis.</p>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="rounded-lg bg-blue-50 border border-blue-100 px-2.5 py-1 text-[10px] font-bold text-brand-blue uppercase tracking-wide">Flash Point</span>
                            <span class="rounded-lg bg-blue-50 border border-blue-100 px-2.5 py-1 text-[10px] font-bold text-brand-blue uppercase tracking-wide">Viskositas</span>
                            <span class="rounded-lg bg-blue-50 border border-blue-100 px-2.5 py-1 text-[10px] font-bold text-brand-blue uppercase tracking-wide">Density</span>
                            <span class="rounded-lg bg-blue-50 border border-blue-100 px-2.5 py-1 text-[10px] font-bold text-brand-blue uppercase tracking-wide">+5 lainnya</span>
                        </div>
                    </div>
                    <div class="absolute bottom-6 right-6 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400 transition-all group-hover:bg-brand-blue group-hover:text-white group-hover:translate-x-0.5">
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- STEP 2: PILIH JENIS BBM -->
    <div x-show="step === 2" x-transition.opacity.duration.500ms style="display: none;" class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center gap-3">
            <button type="button" @click="step = 1" class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-800 transition">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
            </button>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Langkah 2</p>
                <h3 class="text-lg font-bold text-slate-800">Pilih Jenis <span class="capitalize text-brand-blue" x-text="kategori"></span></h3>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <template x-for="(bbm, key) in listBbm" :key="key">
                <button type="button" @click="selectJenis(key)"
                        class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg focus:outline-none text-left"
                        :class="jenisBbm === key ? 'border-brand-blue ring-2 ring-brand-blue shadow-brand-blue/20 shadow-lg' : 'border-slate-200 hover:border-brand-blue/50'">
                    <!-- Accent bar top -->
                    <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl transition-all"
                         :class="jenisBbm === key ? 'bg-brand-blue' : 'bg-slate-100 group-hover:bg-brand-blue/30'"></div>
                    <div class="mb-4 mt-2">
                        <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl transition-all"
                             :class="jenisBbm === key ? 'bg-brand-blue text-white shadow-lg shadow-brand-blue/30' : 'bg-slate-100 text-slate-500 group-hover:bg-brand-blue/10 group-hover:text-brand-blue'">
                            <i data-lucide="fuel" class="h-5 w-5"></i>
                        </div>
                        <h4 class="text-base font-bold" :class="jenisBbm === key ? 'text-brand-blue' : 'text-slate-800'" x-text="bbm.nama"></h4>
                        <p class="mt-0.5 text-[11px] text-slate-400" x-text="bbm.tools ? bbm.tools.length + ' parameter uji' : ''"></p>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="tool in bbm.tools" :key="tool">
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500 uppercase tracking-wide" x-text="tool"></span>
                        </template>
                    </div>
                    <!-- Selected checkmark -->
                    <div x-show="jenisBbm === key" class="absolute top-3 right-3 flex h-5 w-5 items-center justify-center rounded-full bg-brand-blue">
                        <i data-lucide="check" class="h-3 w-3 text-white"></i>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- STEP 3: INPUT HASIL -->
    <div x-show="step === 3" x-transition.opacity.duration.500ms style="display: none;" class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <button type="button" @click="step = 2" class="flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali ke Jenis BBM
            </button>
            <div class="rounded-full bg-brand-blue/5 px-3 py-1 text-sm font-semibold text-brand-blue">
                <span x-text="selectedBbmData?.nama"></span>
            </div>
        </div>

        <!-- Live Alarm Banner: muncul otomatis saat sistem mendeteksi parameter di luar spesifikasi -->
        <div x-show="anyFail" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             class="mb-6 flex items-center gap-4 rounded-2xl border-2 border-rose-500 bg-rose-50 px-5 py-4 shadow-lg shadow-rose-200/60">
            <div class="relative flex h-11 w-11 shrink-0 items-center justify-center">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-500 opacity-40"></span>
                <span class="relative flex h-11 w-11 items-center justify-center rounded-full bg-rose-600 text-white">
                    <i data-lucide="siren" class="h-5 w-5"></i>
                </span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-rose-700 flex items-center gap-1.5"><i data-lucide="alert-triangle" class="h-4 w-4 text-rose-600"></i> Peringatan Sistem: Parameter Di Luar Spesifikasi Terdeteksi</p>
                <p class="text-xs text-rose-600">Smart Spec Detector Fuel Maos mendeteksi nilai yang tidak memenuhi batas mutu secara <span class="font-semibold">real-time</span>. Periksa kembali kolom bertanda merah sebelum diproses.</p>
            </div>
        </div>

        <!-- Live Monitoring Widget: ringkasan status semua parameter yang sudah diisi -->
        <div x-show="Object.keys(fieldStatus).length > 0" x-transition class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white/70 px-5 py-3 shadow-sm backdrop-blur">
            <span class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-400">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                </span>
                Live Monitoring
            </span>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700" x-text="'PASS: ' + countStatus('PASS')"></span>
            <span x-show="countStatus('MARGINAL') > 0" class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700" x-text="'MARGINAL: ' + countStatus('MARGINAL')"></span>
            <span x-show="countStatus('FAIL') > 0" class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700" x-text="'FAIL: ' + countStatus('FAIL')"></span>
        </div>

        <form @submit.prevent="submitUji()" class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-2xl shadow-brand-blue/20/50 backdrop-blur-xl sm:p-10">
            <div class="space-y-10">
                <!-- Data Identifikasi -->
                <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-5 shadow-sm">
                    <h4 class="mb-4 text-sm font-bold uppercase tracking-widest text-slate-500 flex items-center gap-2">
                        <i data-lucide="tag" class="h-4 w-4"></i> Identifikasi Sampel
                    </h4>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor KKW / Kereta <span class="text-xs font-normal text-slate-400">(Opsional)</span></label>
                        <input type="text" placeholder="Contoh: KKW33" class="block w-full rounded-xl border-slate-300 bg-white px-4 py-2.5 text-sm ring-1 ring-inset ring-slate-200 transition focus:border-brand-blue/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-blue" x-model="kkwField">
                    </div>
                </div>

                <template x-for="(tool, index) in selectedBbmData?.tools || []" :key="tool">
                    <div class="relative">
                        <!-- Connecting line for timeline effect -->
                        <div class="absolute bottom-[-2.5rem] left-[15px] top-[3rem] w-[2px] bg-slate-100"></div>
                        
                        <div class="relative flex items-start gap-4">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-blue font-bold text-white shadow-lg z-10 text-xs">
                                <span x-text="index + 1"></span>
                            </div>
                            <div class="flex-1 rounded-2xl border border-slate-100 bg-slate-50/50 p-5 shadow-sm">
                                <h4 class="mb-3 text-sm font-bold uppercase tracking-widest text-slate-500 flex items-center gap-2">
                                    <i data-lucide="activity" class="h-4 w-4"></i>
                                    Alat Uji: <span class="text-brand-blue" x-text="tool"></span>
                                </h4>

                                <!-- Panduan SOP ringkas alat -->
                                <template x-if="toolSop[tool]">
                                    <div class="mb-5 rounded-xl border border-brand-blue/15 bg-brand-blue/5 p-4">
                                        <p class="mb-2 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide text-brand-blue">
                                            <i data-lucide="list-checks" class="h-3.5 w-3.5"></i>
                                            <span x-text="toolSop[tool].alat"></span>
                                        </p>
                                        <ol class="space-y-1 pl-4 text-xs leading-relaxed text-slate-600 list-decimal">
                                            <template x-for="(langkah, i) in toolSop[tool].langkah" :key="i">
                                                <li x-text="langkah"></li>
                                            </template>
                                        </ol>
                                    </div>
                                </template>

                                <div class="grid gap-6 sm:grid-cols-2">
                                    <template x-for="(fieldCfg, fieldKey) in toolFields[tool]" :key="fieldKey">
                                        <div>
                                            <div class="mb-1.5 flex items-center justify-between gap-2">
                                                <label class="block text-sm font-semibold text-slate-700">
                                                    <span x-text="fieldCfg.label"></span>
                                                </label>
                                                <span x-show="fieldStatus[fieldCfg.key]" x-transition
                                                      class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                                                      :class="{
                                                        'bg-emerald-100 text-emerald-700': fieldStatus[fieldCfg.key] === 'PASS',
                                                        'bg-amber-100 text-amber-700': fieldStatus[fieldCfg.key] === 'MARGINAL',
                                                        'bg-rose-100 text-rose-700 animate-pulse': fieldStatus[fieldCfg.key] === 'FAIL',
                                                      }"
                                                      x-text="fieldStatus[fieldCfg.key]"></span>
                                            </div>
                                            <div class="relative mt-1 flex shadow-sm">
                                                <!-- Input teks manual (bukan number) supaya nilai tidak bisa "kegeser" -->
                                                <input type="text"
                                                       inputmode="decimal"
                                                       autocomplete="off"
                                                       spellcheck="false"
                                                       class="block w-full rounded-xl border bg-white px-4 py-2.5 text-sm ring-1 ring-inset transition focus:outline-none focus:ring-2 focus:ring-inset"
                                                       :class="{
                                                         'border-slate-300 ring-slate-200 focus:border-brand-blue/50 focus:ring-brand-blue': !fieldStatus[fieldCfg.key],
                                                         'border-emerald-300 ring-emerald-200 focus:ring-emerald-500': fieldStatus[fieldCfg.key] === 'PASS',
                                                         'border-amber-300 ring-amber-200 focus:ring-amber-500': fieldStatus[fieldCfg.key] === 'MARGINAL',
                                                         'border-rose-400 ring-rose-300 focus:ring-rose-500': fieldStatus[fieldCfg.key] === 'FAIL',
                                                       }"
                                                       required
                                                       x-model="formData[fieldCfg.key]"
                                                       @input="evaluateField(fieldCfg.key, false)"
                                                       @blur="evaluateField(fieldCfg.key, true)">
                                            </div>
                                            <p x-show="fieldMessage[fieldCfg.key]" x-transition
                                               class="mt-1 text-[11px] font-medium"
                                               :class="fieldStatus[fieldCfg.key] === 'FAIL' ? 'text-rose-600' : (fieldStatus[fieldCfg.key] === 'MARGINAL' ? 'text-amber-600' : 'text-emerald-600')"
                                               x-text="fieldMessage[fieldCfg.key]"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Foto Bukti (Jepret / Upload) -->
                <div class="relative">
                     <div class="relative flex items-start gap-4">
                         <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-red font-bold text-white shadow-lg z-10 text-xs">
                            <i data-lucide="camera" class="h-4 w-4"></i>
                         </div>
                         <div class="flex-1 rounded-2xl border border-slate-100 bg-slate-50/50 p-5 shadow-sm">
                            <x-foto-bukti-field
                                name="foto_bukti"
                                label="Bukti Foto (Jepret / Upload)"
                                help-text="Ambil foto langsung dari kamera (tombol Kamera) atau unggah file dari galeri/penyimpanan. Foto akan otomatis tersimpan ke Riwayat Hasil Uji." />
                         </div>
                     </div>
                </div>

            </div>

            <div class="mt-10 flex items-center justify-end border-t border-slate-200 pt-6">
                <button type="submit" :disabled="loading"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-blue to-brand-blue px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-blue/30 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-brand-blue/40 focus:outline-none disabled:opacity-75 disabled:cursor-wait">
                    <i data-lucide="check-circle" x-show="!loading" class="h-5 w-5"></i>
                    <i data-lucide="loader-2" x-show="loading" class="h-5 w-5 animate-spin" x-cloak></i>
                    <span x-text="loading ? 'Memproses...' : 'Proses & Validasi Hasil Uji'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- STEP 4: CERTIFICATE REPORT CARD -->
    <div x-show="step === 4" x-transition.opacity.duration.500ms style="display: none;" class="mx-auto mb-16 text-slate-800" x-cloak>
        
        <div class="mb-6 flex flex-wrap gap-4 max-w-[800px] mx-auto hide-on-print">
            <button type="button" onclick="window.location.reload()" class="flex items-center gap-2 rounded-xl border border-slate-300 bg-white shadow-sm px-4 py-2.5 text-sm font-semibold hover:bg-slate-50 transition">
                <i data-lucide="refresh-cw" class="h-4 w-4"></i> Mulai Baru
            </button>
            <button type="button" onclick="window.print()" class="flex items-center gap-2 rounded-xl bg-brand-red px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-red-700 transition">
                <i data-lucide="printer" class="h-4 w-4 text-white"></i> Cetak / Simpan PDF
            </button>
            <button type="button" @click="downloadAsImage()" class="flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-emerald-700 transition">
                <i data-lucide="image-down" class="h-4 w-4 text-white"></i> Download PNG/JPG
            </button>
            <a x-show="reportData?.riwayat_url" :href="reportData?.riwayat_url" class="flex items-center gap-2 rounded-xl border border-brand-blue/30 bg-brand-blue/5 px-4 py-2.5 text-sm font-semibold text-brand-blue shadow-sm hover:bg-brand-blue/20 transition">
                <i data-lucide="history" class="h-4 w-4"></i> Lihat di Riwayat
            </a>
        </div>

        <div id="report-card" class="bg-white mx-auto shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] relative w-full sm:w-[800px] border-t-[10px] border-brand-red p-0 rounded-b-xl overflow-hidden print:shadow-none print:w-full print:max-w-none">
            
            <!-- Header with Pertamina Logo -->
            <div class="flex flex-col sm:flex-row justify-between items-start border-b border-brand-red p-8 pb-6 bg-white overflow-hidden relative">
                <div class="flex items-center gap-4 z-10">
                    <!-- Assumes logo exists in public as requested. -->
                    <img src="{{ asset('images/pertamina-mark.svg') }}" class="h-12 sm:h-16 w-auto" alt="Pertamina">
                    <div>
                        <h3 class="font-black text-brand-red text-xl sm:text-2xl tracking-tighter leading-none uppercase">Pertamina Patra Niaga</h3>
                        <p class="text-[10px] sm:text-xs uppercase font-bold text-slate-400 tracking-widest mt-1">Fuel Maos &middot; Smart Fuel QC System</p>
                    </div>
                </div>
                <!-- Watermark Background -->
                <div class="absolute right-0 top-0 text-slate-50 opacity-[0.03] text-9xl font-black -rotate-12 transform pointer-events-none select-none">
                    PASS
                </div>
                <div class="text-left py-4 sm:py-0 sm:text-right mt-4 sm:mt-0 z-10">
                    <h4 class="text-slate-500 uppercase text-[10px] font-bold tracking-widest mb-1">Sertifikat Hasil Uji</h4>
                    <p class="font-mono text-slate-800 text-xs font-semibold">No. <span x-text="'FM-' + String(Math.floor(Math.random()*9000)+1000)"></span></p>
                    <p class="text-slate-500 text-[10px] uppercase font-semibold mt-0.5">{{ now()->translatedFormat('d  F  Y') }}</p>
                </div>
            </div>
            
            <!-- Certificate Title Section -->
            <div class="text-center py-10 px-8 relative bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-50 to-white">
                <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-widest text-[#0F2744]">Certificate of Quality</h1>
                <p class="text-xs text-slate-500 uppercase tracking-[0.2em] font-semibold mt-2">Sertifikat Mutu Bahan Bakar Minyak</p>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-y-7 gap-x-12 px-8 sm:px-16 mb-10 text-sm">
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1">Produk / Sampel</p>
                    <p class="font-bold text-slate-800 text-base" x-text="reportData?.nama_bbm"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1">Jenis Pengujian</p>
                    <p class="font-bold text-slate-800 text-base">Uji Kesesuaian Spesifikasi</p>
                </div>
                
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1">Nomor KKW / Kereta</p>
                    <p class="font-bold text-slate-800" x-text="kkwField || '-'"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1">Waktu Pengujian</p>
                    <p class="font-bold text-slate-800" x-text="`{{ now()->translatedFormat('d F Y') }}, ${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})} WIB`"></p>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1">Petugas / Analis</p>
                    <p class="font-bold text-slate-800">{{ auth()->check() ? auth()->user()->name : 'Admin Lab QQ' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-1">Status Mutu</p>
                    <span class="inline-flex rounded border px-3 py-1 text-xs font-bold uppercase tracking-widest"
                          :class="reportData?.status === 'PASS' ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-rose-600 bg-rose-50 text-rose-700'" 
                          x-text="reportData?.status"></span>
                </div>
            </div>

            <!-- Table -->
            <div class="px-8 sm:px-16 mb-8">
                <p class="text-[10px] mb-4 font-bold uppercase text-slate-400 tracking-wider border-b border-slate-200 pb-2">Hasil Pengukuran</p>
                
                <table class="w-full text-left text-sm mb-8 table-fixed">
                    <thead class="text-[10px] uppercase text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 font-bold w-4/12">Parameter</th>
                            <th class="py-3 font-bold w-3/12 pl-4">Nilai</th>
                            <th class="py-3 font-bold w-3/12">Batas Spesifikasi</th>
                            <th class="py-3 font-bold text-right w-2/12 pr-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <template x-for="item in reportData?.report" :key="item.parameter">
                            <tr class="hover:bg-slate-50 transition print:hover:bg-transparent">
                                <td class="py-3 pr-2 font-medium text-slate-800">
                                    <span x-text="item.parameter.replace('_', ' ')"></span>
                                </td>
                                <!-- Nilai with unit inline -->
                                <td class="py-3 font-bold text-slate-900 pl-4">
                                    <span x-text="item.value"></span> <span class="text-xs font-normal text-slate-500 ml-0.5" x-text="item.unit"></span>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-lg px-2 py-0.5 font-mono text-xs font-medium text-slate-600">
                                        <span x-text="item.min !== '-' ? 'Min ' + item.min : ''"></span>
                                        <span x-text="(item.min !== '-' && item.max !== '-') ? ' & ' : ''"></span>
                                        <span x-text="item.max !== '-' ? 'Max ' + item.max : ''"></span>
                                    </span>
                                </td>
                                <td class="py-3 text-right pr-2">
                                    <span class="font-bold text-xs"
                                          :class="item.status === 'PASS' ? 'text-emerald-700' : 'text-rose-600'" x-text="item.status"></span>
                                    <div x-show="item.status !== 'PASS'" class="text-[10px] text-rose-500 font-semibold leading-tight mt-0.5" x-text="item.message"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                
                <!-- Display Photo if uploaded -->
                <div x-show="photoPreviewUrl" class="mb-10">
                    <p class="text-[10px] mb-4 font-bold uppercase text-slate-400 tracking-wider border-b border-slate-200 pb-2">Bukti Dokumentasi Uji</p>
                    <div class="w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <img :src="photoPreviewUrl"
                             class="w-full object-cover"
                             style="max-height: 480px; object-fit: contain; background: #f8fafc;"
                             alt="Bukti foto pengujian" />
                        <div class="flex items-center gap-2 border-t border-slate-100 bg-slate-50 px-4 py-2.5">
                            <i data-lucide="image" class="h-3.5 w-3.5 text-slate-400"></i>
                            <p class="text-[11px] font-medium text-slate-500">Foto bukti pengujian — tersimpan otomatis di Riwayat Hasil Uji</p>
                        </div>
                    </div>
                </div>
                
                <!-- TTD Footer -->
                <div class="grid grid-cols-2 gap-4 mt-8 pb-10 text-center">
                    <!-- Kiri -->
                    <div class="flex flex-col items-center justify-end">
                        <p class="text-[10px] text-slate-500 mb-10">Dianalisis oleh,</p>
                        <p class="font-bold text-sm text-slate-800 border-b border-slate-300 pb-1 px-4 inline-block mb-1">{{ auth()->check() ? auth()->user()->name : 'Admin Lab QQ' }}</p>
                        <p class="text-[10px] text-slate-400">Analis Laboratorium</p>
                    </div>
                    <!-- Kanan -->
                    <div class="flex flex-col items-center justify-end">
                        <p class="text-[10px] text-slate-500 mb-10">Disetujui oleh,</p>
                        <p class="font-bold text-sm text-slate-800 border-b border-slate-300 pb-1 px-4 inline-block mb-1">Supervisor Lab</p>
                        <p class="text-[10px] text-slate-400">Quality Control &middot; Pertamina Patra Niaga</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 py-4 px-8 border-t border-slate-100 text-center border-b-[16px] border-b-brand-blue">
                <p class="text-[9px] text-slate-400 uppercase tracking-widest font-semibold">
                    Dokumen ini diterbitkan secara elektronik dan sah oleh sistem Fuel Maos sesuai spesifikasi mutu BBM (SNI).
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const kategoriGasoline = @json($kategoriGasoline);
    const kategoriGasoil = @json($kategoriGasoil);
    const toolFields = @json($toolFields);
    const toolSop = @json($toolSop);

    function ujiBbmApp() {
        return {
            step: 1,
            kategori: null,
            listBbm: {},
            jenisBbm: null,
            selectedBbmData: null,
            toolFields: toolFields,
            toolSop: toolSop,
            formData: {},
            fieldStatus: {},
            fieldMessage: {},
            kkwField: '',
            loading: false,
            reportData: null,
            photoPreviewUrl: null,

            selectKategori(kat) {
                this.kategori = kat;
                this.listBbm = kat === 'gasoline' ? kategoriGasoline : kategoriGasoil;
                this.step = 2;
                this.jenisBbm = null;
                this.formData = {};
                this.kkwField = '';
                this.photoPreviewUrl = null;
                this.$nextTick(() => lucide.createIcons());
            },

            selectJenis(jenis) {
                this.jenisBbm = jenis;
                this.selectedBbmData = this.listBbm[jenis];
                this.formData = {};
                this.fieldStatus = {};
                this.fieldMessage = {};
                this.kkwField = '';
                
                // Preheat formData keys based on tools required
                this.selectedBbmData.tools.forEach(tool => {
                    const fields = this.toolFields[tool];
                    for (const fKey in fields) {
                        this.formData[fields[fKey].key] = '';
                    }
                });

                this.step = 3;
                this.setupIcons();
            },

            setupIcons() {
                setTimeout(() => { lucide.createIcons(); }, 150);
            },

            /**
             * Smart Spec Detector — mengevaluasi satu field terhadap batas
             * spesifikasi produk yang sedang dipilih.
             *
             * `finished` = true berarti user SUDAH SELESAI mengisi field ini
             * (event @blur, pindah ke field lain / klik di luar). Alarm
             * HANYA dibunyikan pada kondisi ini, supaya tidak langsung
             * bunyi begitu baru mengetik 1 digit angka. Saat masih mengetik
             * (@input, finished = false) status & warna tetap update secara
             * real-time, tapi alarm tidak dibunyikan.
             */
            evaluateField(key, finished = false) {
                const raw = (this.formData[key] ?? '').toString().trim();
                const spec = this.selectedBbmData?.specs?.[key];

                if (!spec || raw === '') {
                    delete this.fieldStatus[key];
                    delete this.fieldMessage[key];
                    return;
                }

                const num = parseFloat(raw.replace(',', '.'));
                if (isNaN(num)) {
                    this.fieldStatus[key] = 'FAIL';
                    this.fieldMessage[key] = 'Nilai tidak valid, gunakan angka.';
                    if (finished) this.playAlarm();
                    return;
                }

                const min = spec.min;
                const max = spec.max;
                let status = 'PASS';
                let msg = 'Sesuai spesifikasi.';

                // Zona MARGINAL: dalam 5% dari batas -> peringatan dini, belum FAIL
                const tol = 0.05;
                if (min !== null && min !== undefined && num < min) {
                    status = (min > 0 && num >= min - (min * tol)) ? 'MARGINAL' : 'FAIL';
                    msg = status === 'FAIL' ? `Di bawah batas minimum ${min}` : `Mendekati batas minimum ${min}, perlu perhatian.`;
                }
                if (max !== null && max !== undefined && num > max) {
                    status = (max > 0 && num <= max + (max * tol)) ? 'MARGINAL' : 'FAIL';
                    msg = status === 'FAIL' ? `Di atas batas maksimum ${max}` : `Mendekati batas maksimum ${max}, perlu perhatian.`;
                }

                this.fieldStatus[key] = status;
                this.fieldMessage[key] = msg;

                // Alarm hanya dibunyikan setelah user SELESAI input (blur)
                // dan hasilnya memang FAIL.
                if (finished && status === 'FAIL') {
                    this.playAlarm();
                }
                this.$nextTick(() => lucide.createIcons());
            },

            get anyFail() {
                return Object.values(this.fieldStatus).includes('FAIL');
            },

            countStatus(status) {
                return Object.values(this.fieldStatus).filter(s => s === status).length;
            },

            /**
             * Bunyi alarm sirine ambulans ("nee-naw") via Web Audio API --
             * tidak butuh file suara eksternal. Frekuensi disapu naik-turun
             * berulang kali seperti sirine ambulans sungguhan, dibunyikan
             * setelah user selesai input (blur) dan hasilnya FAIL.
             */
            playAlarm() {
                try {
                    const ctx = this._audioCtx || (this._audioCtx = new (window.AudioContext || window.webkitAudioContext)());
                    const now = ctx.currentTime;

                    const duration = 1.6;   // total durasi sirine (detik)
                    const cycles = 3;       // jumlah putaran "nee-naw"
                    const lowFreq = 500;    // nada rendah ("naw")
                    const highFreq = 950;   // nada tinggi ("nee")
                    const cycleDuration = duration / cycles;

                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';

                    osc.frequency.setValueAtTime(lowFreq, now);
                    for (let i = 0; i < cycles; i++) {
                        const t0 = now + i * cycleDuration;
                        const tMid = t0 + cycleDuration / 2;
                        const tEnd = t0 + cycleDuration;
                        osc.frequency.linearRampToValueAtTime(highFreq, tMid);
                        osc.frequency.linearRampToValueAtTime(lowFreq, tEnd);
                    }

                    gain.gain.setValueAtTime(0.0001, now);
                    gain.gain.exponentialRampToValueAtTime(0.28, now + 0.08);
                    gain.gain.setValueAtTime(0.28, now + duration - 0.15);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + duration);

                    osc.connect(gain).connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + duration + 0.05);
                } catch (e) {
                    console.warn('Audio tidak tersedia di perangkat ini', e);
                }
            },

            async submitUji() {
                this.loading = true;
                this.reportData = null;

                try {
                    const fd = new FormData();
                    fd.append('kategori', this.kategori);
                    fd.append('jenis', this.jenisBbm);
                    fd.append('hasil_json', JSON.stringify(this.formData));
                    fd.append('nomor_kkw', this.kkwField || '');

                    // Ambil file dari komponen Bukti Foto (hasil "Pilih File" atau "Kamera > Jepret")
                    const fotoInput = this.$root.querySelector('input[name="foto_bukti"]');
                    if (fotoInput && fotoInput.files && fotoInput.files[0]) {
                        fd.append('foto_bukti', fotoInput.files[0]);
                    }

                    const response = await fetch('{{ route("ujibbm.submit") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: fd
                    });

                    const data = await response.json();

                    if(response.ok) {
                        this.reportData = data;
                        this.photoPreviewUrl = data.foto_url || null;
                        this.step = 4;
                        this.setupIcons();
                    } else {
                        alert(data.error || 'Terjadi kesalahan');
                    }

                } catch (e) {
                    alert('Koneksi bermasalah. Mohon coba lagi.');
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            async downloadAsImage() {
                const card = document.getElementById('report-card');
                if (!card) return;

                const btnContainer = document.querySelector('.hide-on-print');
                if (btnContainer) btnContainer.style.display = 'none';

                try {
                    const canvas = await html2canvas(card, {
                        scale: 2,
                        useCORS: true,
                        allowTaint: true,
                        backgroundColor: '#ffffff',
                        logging: false,
                    });

                    // Buat nama file: NomorKKW_JenisBBM_TanggalPengujian
                    const kkw  = (this.kkwField || 'NO-KKW').replace(/[^a-zA-Z0-9\-]/g, '_');
                    const bbm  = (this.reportData?.nama_bbm || 'BBM').replace(/[^a-zA-Z0-9\-]/g, '_');
                    const now  = new Date();
                    const tgl  = `${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}`;
                    const fileName = `${kkw}_${bbm}_${tgl}.png`;

                    const link = document.createElement('a');
                    link.download = fileName;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                } catch (err) {
                    console.error('Gagal mengunduh gambar:', err);
                    alert('Gagal mengunduh gambar. Silakan coba lagi.');
                } finally {
                    if (btnContainer) btnContainer.style.display = '';
                    this.$nextTick(() => lucide.createIcons());
                }
            },
        }
    }
</script>
<style type="text/tailwindcss">
    @media print {
        body { background: #e2e8f0 !important; }
        aside, header, footer, nav, button, .hide-on-print { display: none !important; }
        @page { size: A4 portrait; margin: 0; }
        #report-card { 
            border-top: 10px solid #DA251D !important;
            border-bottom: 16px solid #0057A8 !important;
            box-shadow: none !important; 
            margin: 0 !important; 
            width: 100% !important; 
            max-width: none !important; 
            min-height: 100vh;
            border-radius: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@endsection