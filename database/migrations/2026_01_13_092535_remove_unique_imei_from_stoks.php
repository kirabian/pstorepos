<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('stoks', function (Blueprint $table) {
        $table->dropUnique('stoks_imei_unique'); // Sesuaikan nama index jika beda
        // $table->index('imei'); // Opsional: ganti jadi index biasa biar cepat searchnya
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stoks', function (Blueprint $table) {
            //
        });
    }
};
