<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fitur Scan Karyawan:
 * 1. Tabel log sesi scan karyawan (employee_scan_sessions)
 * 2. Kolom scanned_by_employee_id di items, item_barcodes, company_barcodes
 */
return new class extends Migration
{
    public function up(): void
    {
        // Log setiap kali karyawan scan badge-nya
        Schema::create('employee_scan_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamp('scanned_at');
            $table->timestamps();
        });

        // Relasi: siapa karyawan yang sedang aktif saat data barang dibuat/diubah
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('scanned_by_employee_id')
                ->nullable()
                ->after('operator_forklift_id')
                ->constrained('employees')
                ->nullOnDelete();
        });

        Schema::table('item_barcodes', function (Blueprint $table) {
            $table->foreignId('scanned_by_employee_id')
                ->nullable()
                ->after('barcode_id')
                ->constrained('employees')
                ->nullOnDelete();
        });

        Schema::table('company_barcodes', function (Blueprint $table) {
            $table->foreignId('scanned_by_employee_id')
                ->nullable()
                ->after('barcode_id')
                ->constrained('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('company_barcodes', function (Blueprint $table) {
            $table->dropForeign(['scanned_by_employee_id']);
            $table->dropColumn('scanned_by_employee_id');
        });

        Schema::table('item_barcodes', function (Blueprint $table) {
            $table->dropForeign(['scanned_by_employee_id']);
            $table->dropColumn('scanned_by_employee_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['scanned_by_employee_id']);
            $table->dropColumn('scanned_by_employee_id');
        });

        Schema::dropIfExists('employee_scan_sessions');
    }
};
