<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Fuel Maos QC & Checklist Armada · Pertamina Patra Niaga</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/pertamina-mark.svg') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    boxShadow: {
                        card: '0 1px 2px rgba(15,23,42,.04), 0 1px 3px rgba(15,23,42,.08)',
                        lift: '0 10px 30px -10px rgba(15,23,42,.25)',
                    },
                    colors: {
                        brand: {
                            red: '#ED1B2F',
                            redDark: '#B3101F',
                            blue: '#006CB8',
                            blueDark: '#003D78',
                            gold: '#F2A900',
                            green: '#ACC42A',
                            dark: '#071B33',
                        },
                    },
                },
            },
        };
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <script src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    <!-- ===================== Sidebar ===================== -->
    <aside class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-slate-900 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white p-1.5 shadow-lg shadow-black/40">
                <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-full w-full object-contain">
            </div>
            <div class="min-w-0 leading-tight">
                <p class="truncate text-[15px] font-bold text-white">Fuel Maos</p>
                <p class="truncate text-xs text-brand-gold">QC Lab &amp; Checklist Armada</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-5">
            @php $route = request()->route()?->getName(); @endphp

            @if (auth()->user()->isSpbu())
                {{-- Navigasi Khusus User Viewer (SPBU / Tamu) — Fokus Rekapitulasi & Read-Only --}}
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Laporan &amp; Rekapitulasi</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $route === 'dashboard' ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="layout-grid" class="h-5 w-5 shrink-0"></i>
                        Dashboard Rekap
                    </a>

                    <a href="{{ route('riwayat.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'riwayat.') ? 'bg-brand-blue text-white shadow-lg shadow-brand-blue/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="file-check" class="h-5 w-5 shrink-0"></i>
                        Rekap Hasil Uji BBM
                    </a>

                    <a href="{{ route('retain-sampel.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'retain-sampel.') ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="droplets" class="h-5 w-5 shrink-0"></i>
                        Rekap Penyaluran / Re-tank
                    </a>

                    <a href="{{ route('checklist-mt-maos.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'checklist-mt-maos.') ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="truck" class="h-5 w-5 shrink-0"></i>
                        Rekap Checklist MT
                    </a>

                    <a href="{{ route('fuelmaos.halaman') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $route === 'fuelmaos.halaman' ? 'bg-brand-blue text-white shadow-lg shadow-brand-blue/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="bot" class="h-5 w-5 shrink-0"></i>
                        Asisten Fuel Maos
                    </a>
                </div>
            @else
                {{-- Navigasi Penuh Staf Laboratorium (Admin & Petugas QC) --}}
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Menu Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $route === 'dashboard' ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="layout-grid" class="h-5 w-5 shrink-0"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('ujibbm.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'ujibbm.') ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="clipboard-check" class="h-5 w-5 shrink-0"></i>
                        Uji Spesifikasi BBM
                    </a>

                    <a href="{{ route('uji.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'uji.') ? 'bg-brand-blue text-white shadow-lg shadow-brand-blue/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="flask-conical" class="h-5 w-5 shrink-0"></i>
                        Panduan Uji (SOP)
                    </a>

                    <a href="{{ route('riwayat.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'riwayat.') ? 'bg-brand-blue text-white shadow-lg shadow-brand-blue/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="clock" class="h-5 w-5 shrink-0"></i>
                        Riwayat Hasil Uji
                    </a>

                    <a href="{{ route('fuelmaos.halaman') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $route === 'fuelmaos.halaman' ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="bot" class="h-5 w-5 shrink-0"></i>
                        Asisten Cek Hasil Uji
                    </a>
                </div>

                <p class="mb-2 mt-6 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Checklist Lapangan</p>
                <div class="space-y-1">
                    <a href="{{ route('checklist-mt-maos.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'checklist-mt-maos.') ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="truck" class="h-5 w-5 shrink-0"></i>
                        Checklist MT Maos
                    </a>

                    <a href="{{ route('retain-sampel.index') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with((string) $route, 'retain-sampel.') ? 'bg-brand-red text-white shadow-lg shadow-black/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="droplets" class="h-5 w-5 shrink-0"></i>
                        Penyaluran / Re-tank
                    </a>
                </div>
            @endif
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3 rounded-xl bg-white/5 px-3 py-2.5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-blue/50 to-brand-dark text-sm font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0 leading-tight">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->jabatan ?? ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">
                    <i data-lucide="log-out" class="h-5 w-5"></i>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

    <!-- ===================== Main ===================== -->
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur lg:px-8">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 lg:hidden">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-brand-blue">Pertamina Patra Niaga · Fuel QC &amp; Checklist Armada</p>
                    <h1 class="text-lg font-bold leading-tight text-slate-900">@yield('title', 'Dashboard')</h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <p class="text-xs text-slate-400">{{ now()->translatedFormat('H:i') }} WIB</p>
                </div>
                <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand-red to-brand-blue text-xs font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="hidden leading-tight sm:block">
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ auth()->user()->jabatan ?? ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 lg:px-8">
            @if (session('sukses'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-card">
                    <i data-lucide="circle-check" class="mt-0.5 h-5 w-5 shrink-0"></i>
                    <span>{{ session('sukses') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-card">
                    <i data-lucide="circle-alert" class="mt-0.5 h-5 w-5 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-slate-200 px-4 py-4 text-center text-xs text-slate-400 lg:px-8">
            © {{ date('Y') }} <span class="font-semibold text-brand-red">Fuel Maos</span> · Smart Fuel QC System — PT Pertamina Patra Niaga
        </footer>
    </div>
</div>

<x-confirm-delete-modal />
<!-- ===================== Fuel Maos Chatbot Widget ===================== -->
<div x-data="fuelMaos()" class="fixed bottom-5 right-5 z-50">
    <!-- Tombol buka -->
    <button @click="open = !open; if (open) $nextTick(() => $refs.input.focus())"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-red text-white shadow-lift transition hover:scale-105"
            :class="open ? 'hidden' : ''">
        <i data-lucide="bot" class="h-7 w-7"></i>
    </button>

    <!-- Panel chat -->
    <div x-show="open" x-cloak x-transition.opacity
         class="flex w-[22rem] max-w-[calc(100vw-2rem)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lift">
        <div class="flex items-center gap-3 bg-gradient-to-r from-brand-red to-brand-blue px-4 py-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-white">
                <i data-lucide="bot" class="h-5 w-5"></i>
            </div>
            <div class="flex-1 leading-tight">
                <p class="text-sm font-bold text-white">Fuel Maos</p>
                <p class="text-[11px] text-white/80">Asisten QC — online</p>
            </div>
            <button @click="open = false" class="rounded-lg p-1 text-white/80 hover:bg-white/10 hover:text-white">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div x-ref="log" class="h-80 space-y-3 overflow-y-auto bg-slate-50 px-4 py-4">
            <div class="flex justify-start">
                <div class="max-w-[80%] rounded-2xl rounded-tl-sm bg-white px-3.5 py-2.5 text-sm text-slate-700 shadow-card">
                    Halo! Saya <b>Fuel Maos</b>. Tanya apa saja soal Retain Sampel MT, rumus Density'15, SOP Lab, batas BBM, Checklist MT, atau panduan cetak & export ya.
                </div>
            </div>

            <template x-for="m in messages" :key="m.id">
                <div class="flex flex-col gap-1.5" :class="m.from === 'user' ? 'items-end' : 'items-start'">
                    <div class="max-w-[80%] rounded-2xl px-3.5 py-2.5 text-sm whitespace-pre-line shadow-card"
                         :class="m.from === 'user' ? 'rounded-tr-sm bg-brand-blue text-white' : 'rounded-tl-sm bg-white text-slate-700'"
                         x-text="m.text"></div>
                    <div x-show="m.from === 'bot' && m.saran && m.saran.length" class="flex max-w-[85%] flex-wrap gap-1.5">
                        <template x-for="s in (m.saran || [])" :key="s">
                            <button type="button" @click="draft = s; send()"
                                    class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-brand-blue transition hover:bg-brand-blue/5">
                                <span x-text="s"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="typing" class="flex justify-start">
                <div class="rounded-2xl rounded-tl-sm bg-white px-3.5 py-2.5 text-sm text-slate-400 shadow-card">Mengetik…</div>
            </div>
        </div>

        <form @submit.prevent="send()" class="flex items-center gap-2 border-t border-slate-200 bg-white p-3">
            <input x-ref="input" x-model="draft" type="text" placeholder="Tulis pertanyaan…"
                   :disabled="typing"
                   class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 disabled:cursor-not-allowed disabled:bg-slate-50">
            <button type="submit" :disabled="typing" class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-red text-white transition hover:bg-brand-blue disabled:cursor-not-allowed disabled:opacity-50">
                <i data-lucide="send" class="h-4 w-4"></i>
            </button>
        </form>
    </div>
</div>

<script>
    function fuelMaos() {
        return {
            open: false,
            draft: '',
            typing: false,
            messages: [],
            _seq: 0,
            nextId() {
                // Counter naik terus supaya id pesan selalu unik (Date.now() bisa bentrok
                // kalau dua pesan dibuat di milidetik yang sama -> bikin x-for salah render)
                this._seq += 1;
                return this._seq;
            },
            send() {
                if (this.typing) return; // cegah kirim ganda saat masih menunggu balasan
                const text = this.draft.trim();
                if (!text) return;
                this.messages.push({ id: this.nextId(), from: 'user', text });
                this.draft = '';
                this.typing = true;
                const log = this.$refs.log;
                this.$nextTick(() => log.scrollTop = log.scrollHeight);

                fetch('{{ route('fuelmaos.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ pesan: text }),
                })
                .then(r => r.json())
                .then(data => {
                    this.typing = false;
                    this.messages.push({ id: this.nextId(), from: 'bot', text: data.balasan || 'Maaf, saya belum paham.', saran: data.saran || [] });
                    this.$nextTick(() => log.scrollTop = log.scrollHeight);
                    lucide.createIcons();
                })
                .catch(() => {
                    this.typing = false;
                    this.messages.push({ id: this.nextId(), from: 'bot', text: 'Gagal terhubung ke server.', saran: [] });
                });
            },
        };
    }
</script>
@yield('scripts')
<script>lucide.createIcons();</script>
</body>
</html>
