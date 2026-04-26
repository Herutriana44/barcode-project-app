<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('static_qty')->default(0)->after('qty');
            $table->integer('dynamic_qty')->default(0)->after('static_qty');

            $table->integer('berat_packaging_gram')->nullable()->after('berat');
            $table->integer('berat_per_pcs_gram')->nullable()->after('berat_packaging_gram');
            $table->integer('qty_sub_pack')->nullable()->after('dynamic_qty');
        });

        // Backfill: qty lama masuk ke static & dynamic.
        DB::table('items')->update([
            'static_qty' => DB::raw('COALESCE(qty, 0)'),
            'dynamic_qty' => DB::raw('COALESCE(qty, 0)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'static_qty',
                'dynamic_qty',
                'berat_packaging_gram',
                'berat_per_pcs_gram',
                'qty_sub_pack',
            ]);
        });
    }
};

