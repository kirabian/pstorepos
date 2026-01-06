<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipes', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel Merks
            $table->foreignId('merk_id')->constrained('merks')->onDelete('cascade');
            $table->string('nama');
            // Menyimpan array RAM/ROM (contoh: ["8/128", "8/256"])
            $table->json('ram_storage')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipes');
    }
};