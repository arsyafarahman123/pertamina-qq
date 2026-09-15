<?php

namespace Database\Seeders;

use App\Models\ChecklistMtMaos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ChecklistMtMaosSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('checklists_seed.json');
        if (!File::exists($jsonPath)) {
            $this->command->error("File checklists_seed.json tidak ditemukan!");
            return;
        }

        $records = json_decode(File::get($jsonPath), true);
        if (!$records) {
            $this->command->error("Gagal membaca JSON!");
            return;
        }

        $count = 0;
        foreach ($records as $item) {
            // Hindari duplikasi jika sudah ada data dengan nopol & tgl_periksa yang sama persis
            $existing = ChecklistMtMaos::where('nomor_polisi', $item['nomor_polisi'])
                ->where('tanggal_periksa', $item['tanggal_periksa'])
                ->first();

            if (!$existing) {
                ChecklistMtMaos::create($item);
                $count++;
            } else {
                $existing->update($item);
                $count++;
            }
        }

        $this->command->info("Berhasil mengimpor {$count} data Checklist Mobil Tangki.");
    }
}
