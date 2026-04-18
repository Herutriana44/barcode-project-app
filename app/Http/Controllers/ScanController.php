<?php

namespace App\Http\Controllers;

use App\Models\CompanyBarcode;
use App\Models\ItemBarcode;
use App\Services\FifoStockService;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan.index');
    }

    public function show(Request $request, string $barcodeId)
    {
        if (str_starts_with($barcodeId, 'IB-')) {
            $itemBarcode = ItemBarcode::with([
                'item.company',
                'item.operatorMobil',
                'item.pengirim',
                'item.operatorForklift',
                'itemReceiving',
            ])
                ->where('barcode_id', $barcodeId)
                ->first();

            if (! $itemBarcode) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Barcode tidak ditemukan'], 404);
                }

                return redirect()->route('scan.index')->with('error', 'Barcode barang tidak ditemukan.');
            }

            $fifoOlderStockWarning = FifoStockService::hasOlderBatchWithStock($itemBarcode);

            if ($request->expectsJson()) {
                return response()->json([
                    'type' => 'item',
                    'data' => [
                        'item' => $itemBarcode->item,
                        'receiving' => $itemBarcode->itemReceiving,
                        'company' => $itemBarcode->item->company,
                        'fifo_older_stock_warning' => $fifoOlderStockWarning,
                    ],
                ]);
            }

            return view('scan.result-item', compact('itemBarcode', 'fifoOlderStockWarning'));
        }

        if (str_starts_with($barcodeId, 'CB-')) {
            $companyBarcode = CompanyBarcode::with([
                'company.companyItems.item.operatorMobil',
                'company.companyItems.item.pengirim',
                'company.companyItems.item.operatorForklift',
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
                        'company_items' => $companyBarcode->company->companyItems,
                    ],
                ]);
            }

            return view('scan.result-company', compact('companyBarcode'));
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

        $item = $itemBarcode->item;

        try {
            if ($validated['direction'] === 'in') {
                FifoStockService::incrementItemQty((int) $item->id, (int) $validated['qty']);
            } else {
                FifoStockService::deductFromItems(
                    (int) $item->company_id,
                    (int) $validated['qty'],
                    $item->part_number,
                    $item->part_name
                );
            }
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('scan.show', ['barcode_id' => $barcodeId])
                ->withInput()
                ->withErrors(['qty' => $e->getMessage()]);
        }

        $msg = $validated['direction'] === 'in'
            ? 'Barang masuk: stok bertambah '.$validated['qty'].' unit.'
            : 'Barang keluar: pengurangan FIFO '.$validated['qty'].' unit.';

        return redirect()
            ->route('scan.show', ['barcode_id' => $barcodeId])
            ->with('success', $msg);
    }
}
