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
            
            // 1. Cek & Buat cabang_id
            if (!Schema::hasColumn('users', 'cabang_id')) {
                $table->foreignId('cabang_id')->nullable()->after('is_active');
            }

            // 2. Cek & Buat distributor_id
            if (!Schema::hasColumn('users', 'distributor_id')) {
                // Taruh setelah cabang_id jika ada, atau setelah is_active
                $after = Schema::hasColumn('users', 'cabang_id') ? 'cabang_id' : 'is_active';
                $table->foreignId('distributor_id')->nullable()->after($after);
            }

            // 3. Cek & Buat gudang_id (Ini yang sebelumnya error missing)
            if (!Schema::hasColumn('users', 'gudang_id')) {
                $after = Schema::hasColumn('users', 'distributor_id') ? 'distributor_id' : 'is_active';
                $table->foreignId('gudang_id')->nullable()->after($after);
            }

            // 4. Cek & Buat last_seen
            if (!Schema::hasColumn('users', 'last_seen')) {
                $after = Schema::hasColumn('users', 'gudang_id') ? 'gudang_id' : 'is_active';
                $table->timestamp('last_seen')->nullable()->after($after);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'cabang_id')) {
                $table->dropColumn('cabang_id');
            }
            if (Schema::hasColumn('users', 'distributor_id')) {
                $table->dropColumn('distributor_id');
            }
            if (Schema::hasColumn('users', 'gudang_id')) {
                $table->dropColumn('gudang_id');
            }
            if (Schema::hasColumn('users', 'last_seen')) {
                $table->dropColumn('last_seen');
            }
        });
    }
};