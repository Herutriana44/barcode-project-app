<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemReceiving;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use OpenSpout\Reader\XLSX\Reader;

final class Fqc038CleanListPartSeeder extends Seeder
{
    private const FILE_NAME = '038. F-QC-038. Tag delivery_REV_.LABEL.xlsx';

    private const SHEET_NAME = 'clean_list_part';

    /** Header ada di A4/B4/... (1-indexed row). */
    private const HEADER_ROW_INDEX = 4;

    private const WAREHOUSE_COMPANY_NAME = 'PT TEKUN ASAS SUMBER MAKMUR';

    public function run(): void
    {
        $path = base_path('database/seeders/data/'.self::FILE_NAME);
        if (! is_file($path)) {
            $this->command?->error("File Excel tidak ditemukan: {$path}");
            $this->command?->line('Taruh file Excel di folder tersebut, lalu jalankan seed lagi.');

            return;
        }

        $warehouse = Company::query()->firstOrCreate(['name' => self::WAREHOUSE_COMPANY_NAME]);

        $reader = new Reader;
        $reader->open($path);

        foreach ($reader->getSheetIterator() as $sheet) {
            if (trim((string) $sheet->getName()) !== self::SHEET_NAME) {
                continue;
            }

            $this->seedSheet($sheet->getRowIterator(), $warehouse);
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
        $rowIndex = 0;

        $imported = 0;
        $skipped = 0;

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

            if ($rowIndex < self::HEADER_ROW_INDEX) {
                continue;
            }

            // Header row
            if ($rowIndex === self::HEADER_ROW_INDEX) {
                $header = array_map(fn ($h) => $this->normalizeHeader((string) $h), $cells);
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

            $partCode = $this->asString($assoc['part code'] ?? null);
            $partNo = $this->asString($assoc['current part no'] ?? null);
            $partDesc = $this->asString($assoc['part description'] ?? null);
            $qtyPack = $this->asInt($assoc['qty/pack(pcs)'] ?? null);
            $customerName = $this->asString($assoc['customer'] ?? null);
            $qtySubPack = $this->asInt($assoc['qty sub pack'] ?? ($assoc['qty sub pack(pcs)'] ?? null));
            $beratPackagingGram = $this->asInt($assoc['berat packaging(gram)'] ?? ($assoc['berat packaging (gram)'] ?? null));
            $beratPerPcsGram = $this->asInt($assoc['berat per pcs(gram)'] ?? ($assoc['berat per pcs (gram)'] ?? null));

            // Jika baris benar-benar kosong, stop iterasi (umumnya bawah sheet kosong semua).
            if ($partCode === null && $partNo === null && $partDesc === null && $qtyPack === null && $customerName === null) {
                continue;
            }

            // Minimal part_code atau part_no harus ada.
            if (($partCode === null || $partCode === '') && ($partNo === null || $partNo === '')) {
                $skipped++;
                continue;
            }

            $prodDate = $this->asDate($assoc['prod. date'] ?? null) ?? Carbon::today();
            $expDate = $this->asDate($assoc['exp. date'] ?? null) ?? (clone $prodDate)->addMonthsNoOverflow(3);

            $model = $this->asString($assoc['model'] ?? null);
            if ($model === '-' || $model === '—') {
                $model = null;
            }

            $beratTotal = $this->asFloat($assoc['berat total(kg)'] ?? null);

            DB::transaction(function () use (
                $warehouse,
                $customerName,
                $partCode,
                $partNo,
                $partDesc,
                $qtyPack,
                $qtySubPack,
                $beratPackagingGram,
                $beratPerPcsGram,
                $prodDate,
                $expDate,
                $model,
                $beratTotal,
                &$imported
            ) {
                if ($customerName !== null && $customerName !== '') {
                    Company::query()->firstOrCreate(['name' => $customerName]);
                }

                $item = Item::query()->create([
                    'company_id' => $warehouse->id,
                    'customer' => ($customerName !== null && $customerName !== '') ? $customerName : null,
                    'part_name' => ($partDesc !== null && $partDesc !== '') ? $partDesc : null,
                    'part_number' => ($partNo !== null && $partNo !== '') ? $partNo : null,
                    'model' => $model,
                    'berat' => $beratTotal,
                    'qty' => $qtyPack ?? 0,
                    'static_qty' => $qtyPack ?? 0,
                    'dynamic_qty' => $qtyPack ?? 0,
                    'qty_sub_pack' => ($qtySubPack !== null && $qtySubPack > 0) ? $qtySubPack : null,
                    'berat_packaging_gram' => ($beratPackagingGram !== null && $beratPackagingGram > 0) ? $beratPackagingGram : null,
                    'berat_per_pcs_gram' => ($beratPerPcsGram !== null && $beratPerPcsGram > 0) ? $beratPerPcsGram : null,
                    'tgl_produksi' => $prodDate->format('Y-m-d'),
                    'tgl_expired' => $expDate->format('Y-m-d'),
                    'code' => ($partCode !== null && $partCode !== '') ? $partCode : null,
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
            });
        }

        $this->command?->info("FQC-038 import selesai. Imported: {$imported}, skipped: {$skipped}");
    }

    private function normalizeHeader(string $h): string
    {
        $h = str_replace(["\r", "\n", "\t"], ' ', $h);
        $h = preg_replace('/\s+/', ' ', $h) ?? $h;

        return mb_strtolower(trim($h));
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
            $s = preg_replace('/[^\d\-]/', '', $v) ?? '';
            if ($s === '' || $s === '-') {
                return null;
            }

            return (int) $s;
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
            $s = str_replace([' ', ','], ['', '.'], trim($v));
            if ($s === '' || $s === '-' || $s === '—') {
                return null;
            }

            return is_numeric($s) ? (float) $s : null;
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

