<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_shops', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko'); // Contoh: Shopee PStore Official
            $table->string('platform'); // Contoh: Shopee, Tokopedia, TikTok
            $table->text('url_toko')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_shops');
    }
};