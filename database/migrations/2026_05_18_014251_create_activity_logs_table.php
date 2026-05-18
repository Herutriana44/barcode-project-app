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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Admin/User yang melakukan aksi
            $table->foreignId('employee_id')->nullable()->constrained(); // Karyawan yang sedang aktif
            $table->string('target_type'); // 'Barang', 'Perusahaan', 'Karyawan'
            $table->string('activity'); // 'Buat', 'Edit', 'Hapus'
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
