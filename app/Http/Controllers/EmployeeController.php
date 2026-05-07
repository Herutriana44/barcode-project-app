<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Support\BarcodeQrCodes;
use App\Support\EmployeeUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('employees', 'nip'),
            ],
            'departemen' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        Employee::create([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'departemen' => $validated['departemen'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'status' => $validated['status'] ?? null,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        $profileUrl = EmployeeUrl::forProfile($employee);
        $qrSvg = BarcodeQrCodes::qrSvgForEmployeeProfile($employee, 160, 6);
        $barcodeSvg = BarcodeQrCodes::code128SvgForEmployeeProfile($employee, 1, 40);

        return view('employees.show', compact('employee', 'profileUrl', 'qrSvg', 'barcodeSvg'));
    }

    public function photo(Employee $employee)
    {
        if (! $employee->photo_path) {
            abort(404);
        }

        return Storage::disk('public')->response($employee->photo_path);
    }

    /**
     * Cetak ID card / name tag 9 × 5,3 cm.
     */
    public function idCard(Employee $employee)
    {
        $profileUrl = EmployeeUrl::forProfile($employee);
        $qrSvg = BarcodeQrCodes::qrSvgForEmployeeProfile($employee, 72, 2);
        $barcodeSvg = BarcodeQrCodes::code128SvgForEmployeeProfile($employee, 1, 28);

        return view('employees.id-card', compact('employee', 'profileUrl', 'qrSvg', 'barcodeSvg'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('employees', 'nip')->ignore($employee->id),
            ],
            'departemen' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:4096',
        ]);

        $photoPath = $employee->photo_path;
        if ($request->hasFile('photo')) {
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        $employee->update([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'departemen' => $validated['departemen'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
            'status' => $validated['status'] ?? null,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan dihapus.');
    }
}
