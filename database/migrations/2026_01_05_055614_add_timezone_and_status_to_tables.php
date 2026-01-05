<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cabangs', function (Blueprint $table) {
            // Menambah zona waktu (WIB, WITA, WIT)
            $table->string('timezone')->default('Asia/Jakarta');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen')->nullable();
            $table->boolean('is_online')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
