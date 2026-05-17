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
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
         Schema::dropIfExists('unique_items');
    }
};