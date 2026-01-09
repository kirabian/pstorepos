<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Staff yang melakukan opname
            $table->foreignId('tipe_id')->constrained('tipes')->onDelete('cascade'); // Produk
            
            $table->integer('stok_sistem'); // Stok menurut komputer sebelum opname
            $table->integer('stok_fisik');  // Stok nyata yang dihitung
            $table->integer('selisih');     // fisik - sistem (bisa minus jika hilang)
            
            $table->text('keterangan')->nullable(); // Alasan selisih
            $table->date('tanggal_opname');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};