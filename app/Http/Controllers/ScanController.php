<?php

namespace App\Http\Controllers;

use App\Models\CompanyBarcode;
use App\Models\ItemBarcode;
use App\Models\Employee;
use App\Models\EmployeeScanSession;
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

    public function show(Request $request, string $barcodeId)
    {
        if (str_starts_with($barcodeId, 'EMP-')) {
            $nip = str_replace('EMP-', '', $barcodeId);
            $employee = Employee::where('nip', $nip)->first();

            if (! $employee) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Karyawan tidak ditemukan'], 404);
                }
                return redirect()->route('scan.index')->with('error', 'Karyawan tidak ditemukan.');
            }

            // Catat sesi scan dan set session aktif
            $scannedAt = Carbon::now();
            EmployeeScanSession::create([
                'employee_id' => $employee->id,
                'scanned_at'  => $scannedAt,
            ]);
            session([
                'active_employee_id'         => $employee->id,
                'active_employee_name'       => $employee->name,
                'active_employee_nip'        => $employee->nip,
                'active_employee_scanned_at' => $scannedAt->toDateTimeString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'type' => 'employee',
                    'data' => [
                        'employee' => $employee,
                    ],
                ]);
            }
            return view('scan.result-employee', compact('employee'));
        }

        if (str_starts_with($barcodeId, 'IB-')) {
            $parts = explode('-', $barcodeId);
            // Cek apakah ada ID unique item (format: IB-item_id-receiving_id-unique_id)
            if (count($parts) === 4) {
                $uniqueItemId = $parts[3];
                $uniqueItem = \App\Models\UniqueItem::with('item')->find($uniqueItemId);
                if (!$uniqueItem) {
                    return redirect()->route('scan.index')->with('error', 'Unique Item tidak ditemukan.');
                }
                
                $expiredWarning = $uniqueItem->expired_date?->isPast() ?? false;
                $approachingExpiry = $uniqueItem->expired_date && $uniqueItem->expired_date->isBetween(Carbon::now(), Carbon::now()->addDays(30));

                return view('scan.result-unique', compact('uniqueItem', 'expiredWarning', 'approachingExpiry'));
            }

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
            $expiredWarning = $itemBarcode->item->tgl_expired?->isPast() ?? false;
            $approachingExpiry = $itemBarcode->item->tgl_expired && $itemBarcode->item->tgl_expired->isBetween(Carbon::now(), Carbon::now()->addDays(30));

            if ($request->expectsJson()) {
                return response()->json([
                    'type' => 'item',
                    'data' => [
                        'item' => $itemBarcode->item,
                        'receiving' => $itemBarcode->itemReceiving,
                        'company' => $itemBarcode->item->company,
                        'fifo_older_stock_warning' => $fifoOlderStockWarning,
                        'expired_warning' => $expiredWarning,
                        'approaching_expiry' => $approachingExpiry,
                    ],
                ]);
            }

            return view('scan.result-item', compact('itemBarcode', 'fifoOlderStockWarning', 'expiredWarning', 'approachingExpiry'));
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
                'qty' => 'required|integer|min:1',
            ]);
            
            if ($validated['direction'] === 'out') {
                $uniqueItem->update(['status_keluar' => true]);
                return redirect()->route('scan.index')->with('success', 'Barang berhasil keluar.');
            } else {
                // Duplikasi
                $newUniqueItem = $uniqueItem->replicate(['status_keluar']);
                $newUniqueItem->status_keluar = false;
                $newUniqueItem->save();
                return redirect()->route('scan.index')->with('success', 'Barang masuk, unique item baru dibuat.');
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

        $item = $itemBarcode->item;
        $qtyToDeduct = $validated['qty'] * ($item->qty_sub_pack ?? 1);

        try {
            DB::transaction(function () use ($itemBarcode, $validated, $qtyToDeduct) {
                if ($validated['direction'] === 'in') {
                    FifoStockService::incrementItemQty((int) $itemBarcode->item_id, (int) $qtyToDeduct);
                    $itemBarcode->itemReceiving->increment('jumlah_box', (int) $validated['qty']);
                } else {
                    FifoStockService::deductFromItems(
                        (int) $itemBarcode->item->company_id,
                        (int) $qtyToDeduct,
                        $itemBarcode->item->part_number,
                        $itemBarcode->item->part_name
                    );
                    $itemBarcode->itemReceiving->decrement('jumlah_box', (int) $validated['qty']);
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
