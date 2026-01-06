<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipes', function (Blueprint $table) {
            // Kita tambahkan kolom ENUM 'jenis' setelah kolom nama
            $table->enum('jenis', ['imei', 'non_imei', 'jasa'])
                  ->default('imei') // Default HP (IMEI)
                  ->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('tipes', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};