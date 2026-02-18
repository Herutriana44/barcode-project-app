<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
