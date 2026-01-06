<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stoks', function (Blueprint $table) {
            $table->id();
            // Relasi
            $table->foreignId('merk_id')->constrained('merks')->onDelete('cascade');
            $table->foreignId('tipe_id')->constrained('tipes')->onDelete('cascade');
            
            // Data Unit
            $table->string('ram_storage'); // Contoh: "8/128"
            $table->enum('kondisi', ['Baru', 'Second']);
            $table->string('imei')->unique(); // IMEI wajib unik
            
            // Keuangan
            $table->bigInteger('harga_modal')->nullable()->default(0);
            $table->bigInteger('harga_jual'); // SRP
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};