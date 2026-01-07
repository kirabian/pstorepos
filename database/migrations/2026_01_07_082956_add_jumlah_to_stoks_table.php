<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stoks', function (Blueprint $table) {
            // Menambah kolom jumlah (stok angka), default 1, tidak boleh minus (unsigned)
            $table->unsignedInteger('jumlah')->default(1)->after('imei');
        });
    }

    public function down()
    {
        Schema::table('stoks', function (Blueprint $table) {
            $table->dropColumn('jumlah');
        });
    }
};