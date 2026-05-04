<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raks', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('id');
        });

        Schema::table('raks', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });

        Schema::table('raks', function (Blueprint $table) {
            $table->unique(['company_name', 'code']);
        });
    }

    public function down(): void
    {
        Schema::table('raks', function (Blueprint $table) {
            $table->dropUnique(['company_name', 'code']);
        });

        Schema::table('raks', function (Blueprint $table) {
            $table->unique('code');
        });

        Schema::table('raks', function (Blueprint $table) {
            $table->dropColumn('company_name');
        });
    }
};

