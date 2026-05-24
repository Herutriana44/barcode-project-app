<?php

use App\Http\Controllers\CompanyBarcodeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ItemBarcodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ScanEmployeeController;
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

use App\Http\Controllers\ActivityLogController;

// ... (existing imports)

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['admin'])->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/activity-logs/{activityLog}/edit', [ActivityLogController::class, 'edit'])->name('activity-logs.edit');
        Route::patch('/activity-logs/{activityLog}', [ActivityLogController::class, 'update'])->name('activity-logs.update');
        Route::delete('/activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Scan Karyawan (tidak butuh employee aktif — ini entry point-nya) ───
    Route::get('/scan-employee', [ScanEmployeeController::class, 'index'])->name('scan-employee.index');
    Route::post('/scan-employee', [ScanEmployeeController::class, 'store'])->name('scan-employee.store');
    Route::delete('/scan-employee/session', [ScanEmployeeController::class, 'destroy'])->name('scan-employee.destroy');

    // ─── Semua fitur utama wajib ada karyawan aktif ───────────────────────
    Route::middleware(['employee.active'])->group(function () {

        // Item Barcodes (Modul 1 - Barcode Barang)
        Route::get('item-barcodes/import/template', [ItemBarcodeController::class, 'importTemplate'])->name('item-barcodes.import.template');
        Route::get('item-barcodes/import', [ItemBarcodeController::class, 'importForm'])->name('item-barcodes.import');
        Route::post('item-barcodes/import', [ItemBarcodeController::class, 'importStore'])->name('item-barcodes.import.store');
        Route::get('item-barcodes/labels', [ItemBarcodeController::class, 'labels'])->name('item-barcodes.labels');
        Route::get('item-barcodes/{itemBarcode}/label-print-a4', [ItemBarcodeController::class, 'labelPrintA4'])->name('item-barcodes.label-print-a4');
        Route::get('item-barcodes/{itemBarcode}/label-isi', [ItemBarcodeController::class, 'labelIsi'])->name('item-barcodes.label-isi');
        Route::get('item-barcodes/{itemBarcode}/label-per-box', [ItemBarcodeController::class, 'labelPerBox'])->name('item-barcodes.label-per-box');
        Route::patch('item-barcodes/{itemBarcode}/checker', [ItemBarcodeController::class, 'updateChecker'])->name('item-barcodes.checker');
        Route::get('item-barcodes/{item_barcode}/download-qr', [ItemBarcodeController::class, 'downloadQr'])->name('item-barcodes.download-qr');

        // Unique Items
        Route::post('item-barcodes/{itemBarcode}/unique-items', [ItemBarcodeController::class, 'storeUniqueItem'])->name('item-barcodes.unique-items.store');
        Route::post('item-barcodes/{itemBarcode}/unique-items/generate-bulk', [ItemBarcodeController::class, 'generateBulkUniqueItems'])->name('item-barcodes.unique-items.generate-bulk');
        Route::post('item-barcodes/{itemBarcode}/unique-items/bulk-print', [ItemBarcodeController::class, 'bulkPrintUniqueItems'])->name('item-barcodes.unique-items.bulk-print');
        Route::post('item-barcodes/{itemBarcode}/unique-items/bulk-destroy', [ItemBarcodeController::class, 'bulkDestroyUniqueItems'])->name('item-barcodes.unique-items.bulk-destroy');
        Route::post('item-barcodes/{itemBarcode}/unique-items/bulk-duplicate', [ItemBarcodeController::class, 'bulkDuplicateUniqueItems'])->name('item-barcodes.unique-items.bulk-duplicate');
        Route::post('item-barcodes/{itemBarcode}/unique-items/bulk-keluar', [ItemBarcodeController::class, 'bulkUpdateStatusKeluar'])->name('item-barcodes.unique-items.bulk-keluar');
        Route::patch('item-barcodes/{itemBarcode}/unique-items/{uniqueItem}', [ItemBarcodeController::class, 'updateUniqueItem'])->name('item-barcodes.unique-items.update');
        Route::delete('item-barcodes/{itemBarcode}/unique-items/{uniqueItem}', [ItemBarcodeController::class, 'destroyUniqueItem'])->name('item-barcodes.unique-items.destroy');
        Route::get('item-barcodes/{itemBarcode}/unique-items/{uniqueItem}/print', [ItemBarcodeController::class, 'printUniqueItemLabel'])->name('item-barcodes.unique-items.print');
        Route::get('item-barcodes/{itemBarcode}/unique-items/print-all', [ItemBarcodeController::class, 'printAllUniqueItemLabels'])->name('item-barcodes.unique-items.print-all');


        Route::resource('item-barcodes', ItemBarcodeController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Karyawan
        Route::get('employees/{employee}/photo', [EmployeeController::class, 'photo'])->name('employees.photo');
        Route::get('employees/{employee}/id-card', [EmployeeController::class, 'idCard'])->name('employees.id-card');
        Route::get('employees/{employee}/download-qr', [EmployeeController::class, 'downloadQr'])->name('employees.download-qr');
        Route::get('/employees/download-template', [EmployeeController::class, 'downloadTemplateIdCard'])->name('employees.download-template');
        Route::resource('employees', EmployeeController::class);

        // Pengeluaran FIFO
        Route::get('stock-out', [StockOutController::class, 'create'])->name('stock-out.create');
        Route::post('stock-out', [StockOutController::class, 'store'])->name('stock-out.store');

        // Company Barcodes (Modul 2 - Barcode Perusahaan)
        Route::get('company-barcodes/import/template', [CompanyBarcodeController::class, 'importTemplate'])->name('company-barcodes.import.template');
        Route::get('company-barcodes/import', [CompanyBarcodeController::class, 'importForm'])->name('company-barcodes.import');
        Route::post('company-barcodes/import', [CompanyBarcodeController::class, 'importStore'])->name('company-barcodes.import.store');
        Route::post('company-barcodes/destroy-company/{id}', [CompanyBarcodeController::class, 'destroyCompany'])->name('company-barcodes.destroy-company');
        Route::get('company-barcodes/{company_barcode}/download-qr', [CompanyBarcodeController::class, 'downloadQr'])->name('company-barcodes.download-qr');
        Route::resource('company-barcodes', CompanyBarcodeController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Rak options (untuk dropdown per perusahaan/customer)
        Route::get('raks/options', [RakController::class, 'options'])->name('raks.options');
        Route::get('raks/all-options', [RakController::class, 'allOptions'])->name('raks.all-options');

        // Scan barang / perusahaan / karyawan lain
        $scanPrefix = env('SCAN_URL_MODE') === 'public' ? 'public/scan' : 'scan';
        Route::get('/'.$scanPrefix, [ScanController::class, 'index'])->name('scan.index');
        Route::post('/'.$scanPrefix.'/{barcode_id}/movement', [ScanController::class, 'storeMovement'])->name('scan.movement')->where('barcode_id', '[^/]+');
        Route::get('/'.$scanPrefix.'/{barcode_id}', [ScanController::class, 'show'])->name('scan.show')->where('barcode_id', '[^/]+');

    }); // end employee.active
});

require __DIR__.'/auth.php';
