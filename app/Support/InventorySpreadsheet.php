<?php

namespace App\Support;

use App\Models\Company;
use App\Models\CompanyBarcode;
use App\Models\CompanyItem;
use App\Models\Employee;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemReceiving;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class InventorySpreadsheet
{
    public const FG_COLS = 27;

    public const COMPANY_COLS = 9;

    /** @return list<string> */
    public static function fgHeaderRow(): array
    {
        return [
            'nama_perusahaan',
            'code',
            'customer',
            'part_name',
            'part_number',
            'model',
            'berat',
            'qty',
            // 'inspector_name',
            'tgl_produksi',
            'tgl_expired',
            'posisi_rak',
            // 'tingkat',
            // 'ukuran_material',
            // 'jenis_bahan',
            // 'quantity_material',
            // 'no_surat_jalan_material',
            // 'tanggal_terima_material',
            // 'transfer_slip_no',
            // 'tanggal_terima_fg',
            // 'jumlah_box',
            // 'operator_mobil_nama',
            // 'pengirim_nama',
            // 'operator_forklift_nama',
            //'qty_sub_pack',
            'berat_packaging_gram',
            'berat_per_pcs_gram',
        ];
    }

    /** @return list<string|int|float|null> */
    public static function fgExampleRow(): array
    {
        return [
            '(isi persis nama perusahaan di sistem)',
            'KODE-UNIK-01',
            'PT Customer Contoh',
            'Part contoh',
            'PN-001',
            'Model-A',
            1.25,
            100,
            // 'Inspector',
            '2026-05-31',
            '2026-08-31',
            'Rak-A1',
            // '1',
            // '',
            // 'SPCC',
            // '',
            // '',
            // '',
            // 'TS-001',
            // '2026-02-01',
            // 1,
            // '',
            // '',
            // '',
            // 24,
            0,
            0,
        ];
    }

    /** @return list<string> */
    public static function companyHeaderRow(): array
    {
        return [
            'nama_perusahaan',
            // 'part_name',
            // 'code',
            // 'qty',
            // 'posisi_rak',
            // 'tingkat',
            // 'operator_mobil_nama',
            // 'pengirim_nama',
            // 'operator_forklift_nama',
        ];
    }

    /** @return list<string|int|float|null> */
    public static function companyExampleRow(): array
    {
        return [
            'PT Contoh Import',
            // 'Barang default',
            // 'CB-001',
            // 0,
            // '-',
            // '-',
            // '',
            // '',
            // '',
        ];
    }

    public static function downloadFgTemplate(): StreamedResponse
    {
        return self::streamXlsx('template-import-barang-fg.xlsx', [
            self::fgHeaderRow(),
            self::fgExampleRow(),
        ]);
    }

    public static function downloadCompanyTemplate(): StreamedResponse
    {
        return self::streamXlsx('template-import-perusahaan.xlsx', [
            self::companyHeaderRow(),
            self::companyExampleRow(),
        ]);
    }

    /**
     * @param  list<list<string|int|float|null>>  $rows
     */
    public static function writeXlsxToPath(string $path, array $rows): void
    {
        $writer = new Writer;
        $writer->openToFile($path);
        foreach ($rows as $r) {
            $writer->addRow(Row::fromValues($r));
        }
        $writer->close();
    }

    /**
     * @param  list<list<string|int|float|null>>  $rows
     */
    private static function streamXlsx(string $filename, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows) {
            $writer = new Writer;
            $writer->openToFile('php://output');
            foreach ($rows as $r) {
                $writer->addRow(Row::fromValues($r));
            }
            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return list<list<mixed>>
     */
    public static function readFirstSheet(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if ($path === false) {
            return [];
        }

        $reader = new Reader;
        $reader->open($path);
        $matrix = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $matrix[] = self::readerRowToArray($row);
            }
            break;
        }
        $reader->close();

        return $matrix;
    }

    /**
     * @return list<mixed>
     */
    private static function readerRowToArray(\OpenSpout\Common\Entity\Row $row): array
    {
        $out = [];
        foreach ($row->getCells() as $cell) {
            $v = $cell->getValue();
            if ($v instanceof \DateTimeInterface) {
                $out[] = $v->format('Y-m-d');
            } elseif (is_float($v) || is_int($v)) {
                $out[] = $v;
            } elseif ($v === null) {
                $out[] = null;
            } else {
                $out[] = is_string($v) ? trim($v) : $v;
            }
        }

        return $out;
    }

    /**
     * @param  list<list<mixed>>  $matrix
     * @return array{errors: list<string>, imported: int, message?: string}
     */
    public static function importFgItemsFromMatrix(array $matrix): array
    {
        if (count($matrix) < 2) {
            return ['errors' => ['Berkas kosong atau hanya berisi header.'], 'imported' => 0];
        }

        $headers = array_map(fn($h) => trim(mb_strtolower((string) $h)), $matrix[0]);
        $dataRows = array_slice($matrix, 1);
        $errors = [];
        $payloads = [];

        // Helper to get index by header name (fuzzy matching)
        $getIdx = function(string $name) use ($headers) {
            foreach ($headers as $index => $header) {
                // Remove extra spaces and compare normalized strings
                if (str_contains($header, mb_strtolower($name)) || str_contains(mb_strtolower($name), $header)) {
                    return $index;
                }
            }
            return null;
        };

        foreach ($dataRows as $i => $row) {
            $lineNum = $i + 2;
            $rowErrors = [];

            $companyName = self::str($row[$getIdx('nama_perusahaan')] ?? '');
            $code = self::str($row[$getIdx('code')] ?? '');

            if ($companyName === '' && $code === '') continue;
            if ($companyName === '') { $errors[] = "Baris {$lineNum}: nama_perusahaan wajib diisi."; continue; }
            if ($code === '') { $errors[] = "Baris {$lineNum}: code wajib diisi."; continue; }

            $company = Company::query()
                ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($companyName)])
                ->first();

            if ($company === null) {
                $errors[] = "Baris {$lineNum}: perusahaan \"{$companyName}\" tidak ditemukan.";
                continue;
            }

            $opMobName = self::str($row[$getIdx('operator_mobil')] ?? '');
            $opPengName = self::str($row[$getIdx('pengirim')] ?? '');
            $opForkName = self::str($row[$getIdx('operator_forklift')] ?? '');
            $qtySubPack = self::toNullableInt($row[$getIdx('qty_sub_pack')] ?? 1);
            $beratPackagingG = self::toNullableInt($row[$getIdx('berat_packaging_gram')] ?? 0);
            $beratPerPcsG = self::toNullableInt($row[$getIdx('berat_per_pcs_gram')] ?? 0);

            // ... (rest of logic using $getIdx for all fields)

            $qty = self::toInt($row[$getIdx('qty')] ?? 0);
            if ($qty < 0) {
                $rowErrors[] = "Baris {$lineNum}: qty tidak valid.";
            }

            $jb = self::normalizeJenisBahan(self::str($row[$getIdx('jenis_bahan')] ?? null));

            $dProd = self::parseDateOptional($row[$getIdx('tgl_produksi')] ?? null, "Baris {$lineNum}: tgl_produksi", $rowErrors);
            $dExp = self::parseDateOptional($row[$getIdx('tgl_expired')] ?? null, "Baris {$lineNum}: tgl_expired", $rowErrors);
            $dMat = self::parseDateOptional($row[$getIdx('tanggal_terima_material')] ?? null, "Baris {$lineNum}: tanggal_terima_material", $rowErrors) ?? date('Y-m-d');
            $dFg = self::parseDateOptional($row[$getIdx('tanggal_terima_fg')] ?? null, "Baris {$lineNum}: tanggal_terima_fg", $rowErrors) ?? date('Y-m-d');

            if (count($rowErrors) > 0) {
                array_push($errors, ...$rowErrors);

                continue;
            }

            $payloads[] = [
                'line' => $lineNum,
                'company_id' => $company->id,
                'customer' => self::nullableStr($row[$getIdx('customer')] ?? null),
                'part_name' => self::nullableStr($row[$getIdx('part_name')] ?? null),
                'part_number' => self::nullableStr($row[$getIdx('part_number')] ?? null),
                'model' => self::nullableStr($row[$getIdx('model')] ?? null),
                'berat' => self::toNullableFloat($row[$getIdx('berat')] ?? null),
                'qty' => $qty,
                'inspector_name' => self::nullableStr($row[$getIdx('inspector_name')] ?? null),
                'tgl_produksi' => $dProd,
                'tgl_expired' => $dExp,
                'code' => $code,
                'posisi_rak' => self::nullableStr($row[$getIdx('posisi_rak')] ?? null),
                'tingkat' => self::nullableStr($row[$getIdx('tingkat')] ?? '-'),
                'ukuran_material' => self::nullableStr($row[$getIdx('ukuran_material')] ?? '-'),
                'jenis_bahan' => $jb ?? null,
                'quantity_material' => self::toNullableInt($row[$getIdx('quantity_material')] ?? 0),
                'no_surat_jalan_material' => self::nullableStr($row[$getIdx('no_surat_jalan_material')] ?? '-'),
                'tanggal_terima_material' => $dMat,
                'transfer_slip_no' => self::nullableStr($row[$getIdx('transfer_slip_no')] ?? '-'),
                'tanggal_terima_fg' => $dFg,
                'jumlah_box' => self::toInt($row[$getIdx('jumlah_box')] ?? 0),
                'operator_mobil_id' => $opMobName !== '' ? self::resolveEmployeeIdByName($opMobName) : null,
                'pengirim_id' => $opPengName !== '' ? self::resolveEmployeeIdByName($opPengName) : null,
                'operator_forklift_id' => $opForkName !== '' ? self::resolveEmployeeIdByName($opForkName) : null,
                'qty_sub_pack' => $qtySubPack !== null && $qtySubPack > 0 ? $qtySubPack : 1,
                'berat_packaging_gram' => $beratPackagingG ?? 0,
                'berat_per_pcs_gram' => $beratPerPcsG ?? 0,
            ];
        }

        if (count($payloads) === 0 && count($errors) === 0) {
            return ['errors' => ['Tidak ada baris data yang diisi.'], 'imported' => 0];
        }

        if (count($errors) > 0) {
            return ['errors' => $errors, 'imported' => 0];
        }

        DB::transaction(function () use ($payloads) {
            foreach ($payloads as $p) {
                $item = Item::create([
                    'company_id' => $p['company_id'],
                    'operator_mobil_id' => $p['operator_mobil_id'],
                    'pengirim_id' => $p['pengirim_id'],
                    'operator_forklift_id' => $p['operator_forklift_id'],
                    'customer' => $p['customer'],
                    'part_name' => $p['part_name'],
                    'part_number' => $p['part_number'],
                    'model' => $p['model'],
                    'berat' => $p['berat'],
                    'qty' => $p['qty'],
                    'static_qty' => $p['qty'],
                    'dynamic_qty' => $p['qty'],
                    'qty_sub_pack' => $p['qty_sub_pack'],
                    'berat_packaging_gram' => $p['berat_packaging_gram'],
                    'berat_per_pcs_gram' => $p['berat_per_pcs_gram'],
                    'inspector_name' => $p['inspector_name'],
                    'tgl_produksi' => $p['tgl_produksi'],
                    'tgl_expired' => $p['tgl_expired'],
                    'code' => $p['code'],
                    'posisi_rak' => $p['posisi_rak'],
                    'tingkat' => $p['tingkat'],
                    'ukuran_material' => $p['ukuran_material'],
                    'jenis_bahan' => $p['jenis_bahan'],
                    'quantity_material' => $p['quantity_material'],
                    'no_surat_jalan_material' => $p['no_surat_jalan_material'],
                    'tanggal_terima_material' => $p['tanggal_terima_material'],
                ]);

                $receiving = ItemReceiving::create([
                    'item_id' => $item->id,
                    'transfer_slip_no' => $p['transfer_slip_no'],
                    'tanggal_terima_fg' => $p['tanggal_terima_fg'],
                    'jumlah_box' => $p['jumlah_box'],
                ]);

                ItemBarcode::create([
                    'item_id' => $item->id,
                    'item_receiving_id' => $receiving->id,
                    'barcode_id' => 'IB-'.$item->id.'-'.$receiving->id,
                ]);
            }
        });

        $n = count($payloads);

        return [
            'errors' => [],
            'imported' => $n,
            'message' => "{$n} barcode barang berhasil diimpor dari Excel.",
        ];
    }

    /**
     * @param  list<list<mixed>>  $matrix
     * @return array{errors: list<string>, imported: int, message?: string}
     */
    public static function importCompanyFromMatrix(array $matrix): array
    {
        if (count($matrix) < 2) {
            return ['errors' => ['Berkas kosong atau hanya berisi header.'], 'imported' => 0];
        }

        $dataRows = array_slice($matrix, 1);
        $companies = [];
        $errors = [];

        foreach ($dataRows as $i => $row) {
            $lineNum = $i + 2;
            $row = self::padRow($row, self::COMPANY_COLS);
            $companyName = self::str($row[0] ?? null);

            if ($companyName === '') {
                continue;
            }

            if (in_array($companyName, $companies, true)) {
                $errors[] = "Baris {$lineNum}: nama_perusahaan \"{$companyName}\" duplikat dalam berkas.";

                continue;
            }

            $companies[] = $companyName;
        }

        if ($companies === []) {
            if (count($errors) > 0) {
                return ['errors' => $errors, 'imported' => 0];
            }

            return ['errors' => ['Tidak ada baris data yang diisi.'], 'imported' => 0];
        }

        if (count($errors) > 0) {
            return ['errors' => $errors, 'imported' => 0];
        }

        $companyCount = 0;

        DB::transaction(function () use ($dataRows, &$companyCount) {
            foreach ($dataRows as $i => $row) {
                $lineNum = $i + 2;
                $row = self::padRow($row, self::COMPANY_COLS);
                $companyName = self::str($row[0] ?? null);
                if ($companyName === '') continue;

                $company = Company::firstOrCreate(['name' => $companyName]);

                $partName = self::str($row[1] ?? 'Barang Default');
                $code = self::str($row[2] ?? 'CB-'.$company->id.'-'.uniqid());
                $qty = self::toInt($row[3] ?? 0);
                $posisiRak = self::str($row[4] ?? '-');
                $tingkat = self::str($row[5] ?? '-');
                $opMob = self::str($row[6] ?? '');
                $pengirim = self::str($row[7] ?? '');
                $opFork = self::str($row[8] ?? '');

                $item = Item::create([
                    'company_id' => $company->id,
                    'operator_mobil_id' => self::resolveEmployeeIdByName($opMob),
                    'pengirim_id' => self::resolveEmployeeIdByName($pengirim),
                    'operator_forklift_id' => self::resolveEmployeeIdByName($opFork),
                    'part_name' => $partName,
                    'qty' => $qty,
                    'static_qty' => $qty,
                    'dynamic_qty' => $qty,
                    'code' => $code,
                    'posisi_rak' => $posisiRak,
                    'tingkat' => $tingkat,
                ]);

                CompanyItem::create([
                    'company_id' => $company->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'posisi_rak' => $posisiRak,
                    'tingkat' => $tingkat,
                ]);

                CompanyBarcode::create([
                    'company_id' => $company->id,
                    'barcode_id' => 'CB-'.$company->id.'-'.uniqid(),
                ]);

                $companyCount++;
            }
        });

        return [
            'errors' => [],
            'imported' => $companyCount,
            'message' => "{$companyCount} perusahaan (beserta barcode & item default) berhasil diimpor dari Excel.",
        ];
    }

    private static function str(mixed $v): string
    {
        if ($v === null) {
            return '';
        }

        return trim((string) $v);
    }

    private static function nullableStr(mixed $v): ?string
    {
        $s = self::str($v);

        return $s === '' ? null : $s;
    }

    /**
     * @param  list<mixed>  $row
     * @return list<mixed>
     */
    private static function padRow(array $row, int $len): array
    {
        while (count($row) < $len) {
            $row[] = null;
        }

        return array_slice($row, 0, $len);
    }

    private static function toInt(mixed $v): int
    {
        if ($v === null || $v === '') {
            return 0;
        }
        if (is_numeric($v)) {
            return (int) round((float) $v);
        }

        return (int) $v;
    }

    private static function toNullableInt(mixed $v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }

        return self::toInt($v);
    }

    private static function toNullableFloat(mixed $v): ?float
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            return (float) $v;
        }
        $s = trim((string) $v);
        $s = str_replace(["\u{00A0}"], '', $s);
        if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d+)?$/', $s) === 1) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } elseif (str_contains($s, ',') && ! str_contains($s, '.')) {
            $s = str_replace(',', '.', $s);
        } else {
            $s = str_replace(' ', '', $s);
        }

        return is_numeric($s) ? (float) $s : null;
    }

    /** @return null|string */
    private static function normalizeJenisBahan(?string $v)
    {
        if ($v === null || $v === '') {
            return null;
        }
        $u = strtoupper(trim($v));
        if ($u === 'SPCC' || $u === 'SESE') {
            return $u;
        }

        return null;
    }

    /**
     * @param  list<string>  $rowErrors
     */
    private static function parseDateOptional(mixed $v, string $label, array &$rowErrors): ?string
    {
        if ($v === null || (is_string($v) && trim($v) === '')) {
            return null;
        }

        try {
            if ($v instanceof \DateTimeInterface) {
                return $v->format('Y-m-d');
            }

            if (is_numeric($v)) {
                $excelEpoch = Carbon::create(1899, 12, 30);
                return $excelEpoch->copy()->addDays((int) round((float) $v))->format('Y-m-d');
            }

            if (is_string($v)) {
                $v = trim($v);
                
                // Try specific formats
                $dateFormats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'Y/m/d'];
                foreach ($dateFormats as $format) {
                    try {
                        return Carbon::createFromFormat($format, $v)->format('Y-m-d');
                    } catch (Throwable) {
                        continue;
                    }
                }

                // Final attempt with general parse
                return Carbon::parse($v)->format('Y-m-d');
            }
        } catch (Throwable $e) {
            \Log::error("Date parsing failed for label: {$label}. Input value: " . var_export($v, true) . ". Error: " . $e->getMessage());
            $rowErrors[] = "{$label} tidak valid (input: '{$v}', error: {$e->getMessage()}).";
            return null;
        }

        \Log::error("Date parsing failed for label: {$label}. Input type not recognized. Input value: " . var_export($v, true));
        $rowErrors[] = "{$label} tidak valid (tipe data tidak dikenali).";
        return null;
    }

    private static function resolveEmployeeIdByName(string $name): ?int
    {
        if ($name === '') {
            return null;
        }

        $e = Employee::query()->where('name', $name)->first();
        if ($e !== null) {
            return $e->id;
        }

        $e = Employee::query()->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->first();

        return $e?->id;
    }
}
