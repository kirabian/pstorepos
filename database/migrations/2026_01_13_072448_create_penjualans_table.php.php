<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            // Relasi ke User (Sales)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Relasi ke Cabang (Sales ada di cabang mana)
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade');
            // Relasi ke Stok (Bisa null jika yang dijual adalah Jasa / Non-Stok)
            $table->foreignId('stok_id')->nullable()->constrained('stoks')->onDelete('set null');
            
            // Detail Transaksi
            $table->string('tipe_penjualan'); // Unit / Jasa / Aksesoris
            $table->string('imei_terjual')->nullable(); // Disimpan text jaga-jaga stok dihapus
            $table->string('nama_produk'); // Merk + Tipe
            
            // Biodata Customer
            $table->string('nama_customer');
            $table->string('nomor_wa');
            $table->string('foto_bukti_transaksi')->nullable(); // Path gambar
            
            // Keuangan
            $table->decimal('harga_jual_real', 15, 2);
            $table->text('catatan')->nullable();

            // Audit
            $table->enum('status_audit', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('audited_by')->nullable()->constrained('users'); // Siapa yang verifikasi
            $table->timestamp('audited_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualans');
    }
};