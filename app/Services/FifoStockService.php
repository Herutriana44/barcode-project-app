<?php

namespace App\Services;

use App\Models\CompanyItem;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

final class FifoStockService
{
    /**
     * Kurangi stok pada `items.qty` (alur barcode barang) — batch paling lama dulu.
     */
    public static function deductFromItems(int $companyId, int $qtyToShip, ?string $partNumber = null, ?string $partName = null): void
    {
        if ($qtyToShip < 1) {
            return;
        }

        DB::transaction(function () use ($companyId, $qtyToShip, $partNumber, $partName) {
            $remaining = $qtyToShip;

            $query = Item::query()
                ->where('company_id', $companyId)
                ->where('qty', '>', 0)
                ->with(['itemReceivings' => fn ($q) => $q->orderBy('id')]);

            if ($partNumber !== null && $partNumber !== '') {
                $query->where('part_number', $partNumber);
            }
            if ($partName !== null && $partName !== '') {
                $query->where('part_name', $partName);
            }

            $items = $query->get()->sortBy(function (Item $item) {
                $r = $item->itemReceivings->first();
                if (! $r) {
                    return PHP_INT_MAX;
                }

                return $r->tanggal_terima_fg
                    ? $r->tanggal_terima_fg->timestamp
                    : $r->created_at->timestamp;
            })->values();

            foreach ($items as $item) {
                if ($remaining <= 0) {
                    break;
                }
                $take = min((int) $item->qty, $remaining);
                if ($take <= 0) {
                    continue;
                }
                $item->decrement('qty', $take);
                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new \InvalidArgumentException('Stok tidak mencukupi untuk pengeluaran FIFO (barang).');
            }
        });
    }

    /**
     * Kurangi stok pada `company_items.qty` — entri paling lama dulu.
     */
    public static function deductFromCompanyItems(int $companyId, int $qtyToShip, ?string $partName = null): void
    {
        if ($qtyToShip < 1) {
            return;
        }

        DB::transaction(function () use ($companyId, $qtyToShip, $partName) {
            $remaining = $qtyToShip;

            $query = CompanyItem::query()
                ->where('company_id', $companyId)
                ->where('qty', '>', 0)
                ->with('item');

            if ($partName !== null && $partName !== '') {
                $query->whereHas('item', fn ($q) => $q->where('part_name', $partName));
            }

            $rows = $query->orderBy('created_at')->orderBy('id')->get();

            foreach ($rows as $row) {
                if ($remaining <= 0) {
                    break;
                }
                $take = min((int) $row->qty, $remaining);
                if ($take <= 0) {
                    continue;
                }
                $row->decrement('qty', $take);
                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new \InvalidArgumentException('Stok tidak mencukupi untuk pengeluaran FIFO (perusahaan).');
            }
        });
    }
}
