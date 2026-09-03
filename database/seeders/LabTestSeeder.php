<?php

namespace Database\Seeders;

use App\Services\LabTestCatalogService;
use Illuminate\Database\Seeder;

class LabTestSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/nabl_price_list.csv');

        if (!is_file($path)) {
            $this->command?->warn('NABL price list CSV not found at ' . $path . ' — nothing imported.');
            return;
        }

        $csv = file_get_contents($path);
        if ($csv === false || $csv === '') {
            $this->command?->warn('NABL price list CSV is empty.');
            return;
        }

        try {
            $count = app(LabTestCatalogService::class)->importFromString($csv);
            $this->command?->info("Imported {$count} lab tests.");
        } catch (\Throwable $e) {
            $this->command?->error('Import failed: ' . $e->getMessage());
        }
    }
}
