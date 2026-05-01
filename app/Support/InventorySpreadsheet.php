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
    public const FG_COLS = 24;

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
            'inspector_name',
            'tgl_produksi',
            'tgl_expired',
            'posisi_rak',
            'tingkat',
            'ukuran_material',
            'jenis_bahan',
            'quantity_material',
            'no_surat_jalan_material',
            'tanggal_terima_material',
            'transfer_slip_no',
            'tanggal_terima_fg',
            'jumlah_box',
            'operator_mobil_nama',
            'pengirim_nama',
            'operator_forklift_nama',
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
            'Inspector',
            '2026-01-15',
            '2026-12-31',
            'Rak-A1',
            '1',
            '',
            'SPCC',
            '',
            '',
            '',
            'TS-001',
            '2026-02-01',
            1,
            '',
            '',
            '',
        ];
    }

    /** @return list<string> */
    public static function companyHeaderRow(): array
    {
        return [
            'nama_perusahaan',
            'part_name',
            'code',
            'qty',
            'posisi_rak',
            'tingkat',
            'operator_mobil_nama',
            'pengirim_nama',
            'operator_forklift_nama',
        ];
    }

    /** @return list<string|int|float|null> */
    public static function companyExampleRow(): array
    {
        return [
            'PT Contoh Import',
            'Barang contoh',
            '',
            50,
            'R1',
            '2',
            '',
            '',
            '',
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

        $dataRows = array_slice($matrix, 1);
        $errors = [];
        $payloads = [];

        foreach ($dataRows as $i => $row) {
            $lineNum = $i + 2;
            $row = self::padRow($row, self::FG_COLS);
            $rowErrors = [];

            $companyName = self::str($row[0] ?? null);
            $code = self::str($row[1] ?? null);

            if ($companyName === '' && $code === '') {
                continue;
            }

            if ($companyName === '') {
                $errors[] = "Baris {$lineNum}: nama_perusahaan wajib diisi.";

                continue;
            }

            if ($code === '') {
                $errors[] = "Baris {$lineNum}: code wajib diisi.";

                continue;
            }

            $company = Company::query()
                ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($companyName)])
                ->first();

            if ($company === null) {
                $errors[] = "Baris {$lineNum}: perusahaan \"{$companyName}\" tidak ditemukan. Buat dulu atau samakan ejaan dengan data master.";

                continue;
            }

            $opMobName = self::str($row[21] ?? null);
            $opPengName = self::str($row[22] ?? null);
            $opForkName = self::str($row[23] ?? null);

            foreach (['operator_mobil' => $opMobName, 'pengirim' => $opPengName, 'operator_forklift' => $opForkName] as $label => $ename) {
                if ($ename !== '' && self::resolveEmployeeIdByName($ename) === null) {
                    $rowErrors[] = "Baris {$lineNum}: karyawan ({$label}) \"{$ename}\" tidak ditemukan.";
                }
            }

            $qty = self::toInt($row[7] ?? 0);
            if ($qty < 0) {
                $rowErrors[] = "Baris {$lineNum}: qty tidak valid.";
            }

            $jb = self::normalizeJenisBahan(self::str($row[14] ?? null));
            if ($jb === false) {
                $rowErrors[] = "Baris {$lineNum}: jenis_bahan harus kosong, SPCC, atau SESE.";
            }

            $dProd = self::parseDateOptional($row[9] ?? null, "Baris {$lineNum}: tgl_produksi", $rowErrors);
            $dExp = self::parseDateOptional($row[10] ?? null, "Baris {$lineNum}: tgl_expired", $rowErrors);
            $dMat = self::parseDateOptional($row[17] ?? null, "Baris {$lineNum}: tanggal_terima_material", $rowErrors);
            $dFg = self::parseDateOptional($row[19] ?? null, "Baris {$lineNum}: tanggal_terima_fg", $rowErrors);

            if (count($rowErrors) > 0) {
                array_push($errors, ...$rowErrors);

                continue;
            }

            $payloads[] = [
                'line' => $lineNum,
                'company_id' => $company->id,
                'customer' => self::nullableStr($row[2] ?? null),
                'part_name' => self::nullableStr($row[3] ?? null),
                'part_number' => self::nullableStr($row[4] ?? null),
                'model' => self::nullableStr($row[5] ?? null),
                'berat' => self::toNullableFloat($row[6] ?? null),
                'qty' => $qty,
                'inspector_name' => self::nullableStr($row[8] ?? null),
                'tgl_produksi' => $dProd,
                'tgl_expired' => $dExp,
                'code' => $code,
                'posisi_rak' => self::nullableStr($row[11] ?? null),
                'tingkat' => self::nullableStr($row[12] ?? null),
                'ukuran_material' => self::nullableStr($row[13] ?? null),
                'jenis_bahan' => $jb,
                'quantity_material' => self::toNullableInt($row[15] ?? null),
                'no_surat_jalan_material' => self::nullableStr($row[16] ?? null),
                'tanggal_terima_material' => $dMat,
                'transfer_slip_no' => self::nullableStr($row[18] ?? null),
                'tanggal_terima_fg' => $dFg,
                'jumlah_box' => self::toInt($row[20] ?? 0),
                'operator_mobil_id' => self::resolveEmployeeIdByName($opMobName),
                'pengirim_id' => self::resolveEmployeeIdByName($opPengName),
                'operator_forklift_id' => self::resolveEmployeeIdByName($opForkName),
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
        $groups = [];
        $order = [];
        $errors = [];

        foreach ($dataRows as $i => $row) {
            $lineNum = $i + 2;
            $row = self::padRow($row, self::COMPANY_COLS);
            $companyName = self::str($row[0] ?? null);
            $partName = self::str($row[1] ?? null);
            $code = self::str($row[2] ?? null);
            $qty = self::toInt($row[3] ?? 0);

            if ($companyName === '' && $partName === '' && $code === '' && $qty === 0) {
                continue;
            }

            if ($companyName === '') {
                $errors[] = "Baris {$lineNum}: nama_perusahaan wajib diisi.";

                continue;
            }

            $key = mb_strtolower($companyName);
            if (! isset($groups[$key])) {
                $groups[$key] = ['display_name' => $companyName, 'rows' => []];
                $order[] = $key;
            }

            $groups[$key]['rows'][] = [
                'line' => $lineNum,
                'part_name' => self::nullableStr($row[1] ?? null),
                'code' => self::nullableStr($row[2] ?? null),
                'qty' => $qty,
                'posisi_rak' => self::nullableStr($row[4] ?? null),
                'tingkat' => self::nullableStr($row[5] ?? null),
                'operator_mobil_nama' => self::str($row[6] ?? null),
                'pengirim_nama' => self::str($row[7] ?? null),
                'operator_forklift_nama' => self::str($row[8] ?? null),
            ];
        }

        if ($order === []) {
            if (count($errors) > 0) {
                return ['errors' => $errors, 'imported' => 0];
            }

            return ['errors' => ['Tidak ada baris data yang diisi.'], 'imported' => 0];
        }

        if (count($errors) > 0) {
            return ['errors' => $errors, 'imported' => 0];
        }

        $companyCount = 0;

        foreach ($order as $key) {
            $bundle = $groups[$key];
            $validRows = array_values(array_filter(
                $bundle['rows'],
                fn (array $r) => $r['qty'] > 0
            ));

            if ($validRows === []) {
                $errors[] = "Perusahaan \"{$bundle['display_name']}\": minimal satu baris dengan qty lebih dari 0.";

                continue;
            }

            foreach ($bundle['rows'] as $r) {
                foreach (['operator_mobil_nama' => $r['operator_mobil_nama'], 'pengirim_nama' => $r['pengirim_nama'], 'operator_forklift_nama' => $r['operator_forklift_nama']] as $label => $ename) {
                    if ($ename !== '' && self::resolveEmployeeIdByName($ename) === null) {
                        $errors[] = "Baris {$r['line']}: karyawan \"{$ename}\" tidak ditemukan ({$label}).";
                    }
                }
            }
        }

        if (count($errors) > 0) {
            return ['errors' => $errors, 'imported' => 0];
        }

        DB::transaction(function () use ($order, $groups, &$companyCount) {
            foreach ($order as $key) {
                $bundle = $groups[$key];
                $validRows = array_values(array_filter(
                    $bundle['rows'],
                    fn (array $r) => $r['qty'] > 0
                ));

                if ($validRows === []) {
                    continue;
                }

                $company = Company::create(['name' => $bundle['display_name']]);

                foreach ($validRows as $row) {
                    $itemCode = $row['code'];
                    if ($itemCode === null || $itemCode === '') {
                        $itemCode = 'CB-'.$company->id.'-'.uniqid();
                    }

                    $item = Item::create([
                        'company_id' => $company->id,
                        'operator_mobil_id' => self::resolveEmployeeIdByName($row['operator_mobil_nama']),
                        'pengirim_id' => self::resolveEmployeeIdByName($row['pengirim_nama']),
                        'operator_forklift_id' => self::resolveEmployeeIdByName($row['operator_forklift_nama']),
                        'part_name' => $row['part_name'],
                        'code' => $itemCode,
                        'qty' => 0,
                    ]);

                    CompanyItem::create([
                        'company_id' => $company->id,
                        'item_id' => $item->id,
                        'qty' => $row['qty'],
                        'posisi_rak' => $row['posisi_rak'],
                        'tingkat' => $row['tingkat'],
                    ]);
                }

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
            'message' => "{$companyCount} perusahaan (beserta barcode & stok baris) berhasil diimpor dari Excel.",
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

    /** @return null|string|false */
    private static function normalizeJenisBahan(string $v)
    {
        if ($v === '') {
            return null;
        }
        $u = strtoupper($v);
        if ($u === 'SPCC' || $u === 'SESE') {
            return $u;
        }

        return false;
    }

    /**
     * @param  list<string>  $rowErrors
     */
    private static function parseDateOptional(mixed $v, string $label, array &$rowErrors): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        try {
            if ($v instanceof \DateTimeInterface) {
                return $v->format('Y-m-d');
            }
            if (is_numeric($v)) {
                $excelEpoch = Carbon::create(1899, 12, 30);
                $days = (float) $v;

                return $excelEpoch->copy()->addDays((int) round($days))->format('Y-m-d');
            }

            return Carbon::parse((string) $v)->format('Y-m-d');
        } catch (Throwable) {
            $rowErrors[] = "{$label} tidak valid.";

            return null;
        }
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
