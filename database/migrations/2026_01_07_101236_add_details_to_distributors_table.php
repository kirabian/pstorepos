<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('distributors', function (Blueprint $table) {
            // Cek dulu biar gak error kalau kolom sudah ada
            if (!Schema::hasColumn('distributors', 'lokasi')) {
                $table->string('lokasi')->nullable()->after('nama_distributor');
            }
            if (!Schema::hasColumn('distributors', 'kontak')) {
                $table->string('kontak')->nullable()->after('lokasi');
            }
        });
    }

    public function down()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn(['lokasi', 'kontak']);
        });
    }
};