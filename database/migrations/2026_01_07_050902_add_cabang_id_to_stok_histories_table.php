<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Jalankan: php artisan make:migration add_cabang_id_to_stok_histories_table
    public function up()
    {
        Schema::table('stok_histories', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_histories', function (Blueprint $table) {
            //
        });
    }
};
