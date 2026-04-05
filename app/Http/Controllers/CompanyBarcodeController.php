<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBarcode;
use App\Models\CompanyItem;
use App\Models\Employee;
use App\Models\Item;
use App\Support\BarcodeQrCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyBarcodeController extends Controller
{
    public function index()
    {
        $companyBarcodes = CompanyBarcode::with('company.companyItems.item')
            ->oldest()
            ->paginate(15);

        return view('company-barcodes.index', compact('companyBarcodes'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();

        return view('company-barcodes.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.part_name' => 'nullable|string|max:255',
            'items.*.code' => 'nullable|string|max:255',
            'items.*.qty' => 'nullable|integer|min:0',
            'items.*.posisi_rak' => 'nullable|string|max:255',
            'items.*.tingkat' => 'nullable|string|max:255',
            'items.*.operator_mobil_id' => 'nullable|exists:employees,id',
            'items.*.pengirim_id' => 'nullable|exists:employees,id',
            'items.*.operator_forklift_id' => 'nullable|exists:employees,id',
        ]);

        $rows = collect($request->items)->filter(fn ($r) => (int) ($r['qty'] ?? 0) > 0);
        if ($rows->isEmpty()) {
            return back()->withInput()->withErrors(['items' => 'Isi minimal satu barang dengan qty lebih dari 0.']);
        }

        return DB::transaction(function () use ($request, $rows) {
            $company = Company::create(['name' => $request->company_name]);

            foreach ($rows as $row) {
                $qty = (int) $row['qty'];
                $code = isset($row['code']) && $row['code'] !== '' ? $row['code'] : null;
                if ($code === null) {
                    $code = 'CB-'.$company->id.'-'.uniqid();
                }

                $item = Item::create([
                    'company_id' => $company->id,
                    'operator_mobil_id' => isset($row['operator_mobil_id']) && $row['operator_mobil_id'] !== '' ? (int) $row['operator_mobil_id'] : null,
                    'pengirim_id' => isset($row['pengirim_id']) && $row['pengirim_id'] !== '' ? (int) $row['pengirim_id'] : null,
                    'operator_forklift_id' => isset($row['operator_forklift_id']) && $row['operator_forklift_id'] !== '' ? (int) $row['operator_forklift_id'] : null,
                    'part_name' => isset($row['part_name']) && $row['part_name'] !== '' ? $row['part_name'] : null,
                    'code' => $code,
                    'qty' => 0,
                ]);

                CompanyItem::create([
                    'company_id' => $company->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'posisi_rak' => isset($row['posisi_rak']) && $row['posisi_rak'] !== '' ? $row['posisi_rak'] : null,
                    'tingkat' => isset($row['tingkat']) && $row['tingkat'] !== '' ? $row['tingkat'] : null,
                ]);
            }

            $companyBarcode = CompanyBarcode::create([
                'company_id' => $company->id,
                'barcode_id' => 'CB-'.$company->id.'-'.uniqid(),
            ]);

            return redirect()->route('company-barcodes.show', $companyBarcode)
                ->with('success', 'Barcode perusahaan berhasil dibuat.');
        });
    }

    public function show(CompanyBarcode $companyBarcode)
    {
        $companyBarcode->load([
            'company.companyItems.item.operatorMobil',
            'company.companyItems.item.pengirim',
            'company.companyItems.item.operatorForklift',
        ]);
        $payload = $companyBarcode->barcode_id;
        $barcodeSvg = BarcodeQrCodes::code128Svg($payload);
        $qrCodeSvg = BarcodeQrCodes::qrSvg($payload);

        return view('company-barcodes.show', compact('companyBarcode', 'barcodeSvg', 'qrCodeSvg'));
    }
}
