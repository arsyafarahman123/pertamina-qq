<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk — Fuel Maos · PT Pertamina Patra Niaga</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/pertamina-mark.svg') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts & Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
                            dark: '#07172B',
                        },
                    },
                },
            },
        };
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }

        .enterprise-pattern {
            background-color: #06182c;
            background-image: 
                radial-gradient(at 10% 20%, rgba(0, 108, 184, 0.22) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(172, 196, 42, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(237, 27, 47, 0.08) 0px, transparent 50%),
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
    </style>

    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    <script src="{{ asset('js/lucide.min.js') }}"></script>
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased">

<div class="flex min-h-screen w-full flex-col lg:flex-row">

    <!-- ================================================================= -->
    <!-- PANEL KIRI: CORPORATE IDENTITY & SYSTEM OVERVIEW (ENTERPRISE)     -->
    <!-- ================================================================= -->
    <div class="enterprise-pattern relative flex w-full flex-col justify-between p-6 sm:p-10 lg:w-[54%] lg:p-12 xl:p-14 text-white">
        
        <!-- Header Identitas Resmi Pertamina -->
        <div class="relative z-10 flex items-center justify-between border-b border-white/10 pb-6">
            <div class="flex items-center gap-3.5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white p-2 shadow-lg shadow-black/30">
                    <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-8 w-8 object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-[10.5px] font-bold uppercase tracking-[0.2em] text-slate-300">PT PERTAMINA PATRA NIAGA</p>
                        <span class="rounded bg-brand-blue/30 px-1.5 py-0.5 text-[9px] font-bold text-blue-200 ring-1 ring-brand-blue/50">REGIONAL JBT</span>
                    </div>
                    <h2 class="font-display text-lg sm:text-xl font-extrabold tracking-tight text-white">
                        Fuel Terminal Maos
                    </h2>
                    <p class="text-xs text-blue-200/90 font-medium">Quality &amp; Quantity (QQ) Control Management System</p>
                </div>
            </div>

            <!-- Pertamina Official 3-Color Stripe -->
            <div class="hidden sm:flex h-2 w-20 overflow-hidden rounded-full shadow-inner bg-slate-800">
                <div class="h-full w-1/3 bg-brand-red"></div>
                <div class="h-full w-1/3 bg-brand-blue"></div>
                <div class="h-full w-1/3 bg-brand-green"></div>
            </div>
        </div>

        <!-- Konten Tengah: Overview Modul Operasional Nyata (Bukan AI Sci-Fi) -->
        <div class="relative z-10 my-8 space-y-6 lg:my-auto">
            
            <!-- Headline & Narasi Enterprise -->
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-300 backdrop-blur-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Operasional Laboratorium &amp; Penyaluran BBM
                </div>
                <h1 class="mt-3 font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white lg:text-4xl">
                    Standardisasi Mutu &amp; Pengawasan Kuantitas Terpadu
                </h1>
                <p class="mt-2.5 max-w-xl text-xs sm:text-sm leading-relaxed text-slate-300">
                    Platform digital terintegrasi untuk pengujian spesifikasi laboratorium produk BBM, checklist inspeksi kelaikan armada Mobil Tangki, dan rekapitulasi retain sampel penyerahan harian.
                </p>
            </div>

            <!-- 3 Pilar Fitur Operasional Enterprise -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                
                <!-- Card 1: QC Mutu BBM -->
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 backdrop-blur-md transition hover:border-white/20 hover:bg-white/[0.07]">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-blue/20 text-brand-blue ring-1 ring-brand-blue/40 mb-3">
                        <i data-lucide="flask-conical" class="h-5 w-5 text-blue-400"></i>
                    </div>
                    <h3 class="text-sm font-bold text-white">Uji Mutu BBM</h3>
                    <p class="mt-1 text-[11px] leading-relaxed text-slate-300">
                        Evaluasi otomatis Density 15°C, Flash Point, Distilasi, &amp; Cetane Index sesuai spesifikasi resmi.
                    </p>
                </div>

                <!-- Card 2: Checklist Mobil Tangki -->
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 backdrop-blur-md transition hover:border-white/20 hover:bg-white/[0.07]">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-red/20 text-brand-red ring-1 ring-brand-red/40 mb-3">
                        <i data-lucide="truck" class="h-5 w-5 text-red-400"></i>
                    </div>
                    <h3 class="text-sm font-bold text-white">Checklist MT</h3>
                    <p class="mt-1 text-[11px] leading-relaxed text-slate-300">
                        Inspeksi 16 poin kompartemen, segel, bottom loader, dan grounding armada pra-penyaluran.
                    </p>
                </div>

                <!-- Card 3: Retain Sampel MT -->
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 backdrop-blur-md transition hover:border-white/20 hover:bg-white/[0.07]">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-green/20 text-brand-green ring-1 ring-brand-green/40 mb-3">
                        <i data-lucide="archive" class="h-5 w-5 text-lime-400"></i>
                    </div>
                    <h3 class="text-sm font-bold text-white">Retain Sampel</h3>
                    <p class="mt-1 text-[11px] leading-relaxed text-slate-300">
                        Log retensi sampel 48 jam dengan konversi ASTM-IP Petroleum Measurement Table 53B.
                    </p>
                </div>

            </div>

            <!-- Standar & Regulasi Kepatuhan -->
            <div class="flex flex-wrap items-center gap-2 pt-2 text-xs">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mr-1">Standar Kepatuhan:</span>
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] font-medium text-slate-200">
                    <i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-brand-green"></i> ASTM D1298 &amp; D86
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] font-medium text-slate-200">
                    <i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-brand-green"></i> SK Dirjen Migas
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] font-medium text-slate-200">
                    <i data-lucide="check-circle-2" class="h-3.5 w-3.5 text-brand-green"></i> SNI ISO/IEC 17025
                </span>
            </div>

        </div>

        <!-- Footer Panel Kiri -->
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between border-t border-white/10 pt-4 text-xs text-slate-400 gap-2">
            <p>&copy; {{ date('Y') }} PT Pertamina Patra Niaga &bull; Fuel Terminal Maos</p>
            <div class="flex items-center gap-2 text-[11px]">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                <span>Server Operasional Aktif</span>
            </div>
        </div>
    </div>


    <!-- ================================================================= -->
    <!-- PANEL KANAN: FORM LOGIN ELEGAN & PROFESIONAL                      -->
    <!-- ================================================================= -->
    <div class="flex w-full flex-1 items-center justify-center bg-slate-50 p-6 sm:p-10 lg:w-[46%] lg:p-12 xl:p-16">
        
        <!-- Elevated Card Container yang Bersih & Tegas -->
        <div class="w-full max-w-[440px] rounded-2xl border border-slate-200/80 bg-white p-7 sm:p-9 shadow-xl shadow-slate-200/50">

            <!-- Pertamina 3-Color Top Ribbon Accent -->
            <div class="flex h-1.5 w-full overflow-hidden rounded-full mb-6">
                <div class="h-full w-1/3 bg-brand-red"></div>
                <div class="h-full w-1/3 bg-brand-blue"></div>
                <div class="h-full w-1/3 bg-brand-green"></div>
            </div>

            <!-- Header Card -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-2xl font-bold text-slate-900">
                        Masuk ke Portal
                    </h2>
                    <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-[11px] font-bold text-brand-blue border border-blue-100">
                        Fuel Maos
                    </span>
                </div>
                <p class="mt-1.5 text-xs sm:text-sm text-slate-500">
                    Masukkan kredensial akun Anda untuk mengakses sistem Quality &amp; Quantity Control.
                </p>
            </div>

            <!-- Alert Status / Info -->
            @if (session('status'))
                <div class="mb-5 flex items-start gap-2.5 rounded-xl border border-blue-200 bg-blue-50 p-3.5 text-xs text-blue-700">
                    <i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0 text-brand-blue"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Alert Error -->
            @if ($errors->any())
                <div class="mb-5 flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 p-3.5 text-xs text-red-700">
                    <i data-lucide="alert-circle" class="mt-0.5 h-4 w-4 shrink-0 text-brand-red"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form Login -->
            <form id="loginForm" method="POST" action="{{ route('login.post') }}" class="space-y-4" x-data="{ showPass: false, submitting: false }" @submit="submitting = true">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Email Perusahaan / Akun
                    </label>
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
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Kata Sandi
                        </label>
                    </div>
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

                <!-- Remember Me & Status -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600 select-none">
                        <input type="checkbox" name="remember" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-blue focus:ring-brand-blue/30 accent-brand-blue">
                        <span class="font-medium">Ingat sesi saya</span>
                    </label>
                    <span class="text-[11px] text-slate-400 flex items-center gap-1">
                        <i data-lucide="shield" class="h-3 w-3 text-brand-blue"></i> TLS Terproteksi
                    </span>
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                        :disabled="submitting"
                        class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-blue py-3 px-4 text-sm font-bold text-white shadow-md shadow-brand-blue/25 transition duration-150 hover:bg-brand-blueHover hover:shadow-lg hover:shadow-brand-blue/30 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 disabled:opacity-75 disabled:cursor-not-allowed">
                    <span x-show="!submitting">Masuk ke Fuel Maos</span>
                    <span x-show="submitting" x-cloak class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Memverifikasi...
                    </span>
                    <i x-show="!submitting" data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
            </form>

            <!-- Footer Keamanan Singkat -->
            <div class="mt-6 flex items-center justify-center gap-2 text-center text-[11px] text-slate-400 border-t border-slate-100 pt-4">
                <i data-lucide="lock" class="h-3.5 w-3.5 text-slate-400"></i>
                <span>Sistem Khusus Operasional Internal PT Pertamina Patra Niaga</span>
            </div>

        </div>

    </div>

</div>

<script>
    // Inisialisasi ikon Lucide
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    // Auto-refresh CSRF token untuk mencegah error "419 Page Expired" di HP & Laptop
    async function refreshCsrfToken() {
        try {
            const res = await fetch("{{ route('csrf-token') }}", {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store'
            });
            if (res.ok) {
                const data = await res.json();
                if (data.token) {
                    const csrfInputs = document.querySelectorAll('input[name="_token"]');
                    csrfInputs.forEach(input => input.value = data.token);
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) metaToken.setAttribute('content', data.token);
                }
            }
        } catch (e) {
            // Ignore background fetch error silently
        }
    }

    // Refresh ketika tab aktif kembali (terutama setelah HP dibuka dari layar mati / background)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            refreshCsrfToken();
        }
    });

    // Refresh saat kembali dari cache (bfcache browser HP)
    window.addEventListener('pageshow', function(event) {
        refreshCsrfToken();
    });

    // Refresh berkala setiap 5 menit
    setInterval(refreshCsrfToken, 5 * 60 * 1000);
</script>
</body>
</html>
