<?php

namespace App\Http\Controllers;

use App\Models\CompanyBarcode;
use App\Models\ItemBarcode;
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
            $itemBarcode = ItemBarcode::with(['item.company', 'itemReceiving'])
                ->where('barcode_id', $barcodeId)
                ->first();

            if (!$itemBarcode) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Barcode tidak ditemukan'], 404);
                }
                return redirect()->route('scan.index')->with('error', 'Barcode barang tidak ditemukan.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'type' => 'item',
                    'data' => [
                        'item' => $itemBarcode->item,
                        'receiving' => $itemBarcode->itemReceiving,
                        'company' => $itemBarcode->item->company,
                    ],
                ]);
            }

            return view('scan.result-item', compact('itemBarcode'));
        }

        if (str_starts_with($barcodeId, 'CB-')) {
            $companyBarcode = CompanyBarcode::with('company.companyItems.item')
                ->where('barcode_id', $barcodeId)
                ->first();

            if (!$companyBarcode) {
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
}
