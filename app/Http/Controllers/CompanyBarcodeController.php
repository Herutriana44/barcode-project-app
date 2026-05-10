<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBarcode;
use App\Models\CompanyItem;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemReceiving;
use App\Models\Rak;
use App\Support\BarcodeQrCodes;
use App\Support\InventorySpreadsheet;
use App\Support\ScanUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompanyBarcodeController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q', '');
        $itemCountSort = $request->input('item_count_sort', '');

        $query = Company::query()
            ->with([
                'companyBarcodes' => fn ($q) => $q->oldest(),
            ])
            ->withCount('companyItems');

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        if ($itemCountSort === 'most') {
            $query->orderByDesc('company_items_count');
        } elseif ($itemCountSort === 'least') {
            $query->orderBy('company_items_count');
        } else {
            $query->orderBy('name');
        }

        $companies = $query->paginate(15);

        return view('company-barcodes.index', compact('companies', 'q', 'itemCountSort'));
    }

    public function create(Request $request)
    {
        $company = null;
        if ($request->filled('company_id')) {
            $company = Company::query()->findOrFail((int) $request->input('company_id'));
        }

        return view('company-barcodes.create', compact('company'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|integer|exists:companies,id',
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

        $companyName = (string) $request->company_name;
        $allowedRak = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower(trim($companyName))])
            ->pluck('code')
            ->map(fn ($v) => (string) $v)
            ->all();

        if (count($allowedRak) > 0) {
            foreach ($rows as $i => $row) {
                $rak = isset($row['posisi_rak']) ? trim((string) $row['posisi_rak']) : '';
                if ($rak !== '' && ! in_array($rak, $allowedRak, true)) {
                    return back()->withInput()->withErrors([
                        "items.{$i}.posisi_rak" => "Rak \"{$rak}\" tidak valid untuk perusahaan \"{$companyName}\".",
                    ]);
                }
            }
        }

        return DB::transaction(function () use ($request, $rows) {
            if ($request->filled('company_id')) {
                $company = Company::query()->findOrFail((int) $request->company_id);
                if ($company->name !== $request->company_name) {
                    $company->update(['name' => $request->company_name]);
                }
            } else {
                $company = Company::query()->firstOrCreate(['name' => $request->company_name]);
            }

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

            $companyBarcode = CompanyBarcode::query()->firstOrCreate(
                ['company_id' => $company->id],
                ['barcode_id' => 'CB-'.$company->id.'-'.uniqid()]
            );

            return redirect()->route('company-barcodes.show', $companyBarcode)
                ->with('success', 'Barcode perusahaan berhasil dibuat.');
        });
    }

    public function importForm()
    {
        return view('company-barcodes.import');
    }

    public function importTemplate()
    {
        return InventorySpreadsheet::downloadCompanyTemplate();
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
        $result = InventorySpreadsheet::importCompanyFromMatrix($matrix);

        if (count($result['errors']) > 0) {
            return back()->with('import_errors', $result['errors']);
        }

        return redirect()->route('company-barcodes.index')
            ->with('success', $result['message'] ?? 'Import selesai.');
    }

    public function show(CompanyBarcode $companyBarcode)
    {
        $companyBarcode->load([
            'company.companyItems.item.operatorMobil',
            'company.companyItems.item.pengirim',
            'company.companyItems.item.operatorForklift',
        ]);
        $scanUrl = ScanUrl::forBarcode($companyBarcode->barcode_id);
        $barcodeSvg = BarcodeQrCodes::code128SvgForScan($companyBarcode->barcode_id);
        $qrCodeSvg = BarcodeQrCodes::qrSvgForScan($companyBarcode->barcode_id);

        return view('company-barcodes.show', compact('companyBarcode', 'barcodeSvg', 'qrCodeSvg', 'scanUrl'));
    }

    public function edit(CompanyBarcode $companyBarcode)
    {
        $companyBarcode->load([
            'company.companyItems.item.operatorMobil',
            'company.companyItems.item.pengirim',
            'company.companyItems.item.operatorForklift',
        ]);

        return view('company-barcodes.edit', compact('companyBarcode'));
    }

    public function update(Request $request, CompanyBarcode $companyBarcode)
    {
        $company = $companyBarcode->company;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.company_item_id' => [
                'nullable',
                Rule::exists('company_items', 'id')->where('company_id', $company->id),
            ],
            'items.*.part_name' => 'nullable|string|max:255',
            'items.*.code' => 'nullable|string|max:255',
            'items.*.qty' => 'nullable|integer|min:0',
            'items.*.posisi_rak' => 'nullable|string|max:255',
            'items.*.tingkat' => 'nullable|string|max:255',
            'items.*.operator_mobil_id' => 'nullable|exists:employees,id',
            'items.*.pengirim_id' => 'nullable|exists:employees,id',
            'items.*.operator_forklift_id' => 'nullable|exists:employees,id',
        ]);

        $rows = collect($validated['items'])->filter(fn ($r) => (int) ($r['qty'] ?? 0) > 0);
        if ($rows->isEmpty()) {
            return back()->withInput()->withErrors(['items' => 'Isi minimal satu barang dengan qty lebih dari 0.']);
        }

        $companyName = (string) $validated['company_name'];
        $allowedRak = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower(trim($companyName))])
            ->pluck('code')
            ->map(fn ($v) => (string) $v)
            ->all();

        if (count($allowedRak) > 0) {
            foreach ($rows as $i => $row) {
                $rak = isset($row['posisi_rak']) ? trim((string) $row['posisi_rak']) : '';
                if ($rak !== '' && ! in_array($rak, $allowedRak, true)) {
                    return back()->withInput()->withErrors([
                        "items.{$i}.posisi_rak" => "Rak \"{$rak}\" tidak valid untuk perusahaan \"{$companyName}\".",
                    ]);
                }
            }
        }

        $keptCiIds = $rows->pluck('company_item_id')->filter()->map(fn ($id) => (int) $id)->values()->all();

        DB::transaction(function () use ($company, $validated, $rows, $keptCiIds) {
            $company->update(['name' => $validated['company_name']]);

            $toRemoveQuery = CompanyItem::where('company_id', $company->id);
            if (count($keptCiIds) > 0) {
                $toRemoveQuery->whereNotIn('id', $keptCiIds);
            }
            $toRemove = $toRemoveQuery->get();

            foreach ($toRemove as $ci) {
                $item = $ci->item;
                if ($item->itemBarcodes()->exists()) {
                    throw ValidationException::withMessages([
                        'items' => 'Tidak dapat menghapus baris barang yang memiliki barcode FG. Hapus barcode barang terlebih dahulu.',
                    ]);
                }
                $ci->delete();
                $this->deleteOrphanItemIfUnused($item);
            }

            foreach ($rows as $row) {
                $qty = (int) $row['qty'];
                $opMob = isset($row['operator_mobil_id']) && $row['operator_mobil_id'] !== '' ? (int) $row['operator_mobil_id'] : null;
                $opPeng = isset($row['pengirim_id']) && $row['pengirim_id'] !== '' ? (int) $row['pengirim_id'] : null;
                $opFork = isset($row['operator_forklift_id']) && $row['operator_forklift_id'] !== '' ? (int) $row['operator_forklift_id'] : null;

                if (! empty($row['company_item_id'])) {
                    $ci = CompanyItem::where('company_id', $company->id)->where('id', $row['company_item_id'])->firstOrFail();
                    $item = $ci->item;
                    $code = isset($row['code']) && $row['code'] !== '' ? $row['code'] : $item->code;
                    if ($code === null || $code === '') {
                        $code = 'CB-'.$company->id.'-'.uniqid();
                    }
                    $item->update([
                        'operator_mobil_id' => $opMob,
                        'pengirim_id' => $opPeng,
                        'operator_forklift_id' => $opFork,
                        'part_name' => isset($row['part_name']) && $row['part_name'] !== '' ? $row['part_name'] : null,
                        'code' => $code,
                    ]);
                    $ci->update([
                        'qty' => $qty,
                        'posisi_rak' => isset($row['posisi_rak']) && $row['posisi_rak'] !== '' ? $row['posisi_rak'] : null,
                        'tingkat' => isset($row['tingkat']) && $row['tingkat'] !== '' ? $row['tingkat'] : null,
                    ]);
                } else {
                    $code = isset($row['code']) && $row['code'] !== '' ? $row['code'] : null;
                    if ($code === null) {
                        $code = 'CB-'.$company->id.'-'.uniqid();
                    }

                    $item = Item::create([
                        'company_id' => $company->id,
                        'operator_mobil_id' => $opMob,
                        'pengirim_id' => $opPeng,
                        'operator_forklift_id' => $opFork,
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
            }
        });

        return redirect()->route('company-barcodes.show', $companyBarcode)
            ->with('success', 'Data perusahaan diperbarui.');
    }

    public function destroy(CompanyBarcode $companyBarcode)
    {
        $company = $companyBarcode->company;

        $hasFg = Item::where('company_id', $company->id)->whereHas('itemBarcodes')->exists();
        if ($hasFg) {
            return redirect()->route('company-barcodes.index')
                ->with('error', 'Tidak dapat menghapus perusahaan: masih ada barcode barang (FG) yang menggunakan perusahaan ini.');
        }

        DB::transaction(function () use ($company) {
            $itemIds = Item::where('company_id', $company->id)->pluck('id');
            ItemBarcode::whereIn('item_id', $itemIds)->delete();
            ItemReceiving::whereIn('item_id', $itemIds)->delete();
            CompanyBarcode::where('company_id', $company->id)->delete();
            CompanyItem::where('company_id', $company->id)->delete();
            Item::whereIn('id', $itemIds)->delete();
            $company->delete();
        });

        return redirect()->route('company-barcodes.index')
            ->with('success', 'Data perusahaan dan barcode terkait dihapus.');
    }

    public function destroyCompany($id)
    {
        $company = Company::findOrFail($id);

        $hasFg = Item::where('company_id', $company->id)->whereHas('itemBarcodes')->exists();
        if ($hasFg) {
            return redirect()->route('company-barcodes.index')
                ->with('error', 'Tidak dapat menghapus perusahaan: masih ada barcode barang (FG) yang menggunakan perusahaan ini.');
        }

        DB::transaction(function () use ($company) {
            $itemIds = Item::where('company_id', $company->id)->pluck('id');
            ItemBarcode::whereIn('item_id', $itemIds)->delete();
            ItemReceiving::whereIn('item_id', $itemIds)->delete();
            CompanyBarcode::where('company_id', $company->id)->delete();
            CompanyItem::where('company_id', $company->id)->delete();
            Item::whereIn('id', $itemIds)->delete();
            $company->delete();
        });

        return redirect()->route('company-barcodes.index')
            ->with('success', 'Data perusahaan dihapus.');
    }

    private function deleteOrphanItemIfUnused(Item $item): void
    {
        $item->refresh();
        if ($item->companyItems()->exists() || $item->itemBarcodes()->exists() || $item->itemReceivings()->exists()) {
            return;
        }
        $item->delete();
    }
}
