<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelMaosController;
use App\Http\Controllers\JenisUjiController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// ---- Auth ----
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---- Halaman yang butuh login ----
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/uji', [JenisUjiController::class, 'index'])->name('uji.index');
    Route::get('/uji/{jenisUji:slug}', [JenisUjiController::class, 'show'])->name('uji.show');
    Route::post('/uji/{jenisUji:slug}/simpan', [JenisUjiController::class, 'simpanHasil'])->name('uji.simpan');

    // ---- Pengujian BBM Terpadu (Kesesuaian Spesifikasi) ----
    Route::get('/pengujian-bbm', [\App\Http\Controllers\UjiBbmController::class, 'index'])->name('ujibbm.index');
    Route::post('/pengujian-bbm/submit', [\App\Http\Controllers\UjiBbmController::class, 'submit'])->name('ujibbm.submit');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/export-excel', [RiwayatController::class, 'exportExcel'])->name('riwayat.export-excel');
    Route::get('/riwayat/export-excel-semua', [RiwayatController::class, 'exportExcelAll'])->name('riwayat.export-excel-all');
    Route::get('/riwayat/{hasilUji}', [RiwayatController::class, 'show'])->name('riwayat.show');
    Route::get('/riwayat/{hasilUji}/cetak', [RiwayatController::class, 'cetak'])->name('riwayat.cetak');
    Route::get('/riwayat/{hasilUji}/foto-bukti', [RiwayatController::class, 'fotoBukti'])->name('riwayat.foto-bukti');

    // ---- Asisten Fuel Maos (chatbot) ----
    Route::get('/asisten', [FuelMaosController::class, 'halaman'])->name('fuelmaos.halaman');
    Route::post('/fuel-maos/chat', [FuelMaosController::class, 'chat'])->name('fuelmaos.chat');

    // ---- Kelola hasil uji (hanya admin) ----
    Route::middleware('admin')->group(function () {
        Route::get('/riwayat/{hasilUji}/edit', [RiwayatController::class, 'edit'])->name('riwayat.edit');
        Route::put('/riwayat/{hasilUji}', [RiwayatController::class, 'update'])->name('riwayat.update');
        Route::delete('/riwayat/{hasilUji}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');
    });

    // ---- Rekap Retain Sampel Penyaluran MT (Density'15 otomatis) ----
    Route::prefix('retain-sampel')->name('retain-sampel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RetainSampelMtController::class, 'index'])->name('index');
        Route::get('/semua-tanggal', [\App\Http\Controllers\RetainSampelMtController::class, 'semua'])->name('semua');
        Route::get('/cetak', [\App\Http\Controllers\RetainSampelMtController::class, 'cetak'])->name('cetak');
        Route::get('/export-excel', [\App\Http\Controllers\RetainSampelMtController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-excel-semua', [\App\Http\Controllers\RetainSampelMtController::class, 'exportExcelAll'])->name('export-excel-all');
        Route::get('/baru', [\App\Http\Controllers\RetainSampelMtController::class, 'create'])->name('create');
        Route::post('/preview-density15', [\App\Http\Controllers\RetainSampelMtController::class, 'preview'])->name('preview');
        Route::post('/', [\App\Http\Controllers\RetainSampelMtController::class, 'store'])->name('store');
        Route::post('/foto-sesi', [\App\Http\Controllers\RetainSampelMtController::class, 'simpanFotoSesi'])->name('foto-sesi.store');
        Route::get('/foto-sesi/{fotoSesi}', [\App\Http\Controllers\RetainSampelMtController::class, 'fotoSesi'])->name('foto-sesi.lihat');
        Route::get('/{retainSampel}/edit', [\App\Http\Controllers\RetainSampelMtController::class, 'edit'])->name('edit');
        Route::put('/{retainSampel}', [\App\Http\Controllers\RetainSampelMtController::class, 'update'])->name('update');
        Route::get('/{retainSampel}/foto', [\App\Http\Controllers\RetainSampelMtController::class, 'foto'])->name('foto');

        Route::middleware('admin')->group(function () {
            Route::delete('/{retainSampel}', [\App\Http\Controllers\RetainSampelMtController::class, 'destroy'])->name('destroy');
        });
    });

    // ---- Checklist Mobil Tangki — Fuel Terminal Maos ----
    // Admin/Petugas Lab QQ (internal Pertamina) bisa lihat semua; SPBU/Transportir cuma lihat miliknya.
    Route::prefix('checklist-mt-maos')->name('checklist-mt-maos.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ChecklistMtMaosController::class, 'index'])->name('index');
        Route::get('/export-semua', [\App\Http\Controllers\ChecklistMtMaosController::class, 'exportAll'])->name('export-all');
        Route::get('/cetak-banyak', [\App\Http\Controllers\ChecklistMtMaosController::class, 'cetakBanyak'])->name('cetak-banyak');
        Route::get('/run-import-checklist-all-300-records', [\App\Http\Controllers\ChecklistMtMaosController::class, 'runImportSeed'])->name('import-seed');
        Route::get('/{checklist}', [\App\Http\Controllers\ChecklistMtMaosController::class, 'show'])->name('show');
        Route::get('/{checklist}/export', [\App\Http\Controllers\ChecklistMtMaosController::class, 'exportOne'])->name('export');
        Route::get('/{checklist}/cetak', [\App\Http\Controllers\ChecklistMtMaosController::class, 'cetak'])->name('cetak');

        // Hanya akun Master MAOS (role admin) yang boleh input/ubah/hapus checklist.
        Route::middleware('admin')->group(function () {
            Route::get('/baru/input', [\App\Http\Controllers\ChecklistMtMaosController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\ChecklistMtMaosController::class, 'store'])->name('store');
            Route::get('/{checklist}/edit', [\App\Http\Controllers\ChecklistMtMaosController::class, 'edit'])->name('edit');
            Route::put('/{checklist}', [\App\Http\Controllers\ChecklistMtMaosController::class, 'update'])->name('update');
            Route::delete('/{checklist}', [\App\Http\Controllers\ChecklistMtMaosController::class, 'destroy'])->name('destroy');
        });
    });
});

Route::get('/checklist-mt-maos-import-seed-direct', [\App\Http\Controllers\ChecklistMtMaosController::class, 'runImportSeed']);

