<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // Mapping UUID dari file Excel Anda
        $brands = [
            [
                'uuid' => '484350d1-12ab-44a7-9233-09ed00cd314a', 
                'name' => 'Oppo'
            ],
            [
                'uuid' => '10e3b0a7-b7cb-4708-befc-000ab58ebe84', 
                'name' => 'Samsung'
            ],
            [
                'uuid' => 'd5c1f88a-3f41-4554-a049-fc3177d5ec5e', 
                'name' => 'Realme'
            ],
            [
                'uuid' => '3bc32d6b-8829-4468-97bc-ba10ea5302e4', 
                'name' => 'Vivo'
            ],
            [
                'uuid' => '9bc32d6b-8841-44cc-b992-184d7eed15fe', 
                'name' => 'Apple'
            ],
            [
                'uuid' => 'a5f1fa671c9a0-f1c1-9822-8dabbd6bc0b8', 
                'name' => 'Xiaomi'
            ],
            [
                'uuid' => '800f6c5-2309-4c45-3766-3b0f6d6b8d', 
                'name' => 'Iphone'
            ],
            [
                'uuid' => 'd5c1f8d0-4b0-7d4b-9c11-c0b47b52f4d', 
                'name' => 'Infiniti'
            ],
            [
                'uuid' => 'g511d107-abcc-aab2-2788-078e008f1fc', 
                'name' => 'Tecoro'
            ],
        ];

        foreach ($brands as $brandData) {
            // Hapus karakter non-valid dari UUID jika ada
            $uuid = str_replace('"', '', $brandData['uuid']);
            
            Brand::updateOrCreate(
                ['uuid' => $uuid],
                ['name' => $brandData['name']]
            );
        }
        
        // Generate UUID untuk brand yang belum punya
        Brand::whereNull('uuid')->each(function ($brand) {
            $brand->update(['uuid' => Str::uuid()]);
        });
        
        $this->command->info('Brands seeded successfully!');
        $this->command->info('Total brands: ' . Brand::count());
    }
}