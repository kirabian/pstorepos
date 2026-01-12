<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Stok;
use App\Models\Cabang;
use App\Models\Distributor;
use App\Models\Gudang;

#[Layout('layouts.master')]
#[Title('Dashboard Sistem')]
class Dashboard extends Component
{
    // ==========================================================
    // PROPERTI KHUSUS SIMULASI ANALIS
    // ==========================================================
    public $sim_target_growth = 10; // Default kenaikan target 10%
    public $sim_efficiency = 0;     // Default efisiensi biaya 0%
    
    // ==========================================================
    // RENDER UTAMA
    // ==========================================================
    public function render()
    {
        $user = Auth::user();
        $viewData = []; 

        // 1. LOGIKA UNTUK SUPERADMIN
        if ($user->role === 'superadmin') {
            $viewData = [
                'total_user' => User::count(),
                'total_cabang' => Cabang::count(),
                'total_distributor' => Distributor::count(),
                'active_users' => User::where('is_active', 1)->count(),
                'mode' => 'superadmin'
            ];
            return view('livewire.dashboards.superadmin', $viewData);
        }

        // 2. LOGIKA UNTUK ANALIS (BARU)
        elseif ($user->role === 'analis') {
            // Ambil semua cabang
            $cabangs = Cabang::all();
            
            // Generate Data Dummy Keuangan (Karena tabel transaksi belum dilampirkan)
            // Di production, ganti ini dengan Query SUM ke tabel transaksi
            $performanceData = $cabangs->map(function($cabang) {
                // Simulasi acak agar terlihat real
                $baseOmset = rand(150000000, 500000000); 
                $margin = rand(15, 35); // Margin 15% - 35%
                $profit = $baseOmset * ($margin / 100);
                
                return [
                    'nama_cabang' => $cabang->nama_cabang,
                    'lokasi' => $cabang->lokasi,
                    'omset' => $baseOmset,
                    'profit' => $profit,
                    'margin' => $margin,
                    'transaksi_count' => rand(500, 1500)
                ];
            });

            // Agregasi Total
            $totalOmset = $performanceData->sum('omset');
            $totalProfit = $performanceData->sum('profit');
            
            // Hitung Simulasi (Reaktif berdasarkan Input Livewire)
            $sim_projected_omset = $totalOmset + ($totalOmset * ($this->sim_target_growth / 100));
            // Asumsi: Efisiensi menambah profit langsung dari biaya operasional
            $sim_projected_profit = $totalProfit + ($totalProfit * ($this->sim_target_growth / 100)) + ($totalOmset * ($this->sim_efficiency / 1000)); 

            $viewData = [
                'mode' => 'analis',
                'cabangs_performance' => $performanceData->sortByDesc('omset'),
                'total_omset' => $totalOmset,
                'total_profit' => $totalProfit,
                'avg_margin' => $totalOmset > 0 ? ($totalProfit / $totalOmset) * 100 : 0,
                
                // Data Hasil Simulasi
                'sim_projected_omset' => $sim_projected_omset,
                'sim_projected_profit' => $sim_projected_profit,
                'sim_growth_val' => $sim_projected_omset - $totalOmset,
            ];

            // Menggunakan view khusus Analis
            return view('livewire.dashboards.analis', $viewData);
        }

        // 3. LOGIKA UNTUK INVENTORY STAFF
        elseif ($user->role === 'inventory_staff') {
            if ($user->distributor_id) {
                $viewData = [
                    'mode' => 'distributor',
                    'location_name' => $user->distributor->nama_distributor,
                    'stats' => [
                        ['label' => 'Barang Masuk', 'value' => 150, 'trend' => '+12%', 'color' => 'primary', 'icon' => 'fa-box-open'],
                        ['label' => 'Barang Keluar', 'value' => 80, 'trend' => '-5%', 'color' => 'success', 'icon' => 'fa-truck-loading'],
                        ['label' => 'Perlu Packing', 'value' => 12, 'trend' => 'Urgent', 'color' => 'warning', 'icon' => 'fa-tape'],
                        ['label' => 'Retur', 'value' => 3, 'trend' => 'Normal', 'color' => 'danger', 'icon' => 'fa-undo'],
                    ]
                ];
                // Note: Menggunakan view generic dashboard sesuai snippet user
                return view('livewire.dashboards.inventory-distributor', $viewData); 
            } 
            elseif ($user->gudang_id) {
                $viewData = [
                    'mode' => 'gudang',
                    'location_name' => $user->gudang->nama_gudang,
                    'stats' => [
                        ['label' => 'Total SKU', 'value' => '4,500', 'trend' => 'Stabil', 'color' => 'info', 'icon' => 'fa-tags'],
                        ['label' => 'Stock Low', 'value' => 25, 'trend' => 'Alert', 'color' => 'warning', 'icon' => 'fa-exclamation-triangle'],
                        ['label' => 'Asset Value', 'value' => '5.2M', 'trend' => '+2%', 'color' => 'success', 'icon' => 'fa-coins'],
                        ['label' => 'Opname Due', 'value' => 'Today', 'trend' => '14:00', 'color' => 'danger', 'icon' => 'fa-clipboard-check'],
                    ]
                ];
                return view('livewire.dashboards.inventory-gudang', $viewData);
            }
        }

        // 4. LOGIKA UNTUK OWNER DISTRIBUTOR
        elseif ($user->role === 'distributor') {
            $viewData = [
                'nama_distributor' => $user->distributor->nama_distributor ?? 'Unit Distributor',
                'omset_bulan_ini' => 'Rp 2.500.000.000',
                'cabang_terlayan' => 15,
                'mode' => 'owner_distributor'
            ];
            return view('livewire.dashboards.owner-distributor', $viewData);
        }

        // 5. LOGIKA UNTUK SALES
        elseif ($user->role === 'sales') {
            $viewData = [
                'cabang' => $user->cabang->nama_cabang ?? 'PStore Pusat',
                'penjualan_hari_ini' => 12,
                'target_bulan' => 85,
                'mode' => 'sales'
            ];
            return view('livewire.dashboards.sales', $viewData);
        }

        // 6. FALLBACK
        return view('livewire.dashboards.general-fallback', [
            'role_name' => str_replace('_', ' ', strtoupper($user->role)),
            'mode' => 'general'
        ]);
    }
}