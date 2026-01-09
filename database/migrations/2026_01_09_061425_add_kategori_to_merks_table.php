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
        Schema::table('merks', function (Blueprint $table) {
            // Kita pakai JSON agar 1 Merk bisa punya banyak kategori
            // Contoh: ["imei", "non_imei"]
            $table->json('kategori')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('merks', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
