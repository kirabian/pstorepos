<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk menyimpan List IMEI per Varian
        Schema::create('product_imeis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')
                  ->constrained('product_variants')
                  ->cascadeOnDelete();
            
            $table->string('imei', 20)->unique(); // IMEI biasanya 15 digit
            $table->enum('status', ['available', 'sold', 'returned'])->default('available');
            $table->timestamps();
        });

        // Update tabel products jika kolom description belum ada (opsional, untuk jaga-jaga)
        if (!Schema::hasColumn('products', 'description')) {
            Schema::table('products', function (Blueprint $table) {
                $table->text('description')->nullable()->after('category_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_imeis');
    }
};