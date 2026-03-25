<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemReceiving;
use App\Support\BarcodeQrCodes;
use Illuminate\Http\Request;

class ItemBarcodeController extends Controller
{
    public function index()
    {
        $itemBarcodes = ItemBarcode::with(['item.company', 'itemReceiving'])->latest()->paginate(15);

        return view('item-barcodes.index', compact('itemBarcodes'));
    }

    public function create()
    {
        $companies = Company::with('items')->get();

        return view('item-barcodes.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'customer' => 'nullable|string',
            'part_name' => 'nullable|string',
            'part_number' => 'nullable|string',
            'model' => 'nullable|string',
            'berat' => 'nullable|numeric',
            'qty' => 'required|integer|min:0',
            'inspector_name' => 'nullable|string',
            'tgl_produksi' => 'nullable|date',
            'tgl_expired' => 'nullable|date',
            'code' => 'required|string',
            'posisi_rak' => 'nullable|string',
            'tingkat' => 'nullable|string',
            'ukuran_material' => 'nullable|string',
            'jenis_bahan' => 'nullable|in:SPCC,SESE',
            'quantity_material' => 'nullable|integer|min:0',
            'no_surat_jalan_material' => 'nullable|string',
            'tanggal_terima_material' => 'nullable|date',
            'transfer_slip_no' => 'nullable|string',
            'tanggal_terima_fg' => 'nullable|date',
            'jumlah_box' => 'nullable|integer|min:0',
        ]);

        $item = Item::create([
            'company_id' => $validated['company_id'],
            'customer' => $validated['customer'] ?? null,
            'part_name' => $validated['part_name'] ?? null,
            'part_number' => $validated['part_number'] ?? null,
            'model' => $validated['model'] ?? null,
            'berat' => $validated['berat'] ?? null,
            'qty' => $validated['qty'],
            'inspector_name' => $validated['inspector_name'] ?? null,
            'tgl_produksi' => $validated['tgl_produksi'] ?? null,
            'tgl_expired' => $validated['tgl_expired'] ?? null,
            'code' => $validated['code'],
            'posisi_rak' => $validated['posisi_rak'] ?? null,
            'tingkat' => $validated['tingkat'] ?? null,
            'ukuran_material' => $validated['ukuran_material'] ?? null,
            'jenis_bahan' => $validated['jenis_bahan'] ?? null,
            'quantity_material' => $validated['quantity_material'] ?? null,
            'no_surat_jalan_material' => $validated['no_surat_jalan_material'] ?? null,
            'tanggal_terima_material' => $validated['tanggal_terima_material'] ?? null,
        ]);

        $receiving = ItemReceiving::create([
            'item_id' => $item->id,
            'transfer_slip_no' => $validated['transfer_slip_no'] ?? null,
            'tanggal_terima_fg' => $validated['tanggal_terima_fg'] ?? null,
            'jumlah_box' => $validated['jumlah_box'] ?? 0,
        ]);

        $barcodeId = 'IB-'.$item->id.'-'.$receiving->id;
        $itemBarcode = ItemBarcode::create([
            'item_id' => $item->id,
            'item_receiving_id' => $receiving->id,
            'barcode_id' => $barcodeId,
        ]);

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', 'Barcode barang berhasil dibuat.');
    }

    public function show(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load(['item.company', 'itemReceiving']);
        $payload = $itemBarcode->barcode_id;
        $barcodeSvg = BarcodeQrCodes::code128Svg($payload);
        $qrCodeSvg = BarcodeQrCodes::qrSvg($payload);

        return view('item-barcodes.show', compact('itemBarcode', 'barcodeSvg', 'qrCodeSvg'));
    }
}
