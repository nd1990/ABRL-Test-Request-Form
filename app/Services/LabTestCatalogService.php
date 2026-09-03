<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LabTestCatalogService
{
    public const SHEET_URL = 'https://docs.google.com/spreadsheets/d/1RrS0smEuSgJFvqZPAGO-0OXNXKY0jCLH/export?format=csv';

    /**
     * Download the current public Google Sheet CSV and persist it locally.
     */
    public function download(): string
    {
        $ctx = stream_context_create(['http' => ['timeout' => 60]]);
        $csv = @file_get_contents(self::SHEET_URL, false, $ctx);

        if ($csv === false) {
            throw new \RuntimeException('Could not download the Google Sheet. Check network access to docs.google.com.');
        }

        $path = database_path('data/nabl_price_list.csv');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $csv);

        return $csv;
    }

    /**
     * Import lab tests from CSV content. Replaces all existing records.
     */
    public function importFromString(string $csv, bool $replaceExisting = false): int
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csv);
        rewind($handle);

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \RuntimeException('The CSV is empty or unreadable.');
        }
        $headers = array_map(fn ($h) => preg_replace('/\s+/', ' ', trim((string) $h)), $headers);

        $index = function (string $name) use ($headers) {
            foreach ($headers as $i => $h) {
                if (stripos($h, $name) !== false) {
                    return $i;
                }
            }
            return null;
        };

        $colSNo = $index('S.No');
        $colNabl = $index('NABL/NON NABL');
        $colDiscipline = $index('Discipline');
        $colMaterial = $index('Materials or Products');
        $colParameter = $index('Parameter');
        $colMethod = $index('Method');
        $colSampleQty = $index('Sample Quantity');
        $colLeadTime = $index('Lead time');
        $colCharges = $index('Charges per sample');
        $colGst = $index('GST SAC');
        $colTotal = $index('Total amount');
        $colProtocol = $index('PROTOCOL NO');
        $colRange = $index('NABL Range');
        $colLoq = $index('Limit of quantification');
        $colRemarks = $index('Minimum sample handeling');
        $colLink = $index('Protocol Link');

        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            $get = function ($i) use ($row) {
                if ($i === null || !isset($row[$i])) {
                    return null;
                }
                $val = preg_replace('/\s+/', ' ', trim((string) $row[$i]));
                return $val === '' ? null : $val;
            };

            $nabl = $get($colNabl);
            if ($nabl === null) {
                continue;
            }
            $nablNormalized = (strtoupper($nabl) === 'NABL') ? 'NABL' : 'NON NABL';

            $rows[] = [
                's_no' => $get($colSNo) !== null ? (int) preg_replace('/\D/', '', (string) $get($colSNo)) : null,
                'nabl_type' => $nablNormalized,
                'discipline' => $get($colDiscipline) ?? '',
                'material' => $get($colMaterial),
                'parameter' => $get($colParameter) ?? '',
                'method' => $get($colMethod),
                'sample_quantity' => $get($colSampleQty),
                'lead_time' => $get($colLeadTime),
                'charges_per_sample' => (float) str_replace(',', '', (string) ($get($colCharges) ?? 0)),
                'gst_amount' => (float) str_replace(',', '', (string) ($get($colGst) ?? 0)),
                'total_amount' => (float) str_replace(',', '', (string) ($get($colTotal) ?? 0)),
                'protocol_no' => $get($colProtocol),
                'nabl_range' => $get($colRange),
                'limit_of_quantification' => $get($colLoq),
                'remarks' => $get($colRemarks),
                'protocol_link' => $get($colLink),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        fclose($handle);

        if ($replaceExisting) {
            DB::table('lab_tests')->truncate();
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('lab_tests')->insert($chunk);
            }
            return count($rows);
        }

        $inserted = 0;
        $skipped = 0;

        foreach (array_chunk($rows, 500) as $chunk) {
            foreach ($chunk as $row) {
                $exists = DB::table('lab_tests')
                    ->where('nabl_type', $row['nabl_type'])
                    ->where('discipline', $row['discipline'])
                    ->where('parameter', $row['parameter'])
                    ->where('material', $row['material'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                } else {
                    DB::table('lab_tests')->insert($row);
                    $inserted++;
                }
            }
        }

        return $inserted;
    }

    public function reSync(): int
    {
        return $this->importFromString($this->download(), replaceExisting: true);
    }
}
