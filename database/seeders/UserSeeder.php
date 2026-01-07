<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Distributor;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Data Master Cabang (Dummy)
        $cabangPusat = Cabang::create([
            'kode_cabang' => 'CB-001', 'nama_cabang' => 'PSTORE PUSAT (JAKARTA)', 'lokasi' => 'Jakarta', 'timezone' => 'Asia/Jakarta'
        ]);
        $cabangBdg = Cabang::create([
            'kode_cabang' => 'CB-002', 'nama_cabang' => 'PSTORE BANDUNG', 'lokasi' => 'Bandung', 'timezone' => 'Asia/Jakarta'
        ]);
        $cabangSby = Cabang::create([
            'kode_cabang' => 'CB-003', 'nama_cabang' => 'PSTORE SURABAYA', 'lokasi' => 'Surabaya', 'timezone' => 'Asia/Jakarta'
        ]);
        $cabangMks = Cabang::create([
            'kode_cabang' => 'CB-004', 'nama_cabang' => 'PSTORE MAKASSAR', 'lokasi' => 'Makassar', 'timezone' => 'Asia/Makassar'
        ]);

        // 2. Buat Data Master Distributor (Dummy)
        $dist = Distributor::create([
            'nama_distributor' => 'DISTRIBUTOR UTAMA', 'lokasi' => 'Jakarta', 'kontak' => '08123456789'
        ]);

        // 3. Buat User untuk Setiap Role
        $passwordDefault = Hash::make('password'); // Password seragam: 'password'

        // SUPERADMIN (Akses Semua)
        User::create([
            'nama_lengkap' => 'Super Admin PStore',
            'idlogin'      => 'superadmin',
            'email'        => 'superadmin@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'superadmin',
            'cabang_id'    => null,
            'is_active'    => true,
        ]);

        // ADMIN PRODUK (Akses Inventory)
        User::create([
            'nama_lengkap' => 'Admin Produk',
            'idlogin'      => 'adminproduk',
            'email'        => 'produk@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'adminproduk',
            'cabang_id'    => $cabangPusat->id, // Admin produk biasanya di pusat
            'is_active'    => true,
        ]);

        // ANALIST (Analisa Data)
        User::create([
            'nama_lengkap' => 'Tim Analis Data',
            'idlogin'      => 'analis',
            'email'        => 'analis@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'analis',
            'cabang_id'    => $cabangPusat->id,
            'is_active'    => true,
        ]);

        // AUDIT (Multi Cabang) - Penting!
        $userAudit = User::create([
            'nama_lengkap' => 'Tim Audit Internal',
            'idlogin'      => 'audit',
            'email'        => 'audit@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'audit',
            'cabang_id'    => null, // Audit tidak terikat 1 cabang di kolom ini
            'is_active'    => true,
        ]);
        // Audit ini memegang Cabang Pusat & Bandung
        $userAudit->branches()->attach([$cabangPusat->id, $cabangBdg->id]);

        // LEADER (Kepala Cabang)
        User::create([
            'nama_lengkap' => 'Leader Surabaya',
            'idlogin'      => 'leader_sby',
            'email'        => 'leader@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'leader',
            'cabang_id'    => $cabangSby->id,
            'is_active'    => true,
        ]);

        // DISTRIBUTOR (Mitra Luar)
        User::create([
            'nama_lengkap' => 'Mitra Distributor 1',
            'idlogin'      => 'distributor',
            'email'        => 'mitra@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'distributor',
            'distributor_id' => $dist->id,
            'cabang_id'    => null,
            'is_active'    => true,
        ]);

        // SALES / KASIR
        User::create([
            'nama_lengkap' => 'Sales Counter 1',
            'idlogin'      => 'sales',
            'email'        => 'sales@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'sales',
            'cabang_id'    => $cabangPusat->id,
            'is_active'    => true,
        ]);

        // GUDANG
        User::create([
            'nama_lengkap' => 'Staf Gudang',
            'idlogin'      => 'gudang',
            'email'        => 'gudang@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'gudang',
            'cabang_id'    => $cabangPusat->id,
            'is_active'    => true,
        ]);

        // SECURITY
        User::create([
            'nama_lengkap' => 'Security Pos 1',
            'idlogin'      => 'security',
            'email'        => 'security@pstore.com',
            'password'     => $passwordDefault,
            'role'         => 'security',
            'cabang_id'    => $cabangPusat->id,
            'is_active'    => true,
        ]);
    }
}