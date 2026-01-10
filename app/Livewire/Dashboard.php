<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Dashboard Operasional')]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        // Default Data (Untuk Admin/Role lain)
        $viewData = [
            'mode' => 'general',
            'stats' => [],
            'activities' => []
        ];

        // LOGIKA KHUSUS ROLE INVENTORY STAFF (GUDANG)
        if ($user->role === 'gudang') {
            
            // 1. JIKA PENEMPATAN DI DISTRIBUTOR
            if ($user->distributor_id) {
                $viewData['mode'] = 'distributor';
                $viewData['location_name'] = $user->distributor->nama_distributor ?? 'Unknown Distributor';
                
                // Mockup Data Statistik Distributor (Ganti dengan Count DB Asli)
                $viewData['stats'] = [
                    [
                        'label' => 'Total Supply Masuk',
                        'value' => '1,240', // Ganti: BarangMasuk::where('distributor_id', ...)->count()
                        'icon' => 'fa-truck-loading',
                        'color' => 'primary',
                        'trend' => '+12% minggu ini'
                    ],
                    [
                        'label' => 'Distribusi ke Cabang',
                        'value' => '85', // Ganti: Pengiriman::where(...)
                        'icon' => 'fa-paper-plane',
                        'color' => 'info',
                        'trend' => '5 pending'
                    ],
                    [
                        'label' => 'Total SKU Unit',
                        'value' => '450',
                        'icon' => 'fa-boxes-stacked',
                        'color' => 'warning',
                        'trend' => 'Stok aman'
                    ],
                    [
                        'label' => 'Cabang Terhubung',
                        'value' => '12',
                        'icon' => 'fa-network-wired',
                        'color' => 'success',
                        'trend' => 'Active'
                    ]
                ];
            } 
            // 2. JIKA PENEMPATAN DI GUDANG FISIK (WAREHOUSE)
            elseif ($user->gudang_id) {
                $viewData['mode'] = 'gudang';
                $viewData['location_name'] = $user->gudang->nama_gudang ?? 'Unknown Warehouse';

                // Mockup Data Statistik Gudang (Ganti dengan Count DB Asli)
                $viewData['stats'] = [
                    [
                        'label' => 'Kapasitas Rak',
                        'value' => '85%',
                        'icon' => 'fa-warehouse',
                        'color' => 'danger',
                        'trend' => 'Hampir Penuh'
                    ],
                    [
                        'label' => 'Barang Retur',
                        'value' => '24',
                        'icon' => 'fa-rotate-left',
                        'color' => 'warning',
                        'trend' => 'Perlu QC'
                    ],
                    [
                        'label' => 'Stock Opname',
                        'value' => 'Verified',
                        'icon' => 'fa-clipboard-check',
                        'color' => 'success',
                        'trend' => 'Last: Hari ini'
                    ],
                    [
                        'label' => 'Total Item Fisik',
                        'value' => '5,600',
                        'icon' => 'fa-box-open',
                        'color' => 'primary',
                        'trend' => '+200 unit'
                    ]
                ];
            }
        }

        return view('livewire.dashboard', $viewData);
    }
}