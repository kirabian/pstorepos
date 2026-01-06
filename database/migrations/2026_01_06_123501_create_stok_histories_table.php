<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_histories', function (Blueprint $table) {
            $table->id();
            $table->string('imei')->index(); // Index biar pencarian cepat
            $table->string('status'); // Contoh: "Stok Masuk", "Terjual", "Mutasi"
            $table->text('keterangan')->nullable(); // Detail: "Masuk dari Supplier A"
            $table->foreignId('user_id')->constrained('users'); // Siapa yang input
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_histories');
    }
};