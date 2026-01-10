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
       Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('nama_lengkap');
    $table->string('idlogin')->unique(); // ID unik untuk login
    $table->string('email')->unique();
    $table->string('password');
    $table->date('tanggal_lahir'); // Field ultah
    $table->enum('role', [
        'superadmin', 'adminproduk', 'analis', 'audit', 'security', 'leader', 'sales',
        'distributor', 'inventory_staff', 'gudang', 'toko_offline', 'toko_online'
    ]);
    $table->rememberToken();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
