<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyBarcode;
use App\Models\CompanyItem;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemReceiving;
use App\Support\Fqc038CleanListHeader;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use OpenSpout\Reader\XLSX\Reader;

final class Fqc038CleanListPartSeeder extends Seeder
{
    private const FILE_NAME = 'data.xlsx';

    private const SHEET_NAME = 'clean_list_part';

    /**
     * Fallback jika auto-detect header gagal.
     * Excel lama pernah menaruh header di A4/B4/... (1-indexed row).
     */
    private const FALLBACK_HEADER_ROW_INDEX = 4;

    private const WAREHOUSE_COMPANY_NAME = 'PT TEKUN ASAS SUMBER MAKMUR';

    public function run(): void
    {
        $path = base_path('database/seeders/data/'.self::FILE_NAME);
        if (! is_file($path)) {
            $this->command?->error("File Excel tidak ditemukan: {$path}");
            $this->command?->line('Taruh file Excel di folder tersebut, lalu jalankan seed lagi.');

            return;
        }

        // $warehouse = Company::query()->firstOrCreate(['name' => self::WAREHOUSE_COMPANY_NAME]);

        $reader = new Reader;
        $reader->open($path);

        foreach ($reader->getSheetIterator() as $sheet) {
            if (trim((string) $sheet->getName()) !== self::SHEET_NAME) {
                continue;
            }

            // $this->seedSheet($sheet->getRowIterator(), $warehouse);
            break;
        }

        $reader->close();
    }

    /**
     * @param  \Iterator<\OpenSpout\Common\Entity\Row>  $rows
     */
    private function seedSheet(\Iterator $rows, Company $warehouse): void
    {
        $header = null;
        $headerRowIndex = null;
        $rowIndex = 0;

        $imported = 0;
        $skipped = 0;

        /** @var array<int, array<string, mixed>> $parsedRows */
        $parsedRows = [];

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

            // Auto-detect header row (lebih robust dibanding hardcode baris 4).
            if (! is_array($header)) {
                $maybeHeader = array_map(fn ($h) => Fqc038CleanListHeader::canonicalHeader((string) $h), $cells);
                if (Fqc038CleanListHeader::looksLikeHeaderRow($maybeHeader)) {
                    $header = $maybeHeader;
                    $headerRowIndex = $rowIndex;
                    continue;
                }

                // Fallback: jika sampai baris fallback, pakai baris itu sebagai header.
                if ($rowIndex === self::FALLBACK_HEADER_ROW_INDEX) {
                    $header = $maybeHeader;
                    $headerRowIndex = $rowIndex;
                    continue;
                }

                // Belum ketemu header, lanjut.
                continue;
            }

            if (! is_array($header) || count($header) === 0) {
                continue;
            }

            $assoc = [];
            foreach ($header as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $cells[$i] ?? null;
            }

            $partCode = $this->asString($assoc['part_code'] ?? null);
            $partNo = $this->asString($assoc['part_no'] ?? null);
            $partDesc = $this->asString($assoc['part_description'] ?? null);
            $qtyPack = $this->asInt($assoc['qty_pack_pcs'] ?? null);
            $customerName = $this->asString($assoc['customer'] ?? null);
            $qtySubPack = $this->asInt($assoc['qty_sub_pack_pcs'] ?? null);
            $beratPackagingGram = $this->asInt($assoc['berat_packaging_gram'] ?? null);
            $beratPerPcsGram = $this->asInt($assoc['berat_per_pcs_gram'] ?? null);

            $hasAnyCellValue = collect($cells)->contains(fn ($v) => ! ($v === null || $v === ''));
            // Jika baris benar-benar kosong, skip (umumnya bawah sheet kosong semua).
            if (! $hasAnyCellValue) {
                continue;
            }

            // Minimal part_code atau part_no harus ada.
            if (($partCode === null || $partCode === '') && ($partNo === null || $partNo === '')) {
                $skipped++;
                continue;
            }

            $prodDate = $this->asDate($assoc['prod_date'] ?? null) ?? Carbon::today();
            $expDate = $this->asDate($assoc['exp_date'] ?? null) ?? (clone $prodDate)->addMonths(3);

            $model = $this->asString($assoc['model'] ?? null);
            if ($model === '-' || $model === '—') {
                $model = null;
            }

            $beratTotal = $this->asFloat($assoc['berat_total_kg'] ?? null);
            $rak = $this->asString($assoc['rak'] ?? null);

            $parsedRows[] = [
                'customer_name' => $customerName,
                'part_code' => $partCode,
                'part_no' => $partNo,
                'part_desc' => $partDesc,
                'qty_pack' => $qtyPack,
                'qty_sub_pack' => $qtySubPack,
                'berat_packaging_gram' => $beratPackagingGram,
                'berat_per_pcs_gram' => $beratPerPcsGram,
                'prod_date' => $prodDate,
                'exp_date' => $expDate,
                'model' => $model,
                'berat_total_kg' => $beratTotal,
                'rak' => $rak,
            ];
        }

        // 1) Buat barcode semua perusahaan terlebih dahulu.
        $companyNames = collect($parsedRows)
            ->pluck('customer_name')
            ->filter(fn ($n) => is_string($n) && trim($n) !== '')
            ->map(fn ($n) => trim($n))
            ->unique()
            ->values();

        // Sertakan warehouse agar ikut punya barcode perusahaan.
        $companyNames->prepend($warehouse->name);
        $companyNames = $companyNames->unique()->values();

        /** @var array<string, \App\Models\Company> $companiesByName */
        $companiesByName = [];

        DB::transaction(function () use ($companyNames, &$companiesByName) {
            foreach ($companyNames as $name) {
                $company = Company::query()->firstOrCreate(['name' => $name]);
                CompanyBarcode::query()->firstOrCreate(
                    ['company_id' => $company->id],
                    ['barcode_id' => 'CB-'.$company->id.'-'.uniqid()]
                );
                $companiesByName[$name] = $company;
            }
        });

        // 2) Baru buat barcode semua barang + relasi ke perusahaan (CompanyItem).
        DB::transaction(function () use ($parsedRows, $warehouse, $companiesByName, &$imported) {
            foreach ($parsedRows as $r) {
                $customerName = is_string($r['customer_name'] ?? null) ? trim((string) $r['customer_name']) : null;
                $company = ($customerName !== null && $customerName !== '' && isset($companiesByName[$customerName]))
                    ? $companiesByName[$customerName]
                    : $warehouse;

                $qtyPack = $r['qty_pack'] ?? null;
                $qtyForItem = is_int($qtyPack) ? $qtyPack : 0;

                $prodDate = $r['prod_date'] instanceof Carbon ? $r['prod_date'] : Carbon::today();
                $expDate = $r['exp_date'] instanceof Carbon ? $r['exp_date'] : (clone $prodDate)->addMonths(3);

                // Konsisten dengan modul barcode perusahaan:
                // - item.company_id = perusahaan (customer)
                // - qty disimpan di company_items
                // - static_qty / dynamic_qty disamakan dengan qty pack agar label & FIFO konsisten
                $item = Item::query()->create([
                    'company_id' => $company->id,
                    'customer' => ($customerName !== null && $customerName !== '') ? $customerName : null,
                    'part_name' => ($r['part_desc'] ?? null) ?: null,
                    'part_number' => ($r['part_no'] ?? null) ?: null,
                    'model' => $r['model'] ?? null,
                    'berat' => $r['berat_total_kg'] ?? null,
                    'qty' => $qtyForItem,
                    'static_qty' => $qtyForItem,
                    'dynamic_qty' => $qtyForItem,
                    'qty_sub_pack' => ($r['qty_sub_pack'] ?? null) ?: null,
                    'berat_packaging_gram' => ($r['berat_packaging_gram'] ?? null) ?: null,
                    'berat_per_pcs_gram' => ($r['berat_per_pcs_gram'] ?? null) ?: null,
                    'tgl_produksi' => $prodDate->format('Y-m-d'),
                    'tgl_expired' => $expDate->format('Y-m-d'),
                    'code' => ($r['part_code'] ?? null) ?: null,
                    'posisi_rak' => ($r['rak'] ?? null) ?: null,
                ]);

                CompanyItem::query()->create([
                    'company_id' => $company->id,
                    'item_id' => $item->id,
                    'qty' => is_int($qtyPack) ? $qtyPack : 0,
                    'posisi_rak' => ($r['rak'] ?? null) ?: null,
                    'tingkat' => null,
                ]);

                $receiving = ItemReceiving::query()->create([
                    'item_id' => $item->id,
                    'transfer_slip_no' => null,
                    'tanggal_terima_fg' => $prodDate->format('Y-m-d'),
                    'jumlah_box' => 0,
                ]);

                ItemBarcode::query()->create([
                    'item_id' => $item->id,
                    'item_receiving_id' => $receiving->id,
                    'barcode_id' => 'IB-'.$item->id.'-'.$receiving->id,
                ]);

                $imported++;
            }
        });

        if ($headerRowIndex === null) {
            $this->command?->warn('Header tidak terdeteksi. Pastikan sheet memiliki kolom part code/part no/qty pack/customer.');
        }
        $this->command?->info("FQC-038 import selesai. Imported: {$imported}, skipped: {$skipped}");
    }

    private function asString(mixed $v): ?string
    {
        if ($v === null) {
            return null;
        }
        if (is_string($v)) {
            $s = trim($v);

            return $s === '' ? null : $s;
        }
        if (is_int($v) || is_float($v)) {
            return (string) $v;
        }

        return null;
    }

    private function asInt(mixed $v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_int($v)) {
            return $v;
        }
        if (is_float($v)) {
            return (int) round($v);
        }
        if (is_string($v)) {
            $s = trim($v);
            if ($s === '' || $s === '-' || $s === '—') {
                return null;
            }
            $s = str_replace(["\u{00A0}"], '', $s);
            // 1.234,5 → 1234.5
            if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d+)?$/', $s) === 1) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } elseif (str_contains($s, ',') && ! str_contains($s, '.')) {
                $s = str_replace(',', '.', $s);
            }
            if (is_numeric($s)) {
                return (int) round((float) $s);
            }
            $digits = preg_replace('/[^\d\-]/', '', $s) ?? '';
            if ($digits === '' || $digits === '-') {
                return null;
            }

            return (int) $digits;
        }

        return null;
    }

    private function asFloat(mixed $v): ?float
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_float($v) || is_int($v)) {
            return (float) $v;
        }
        if (is_string($v)) {
            $s = trim($v);
            if ($s === '' || $s === '-' || $s === '—') {
                return null;
            }
            $s = str_replace(["\u{00A0}"], '', $s);
            if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d+)?$/', $s) === 1) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } elseif (str_contains($s, ',') && ! str_contains($s, '.')) {
                $s = str_replace(',', '.', $s);
            } else {
                $s = str_replace(' ', '', $s);
            }
            if ($s === '' || ! is_numeric($s)) {
                return null;
            }

            return (float) $s;
        }

        return null;
    }

    private function asDate(mixed $v): ?Carbon
    {
        if ($v === null || $v === '') {
            return null;
        }
        if ($v instanceof \DateTimeInterface) {
            return Carbon::instance(\DateTime::createFromInterface($v))->startOfDay();
        }
        if (is_string($v)) {
            $s = trim($v);
            if ($s === '' || $s === '-' || $s === '—') {
                return null;
            }
            try {
                return Carbon::parse($s)->startOfDay();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }
}

