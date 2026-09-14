<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk — Fuel Maos · Pertamina Patra Niaga</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/pertamina-mark.svg') }}">

    <!-- Google Fonts & Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'Inter', 'ui-sans-serif', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            red: '#ED1B2F',
                            blue: '#006CB8',
                            blueDark: '#003D78',
                            blueHover: '#005596',
                            green: '#ACC42A',
                            dark: '#05162B',
                        },
                    },
                },
            },
        };
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }

        .panel-left-bg {
            background:
                radial-gradient(900px 500px at 15% 15%, rgba(0, 108, 184, 0.28) 0%, transparent 60%),
                radial-gradient(700px 500px at 85% 85%, rgba(237, 27, 47, 0.15) 0%, transparent 60%),
                linear-gradient(160deg, #05162B 0%, #002B54 50%, #030F1D 100%);
        }
    </style>

    <script src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased"
      x-data="{
          activeDemo: '',
          fillDemo(email, password, role) {
              document.getElementById('email').value = email;
              document.getElementById('password').value = password;
              this.activeDemo = role;
          }
      }">

<div class="flex min-h-screen w-full flex-col lg:flex-row">

    <!-- ================================================================= -->
    <!-- PANEL KIRI: VISUAL PERTAMINA LABORATORY                           -->
    <!-- ================================================================= -->
    <div class="panel-left-bg relative flex w-full flex-col justify-between p-8 text-white sm:p-12 lg:w-1/2 lg:p-14 xl:p-16">
        
        <!-- Header Identitas Resmi -->
        <div class="relative z-10 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-white p-2.5 shadow-lg ring-1 ring-white/20">
                    <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-9 w-9 object-contain">
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-300">PT Pertamina Patra Niaga</p>
                    <h2 class="font-display text-lg font-extrabold tracking-tight text-white">Fuel Terminal Maos</h2>
                    <p class="text-xs text-blue-200/80">Quality &amp; Quantity (QQ) Control</p>
                </div>
            </div>

            <!-- Pertamina 3-Color Ribbon Mini -->
            <div class="hidden sm:flex h-1.5 w-16 overflow-hidden rounded-full shadow-sm">
                <div class="h-full w-1/3 bg-brand-red"></div>
                <div class="h-full w-1/3 bg-brand-blue"></div>
                <div class="h-full w-1/3 bg-brand-green"></div>
            </div>
        </div>

        <!-- Bagian Tengah: Visual 3D Pertamina Laboratory Emblem & Deskripsi -->
        <div class="relative z-10 my-8 flex flex-col items-center text-center lg:my-0">
            
            <!-- Gambar 3D Pertamina Laboratory Badge yang Elegan -->
            <div class="group relative mb-6">
                <!-- Soft ambient aura -->
                <div class="absolute -inset-2 rounded-3xl bg-brand-blue/30 blur-xl transition duration-500 group-hover:bg-brand-blue/40"></div>
                
                <div class="relative h-60 w-60 sm:h-64 sm:w-64 overflow-hidden rounded-3xl border border-white/20 bg-slate-900/60 p-2 shadow-2xl backdrop-blur-md">
                    <img src="{{ asset('images/pertamina-lab-badge.jpg') }}" 
                         alt="Pertamina Fuel Quality Control Laboratory" 
                         class="h-full w-full rounded-2xl object-cover transition duration-300 group-hover:scale-105">
                </div>
            </div>

            <!-- Teks Judul & Keterangan -->
            <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                Fuel Maos
            </h1>
            <p class="mt-2.5 max-w-md text-xs sm:text-sm leading-relaxed text-blue-100/85">
                Satu sistem terpadu QC BBM: uji spesifikasi &amp; sertifikat mutu, checklist armada Mobil Tangki, dan rekap Retain Sampel Penyaluran MT dengan Density'15 otomatis.
            </p>

            <!-- 4 Poin Keunggulan Simpel & Rapi -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-medium text-slate-200 backdrop-blur-sm">
                    <i data-lucide="check" class="h-3.5 w-3.5 text-brand-green"></i> Evaluasi Spesifikasi Otomatis
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-medium text-slate-200 backdrop-blur-sm">
                    <i data-lucide="check" class="h-3.5 w-3.5 text-brand-green"></i> Berita Acara &amp; Sertifikat Mutu
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-medium text-slate-200 backdrop-blur-sm">
                    <i data-lucide="check" class="h-3.5 w-3.5 text-brand-green"></i> Checklist Armada Mobil Tangki
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-medium text-slate-200 backdrop-blur-sm">
                    <i data-lucide="check" class="h-3.5 w-3.5 text-brand-green"></i> Rekap Retain Sampel MT &amp; Density'15 Otomatis
                </span>
            </div>
        </div>

        <!-- Footer Panel Kiri -->
        <div class="relative z-10 flex items-center justify-between text-xs text-blue-200/60">
            <p>&copy; {{ date('Y') }} PT Pertamina Patra Niaga</p>
            <span class="text-[11px] text-slate-400">Sistem Internal Terbatas</span>
        </div>
    </div>


    <!-- ================================================================= -->
    <!-- PANEL KANAN: FORM LOGIN ELEGAN DALAM CARD                         -->
    <!-- ================================================================= -->
    <div class="flex w-full flex-1 items-center justify-center bg-slate-100/80 p-6 sm:p-10 lg:w-1/2 lg:p-12 xl:p-16">
        
        <!-- Elevated Card Container yang Tegas & Rapi -->
        <div class="w-full max-w-[420px] overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-7 sm:p-9 shadow-xl shadow-slate-200/60">

            <!-- Pertamina 3-Color Top Accent -->
            <div class="h-1.5 w-full bg-gradient-to-r from-brand-red via-brand-blue to-brand-green rounded-full mb-6"></div>

            <!-- Header Card -->
            <div class="mb-6">
                <h2 class="font-display text-2xl font-bold text-slate-900">
                    Masuk ke Sistem
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Gunakan akun resmi untuk mengakses Uji Mutu BBM, Checklist MT Maos, dan Retain Sampel MT.
                </p>
            </div>

            <!-- Alert Error -->
            @if ($errors->any())
                <div class="mb-5 flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                    <i data-lucide="alert-circle" class="mt-0.5 h-4 w-4 shrink-0 text-brand-red"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form Login -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4" x-data="{ showPass: false }">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="mail" class="h-4 w-4"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pl-10 pr-3.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20"
                               placeholder="nama@pertamina.com">
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div>
                    <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Kata Sandi</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="lock" class="h-4 w-4"></i>
                        </span>
                        <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                               autocomplete="current-password"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pl-10 pr-10 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20"
                               placeholder="••••••••">
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-slate-600 focus:outline-none"
                                title="Lihat kata sandi">
                            <i x-show="!showPass" data-lucide="eye" class="h-4 w-4"></i>
                            <i x-show="showPass" x-cloak data-lucide="eye-off" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600 select-none">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-blue focus:ring-brand-blue/30 accent-brand-blue">
                        <span>Ingat saya</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Sesi Terproteksi</span>
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                        class="mt-1.5 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-blue py-3 px-4 text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition duration-150 hover:bg-brand-blueHover hover:shadow-lg hover:shadow-brand-blue/30 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2">
                    <span>Masuk ke Fuel Maos</span>
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
            </form>

            <!-- Akses Cepat Akun Demo (3 Sample Peran) -->
            <div class="mt-6 border-t border-slate-100 pt-5">
                <p class="mb-2.5 text-center text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Akses Cepat Pengujian (3 Sample Peran)
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button"
                            @click="fillDemo('admin@labqq.test', 'password123', 'admin')"
                            :class="activeDemo === 'admin' ? 'border-brand-blue bg-blue-50 text-brand-blue font-semibold ring-1 ring-brand-blue' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                            class="flex flex-col items-center justify-center rounded-xl border py-2 px-1.5 text-center transition">
                        <i data-lucide="shield-check" class="h-4 w-4 text-brand-blue mb-1"></i>
                        <span class="text-[11px] font-bold leading-tight">Admin Lab</span>
                        <span class="text-[9.5px] text-slate-400 leading-tight">Supervisor</span>
                    </button>
                    <button type="button"
                            @click="fillDemo('petugas@labqq.test', 'password123', 'petugas')"
                            :class="activeDemo === 'petugas' ? 'border-brand-red bg-red-50 text-brand-red font-semibold ring-1 ring-brand-red' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                            class="flex flex-col items-center justify-center rounded-xl border py-2 px-1.5 text-center transition">
                        <i data-lucide="truck" class="h-4 w-4 text-brand-red mb-1"></i>
                        <span class="text-[11px] font-bold leading-tight">Petugas Lapangan</span>
                        <span class="text-[9.5px] text-slate-400 leading-tight">QC &amp; Checklist MT</span>
                    </button>
                    <button type="button"
                            @click="fillDemo('viewer@labqq.test', 'password123', 'viewer')"
                            :class="activeDemo === 'viewer' ? 'border-emerald-600 bg-emerald-50 text-emerald-700 font-semibold ring-1 ring-emerald-600' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                            class="flex flex-col items-center justify-center rounded-xl border py-2 px-1.5 text-center transition">
                        <i data-lucide="eye" class="h-4 w-4 text-emerald-600 mb-1"></i>
                        <span class="text-[11px] font-bold leading-tight">User Viewer</span>
                        <span class="text-[9.5px] text-slate-400 leading-tight">Hanya Lihat (SPBU)</span>
                    </button>
                </div>
            </div>

            <!-- Footer Keamanan Singkat -->
            <div class="mt-6 flex items-center justify-center gap-1.5 text-center text-[11px] text-slate-400">
                <i data-lucide="shield-check" class="h-3.5 w-3.5 text-emerald-600"></i>
                <span>Enkripsi TLS &bull; Standar ASTM &amp; SNI Mutu BBM</span>
            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>
