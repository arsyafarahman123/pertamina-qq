<?php
/**
 * ==================================================================
 *  SETUP ONE-TIME — PERTAMINA QQ · FUEL TERMINAL MAOS
 * ==================================================================
 *  Script ini dijalankan SEKALI lewat browser setelah upload ke
 *  InfinityFree. URL: https://pertamina-qq-maos.infinityfreeapp.com/setup.php
 *
 *  Fungsi:
 *  1. Menjalankan migration (buat tabel MySQL)
 *  2. Menjalankan seeder (buat user + jenis uji + langkah SOP)
 *  3. Membuat symlink public/storage -> storage/app/public
 *  4. Membuat folder storage yang dibutuhkan (sessions, cache, views, logs)
 *
 *  Setelah selesai, HAPUS file ini demi keamanan.
 * ==================================================================
 */

// Naikkan batas waktu (migration bisa lambat di shared hosting)
ini_set('max_execution_time', 300);
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo '<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
echo '<title>Setup — Pertamina QQ Fuel Terminal Maos</title>';
echo '<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #f1f5f9; color: #1e293b; padding: 2rem; }
    .container { max-width: 720px; margin: 0 auto; }
    h1 { font-size: 1.5rem; margin-bottom: .5rem; }
    .subtitle { color: #64748b; font-size: .875rem; margin-bottom: 1.5rem; }
    .step { background: #fff; border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1rem 1.25rem; margin-bottom: .75rem; }
    .step-ok { border-left: 4px solid #22c55e; }
    .step-err { border-left: 4px solid #ef4444; }
    .step-warn { border-left: 4px solid #f59e0b; }
    .step h3 { font-size: .9rem; margin-bottom: .25rem; }
    .step p { font-size: .8rem; color: #64748b; white-space: pre-wrap; word-break: break-all; }
    .badge { display: inline-block; font-size: .7rem; font-weight: 700; padding: .15rem .5rem; border-radius: 999px; }
    .badge-ok { background: #dcfce7; color: #166534; }
    .badge-err { background: #fef2f2; color: #991b1b; }
    .badge-warn { background: #fef3c7; color: #92400e; }
    .final { background: #f0fdf4; border: 2px solid #22c55e; border-radius: .75rem; padding: 1.5rem; margin-top: 1.5rem; text-align: center; }
    .final h2 { color: #166534; margin-bottom: .5rem; }
    .final a { color: #2563eb; font-weight: 600; text-decoration: none; }
    .final a:hover { text-decoration: underline; }
    .err-final { background: #fef2f2; border-color: #ef4444; }
    .err-final h2 { color: #991b1b; }
</style></head><body><div class="container">';
echo '<h1>🔧 Setup Pertamina QQ — Fuel Terminal Maos</h1>';
echo '<p class="subtitle">Menjalankan migration, seeder, dan konfigurasi storage...</p>';

$hasError = false;

// ---- STEP 0: Boot Laravel ----
try {
    $baseDir = file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ : __DIR__ . '/..';
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo '<div class="step step-ok"><h3>✅ Laravel Bootstrap</h3>';
    echo '<p>Berhasil memuat framework Laravel.</p></div>';
} catch (\Throwable $e) {
    echo '<div class="step step-err"><h3>❌ Laravel Bootstrap GAGAL</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p></div>';
    $hasError = true;
    goto done;
}

// ---- STEP 1: Test Koneksi Database ----
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
    $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();

    echo '<div class="step step-ok"><h3>✅ Koneksi Database</h3>';
    echo '<p>Driver: ' . $driver . ' | Database: ' . $dbName . '</p></div>';
} catch (\Throwable $e) {
    echo '<div class="step step-err"><h3>❌ Koneksi Database GAGAL</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>⚠️  Pastikan .env sudah diisi dengan detail MySQL dari panel InfinityFree!</p></div>';
    $hasError = true;
    goto done;
}

// ---- STEP 2: Migration ----
try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $output = \Illuminate\Support\Facades\Artisan::output();

    echo '<div class="step step-ok"><h3>✅ Database Migration</h3>';
    echo '<p>' . htmlspecialchars(trim($output) ?: 'Semua tabel berhasil dibuat.') . '</p></div>';
} catch (\Throwable $e) {
    echo '<div class="step step-err"><h3>❌ Migration GAGAL</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p></div>';
    $hasError = true;
}

// ---- STEP 3: Seeder ----
try {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    $output = \Illuminate\Support\Facades\Artisan::output();

    echo '<div class="step step-ok"><h3>✅ Database Seeder</h3>';
    echo '<p>' . htmlspecialchars(trim($output) ?: 'User & Jenis Uji berhasil dibuat.') . '</p></div>';
} catch (\Throwable $e) {
    echo '<div class="step step-warn"><h3>⚠️ Seeder Gagal (mungkin sudah ada data)</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p></div>';
}

// ---- STEP 4: Storage directories ----
$storageDirs = [
    storage_path('app/public'),
    storage_path('app/public/bukti-uji'),
    storage_path('app/public/retain-sampel'),
    storage_path('app/public/retain-sampel/sesi'),
    storage_path('framework/cache/data'),
    storage_path('framework/sessions'),
    storage_path('framework/testing'),
    storage_path('framework/views'),
    storage_path('logs'),
];

$dirCreated = 0;
foreach ($storageDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
        $dirCreated++;
    }
}

// Pastikan file .gitignore ada di setiap subfolder agar folder persist
foreach ($storageDirs as $dir) {
    $gitignore = $dir . '/.gitignore';
    if (! file_exists($gitignore)) {
        @file_put_contents($gitignore, "*\n!.gitignore\n");
    }
}

// Buat file log kosong kalau belum ada
$logFile = storage_path('logs/laravel.log');
if (!file_exists($logFile)) {
    @file_put_contents($logFile, '');
}

echo '<div class="step step-ok"><h3>✅ Storage Directories</h3>';
echo '<p>' . $dirCreated . ' folder dibuat. Semua folder storage siap.</p></div>';

// ---- STEP 5: Storage symlink ----
$linkPath = __DIR__ . '/storage';
$targetPath = storage_path('app/public');

if (is_link($linkPath) || is_dir($linkPath)) {
    echo '<div class="step step-ok"><h3>✅ Storage Symlink</h3>';
    echo '<p>Sudah ada: public/storage → storage/app/public</p></div>';
} else {
    // InfinityFree mungkin tidak mendukung symlink, coba buat
    $linked = @symlink($targetPath, $linkPath);
    if ($linked) {
        echo '<div class="step step-ok"><h3>✅ Storage Symlink Dibuat</h3>';
        echo '<p>public/storage → storage/app/public</p></div>';
    } else {
        // Fallback: buat .htaccess redirect
        echo '<div class="step step-warn"><h3>⚠️ Symlink Tidak Didukung</h3>';
        echo '<p>InfinityFree tidak mengizinkan symlink. Foto/file tetap bisa diakses lewat route terkontrol (tanpa symlink).</p></div>';
    }
}

// ---- STEP 6: Clear & cache config ----
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');

    echo '<div class="step step-ok"><h3>✅ Cache Cleared</h3>';
    echo '<p>Config, route, dan view cache dibersihkan.</p></div>';
} catch (\Throwable $e) {
    echo '<div class="step step-warn"><h3>⚠️ Cache Clear</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p></div>';
}

done:

if ($hasError) {
    echo '<div class="final err-final">';
    echo '<h2>❌ Setup Belum Selesai</h2>';
    echo '<p>Ada error yang harus diperbaiki. Cek detail di atas, lalu refresh halaman ini.</p>';
    echo '</div>';
} else {
    echo '<div class="final">';
    echo '<h2>✅ Setup Berhasil!</h2>';
    echo '<p style="margin-bottom:.5rem">Semua tabel database, user, dan jenis uji sudah dibuat.</p>';
    echo '<p><strong>Akun Login:</strong></p>';
    echo '<p>Admin: <code>admin@labqq.test</code> / <code>password123</code></p>';
    echo '<p>Petugas: <code>petugas@labqq.test</code> / <code>password123</code></p>';
    echo '<p>Viewer: <code>viewer@labqq.test</code> / <code>password123</code></p>';
    echo '<p style="margin-top:1rem"><a href="' . url('/login') . '">→ Masuk ke Login</a></p>';
    echo '<p style="margin-top:1rem;color:#ef4444;font-size:.8rem"><strong>⚠️ HAPUS file setup.php setelah selesai demi keamanan!</strong></p>';
    echo '</div>';
}

echo '</div></body></html>';
