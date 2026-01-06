<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom uuid di brands
        if (!Schema::hasColumn('brands', 'uuid')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });
        }

        // 2. Generate UUID untuk brand yang sudah ada
        $brands = DB::table('brands')->whereNull('uuid')->get();
        foreach ($brands as $brand) {
            DB::table('brands')
                ->where('id', $brand->id)
                ->update(['uuid' => Str::uuid()]);
        }

        // 3. Buat kolom uuid di products sebagai sementara
        if (!Schema::hasColumn('products', 'brand_uuid')) {
            Schema::table('products', function (Blueprint $table) {
                $table->uuid('brand_uuid')->nullable()->after('brand_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('brand_uuid');
        });
    }
};