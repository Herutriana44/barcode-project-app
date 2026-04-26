<?php

use App\Http\Controllers\CompanyBarcodeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ItemBarcodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\StockOutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('guest-dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Item Barcodes (Modul 1 - Barcode Barang)
    Route::get('item-barcodes/import/template', [ItemBarcodeController::class, 'importTemplate'])->name('item-barcodes.import.template');
    Route::get('item-barcodes/import', [ItemBarcodeController::class, 'importForm'])->name('item-barcodes.import');
    Route::post('item-barcodes/import', [ItemBarcodeController::class, 'importStore'])->name('item-barcodes.import.store');
    Route::get('item-barcodes/labels', [ItemBarcodeController::class, 'labels'])->name('item-barcodes.labels');
    Route::get('item-barcodes/{itemBarcode}/label-isi', [ItemBarcodeController::class, 'labelIsi'])->name('item-barcodes.label-isi');
    Route::resource('item-barcodes', ItemBarcodeController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Karyawan
    Route::get('employees/{employee}/id-card', [EmployeeController::class, 'idCard'])->name('employees.id-card');
    Route::resource('employees', EmployeeController::class);

    // Pengeluaran FIFO
    Route::get('stock-out', [StockOutController::class, 'create'])->name('stock-out.create');
    Route::post('stock-out', [StockOutController::class, 'store'])->name('stock-out.store');

    // Company Barcodes (Modul 2 - Barcode Perusahaan)
    Route::get('company-barcodes/import/template', [CompanyBarcodeController::class, 'importTemplate'])->name('company-barcodes.import.template');
    Route::get('company-barcodes/import', [CompanyBarcodeController::class, 'importForm'])->name('company-barcodes.import');
    Route::post('company-barcodes/import', [CompanyBarcodeController::class, 'importStore'])->name('company-barcodes.import.store');
    Route::resource('company-barcodes', CompanyBarcodeController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Scan
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/{barcode_id}/movement', [ScanController::class, 'storeMovement'])->name('scan.movement')->where('barcode_id', '[^/]+');
    Route::get('/scan/{barcode_id}', [ScanController::class, 'show'])->name('scan.show')->where('barcode_id', '[^/]+');
});

require __DIR__.'/auth.php';
