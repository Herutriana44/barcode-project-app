<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemReceiving;
use App\Support\BarcodeQrCodes;
use App\Support\InventorySpreadsheet;
use App\Support\ScanUrl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemBarcodeController extends Controller
{
    private const WAREHOUSE_COMPANY_NAME = 'PT TEKUN ASAS SUMBER MAKMUR';

    private static function warehouseCompanyOrFail(): Company
    {
        return Company::query()->firstOrCreate(['name' => self::WAREHOUSE_COMPANY_NAME]);
    }
    public function index()
    {
        $itemBarcodes = ItemBarcode::query()
            ->with(['item.company', 'itemReceiving'])
            ->join('item_receivings', 'item_receivings.id', '=', 'item_barcodes.item_receiving_id')
            ->orderByRaw('COALESCE(item_receivings.tanggal_terima_fg, DATE(item_receivings.created_at)) ASC')
            ->orderBy('item_receivings.id')
            ->orderBy('item_barcodes.id')
            ->select('item_barcodes.*')
            ->paginate(15);

        return view('item-barcodes.index', compact('itemBarcodes'));
    }

    /**
     * Label cetak: semua barcode barang + data + QR (tampilan seperti kertas).
     */
    public function labels()
    {
        $itemBarcodes = ItemBarcode::with([
            'item.company',
            'item.operatorMobil',
            'item.pengirim',
            'item.operatorForklift',
            'itemReceiving',
        ])
            ->join('item_receivings', 'item_receivings.id', '=', 'item_barcodes.item_receiving_id')
            ->orderByRaw('COALESCE(item_receivings.tanggal_terima_fg, DATE(item_receivings.created_at)) ASC')
            ->orderBy('item_receivings.id')
            ->orderBy('item_barcodes.id')
            ->select('item_barcodes.*')
            ->get();

        $rows = $itemBarcodes->map(function (ItemBarcode $ib) {
            return [
                'itemBarcode' => $ib,
                /** Code 128 berisi URL scan; lebar disesuaikan untuk label kecil. */
                'labelBarcodeSvg' => BarcodeQrCodes::code128SvgForScan($ib->barcode_id, 1, 28),
                'qrSvg' => BarcodeQrCodes::qrSvgForScan($ib->barcode_id, 88, 2),
            ];
        });

        return view('item-barcodes.labels', compact('rows'));
    }

    /**
     * Label per isi/sub-pack untuk satu barcode box.
     */
    public function labelIsi(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load(['item.company', 'itemReceiving']);

        $item = $itemBarcode->item;
        $staticQty = max(0, (int) ($item->static_qty ?? 0));
        $sub = (int) ($item->qty_sub_pack ?? 0);

        // Jika qty_sub_pack ada, asumsi: 1 label = 1 sub-pack berisi qty_sub_pack pcs.
        // Jika tidak ada, asumsi: 1 label = 1 pcs.
        $qtyPerLabel = $sub > 0 ? $sub : 1;
        $labelCount = $staticQty > 0 ? (int) ceil($staticQty / $qtyPerLabel) : 1;

        // Hindari render ribuan label secara tidak sengaja.
        $labelCount = min($labelCount, 500);

        $labels = [];
        $remaining = max(0, $staticQty);
        for ($i = 0; $i < $labelCount; $i++) {
            $q = $qtyPerLabel;
            if ($sub > 0 && $remaining > 0) {
                $q = min($qtyPerLabel, $remaining);
            }
            $remaining = max(0, $remaining - $q);

            // Pakai QR Code (sesuai permintaan label per isi).
            $qrSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id, 140, 2);

            $labels[] = [
                'qtyInPack' => $q,
                'qrSvg' => $qrSvg,
            ];
        }

        return view('item-barcodes.label-isi', compact('itemBarcode', 'labels'));
    }

    public function updateChecker(Request $request, ItemBarcode $itemBarcode)
    {
        $validated = $request->validate([
            'checker' => 'nullable|string|max:255',
        ]);

        $itemBarcode->load('item');
        $itemBarcode->item->update([
            // re-use kolom yang sudah ada supaya tidak perlu migration baru
            'inspector_name' => $validated['checker'] ?? null,
        ]);

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', 'Checker diperbarui.');
    }

    public function create()
    {
        $warehouseCompany = self::warehouseCompanyOrFail();
        $customers = Company::query()->orderBy('name')->get();

        return view('item-barcodes.create', compact('warehouseCompany', 'customers'));
    }

    public function store(Request $request)
    {
        $warehouseCompany = self::warehouseCompanyOrFail();

        $validated = $request->validate([
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
            'operator_mobil_id' => 'nullable|exists:employees,id',
            'pengirim_id' => 'nullable|exists:employees,id',
            'operator_forklift_id' => 'nullable|exists:employees,id',
        ]);

        $item = Item::create([
            'company_id' => $warehouseCompany->id,
            'operator_mobil_id' => $validated['operator_mobil_id'] ?? null,
            'pengirim_id' => $validated['pengirim_id'] ?? null,
            'operator_forklift_id' => $validated['operator_forklift_id'] ?? null,
            'customer' => $validated['customer'] ?? null,
            'part_name' => $validated['part_name'] ?? null,
            'part_number' => $validated['part_number'] ?? null,
            'model' => $validated['model'] ?? null,
            'berat' => $validated['berat'] ?? null,
            'qty' => $validated['qty'],
            'static_qty' => $validated['qty'],
            'dynamic_qty' => $validated['qty'],
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

    public function edit(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load(['item', 'itemReceiving']);
        $warehouseCompany = self::warehouseCompanyOrFail();
        $customers = Company::query()->orderBy('name')->get();

        return view('item-barcodes.edit', compact('itemBarcode', 'warehouseCompany', 'customers'));
    }

    public function update(Request $request, ItemBarcode $itemBarcode)
    {
        $itemBarcode->load(['item', 'itemReceiving']);
        $warehouseCompany = self::warehouseCompanyOrFail();

        $validated = $request->validate([
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
            'operator_mobil_id' => 'nullable|exists:employees,id',
            'pengirim_id' => 'nullable|exists:employees,id',
            'operator_forklift_id' => 'nullable|exists:employees,id',
        ]);

        DB::transaction(function () use ($itemBarcode, $validated, $warehouseCompany) {
            $itemBarcode->item->update([
                'company_id' => $warehouseCompany->id,
                'operator_mobil_id' => $validated['operator_mobil_id'] ?? null,
                'pengirim_id' => $validated['pengirim_id'] ?? null,
                'operator_forklift_id' => $validated['operator_forklift_id'] ?? null,
                'customer' => $validated['customer'] ?? null,
                'part_name' => $validated['part_name'] ?? null,
                'part_number' => $validated['part_number'] ?? null,
                'model' => $validated['model'] ?? null,
                'berat' => $validated['berat'] ?? null,
                // qty input dianggap "isi per box" (static). Dynamic tidak diubah lewat edit form.
                'qty' => $validated['qty'],
                'static_qty' => $validated['qty'],
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

            $itemBarcode->itemReceiving->update([
                'transfer_slip_no' => $validated['transfer_slip_no'] ?? null,
                'tanggal_terima_fg' => $validated['tanggal_terima_fg'] ?? null,
                'jumlah_box' => $validated['jumlah_box'] ?? 0,
            ]);
        });

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', 'Data barang diperbarui.');
    }

    public function destroy(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load(['item', 'itemReceiving']);

        DB::transaction(function () use ($itemBarcode) {
            $item = $itemBarcode->item;
            $receiving = $itemBarcode->itemReceiving;
            $itemBarcode->delete();
            $receiving->delete();

            $item->refresh();
            if ($item->itemBarcodes()->exists() || $item->itemReceivings()->exists() || $item->companyItems()->exists()) {
                return;
            }
            $item->delete();
        });

        return redirect()->route('item-barcodes.index')
            ->with('success', 'Barcode barang dihapus.');
    }

    public function importForm()
    {
        return view('item-barcodes.import');
    }

    public function importTemplate()
    {
        return InventorySpreadsheet::downloadFgTemplate();
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:10240',
        ], [
            'file.required' => 'Pilih berkas Excel (.xlsx).',
            'file.mimes' => 'Hanya format .xlsx yang didukung.',
        ]);

        $matrix = InventorySpreadsheet::readFirstSheet($request->file('file'));
        $result = InventorySpreadsheet::importFgItemsFromMatrix($matrix);

        if (count($result['errors']) > 0) {
            return back()->with('import_errors', $result['errors']);
        }

        return redirect()->route('item-barcodes.index')
            ->with('success', $result['message'] ?? 'Import selesai.');
    }

    public function show(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load([
            'item.company',
            'item.operatorMobil',
            'item.pengirim',
            'item.operatorForklift',
            'itemReceiving',
        ]);
        $scanUrl = ScanUrl::forBarcode($itemBarcode->barcode_id);
        $barcodeSvg = BarcodeQrCodes::code128SvgForScan($itemBarcode->barcode_id);
        $qrCodeSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id);
        $qcLabelQrSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id, 88, 2);
        $qcLabelBarcodeSvg = BarcodeQrCodes::code128SvgForScan($itemBarcode->barcode_id, 1, 28);

        return view('item-barcodes.show', compact('itemBarcode', 'barcodeSvg', 'qrCodeSvg', 'qcLabelQrSvg', 'qcLabelBarcodeSvg', 'scanUrl'));
    }
}
