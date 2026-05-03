<?php

namespace Database\Seeders;

use App\Models\Rak;
use App\Support\Fqc038CleanListHeader;
use Illuminate\Database\Seeder;
use OpenSpout\Reader\XLSX\Reader;

/**
 * Mengisi tabel raks dari nilai unik kolom Rak pada sheet clean_list_part (data.xlsx).
 */
final class RakSeeder extends Seeder
{
    private const FILE_NAME = 'data.xlsx';

    private const SHEET_NAME = 'clean_list_part';

    private const FALLBACK_HEADER_ROW_INDEX = 4;

    public function run(): void
    {
        $path = base_path('database/seeders/data/'.self::FILE_NAME);
        if (! is_file($path)) {
            $this->command?->error("File Excel tidak ditemukan: {$path}");
            $this->command?->line('Taruh file Excel di folder tersebut, lalu jalankan seed lagi.');

            return;
        }

        $reader = new Reader;
        $reader->open($path);

        $sheetIterator = $reader->getSheetIterator();
        $rowIterator = null;
        foreach ($sheetIterator as $sheet) {
            if (trim((string) $sheet->getName()) !== self::SHEET_NAME) {
                continue;
            }
            $rowIterator = $sheet->getRowIterator();
            break;
        }

        if ($rowIterator === null) {
            $reader->close();
            $this->command?->warn('Sheet "'.self::SHEET_NAME.'" tidak ditemukan.');

            return;
        }

        $codes = $this->collectUniqueRakCodes($rowIterator);
        $reader->close();

        $n = 0;
        foreach ($codes as $code) {
            Rak::query()->firstOrCreate(['code' => $code]);
            $n++;
        }

        $this->command?->info("RakSeeder: {$n} kode rak unik dari Excel.");
    }

    /**
     * @param  \Iterator<\OpenSpout\Common\Entity\Row>  $rows
     * @return list<string>
     */
    private function collectUniqueRakCodes(\Iterator $rows): array
    {
        $header = null;
        $rowIndex = 0;
        $seen = [];

        foreach ($rows as $row) {
            $rowIndex++;

            $cells = [];
            foreach ($row->getCells() as $cell) {
                $v = $cell->getValue();
                if ($v instanceof \DateTimeInterface) {
                    $cells[] = $v->format('Y-m-d');
                } elseif (is_string($v)) {
                    $cells[] = trim($v);
                } else {
                    $cells[] = $v;
                }
            }

            if (! is_array($header)) {
                $maybeHeader = array_map(fn ($h) => Fqc038CleanListHeader::canonicalHeader((string) $h), $cells);
                if (Fqc038CleanListHeader::looksLikeHeaderRow($maybeHeader)) {
                    $header = $maybeHeader;
                    continue;
                }
                if ($rowIndex === self::FALLBACK_HEADER_ROW_INDEX) {
                    $header = $maybeHeader;
                    continue;
                }
                continue;
            }

            $assoc = [];
            foreach ($header as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $cells[$i] ?? null;
            }

            $raw = $assoc['rak'] ?? null;
            if ($raw === null || $raw === '') {
                continue;
            }
            $code = is_string($raw) ? trim($raw) : trim((string) $raw);
            if ($code === '' || $code === '-' || $code === '—') {
                continue;
            }
            $seen[$code] = true;
        }

        $list = array_keys($seen);
        sort($list);

        return $list;
    }
}
