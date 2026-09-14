@once
<div
    x-data="{
        open: false,
        title: '',
        message: '',
        formEl: null,
        submitting: false,
        show(formEl, title, message) {
            this.formEl = formEl;
            this.title = title;
            this.message = message;
            this.submitting = false;
            this.open = true;
        },
        confirmDelete() {
            if (!this.formEl) return;
            this.submitting = true;
            this.formEl.submit();
        }
    }"
    x-init="window.__confirmDeleteModal = $data"
    x-show="open"
    x-cloak
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
    style="display:none;"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="open = false"
        class="w-full max-w-sm overflow-hidden rounded-3xl bg-white shadow-2xl"
    >
        <!-- Header bertema Pertamina -->
        <div class="relative overflow-hidden bg-gradient-to-r from-brand-red to-red-600 px-6 pt-6 pb-8">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -left-8 -bottom-10 h-28 w-28 rounded-full bg-brand-blue/30 blur-2xl"></div>
            <div class="relative flex items-center gap-3">
                <img src="{{ asset('images/pertamina-mark.svg') }}" alt="Pertamina" class="h-9 w-auto drop-shadow">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/70">Fuel Maos &middot; Smart Fuel QC System</p>
                    <p class="text-sm font-bold text-white">Konfirmasi Hapus Data</p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="relative px-6 pb-6 pt-0">
            <div class="-mt-6 mb-4 flex justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border-4 border-white bg-rose-50 shadow-lg">
                    <i data-lucide="trash-2" class="h-5 w-5 text-rose-500"></i>
                </div>
            </div>

            <h3 class="text-center text-base font-bold text-slate-900" x-text="title"></h3>
            <p class="mt-2 text-center text-sm leading-relaxed text-slate-500" x-text="message"></p>

            <div class="mt-3 flex items-center justify-center gap-1.5 rounded-xl bg-amber-50 px-3 py-2 text-center text-xs font-medium text-amber-700">
                <i data-lucide="alert-triangle" class="h-3.5 w-3.5 shrink-0"></i>
                Tindakan ini tidak dapat dibatalkan.
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" @click="open = false"
                        class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" @click="confirmDelete()" :disabled="submitting"
                        class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-500/30 transition hover:from-rose-700 hover:to-red-700 disabled:opacity-70">
                    <i data-lucide="loader-2" x-show="submitting" x-cloak class="h-4 w-4 animate-spin"></i>
                    <i data-lucide="trash-2" x-show="!submitting" class="h-4 w-4"></i>
                    <span x-text="submitting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endonce
