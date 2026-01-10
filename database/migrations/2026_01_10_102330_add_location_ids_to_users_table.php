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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom baru setelah kolom 'is_active' agar rapi
            $table->foreignId('cabang_id')->nullable()->after('is_active');
            $table->foreignId('distributor_id')->nullable()->after('cabang_id');
            $table->foreignId('gudang_id')->nullable()->after('distributor_id'); // Ini yang bikin error tadi
            
            $table->timestamp('last_seen')->nullable()->after('gudang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jika di-rollback
            $table->dropColumn([
                'cabang_id', 
                'distributor_id', 
                'gudang_id', 
                'last_seen'
            ]);
        });
    }
};