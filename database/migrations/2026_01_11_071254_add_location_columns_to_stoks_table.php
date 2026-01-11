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
        Schema::table('stoks', function (Blueprint $table) {
            // Menambahkan kolom lokasi stok
            $table->unsignedBigInteger('cabang_id')->nullable()->after('id');
            $table->unsignedBigInteger('gudang_id')->nullable()->after('cabang_id');
            $table->unsignedBigInteger('distributor_id')->nullable()->after('gudang_id');
            
            // Menambahkan status & nama barang manual (jika tidak pakai relasi tipe)
            $table->string('nama_barang')->nullable()->after('imei');
            $table->string('status')->default('ready')->after('kondisi'); // ready, terjual, keluar, rusak

            // Indexing agar query cepat
            $table->index(['cabang_id', 'status']);
            $table->index(['gudang_id', 'status']);
            $table->index(['distributor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stoks', function (Blueprint $table) {
            $table->dropColumn(['cabang_id', 'gudang_id', 'distributor_id', 'nama_barang', 'status']);
        });
    }
};