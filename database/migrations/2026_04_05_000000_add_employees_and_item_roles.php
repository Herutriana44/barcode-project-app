<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nip')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('operator_mobil_id')->nullable()->after('company_id')->constrained('employees')->nullOnDelete();
            $table->foreignId('pengirim_id')->nullable()->after('operator_mobil_id')->constrained('employees')->nullOnDelete();
            $table->foreignId('operator_forklift_id')->nullable()->after('pengirim_id')->constrained('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['operator_mobil_id']);
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['operator_forklift_id']);
            $table->dropColumn(['operator_mobil_id', 'pengirim_id', 'operator_forklift_id']);
        });

        Schema::dropIfExists('employees');
    }
};
