<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin (Akses Penuh)
        User::create([
            'nama_lengkap'  => 'Fabian Super Admin',
            'idlogin'       => 'superbian',
            'email'         => 'superadmin@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '1995-05-20',
            'role'          => 'superadmin',
        ]);

        // 2. Admin Produk
        User::create([
            'nama_lengkap'  => 'Admin Produk PSTORE',
            'idlogin'       => 'adminproduk',
            'email'         => 'produk@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '1998-10-12',
            'role'          => 'adminproduk',
        ]);

        // 3. Inventory - Distributor
        User::create([
            'nama_lengkap'  => 'Inventory Distributor',
            'idlogin'       => 'distributor01',
            'email'         => 'dist@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '1992-01-01',
            'role'          => 'distributor',
        ]);

        // 4. Inventory - Gudang
        User::create([
            'nama_lengkap'  => 'Kepala Gudang Pusat',
            'idlogin'       => 'gudangpusat',
            'email'         => 'gudang@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '1990-03-15',
            'role'          => 'gudang',
        ]);

        // 5. Inventory - Toko Offline
        User::create([
            'nama_lengkap'  => 'Admin Toko Cabang',
            'idlogin'       => 'toko_offline',
            'email'         => 'toko_offline@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '1997-07-07',
            'role'          => 'toko_offline',
        ]);

        // 6. Inventory - Toko Online
        User::create([
            'nama_lengkap'  => 'Admin Toko Online',
            'idlogin'       => 'toko_online',
            'email'         => 'toko_online@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '1999-09-09',
            'role'          => 'toko_online',
        ]);

        // 7. Sales
        User::create([
            'nama_lengkap'  => 'Sales Executive',
            'idlogin'       => 'salespstore',
            'email'         => 'sales@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '2001-02-28',
            'role'          => 'sales',
        ]);

        // 8. Audit & Analis
        User::create([
            'nama_lengkap'  => 'Tim Audit Internal',
            'idlogin'       => 'audit01',
            'email'         => 'audit@pstore.com',
            'password'      => Hash::make('password123'),
            'tanggal_lahir' => '1988-12-12',
            'role'          => 'audit',
        ]);
        
        // Anda bisa menambahkan role lainnya (security, leader, analis) dengan pola yang sama
    }
}