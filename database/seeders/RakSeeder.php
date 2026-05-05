<?php

namespace Database\Seeders;

use App\Models\Rak;
use Illuminate\Database\Seeder;
use OpenSpout\Reader\XLSX\Reader;

/**
 * Mengisi tabel raks dari sheet "rak" pada Excel (database/seeders/data/data.xlsx).
 *
 * Kolom wajib:
 * - Perusahaan
 * - Rak
 */
final class RakSeeder extends Seeder
{
    private const FILE_NAME = 'data.xlsx';

    private const SHEET_NAME = 'rak';

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

        $rows = $this->collectUniqueRaks($rowIterator);
        $reader->close();

        $n = 0;
        foreach ($rows as $r) {
            Rak::query()->firstOrCreate([
                'company_name' => $r['company_name'],
                'code' => $r['code'],
            ]);
            $n++;
        }

        $this->command?->info("RakSeeder: {$n} rak (per perusahaan) dari Excel.");
    }

    /**
     * @param  \Iterator<\OpenSpout\Common\Entity\Row>  $rows
     * @return list<array{company_name: string, code: string}>
     */
    private function collectUniqueRaks(\Iterator $rows): array
    {
        $header = null;
        $seen = [];

        foreach ($rows as $row) {
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
                $header = array_map(fn ($h) => self::canon((string) $h), $cells);
                continue;
            }

            $assoc = [];
            foreach ($header as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $cells[$i] ?? null;
            }

            $company = self::cleanStr($assoc['perusahaan'] ?? null);
            $code = self::cleanRakCode($assoc['rak'] ?? null);
            if ($company === '' || $code === '') {
                continue;
            }
            $k = mb_strtolower(trim($company)).'|'.mb_strtolower(trim($code));
            $seen[$k] = ['company_name' => $company, 'code' => $code];
        }

        $list = array_values($seen);
        usort($list, fn ($a, $b) => [$a['company_name'], $a['code']] <=> [$b['company_name'], $b['code']]);

        return $list;
    }

    private static function canon(string $s): string
    {
        $s = trim($s);
        $s = str_replace(['`', '"', "'"], '', $s);
        $s = preg_replace('/\s+/', ' ', $s) ?? $s;
        $s = mb_strtolower($s);

        return match ($s) {
            'perusahaan', 'nama perusahaan', 'company', 'customer' => 'perusahaan',
            'rak', 'posisi rak', 'posisi_rak' => 'rak',
            default => $s,
        };
    }

    private static function cleanStr(mixed $v): string
    {
        if ($v === null) {
            return '';
        }
        $s = trim((string) $v);
        if ($s === '' || $s === '-' || $s === '—') {
            return '';
        }

        return $s;
    }

    private static function cleanRakCode(mixed $v): string
    {
        $s = self::cleanStr($v);
        if ($s === '') {
            return '';
        }
        // Konsisten dengan format excel seperti "B4, B6, C10"
        $s = strtoupper($s);
        $s = preg_replace('/\s+/', '', $s) ?? $s;

        return $s;
    }
}
