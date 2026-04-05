<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\FifoStockService;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function create()
    {
        $companies = Company::orderBy('name')->get();

        return view('stock-out.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'scope' => 'required|in:item,company_item',
            'qty' => 'required|integer|min:1',
            'part_number' => 'nullable|string|max:255',
            'part_name' => 'nullable|string|max:255',
        ]);

        try {
            if ($validated['scope'] === 'item') {
                FifoStockService::deductFromItems(
                    (int) $validated['company_id'],
                    (int) $validated['qty'],
                    $validated['part_number'] ?? null,
                    $validated['part_name'] ?? null
                );
            } else {
                FifoStockService::deductFromCompanyItems(
                    (int) $validated['company_id'],
                    (int) $validated['qty'],
                    $validated['part_name'] ?? null
                );
            }
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['qty' => $e->getMessage()]);
        }

        return redirect()->route('stock-out.create')
            ->with('success', 'Pengeluaran FIFO berhasil dicatat.');
    }
}
