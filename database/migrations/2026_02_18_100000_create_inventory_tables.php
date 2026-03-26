<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Single migration so table creation order is guaranteed (FK-safe on MySQL).
 * Do not split into multiple files with the same timestamp — alphabetical order breaks FKs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('customer')->nullable();
            $table->string('part_name')->nullable();
            $table->string('part_number')->nullable();
            $table->string('model')->nullable();
            $table->decimal('berat', 10, 2)->nullable();
            $table->integer('qty')->default(0);
            $table->string('inspector_name')->nullable();
            $table->date('tgl_produksi')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->string('code')->nullable();
            $table->string('posisi_rak')->nullable();
            $table->string('tingkat')->nullable();
            $table->string('ukuran_material')->nullable();
            $table->enum('jenis_bahan', ['SPCC', 'SESE'])->nullable();
            $table->integer('quantity_material')->nullable();
            $table->string('no_surat_jalan_material')->nullable();
            $table->date('tanggal_terima_material')->nullable();
            $table->timestamps();
        });

        Schema::create('item_receivings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('transfer_slip_no')->nullable();
            $table->date('tanggal_terima_fg')->nullable();
            $table->integer('jumlah_box')->default(0);
            $table->timestamps();
        });

        Schema::create('company_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('qty')->default(0);
            $table->string('posisi_rak')->nullable();
            $table->string('tingkat')->nullable();
            $table->timestamps();
        });

        Schema::create('item_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_receiving_id')->constrained()->onDelete('cascade');
            $table->string('barcode_id')->unique();
            $table->timestamps();
        });

        Schema::create('company_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('barcode_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_barcodes');
        Schema::dropIfExists('item_barcodes');
        Schema::dropIfExists('company_items');
        Schema::dropIfExists('item_receivings');
        Schema::dropIfExists('items');
        Schema::dropIfExists('companies');
    }
};
