<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('phone');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('nip');
            $table->string('photo_path')->nullable()->after('jabatan');
        });

        foreach (DB::table('employees')->select('id', 'nip')->get() as $row) {
            if ($row->nip === null || $row->nip === '') {
                DB::table('employees')->where('id', $row->id)->update(['nip' => 'LEGACY-'.$row->id]);
            }
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->unique('nip');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['nip']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['jabatan', 'photo_path']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('nip');
        });
    }
};
