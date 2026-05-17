<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// 1. Perbaikan: Import DB dicantumkan jika memang dibutuhkan
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unique_items', function (Blueprint $table) {
            $table->id();
            
            // 3. Perbaikan: Mengubah 'items' menjadi 'item_id' dan memilih salah satu onDelete
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // 4. Perbaikan: Mengatur posisi kolom langsung lewat urutan baris (Menghapus ->after())
            $table->foreignId('operator_mobil_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('pengirim_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('operator_forklift_id')->nullable()->constrained('employees')->nullOnDelete();
            
            $table->string('customer')->nullable();
            $table->string('part_name')->nullable();
            $table->string('part_number')->nullable();
            $table->string('model')->nullable();
            
            $table->decimal('berat', 10, 2)->nullable();
            $table->integer('berat_packaging_gram')->nullable();
            $table->integer('berat_per_pcs_gram')->nullable();
            
            $table->integer('qty')->default(0);
            $table->integer('static_qty')->default(0);
            $table->integer('dynamic_qty')->default(0);
            $table->integer('qty_sub_pack')->nullable();
            
            $table->string('inspector_name')->nullable();
            $table->string('checker_name')->nullable();
            
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
    }

    public function down(): void
    {
         Schema::dropIfExists('unique_items');
    }
};