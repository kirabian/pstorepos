<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Stok; // Asumsi ada model Stok
use App\Models\StokHistory; // Asumsi ada model History

#[Layout('layouts.master')]
#[Title('Dashboard Operasional')]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        // Init Variable View Data
        $viewData = [
            'mode' => 'general',
            'location_name' => 'General Area',
            'stats' => [],
            'activities' => []
        ];

        // ==========================================
        // 1. LOGIKA UNTUK ROLE: INVENTORY STAFF (GUDANG)
        // ==========================================
        if ($user->role === 'gudang') {
            
            // KASUS A: STAFF PENEMPATAN DISTRIBUTOR
            if ($user->distributor_id) {
                $viewData['mode'] = 'distributor';
                $viewData['location_name'] = $user->distributor->nama_distributor ?? 'Distributor Utama';
                
                // Data Statistik Khusus Distributor (Fokus: Supply Chain & Distribusi ke Cabang)
                $viewData['stats'] = [
                    [
                        'label' => 'Total Supply Masuk',
                        'value' => '1,240', // Ganti: BarangMasuk::where('distributor_id', $user->distributor_id)->sum('qty')
                        'icon' => 'fa-truck-loading',
                        'color' => 'primary',
                        'trend' => '+12% minggu ini'
                    ],
                    [
                        'label' => 'Distribusi ke Cabang',
                        'value' => '85', // Ganti: Pengiriman::where('distributor_id',...)->count()
                        'icon' => 'fa-paper-plane',
                        'color' => 'info',
                        'trend' => '5 Pengiriman Pending'
                    ],
                    [
                        'label' => 'Total SKU Unit',
                        'value' => '450',
                        'icon' => 'fa-boxes-stacked',
                        'color' => 'warning',
                        'trend' => 'Stok Aman'
                    ],
                    [
                        'label' => 'Cabang Terhubung',
                        'value' => '12',
                        'icon' => 'fa-network-wired',
                        'color' => 'success',
                        'trend' => 'Active Connection'
                    ]
                ];
            } 
            // KASUS B: STAFF PENEMPATAN GUDANG FISIK (WAREHOUSE)
            elseif ($user->gudang_id) {
                $viewData['mode'] = 'gudang';
                $viewData['location_name'] = $user->gudang->nama_gudang ?? 'Gudang Penyimpanan';

                // Data Statistik Khusus Gudang (Fokus: Fisik Barang, Rak, Opname)
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
                        'value' => '24', // Ganti: Stok::where('kondisi', 'rusak')->count()
                        'icon' => 'fa-rotate-left',
                        'color' => 'warning',
                        'trend' => 'Perlu QC Ulang'
                    ],
                    [
                        'label' => 'Stock Opname',
                        'value' => 'Verified',
                        'icon' => 'fa-clipboard-check',
                        'color' => 'success',
                        'trend' => 'Last: Hari ini 08:00'
                    ],
                    [
                        'label' => 'Total Item Fisik',
                        'value' => '5,600',
                        'icon' => 'fa-box-open',
                        'color' => 'primary',
                        'trend' => '+200 unit baru'
                    ]
                ];
            }
        }
        
        // ==========================================
        // 2. LOGIKA UNTUK SUPERADMIN / ROLE LAIN
        // ==========================================
        else {
            $viewData['mode'] = 'admin'; // Fallback
        }

        return view('livewire.dashboard', $viewData);
    }
}