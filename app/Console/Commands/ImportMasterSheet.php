<?php

namespace App\Console\Commands;

use App\Services\LabTestCatalogService;
use Illuminate\Console\Command;

class ImportMasterSheet extends Command
{
    protected $signature = 'master:import
        {file? : Path to the Master Sheet (.xlsx, .xls, .ods or .csv). Defaults to the newest "Master Sheet*.xlsx" in the project root}
        {--keep : Keep existing records instead of replacing the whole lab tests table}';

    protected $description = 'Import the Master Sheet into the lab_tests table (replaces existing data by default).';

    public function handle(LabTestCatalogService $catalog): int
    {
        $file = $this->argument('file') ?: $this->discoverMasterSheet();

        if (!$file || !is_file($file)) {
            $this->error('Master sheet not found. Pass a path, for example:');
            $this->line('  php artisan master:import "Final Master Sheet 16-10-2026.xlsx"');
            return self::FAILURE;
        }

        $replace = !$this->option('keep');

        $this->info(($replace ? 'Replacing existing data with ' : 'Importing ') . basename($file) . ' ...');

        try {
            $count = $catalog->importFromFile($file, $replace);
        } catch (\Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info(($replace ? 'Replaced with ' : 'Added ') . $count . ' lab tests from the Master Sheet.');

        return self::SUCCESS;
    }

    protected function discoverMasterSheet(): ?string
    {
        $candidates = glob(base_path('*Master Sheet*.xlsx')) ?: [];

        if (empty($candidates)) {
            return null;
        }

        usort($candidates, fn ($a, $b) => filemtime($b) <=> filemtime($a));

        return $candidates[0];
    }
}
