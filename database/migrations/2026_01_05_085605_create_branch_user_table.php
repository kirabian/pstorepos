<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus tabel lama jika ada biar bersih
        Schema::dropIfExists('branch_user');

        Schema::create('branch_user', function (Blueprint $table) {
            $table->id();
            
            // PENTING: Gunakan 'cabang_id' agar sesuai dengan Model & Seeder
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade'); 
            
            $table->timestamps();

            // Mencegah duplikat data
            $table->unique(['user_id', 'cabang_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_user');
    }
};