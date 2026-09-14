@extends('layouts.app')
@section('title', 'Asisten Cek Hasil Uji')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    <!-- ===== Kolom kiri: ringkasan + filter cepat ===== -->
    <div class="space-y-6 lg:col-span-1">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Ringkasan Hari Ini</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 p-3 text-center">
                    <p class="text-2xl font-extrabold text-slate-800">{{ $totalHariIni }}</p>
                    <p class="text-xs text-slate-500">Total Uji</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-3 text-center">
                    <p class="text-2xl font-extrabold text-emerald-600">{{ $ringkasan['PASS'] }}</p>
                    <p class="text-xs text-emerald-700">PASS</p>
                </div>
                <div class="rounded-xl bg-amber-50 p-3 text-center">
                    <p class="text-2xl font-extrabold text-amber-600">{{ $ringkasan['MARGINAL'] }}</p>
                    <p class="text-xs text-amber-700">MARGINAL</p>
                </div>
                <div class="rounded-xl bg-rose-50 p-3 text-center">
                    <p class="text-2xl font-extrabold text-rose-600">{{ $ringkasan['FAIL'] }}</p>
                    <p class="text-xs text-rose-700">FAIL</p>
                </div>
            </div>
        </div>

        <div x-data="{
                activeProduk: null,
                activeStatus: null,
                activePeriode: null,
                activeContoh: null,
                ask(t) {
                    // Cegah kirim ganda: tunggu chat selesai balas sebelum bisa klik lagi
                    if (window.__fuelMaosAsistenTyping) return;
                    window.dispatchEvent(new CustomEvent('asisten-ask', { detail: t }));
                },
             }" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Filter &amp; Pertanyaan Cepat</p>

            <p class="mb-1.5 text-xs font-semibold text-slate-500">Produk</p>
            <div class="mb-3 flex flex-wrap gap-1.5">
                @foreach (['Solar', 'Biosolar', 'Dexlite', 'Pertamina Dex', 'Pertalite', 'Pertamax', 'Pertamax Turbo'] as $p)
                    <button type="button"
                            @click="activeProduk = '{{ $p }}'; ask('riwayat {{ strtolower($p) }} bulan ini')"
                            :class="activeProduk === '{{ $p }}'
                                ? 'border-brand-blue bg-brand-blue text-white shadow-sm'
                                : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-brand-blue/5 hover:text-brand-blue'"
                            class="rounded-full border px-3 py-1 text-xs font-medium transition">
                        {{ $p }}
                    </button>
                @endforeach
            </div>

            <p class="mb-1.5 text-xs font-semibold text-slate-500">Status</p>
            <div class="mb-3 flex flex-wrap gap-1.5">
                <button type="button" @click="activeStatus = 'PASS'; ask('berapa hasil PASS bulan ini')"
                        :class="activeStatus === 'PASS' ? 'border-emerald-600 bg-emerald-600 text-white shadow-sm' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition">PASS</button>
                <button type="button" @click="activeStatus = 'MARGINAL'; ask('berapa hasil MARGINAL bulan ini')"
                        :class="activeStatus === 'MARGINAL' ? 'border-amber-600 bg-amber-600 text-white shadow-sm' : 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100'"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition">MARGINAL</button>
                <button type="button" @click="activeStatus = 'FAIL'; ask('berapa hasil FAIL bulan ini')"
                        :class="activeStatus === 'FAIL' ? 'border-rose-600 bg-rose-600 text-white shadow-sm' : 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100'"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition">FAIL</button>
            </div>

            <p class="mb-1.5 text-xs font-semibold text-slate-500">Periode</p>
            <div class="mb-3 flex flex-wrap gap-1.5">
                <button type="button" @click="activePeriode = 'hari'; ask('riwayat hasil uji hari ini')"
                        :class="activePeriode === 'hari' ? 'border-brand-blue bg-brand-blue text-white shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-brand-blue/5 hover:text-brand-blue'"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition">Hari ini</button>
                <button type="button" @click="activePeriode = 'minggu'; ask('riwayat hasil uji minggu ini')"
                        :class="activePeriode === 'minggu' ? 'border-brand-blue bg-brand-blue text-white shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-brand-blue/5 hover:text-brand-blue'"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition">Minggu ini</button>
                <button type="button" @click="activePeriode = 'bulan'; ask('riwayat hasil uji bulan ini')"
                        :class="activePeriode === 'bulan' ? 'border-brand-blue bg-brand-blue text-white shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-brand-blue/5 hover:text-brand-blue'"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition">Bulan ini</button>
            </div>

            <p class="mb-1.5 text-xs font-semibold text-slate-500">Contoh pertanyaan lain</p>
            <div class="flex flex-col gap-1.5">
                <button type="button" @click="activeContoh = 0; ask('hasil uji yang saya input minggu ini')"
                        :class="activeContoh === 0 ? 'border-brand-blue bg-brand-blue/5 text-brand-blue' : 'border-slate-200 text-slate-600 hover:bg-brand-blue/5 hover:text-brand-blue'"
                        class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-xs font-medium transition">
                    <i data-lucide="user-check" class="h-4 w-4 shrink-0 text-brand-blue"></i>
                    <span>Hasil uji yang saya input minggu ini</span>
                </button>
                <button type="button" @click="activeContoh = 1; ask('beda density solar sama biosolar')"
                        :class="activeContoh === 1 ? 'border-brand-blue bg-brand-blue/5 text-brand-blue' : 'border-slate-200 text-slate-600 hover:bg-brand-blue/5 hover:text-brand-blue'"
                        class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-xs font-medium transition">
                    <i data-lucide="bar-chart-2" class="h-4 w-4 shrink-0 text-brand-blue"></i>
                    <span>Beda density Solar vs Biosolar</span>
                </button>
                <button type="button" @click="activeContoh = 2; ask('cek flash point 58 solar')"
                        :class="activeContoh === 2 ? 'border-brand-blue bg-brand-blue/5 text-brand-blue' : 'border-slate-200 text-slate-600 hover:bg-brand-blue/5 hover:text-brand-blue'"
                        class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-xs font-medium transition">
                    <i data-lucide="search" class="h-4 w-4 shrink-0 text-brand-blue"></i>
                    <span>Cek flash point 58°C untuk Solar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ===== Kolom kanan: chat ===== -->
    <div class="lg:col-span-2" x-data="asistenChat()" x-init="init()">
        <div class="flex h-[calc(100vh-9.5rem)] flex-col rounded-2xl border border-slate-200 bg-white shadow-card">
            <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-brand-red to-brand-blue text-white">
                    <i data-lucide="bot" class="h-5 w-5"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Fuel Maos — Asisten Cek Hasil Uji</p>
                    <p class="text-xs text-slate-400">Tanya spesifikasi, riwayat, atau status hasil uji</p>
                </div>
            </div>

            <div x-ref="log" class="flex-1 space-y-4 overflow-y-auto bg-slate-50 px-5 py-5">
                <div class="flex justify-start">
                    <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm text-slate-700 shadow-card">
                        Halo! Aku bisa bantu cek hasil uji, spesifikasi, dan riwayat pengujian. Coba klik salah satu filter/pertanyaan di kiri, atau ketik langsung pada kolom input di bawah.
                    </div>
                </div>

                <template x-for="m in messages" :key="m.id">
                    <div class="flex flex-col gap-1.5" :class="m.from === 'user' ? 'items-end' : 'items-start'">
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm whitespace-pre-line shadow-card"
                             :class="m.from === 'user' ? 'rounded-tr-sm bg-brand-blue text-white' : 'rounded-tl-sm bg-white text-slate-700'"
                             x-text="m.text"></div>

                        <!-- Daftar hasil uji terstruktur (card per item + badge status warna) -->
                        <div x-show="m.from === 'bot' && m.items && m.items.length" class="w-full max-w-[90%] space-y-1.5">
                            <template x-for="it in (m.items || [])" :key="it.kkw + it.tanggal">
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs shadow-card">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-700" x-text="it.produk + ' · KKW ' + it.kkw"></p>
                                        <p class="truncate text-slate-400" x-text="it.jenis + ' — ' + it.tanggal"></p>
                                    </div>
                                    <span class="shrink-0 rounded-full px-2 py-1 text-[11px] font-bold"
                                          :class="{
                                              'bg-emerald-100 text-emerald-700': it.status === 'PASS',
                                              'bg-amber-100 text-amber-700': it.status === 'MARGINAL',
                                              'bg-rose-100 text-rose-700': it.status === 'FAIL',
                                          }"
                                          x-text="it.status"></span>
                                </div>
                            </template>
                            <a x-show="m.lihatSemuaUrl" :href="m.lihatSemuaUrl"
                               class="block rounded-xl border border-dashed border-brand-blue/30 px-3 py-2 text-center text-xs font-semibold text-brand-blue hover:bg-brand-blue/5">
                                Lihat <span x-text="m.total"></span> hasil lainnya di menu Riwayat →
                            </a>
                        </div>

                        <div x-show="m.from === 'bot' && m.saran && m.saran.length" class="flex max-w-[90%] flex-wrap gap-1.5">
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
                    <div class="rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm text-slate-400 shadow-card">Mengetik…</div>
                </div>
            </div>

            <form @submit.prevent="send()" class="flex items-center gap-2 border-t border-slate-200 bg-white p-4">
                <input x-ref="input" x-model="draft" type="text" placeholder="Contoh: cek flash point 62 solar, atau riwayat KKW 325…"
                       :disabled="typing"
                       class="flex-1 rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 disabled:cursor-not-allowed disabled:bg-slate-50">
                <button type="submit" :disabled="typing" class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-red text-white transition hover:bg-brand-blue disabled:cursor-not-allowed disabled:opacity-50">
                    <i data-lucide="send" class="h-4 w-4"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function asistenChat() {
        return {
            draft: '',
            typing: false,
            messages: [],
            _seq: 0,
            init() {
                window.__fuelMaosAsistenTyping = false;
                window.addEventListener('asisten-ask', (e) => {
                    // Guard: kalau masih menunggu balasan sebelumnya, abaikan klik baru
                    if (this.typing) return;
                    this.draft = e.detail;
                    this.send();
                });
            },
            nextId() {
                // Counter naik terus supaya id pesan selalu unik (Date.now() bisa bentrok
                // kalau dua pesan dibuat di milidetik yang sama -> bikin x-for salah render)
                this._seq += 1;
                return this._seq;
            },
            send() {
                if (this.typing) return; // cegah submit ganda saat masih menunggu balasan
                const text = this.draft.trim();
                if (!text) return;
                this.messages.push({ id: this.nextId(), from: 'user', text });
                this.draft = '';
                this.typing = true;
                window.__fuelMaosAsistenTyping = true;
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
                    window.__fuelMaosAsistenTyping = false;
                    this.messages.push({
                        id: this.nextId(),
                        from: 'bot',
                        text: data.balasan || 'Maaf, saya belum paham.',
                        saran: data.saran || [],
                        items: data.items || [],
                        total: data.total || 0,
                        lihatSemuaUrl: data.lihatSemuaUrl || null,
                    });
                    this.$nextTick(() => { log.scrollTop = log.scrollHeight; lucide.createIcons(); });
                })
                .catch(() => {
                    this.typing = false;
                    window.__fuelMaosAsistenTyping = false;
                    this.messages.push({ id: this.nextId(), from: 'bot', text: 'Gagal terhubung ke server.', saran: [] });
                });
            },
        };
    }
</script>
@endsection
