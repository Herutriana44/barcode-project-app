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
    Route::get('item-barcodes/labels', [ItemBarcodeController::class, 'labels'])->name('item-barcodes.labels');
    Route::resource('item-barcodes', ItemBarcodeController::class)->only(['index', 'create', 'store', 'show']);

    // Karyawan
    Route::resource('employees', EmployeeController::class)->except(['show']);

    // Pengeluaran FIFO
    Route::get('stock-out', [StockOutController::class, 'create'])->name('stock-out.create');
    Route::post('stock-out', [StockOutController::class, 'store'])->name('stock-out.store');

    // Company Barcodes (Modul 2 - Barcode Perusahaan)
    Route::resource('company-barcodes', CompanyBarcodeController::class)->only(['index', 'create', 'store', 'show']);

    // Scan
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::get('/scan/{barcode_id}', [ScanController::class, 'show'])->name('scan.show')->where('barcode_id', '[^/]+');
});

require __DIR__.'/auth.php';
