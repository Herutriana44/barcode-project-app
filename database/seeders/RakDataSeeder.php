<?php

namespace Database\Seeders;

use App\Models\Rak;
use Illuminate\Database\Seeder;
use OpenSpout\Reader\XLSX\Reader;
use App\Support\Fqc038CleanListHeader;

class RakDataSeeder extends Seeder
{
    private const FILE_NAME = 'data.xlsx';
    private const SHEET_NAME = 'clean_list_part';

    public function run(): void
    {
        $path = base_path('database/seeders/data/'.self::FILE_NAME);
        if (!file_exists($path)) {
            return;
        }

        $reader = new Reader;
        $reader->open($path);

        $rakMap = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            if (trim((string) $sheet->getName()) !== self::SHEET_NAME) {
                continue;
            }

            $header = null;
            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                $cells = [];
                foreach ($row->getCells() as $cell) {
                    $cells[] = trim((string) $cell->getValue());
                }

                if ($header === null) {
                    $header = array_map(fn ($h) => Fqc038CleanListHeader::canonicalHeader($h), $cells);
                    continue;
                }

                $assoc = [];
                foreach ($header as $i => $key) {
                    $assoc[$key] = $cells[$i] ?? null;
                }

                $companyName = trim((string) ($assoc['customer'] ?? ''));
                $rak = trim((string) ($assoc['rak'] ?? ''));

                if ($companyName !== '' && $rak !== '') {
                    if (!isset($rakMap[$companyName])) {
                        $rakMap[$companyName] = [];
                    }
                    if (!in_array($rak, $rakMap[$companyName])) {
                        $rakMap[$companyName][] = $rak;
                    }
                }
            }
        }
        $reader->close();

        foreach ($rakMap as $companyName => $codes) {
            sort($codes);
            Rak::updateOrCreate(
                ['company_name' => $companyName],
                ['code' => implode(', ', $codes)]
            );
        }
    }
}
