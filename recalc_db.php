<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RetainSampelMt;
use App\Services\DensityCorrectionService;

$samples = RetainSampelMt::all();
echo "Found " . $samples->count() . " retain sample records to recalculate.\n";

$updated = 0;
foreach ($samples as $sample) {
    if ($sample->density_obs && $sample->temperatur) {
        $oldD15 = $sample->density_15;
        $newD15 = DensityCorrectionService::hitungDensity15((float)$sample->density_obs, (float)$sample->temperatur);
        $sample->density_15 = $newD15;
        $sample->save();
        $updated++;
        echo "ID {$sample->id} ({$sample->produk}): Obs {$sample->density_obs} @ {$sample->temperatur}C => Old: {$oldD15} -> New: {$newD15}\n";
    }
}

echo "Successfully updated {$updated} records with Table 53B formula.\n";
