<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_company_barcode_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('barcode_id')->constrained('item_barcodes')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->string('posisi_rak')->nullable();
            $table->string('tingkat')->nullable();
            $table->timestamps();

            $table->unique(['item_id', 'company_id', 'barcode_id']);
            $table->index(['company_id', 'barcode_id']);
            $table->index(['posisi_rak']);
            $table->index(['tingkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_company_barcode_pivot');
    }
};