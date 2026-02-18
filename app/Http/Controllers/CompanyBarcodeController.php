<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBarcode;
use App\Models\CompanyItem;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorSVG;

class CompanyBarcodeController extends Controller
{
    public function index()
    {
        $companyBarcodes = CompanyBarcode::with('company.companyItems.item')->latest()->paginate(15);
        return view('company-barcodes.index', compact('companyBarcodes'));
    }

    public function create()
    {
        $companies = Company::with('items')->get();
        $companiesJson = $companies->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'items' => $c->items->map(fn($i) => [
                'id' => $i->id,
                'part_name' => $i->part_name,
                'part_number' => $i->part_number,
                'code' => $i->code,
            ])->values(),
        ])->values();
        return view('company-barcodes.create', compact('companies', 'companiesJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);
        $itemInclude = $request->item_include ?? [];
        $itemQty = $request->item_qty ?? [];
        $itemPosisi = $request->item_posisi ?? [];
        $itemTingkat = $request->item_tingkat ?? [];

        $hasItems = false;
        foreach ($itemInclude as $itemId => $include) {
            if ($include && isset($itemQty[$itemId]) && (int) $itemQty[$itemId] > 0) {
                CompanyItem::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'item_id' => $itemId,
                    ],
                    [
                        'qty' => (int) $itemQty[$itemId],
                        'posisi_rak' => $itemPosisi[$itemId] ?? null,
                        'tingkat' => $itemTingkat[$itemId] ?? null,
                    ]
                );
                $hasItems = true;
            }
        }

        if (!$hasItems) {
            return back()->withInput()->withErrors(['items' => 'Pilih minimal 1 barang dengan qty > 0.']);
        }

        $companyBarcode = CompanyBarcode::create([
            'company_id' => $company->id,
            'barcode_id' => 'CB-' . $company->id . '-' . uniqid(),
        ]);

        return redirect()->route('company-barcodes.show', $companyBarcode)
            ->with('success', 'Barcode perusahaan berhasil dibuat.');
    }

    public function show(CompanyBarcode $companyBarcode)
    {
        $companyBarcode->load('company.companyItems.item');
        $generator = new BarcodeGeneratorSVG();
        $barcodeSvg = $generator->getBarcode(
            $companyBarcode->barcode_id,
            $generator::TYPE_CODE_128,
            2,
            50
        );
        return view('company-barcodes.show', compact('companyBarcode', 'barcodeSvg'));
    }
}
