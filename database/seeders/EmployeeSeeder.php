<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use OpenSpout\Reader\XLSX\Reader;

/**
 * Seeder karyawan dari Excel.
 *
 * Kolom yang didukung:
 * - No (diabaikan)
 * - Nama
 * - Nomor Induk Pegawai
 * - Departemen
 * - Jabatan
 * - Status
 */
final class EmployeeSeeder extends Seeder
{
    private const FILE_NAME = 'data karyawan 4.xlsx';

    private const SHEET_NAME = 'karyawan';

    public function run(): void
    {
        $path = base_path('database/seeders/data/'.self::FILE_NAME);
        if (! is_file($path)) {
            $this->command?->warn("EmployeeSeeder: file Excel tidak ditemukan: {$path}");
            $this->command?->line('Taruh file tersebut, lalu jalankan seed lagi.');

            return;
        }

        $reader = new Reader;
        $reader->open($path);

        $rowIterator = null;
        foreach ($reader->getSheetIterator() as $sheet) {
            $name = trim((string) $sheet->getName());
            if ($name === '' || mb_strtolower($name) === mb_strtolower(self::SHEET_NAME)) {
                $rowIterator = $sheet->getRowIterator();
                break;
            }
        }

        if ($rowIterator === null) {
            $reader->close();
            $this->command?->warn('EmployeeSeeder: sheet "'.self::SHEET_NAME.'" tidak ditemukan.');

            return;
        }

        $result = $this->importRows($rowIterator);
        $reader->close();

        $this->command?->info("EmployeeSeeder: {$result} karyawan diimpor/diupdate.");
    }

    /**
     * @param  \Iterator<\OpenSpout\Common\Entity\Row>  $rows
     */
    private function importRows(\Iterator $rows): int
    {
        $header = null;
        $imported = 0;

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
                if ($key === '') continue;
                $assoc[$key] = $cells[$i] ?? null;
            }

            $name = self::cleanStr($assoc['nama'] ?? null);
            $nip = self::cleanStr($assoc['nip'] ?? null);
            if ($name === '' && $nip === '') {
                continue;
            }
            if ($name === '' || $nip === '') {
                continue;
            }

            $payload = [
                'name' => $name,
                'departemen' => self::cleanStrOrNull($assoc['departemen'] ?? null),
                'jabatan' => self::cleanStrOrNull($assoc['jabatan'] ?? null),
                'status' => self::cleanStrOrNull($assoc['status'] ?? null),
            ];

            Employee::query()->updateOrCreate(
                ['nip' => $nip],
                $payload
            );

            $imported++;
        }

        return $imported;
    }

    private static function canon(string $s): string
    {
        $s = trim($s);
        $s = str_replace(['`', '"', "'"], '', $s);
        $s = preg_replace('/\s+/', ' ', $s) ?? $s;
        $s = mb_strtolower($s);

        return match ($s) {
            'no', 'nomor', 'nomor urut' => 'no',
            'nama', 'name' => 'nama',
            'nomor induk pegawai', 'nomorindukpegawai', 'nip', 'nomor induk pegawai ' => 'nip',
            'departemen', 'department', 'dept' => 'departemen',
            'jabatan', 'position' => 'jabatan',
            'status', 'status ' => 'status',
            default => $s,
        };
    }

    private static function cleanStr(mixed $v): string
    {
        if ($v === null) return '';
        $s = trim((string) $v);
        if ($s === '' || $s === '-' || $s === '—') return '';

        return $s;
    }

    private static function cleanStrOrNull(mixed $v): ?string
    {
        $s = self::cleanStr($v);

        return $s === '' ? null : $s;
    }
}

