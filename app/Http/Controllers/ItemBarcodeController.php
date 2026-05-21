<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemReceiving;
use App\Models\Rak;
use App\Models\UniqueItem;
use App\Services\ActivityLogger;
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
        $q = trim((string) request()->query('q', ''));
        $expiredSort = (string) request()->query('expired_sort', '');
        $companyFilter = trim((string) request()->query('company', ''));
        $partNameFilter = trim((string) request()->query('part_name', ''));

        $itemBarcodes = ItemBarcode::query()
            ->with(['item.company', 'itemReceiving'])
            ->join('items', 'items.id', '=', 'item_barcodes.item_id')
            ->join('item_receivings', 'item_receivings.id', '=', 'item_barcodes.item_receiving_id')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('items.code', 'like', '%'.$q.'%');
            })
            ->when($companyFilter !== '', function ($query) use ($companyFilter) {
                $query->whereHas('item.company', fn ($q) => $q->where('name', 'like', '%'.$companyFilter.'%'));
            })
            ->when($partNameFilter !== '', function ($query) use ($partNameFilter) {
                $query->where('items.part_name', 'like', '%'.$partNameFilter.'%');
            })
            ->when($expiredSort === 'expired_first', function ($query) {
                $query->orderByRaw("CASE WHEN items.tgl_expired IS NOT NULL AND items.tgl_expired < CURDATE() THEN 0 ELSE 1 END ASC");
            })
            ->when($expiredSort === 'valid_first', function ($query) {
                $query->orderByRaw("CASE WHEN items.tgl_expired IS NOT NULL AND items.tgl_expired < CURDATE() THEN 0 ELSE 1 END DESC");
            })
            ->orderByRaw('COALESCE(item_receivings.tanggal_terima_fg, DATE(item_receivings.created_at)) ASC')
            ->orderBy('item_receivings.id')
            ->orderBy('item_barcodes.id')
            ->select('item_barcodes.*')
            ->paginate(15);

        $appends = [];
        if ($q !== '') $appends['q'] = $q;
        if ($expiredSort !== '') $appends['expired_sort'] = $expiredSort;
        if ($companyFilter !== '') $appends['company'] = $companyFilter;
        if ($partNameFilter !== '') $appends['part_name'] = $partNameFilter;
        if ($appends !== []) $itemBarcodes->appends($appends);

        return view('item-barcodes.index', compact('itemBarcodes', 'q', 'expiredSort', 'companyFilter', 'partNameFilter'));
    }

    public function labelPerBox(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load([
            'item.company',
            'itemReceiving',
        ]);

        $item = $itemBarcode->item;
        $staticQty = max(0, (int) ($item->static_qty ?? 0));
        $sub = max(1, (int) ($item->qty_sub_pack ?? 1));

        $labelCount = (int) ceil($staticQty / $sub);
        $labelCount = max(1, min($labelCount, 500)); // safety limit

        $labelBarcodeSvg = BarcodeQrCodes::code128SvgForScan($itemBarcode->barcode_id, 1, 28);
        $qrSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id, 88, 2);

        $rows = collect();
        $remaining = $staticQty;
        for ($i = 0; $i < $labelCount; $i++) {
            $pcs = min($sub, $remaining);
            $remaining -= $pcs;
            $rows->push([
                'itemBarcode' => $itemBarcode,
                'labelBarcodeSvg' => $labelBarcodeSvg,
                'qrSvg' => $qrSvg,
                'labelQtyPcs' => $pcs > 0 ? $pcs : $staticQty,
            ]);
            if ($remaining <= 0) break;
        }

        $labelHeaderCompanyName = self::WAREHOUSE_COMPANY_NAME;
        return view('item-barcodes.labels', compact('rows', 'labelHeaderCompanyName'));
    }
    public function labelPrintA4(Request $request, ItemBarcode $itemBarcode)
    {
        $itemBarcode->load([
            'item.company',
            'itemReceiving',
        ]);

        $item = $itemBarcode->item;
        $staticQty = max(0, (int) ($item->static_qty ?? 0));
        
        // Input 'pages' dikali 10 untuk menentukan jumlah box (label) yang dicetak
        $pages = (int) $request->query('pages', 1);
        $totalLabels = $pages * 10;

        $labelBarcodeSvg = BarcodeQrCodes::code128SvgForScan($itemBarcode->barcode_id, 1, 28);
        $qrSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id, 88, 2);

        $rows = collect();
        for ($i = 0; $i < $totalLabels; $i++) {
            $rows->push([
                'itemBarcode' => $itemBarcode,
                'labelBarcodeSvg' => $labelBarcodeSvg,
                'qrSvg' => $qrSvg,
                'labelQtyPcs' => $staticQty,
            ]);
        }

        $labelHeaderCompanyName = self::WAREHOUSE_COMPANY_NAME;
        return view('item-barcodes.labels-a4', compact('rows', 'labelHeaderCompanyName'));
    }

    /**
     * Label cetak: semua barcode barang + data + QR (tampilan seperti kertas).
     */
    public function labels()
    {
        $q = trim((string) request()->query('q', ''));

        $itemBarcodes = ItemBarcode::with([
            'item.company',
            'item.operatorMobil',
            'item.pengirim',
            'item.operatorForklift',
            'itemReceiving',
        ])
            ->join('items', 'items.id', '=', 'item_barcodes.item_id')
            ->join('item_receivings', 'item_receivings.id', '=', 'item_barcodes.item_receiving_id')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('items.code', 'like', '%'.$q.'%');
            })
            ->orderByRaw('COALESCE(item_receivings.tanggal_terima_fg, DATE(item_receivings.created_at)) ASC')
            ->orderBy('item_receivings.id')
            ->orderBy('item_barcodes.id')
            ->select('item_barcodes.*')
            ->get();

        /** @var \Illuminate\Support\Collection<int, array{itemBarcode: ItemBarcode, labelBarcodeSvg: string, qrSvg: string, labelQtyPcs: int|null}> $rows */
        $rows = collect();

        foreach ($itemBarcodes as $ib) {
            $labelBarcodeSvg = BarcodeQrCodes::code128SvgForScan($ib->barcode_id, 1, 28);
            $qrSvg = BarcodeQrCodes::qrSvgForScan($ib->barcode_id, 88, 2);

            $item = $ib->item;
            $staticQty = max(0, (int) ($item->static_qty ?? 0));
            // Gunakan jumlah box dari input (misalnya request()->input('num_boxes')) 
            // atau jika tidak ada, gunakan logika sub-pack saat ini sebagai default atau 1 jika tidak diset.
            $numBoxes = abs((int) (request()->query('num_boxes', 0)));
            if ($numBoxes <= 0) {
                // Fallback ke logika permintaan user: static_qty / qty_sub_pack
                $sub = max(0, (int) ($item->qty_sub_pack ?? 0));
                if ($sub > 0) {
                    $labelCount = (int) ceil($staticQty / $sub);
                    $labelCount = min($labelCount, 500); // safety limit
                    $remaining = $staticQty;
                    for ($i = 0; $i < $labelCount; $i++) {
                        $pcs = min($sub, $remaining);
                        $remaining -= $pcs;
                        $rows->push([
                            'itemBarcode' => $ib,
                            'labelBarcodeSvg' => $labelBarcodeSvg,
                            'qrSvg' => $qrSvg,
                            'labelQtyPcs' => $pcs > 0 ? $pcs : $sub,
                        ]);
                    }
                } else {
                    // Jika qty_sub_pack kosong, maka menggunakan static_qty sebagai jumlah label
                    $labelCount = min($staticQty, 500); // safety limit
                    for ($i = 0; $i < $labelCount; $i++) {
                        $rows->push([
                            'itemBarcode' => $ib,
                            'labelBarcodeSvg' => $labelBarcodeSvg,
                            'qrSvg' => $qrSvg,
                            'labelQtyPcs' => 1,
                        ]);
                    }
                }
            } else {
                // Logika numBoxes tetap ada jika dipanggil eksplisit via query param
                $pcsPerBox = (int) floor($staticQty / $numBoxes);
                $remainder = $staticQty % $numBoxes;
                for ($i = 0; $i < $numBoxes; $i++) {
                    $pcs = $pcsPerBox + ($i < $remainder ? 1 : 0);
                    $rows->push([
                        'itemBarcode' => $ib,
                        'labelBarcodeSvg' => $labelBarcodeSvg,
                        'qrSvg' => $qrSvg,
                        'labelQtyPcs' => $pcs,
                    ]);
                }
            }
        }

        $labelHeaderCompanyName = self::WAREHOUSE_COMPANY_NAME;

        return view('item-barcodes.labels', compact('rows', 'labelHeaderCompanyName'));
    }

    /**
     * Label per isi/sub-pack untuk satu barcode box.
     */
    public function labelIsi(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load(['item.company', 'itemReceiving']);

        $item = $itemBarcode->item;
        $staticQty = max(0, (int) ($item->static_qty ?? 0));

        // Jika sub_pack diset, maka label per isi akan mengikuti qty_sub_pack.
        // Jika tidak diset, maka 1 label berisi seluruh static_qty.
        $sub = (int) ($item->qty_sub_pack ?? 0);
        $qtyPerLabel = $sub > 0 ? $sub : $staticQty;

        $labelCount = ($qtyPerLabel > 0) ? (int) ceil($staticQty / $qtyPerLabel) : 1;
        $labelCount = min($labelCount, 500);

        $labels = [];
        $remaining = $staticQty;
        for ($i = 0; $i < $labelCount; $i++) {
            $q = ($sub > 0) ? min($qtyPerLabel, $remaining) : $staticQty;
            $remaining -= $q;

            $qrSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id, 140, 2);

            $labels[] = [
                'qtyInPack' => $staticQty,
                'qrSvg' => $qrSvg,
            ];
            if ($remaining <= 0) break;
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
            'checker_name' => $validated['checker'] ?? null,
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

        $customerName = isset($validated['customer']) ? trim((string) $validated['customer']) : '';
        $allowedRak = [];
        if ($customerName !== '') {
            $allowedRak = Rak::query()
                ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($customerName)])
                ->pluck('code')
                ->map(fn ($v) => (string) $v)
                ->all();
        }
        if (count($allowedRak) > 0) {
            $rak = isset($validated['posisi_rak']) ? trim((string) $validated['posisi_rak']) : '';
            if ($rak !== '' && ! in_array($rak, $allowedRak, true)) {
                return back()->withInput()->withErrors([
                    'posisi_rak' => "Rak \"{$rak}\" tidak valid untuk customer \"{$customerName}\".",
                ]);
            }
        }

        $item = Item::create([
            'company_id' => $warehouseCompany->id,
            'operator_mobil_id' => $validated['operator_mobil_id'] ?? null,
            'pengirim_id' => $validated['pengirim_id'] ?? null,
            'operator_forklift_id' => $validated['operator_forklift_id'] ?? null,
            'scanned_by_employee_id' => session('active_employee_id'),
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
            // 'tgl_expired' => ($validated['tgl_produksi']) 
            //     ? Carbon::parse($validated['tgl_produksi'])->addMonths(3)->format('Y-m-d') 
            //     : Carbon::now()->addMonths(3)->format('Y-m-d'),
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
            'scanned_by_employee_id' => session('active_employee_id'),
        ]);

        ActivityLogger::log('Barang', 'Buat', 'Membuat barcode barang: ' . $barcodeId . ' (Part: ' . $item->part_name . ')');

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
            'qty_sub_pack' => 'nullable|integer|min:0',
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

        $customerName = isset($validated['customer']) ? trim((string) $validated['customer']) : '';
        $allowedRak = [];
        if ($customerName !== '') {
            $allowedRak = Rak::query()
                ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($customerName)])
                ->pluck('code')
                ->map(fn ($v) => (string) $v)
                ->all();
        }
        if (count($allowedRak) > 0) {
            $rak = isset($validated['posisi_rak']) ? trim((string) $validated['posisi_rak']) : '';
            if ($rak !== '' && ! in_array($rak, $allowedRak, true)) {
                return back()->withInput()->withErrors([
                    'posisi_rak' => "Rak \"{$rak}\" tidak valid untuk customer \"{$customerName}\".",
                ]);
            }
        }

        DB::transaction(function () use ($itemBarcode, $validated, $warehouseCompany, $request) {
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
                'qty' => $validated['qty'],
                'static_qty' => $validated['qty'],
                'qty_sub_pack' => $validated['qty_sub_pack'] ?? null,
                'inspector_name' => $validated['inspector_name'] ?? null,
                'tgl_produksi' => $validated['tgl_produksi'] ?? null,
                // 'tgl_expired' => ($validated['tgl_produksi']) 
                //     ? Carbon::parse($validated['tgl_produksi'])->addMonths(3)->format('Y-m-d') 
                //     : Carbon::now()->addMonths(3)->format('Y-m-d'),
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
                'jumlah_box' => $request->has('jumlah_box') ? $validated['jumlah_box'] : $itemBarcode->itemReceiving->jumlah_box,
            ]);
        });

        ActivityLogger::log('Barang', 'Edit', 'Mengedit barang dengan barcode: ' . $itemBarcode->barcode_id);

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', 'Data barang diperbarui.');
    }

    public function destroy(ItemBarcode $itemBarcode)
    {
        $barcodeId = $itemBarcode->barcode_id;
        $partName = $itemBarcode->item->part_name;
        
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

        ActivityLogger::log('Barang', 'Hapus', 'Menghapus barang: ' . $partName . ' (Barcode: ' . $barcodeId . ')');

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

    public function downloadQr(ItemBarcode $itemBarcode)
    {
        $data = \App\Support\ScanUrl::forBarcode($itemBarcode->barcode_id);
        
        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->data($data)
            ->size(300)
            ->margin(10)
            ->build();

        return response($qrCode->getString())
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qr-'.$itemBarcode->barcode_id.'.png"');
    }

    public function show(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load([
            'item.company',
            'item.operatorMobil',
            'item.pengirim',
            'item.operatorForklift',
            'item.uniqueItems',
            'itemReceiving',
        ]);
        $scanUrl = ScanUrl::forBarcode($itemBarcode->barcode_id);
        $barcodeSvg = BarcodeQrCodes::code128SvgForScan($itemBarcode->barcode_id);
        $qrCodeSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id);
        $qcLabelQrSvg = BarcodeQrCodes::qrSvgForScan($itemBarcode->barcode_id, 88, 2);
        $qcLabelBarcodeSvg = BarcodeQrCodes::code128SvgForScan($itemBarcode->barcode_id, 1, 28);

        $labelHeaderCompanyName = self::WAREHOUSE_COMPANY_NAME;

        return view('item-barcodes.show', compact('itemBarcode', 'barcodeSvg', 'qrCodeSvg', 'qcLabelQrSvg', 'qcLabelBarcodeSvg', 'scanUrl', 'labelHeaderCompanyName'));
    }

    public function generateBulkUniqueItems(Request $request, ItemBarcode $itemBarcode)
    {
        $validated = $request->validate([
            'n' => 'required|integer|min:1',
        ]);

        $itemBarcode->load('item');
        $item = $itemBarcode->item;
        
        $count = (int) $validated['n'] * 10;

        for ($i = 0; $i < $count; $i++) {
            UniqueItem::create([
                'item_id' => $item->id,
                'qty' => $item->qty, // Assuming item->qty is the default
                'production_date' => $item->tgl_produksi,
                'expired_date' => $item->tgl_expired,
            ]);
        }

        ActivityLogger::log('Item Pecahan', 'Buat', 'Membuat ' . $count . ' box pecahan untuk: ' . $item->part_name);

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', $count . ' Unique items berhasil dibuat.');
    }

    public function storeUniqueItem(Request $request, ItemBarcode $itemBarcode)
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
            'production_date' => 'nullable|date',
            'expired_date' => 'nullable|date',
        ]);

        $itemBarcode->load('item');

        $uniqueItem = UniqueItem::create([
            'item_id' => $itemBarcode->item->id,
            'qty' => $validated['qty'],
            'production_date' => $validated['production_date'] ?? null,
            'expired_date' => $validated['expired_date'] ?? null,
        ]);

        ActivityLogger::log('Item Pecahan', 'Buat', 'Menambahkan box pecahan baru: ' . $itemBarcode->item->part_name . ' (Qty: ' . $validated['qty'] . ')');

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', 'Unique item berhasil ditambahkan.');
    }

    public function updateUniqueItem(Request $request, ItemBarcode $itemBarcode, UniqueItem $uniqueItem)
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
            'production_date' => 'nullable|date',
            'expired_date' => 'nullable|date',
        ]);

        $uniqueItem->update([
            'qty' => $validated['qty'],
            'production_date' => $validated['production_date'] ?? null,
            'expired_date' => $validated['expired_date'] ?? null,
        ]);

        ActivityLogger::log('Item Pecahan', 'Edit', 'Mengubah box pecahan: ' . $itemBarcode->item->part_name . ' (ID: ' . $uniqueItem->id . ')');

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', 'Unique item berhasil diperbarui.');
    }

    public function destroyUniqueItem(ItemBarcode $itemBarcode, UniqueItem $uniqueItem)
    {
        $id = $uniqueItem->id;
        $partName = $itemBarcode->item->part_name;
        
        $uniqueItem->delete();

        ActivityLogger::log('Item Pecahan', 'Hapus', 'Menghapus box pecahan: ' . $partName . ' (ID: ' . $id . ')');

        return redirect()->route('item-barcodes.show', $itemBarcode)
            ->with('success', 'Unique item berhasil dihapus.');
    }

    public function printUniqueItemLabel(ItemBarcode $itemBarcode, UniqueItem $uniqueItem)
    {
        $itemBarcode->load([
            'item.company',
            'itemReceiving',
        ]);

        $itemId = (string) $itemBarcode->item->id;
        $receivingId = (string) $itemBarcode->item_receiving_id;
        $uniqueId = (string) $uniqueItem->id;

        $labelBarcodeSvg = BarcodeQrCodes::code128SvgForUniqueItem($itemId, $receivingId, $uniqueId, 1, 28);
        $qrSvg = BarcodeQrCodes::qrSvgForUniqueItem($itemId, $receivingId, $uniqueId, 88, 2);

        $rows = collect([
            [
                'itemBarcode' => $itemBarcode,
                'labelBarcodeSvg' => $labelBarcodeSvg,
                'qrSvg' => $qrSvg,
                'labelQtyPcs' => $uniqueItem->qty,
                'uniqueItemId' => $uniqueId,
                'productionDate' => $uniqueItem->production_date ? \Carbon\Carbon::parse($uniqueItem->production_date) : null,
                'expiryDate' => $uniqueItem->expired_date ? \Carbon\Carbon::parse($uniqueItem->expired_date) : null,
            ]
        ]);

        $labelHeaderCompanyName = self::WAREHOUSE_COMPANY_NAME;

        return view('item-barcodes.unique-item-label', compact('rows', 'labelHeaderCompanyName'));
    }

    public function printAllUniqueItemLabels(ItemBarcode $itemBarcode)
    {
        $itemBarcode->load([
            'item.company',
            'item.uniqueItems',
            'itemReceiving',
        ]);

        $itemId = (string) $itemBarcode->item->id;
        $receivingId = (string) $itemBarcode->item_receiving_id;

        $rows = $itemBarcode->item->uniqueItems->map(function ($uniqueItem) use ($itemBarcode, $itemId, $receivingId) {
            $uniqueId = (string) $uniqueItem->id;

            return [
                'itemBarcode' => $itemBarcode,
                'labelBarcodeSvg' => BarcodeQrCodes::code128SvgForUniqueItem($itemId, $receivingId, $uniqueId, 1, 28),
                'qrSvg' => BarcodeQrCodes::qrSvgForUniqueItem($itemId, $receivingId, $uniqueId, 88, 2),
                'labelQtyPcs' => $uniqueItem->qty,
                'uniqueItemId' => $uniqueId,
                'productionDate' => $uniqueItem->production_date ? \Carbon\Carbon::parse($uniqueItem->production_date) : null,
                'expiryDate' => $uniqueItem->expired_date ? \Carbon\Carbon::parse($uniqueItem->expired_date) : null,
            ];
        });

        $labelHeaderCompanyName = self::WAREHOUSE_COMPANY_NAME;

        return view('item-barcodes.unique-item-label', compact('rows', 'labelHeaderCompanyName'));
    }
}
