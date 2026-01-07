<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branch_user', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User (Role Audit)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Relasi ke Cabang
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade'); // Pastikan nama tabel cabang Anda 'cabangs'
            
            $table->timestamps();

            // Mencegah duplikasi (1 user tidak bisa punya 2 akses ke cabang yang sama)
            $table->unique(['user_id', 'cabang_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_user');
    }
};