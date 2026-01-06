<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Brand (Merk)
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Products (Tipe HP)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: iPhone 15 Pro
            $table->string('category')->default('Handphone'); // Handphone, Aksesoris, Jasa
            $table->timestamps();
        });

        // 3. Tabel Varian (Detail Spesifikasi & Harga)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('ram')->nullable();     // 8GB
            $table->string('storage')->nullable(); // 256GB
            $table->string('color')->nullable();   // Deep Purple
            $table->string('condition')->default('Baru'); // Baru, Second, BNOB
            $table->decimal('cost_price', 15, 2)->default(0); // Modal
            $table->decimal('srp_price', 15, 2)->default(0);  // Jual
            $table->integer('stock')->default(0); // Total Stok
            $table->timestamps();
        });

        // 4. Tabel IMEI (Unit Unik)
        Schema::create('product_imeis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('imei')->unique();
            $table->enum('status', ['available', 'sold', 'refund'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_imeis');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
    }
};