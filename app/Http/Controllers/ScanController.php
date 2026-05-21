<?php

namespace App\Http\Controllers;

use App\Models\CompanyBarcode;
use App\Models\ItemBarcode;
use App\Models\Employee;
use App\Models\EmployeeScanSession;
use App\Services\ActivityLogger;
use App\Services\FifoStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan.index');
    }

    private function getExpiringItemsList()
    {
        $threshold = Carbon::now()->addDays(30);
        $now = Carbon::now();

        // Mengambil semua item yang mendekati expired
        $expiringItems = \App\Models\Item::whereNotNull('tgl_expired')
            ->where('tgl_expired', '>=', $now->toDateString())
            ->where('tgl_expired', '<=', $threshold->toDateString())
            ->get();

        // Mengambil semua unique item yang mendekati expired
        $expiringUniqueItems = \App\Models\UniqueItem::whereNotNull('expired_date')
            ->where('expired_date', '>=', $now->toDateString())
            ->where('expired_date', '<=', $threshold->toDateString())
            ->where('status_keluar', false)
            ->get();

        $totalCount = $expiringItems->count() + $expiringUniqueItems->count();
        $earliestDate = null;
        
        if ($totalCount > 0) {
            $allDates = collect();
            foreach ($expiringItems as $item) {
                $allDates->push($item->tgl_expired);
            }
            foreach ($expiringUniqueItems as $unique) {
                $allDates->push($unique->expired_date);
            }
            $earliestDate = $allDates->min();
        }

        // Menggabungkan keduanya agar view bisa menampilkan semua peringatan
        return [
            'items' => $expiringItems, 
            'uniqueItems' => $expiringUniqueItems,
            'totalCount' => $totalCount,
            'earliestDate' => $earliestDate
        ];
    }

    public function show(Request $request, string $barcodeId)
    {
        $expiringList = $this->getExpiringItemsList();

        if (str_starts_with($barcodeId, 'IB-')) {
            $parts = explode('-', $barcodeId);
            
            // Logika Scan Unique Item
            if (count($parts) === 4) {
                $uniqueItemId = $parts[3];
                $uniqueItem = \App\Models\UniqueItem::with('item')->find($uniqueItemId);
                if (!$uniqueItem) {
                    return redirect()->route('scan.index')->with('error', 'Unique Item tidak ditemukan.');
                }
                
                $expiredWarning = $uniqueItem->expired_date?->isPast() ?? false;
                $approachingExpiry = $uniqueItem->expired_date && $uniqueItem->expired_date->isBetween(Carbon::now(), Carbon::now()->addDays(30));

                return view('scan.result-unique', compact('uniqueItem', 'expiredWarning', 'approachingExpiry', 'expiringList', 'barcodeId'));
            }

            // Logika Scan Item Umum
            $itemBarcode = ItemBarcode::with([
                'item.company',
                'itemReceiving',
            ])
                ->where('barcode_id', $barcodeId)
                ->first();

            if (! $itemBarcode) {
                return redirect()->route('scan.index')->with('error', 'Barcode barang tidak ditemukan.');
            }
            
            $fifoOlderStockWarning = FifoStockService::hasOlderBatchWithStock($itemBarcode);
            $expiredWarning = $itemBarcode->item->tgl_expired?->isPast() ?? false;
            $approachingExpiry = $itemBarcode->item->tgl_expired && $itemBarcode->item->tgl_expired->isBetween(Carbon::now(), Carbon::now()->addDays(30));

            return view('scan.result-item', compact('itemBarcode', 'fifoOlderStockWarning', 'expiredWarning', 'approachingExpiry', 'expiringList'));
        }

        if (str_starts_with($barcodeId, 'CB-')) {
            $companyBarcode = CompanyBarcode::with([ 
                'company.items.itemBarcodes',
            ])
                ->where('barcode_id', $barcodeId)
                ->first();

            if (! $companyBarcode) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Barcode tidak ditemukan'], 404);
                }

                return redirect()->route('scan.index')->with('error', 'Barcode perusahaan tidak ditemukan.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'type' => 'company',
                    'data' => [
                        'company' => $companyBarcode->company,
                        'items' => $companyBarcode->company->items,
                    ],
                ]);
            }

            return view('scan.result-company', compact('companyBarcode', 'expiringList'));
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Format barcode tidak valid'], 400);
        }

        return redirect()->route('scan.index')->with('error', 'Format barcode tidak valid.');
    }

    /**
     * Barang masuk / keluar (FIFO) dari halaman hasil scan barcode barang (IB-…).
     */
    public function storeMovement(Request $request, string $barcodeId)
    {
        if (! str_starts_with($barcodeId, 'IB-')) {
            return redirect()->route('scan.index')
                ->with('error', 'Mutasi stok dari scan hanya untuk barcode barang (IB-…).');
        }

        // Cek apakah barcodeId adalah UniqueItem
        $parts = explode('-', $barcodeId);
        $uniqueItem = null;
        if (count($parts) === 4) {
            $uniqueItem = \App\Models\UniqueItem::find($parts[3]);
        }

        // Jika ini adalah UniqueItem, proses mutasi khusus
        if ($uniqueItem) {
            $validated = $request->validate([
                'direction' => 'required|in:in,out',
                'qty' => 'nullable|integer|min:1',
            ]);
            
            $qty = $validated['qty'] ?? 1;
            
            // Cek expiry date dari unique item, jika kosong cek dari parent item
            $expiryDate = $uniqueItem->expired_date;
            if (!$expiryDate && $uniqueItem->item) {
                $expiryDate = $uniqueItem->expired_date_from_item; // Assuming relation or property exists
            }
            
            $now = \Carbon\Carbon::now();
            $fiveDaysAhead = $now->copy()->addDays(5);
            
            // Cek jika barang sudah expired
            if ($expiryDate && $expiryDate->isPast()) {
                return redirect()->route('scan.show', ['barcode_id' => $barcodeId])
                    ->with('error', 'Barang sudah expired! Tidak dapat melakukan mutasi.');
            }
            
            // Cek jika barang expired dalam 5 hari kedepan
            if ($validated['direction'] === 'out' && $expiryDate && $expiryDate <= $fiveDaysAhead && $expiryDate >= $now) {
                return redirect()->route('scan.show', ['barcode_id' => $barcodeId])
                    ->with('error', 'Barang mendekati expired (dalam 5 hari)! Tidak dapat dikeluarkan.');
            }
            
            $approachingExpiry = $expiryDate && $expiryDate->isBetween($now, $now->copy()->addDays(5));
            $warningMessage = $approachingExpiry ? 'Barang mendekati expired, harap segera diproses. ' : '';
            
            if ($validated['direction'] === 'out') {
                $uniqueItem->update(['status_keluar' => true]);
                
                ActivityLogger::log('Stok', 'Keluar', 'Box Pecahan Keluar: ' . ($uniqueItem->item->part_name ?? 'Unknown') . ' (Qty: ' . ($uniqueItem->qty ?? 0) . ')');

                return redirect()->route('scan.index')->with('success', $warningMessage . 'Barang berhasil keluar.');
            } else {
                // Duplikasi
                $newUniqueItem = $uniqueItem->replicate(['status_keluar']);
                $newUniqueItem->status_keluar = false;
                $newUniqueItem->save();

                ActivityLogger::log('Stok', 'Masuk', 'Box Pecahan Masuk (Replikasi): ' . ($uniqueItem->item->part_name ?? 'Unknown') . ' (Qty: ' . ($newUniqueItem->qty ?? 0) . ')');

                return redirect()->route('scan.index')->with('success', $warningMessage . 'Barang masuk, unique item baru dibuat.');
            }
        }

        $validated = $request->validate([
            'direction' => 'required|in:in,out',
            'qty' => 'required|integer|min:1',
        ]);

        $itemBarcode = ItemBarcode::with(['item.company', 'itemReceiving'])
            ->where('barcode_id', $barcodeId)
            ->first();

        if (! $itemBarcode) {
            return redirect()->route('scan.index')
                ->with('error', 'Barcode barang tidak ditemukan.');
        }
        
        // Cek expiry item umum
        $expiryDate = $itemBarcode->item->tgl_expired;
        $now = \Carbon\Carbon::now();
        $fiveDaysAhead = $now->copy()->addDays(5);

        // Cek jika barang sudah expired
        if ($expiryDate && $expiryDate->isPast()) {
            return redirect()->route('scan.show', ['barcode_id' => $barcodeId])
                ->with('error', 'Barang sudah expired! Tidak dapat melakukan mutasi.');
        }

        if ($validated['direction'] === 'out' && $expiryDate && $expiryDate <= $fiveDaysAhead && $expiryDate >= $now) {
            return redirect()->route('scan.show', ['barcode_id' => $barcodeId])
                ->with('error', 'Barang mendekati expired (dalam 5 hari)! Tidak dapat dikeluarkan.');
        }

        $item = $itemBarcode->item;
        $qtyToDeduct = $validated['qty'] * ($item->qty_sub_pack ?? 1);

        try {
            DB::transaction(function () use ($itemBarcode, $validated, $qtyToDeduct) {
                // Ensure the receiving model is fresh
                $receiving = $itemBarcode->itemReceiving()->lockForUpdate()->first();
                
                if ($validated['direction'] === 'in') {
                    FifoStockService::incrementItemQty((int) $itemBarcode->item_id, (int) $qtyToDeduct);
                    
                    $currentBox = (int) ($receiving->jumlah_box ?? 0);
                    $receiving->jumlah_box = $currentBox + (int) $validated['qty'];
                    $receiving->save();
                } else {
                    FifoStockService::deductFromItems(
                        (int) $itemBarcode->item->company_id,
                        (int) $qtyToDeduct,
                        $itemBarcode->item->part_number,
                        $itemBarcode->item->part_name
                    );
                    
                    $currentBox = (int) ($receiving->jumlah_box ?? 0);
                    $receiving->jumlah_box = max(0, $currentBox - (int) $validated['qty']);
                    $receiving->save();
                }
            });
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('scan.show', ['barcode_id' => $barcodeId])
                ->withInput()
                ->withErrors(['qty' => $e->getMessage()]);
        }

        $msg = $validated['direction'] === 'in'
            ? 'Barang masuk: stok bertambah '.$validated['qty'].' box.'
            : 'Barang keluar: pengurangan FIFO '.$validated['qty'].' box.';

        return redirect()
            ->route('scan.show', ['barcode_id' => $barcodeId])
            ->with('success', $msg);
    }
}
