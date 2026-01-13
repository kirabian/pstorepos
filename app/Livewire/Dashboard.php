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
    public $sim_target_growth = 10;
    public $sim_efficiency = 0;
    
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

        // 2. LOGIKA UNTUK ANALIS
        elseif ($user->role === 'analis') {
            $cabangs = Cabang::all();
            $performanceData = $cabangs->map(function($cabang) {
                $baseOmset = rand(150000000, 500000000); 
                $margin = rand(15, 35); 
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

            $totalOmset = $performanceData->sum('omset');
            $totalProfit = $performanceData->sum('profit');
            
            $sim_projected_omset = $totalOmset + ($totalOmset * ($this->sim_target_growth / 100));
            $sim_projected_profit = $totalProfit + ($totalProfit * ($this->sim_target_growth / 100)) + ($totalOmset * ($this->sim_efficiency / 1000)); 

            $viewData = [
                'mode' => 'analis',
                'cabangs_performance' => $performanceData->sortByDesc('omset'),
                'total_omset' => $totalOmset,
                'total_profit' => $totalProfit,
                'avg_margin' => $totalOmset > 0 ? ($totalProfit / $totalOmset) * 100 : 0,
                'sim_projected_omset' => $sim_projected_omset,
                'sim_projected_profit' => $sim_projected_profit,
                'sim_growth_val' => $sim_projected_omset - $totalOmset,
            ];
            return view('livewire.dashboards.analis', $viewData);
        }

        // 3. LOGIKA KHUSUS LEADER
        elseif ($user->role === 'leader') {
            // Pastikan leader punya cabang
            $cabang = Cabang::find($user->cabang_id);
            $namaCabang = $cabang ? $cabang->nama_cabang : 'Cabang Tidak Diketahui';

            // Simulasi Data Realtime Cabang Ini
            $omsetHariIni = rand(5000000, 15000000);
            $omsetBulanIni = rand(150000000, 300000000);
            $transaksiHariIni = rand(15, 50);
            $topSalesName = 'Andi Saputra'; 

            $viewData = [
                'mode' => 'leader',
                'nama_cabang' => $namaCabang,
                'lokasi' => $cabang->lokasi ?? '-',
                'omset_hari_ini' => $omsetHariIni,
                'omset_bulan_ini' => $omsetBulanIni,
                'transaksi_hari_ini' => $transaksiHariIni,
                'top_sales' => $topSalesName,
                'staff_list' => User::where('cabang_id', $user->cabang_id)->where('role', '!=', 'leader')->get(),
            ];

            return view('livewire.dashboards.leader', $viewData);
        }

        // 4. LOGIKA INVENTORY STAFF
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

        // 5. OWNER DISTRIBUTOR
        elseif ($user->role === 'distributor') {
            $viewData = [
                'nama_distributor' => $user->distributor->nama_distributor ?? 'Unit Distributor',
                'omset_bulan_ini' => 'Rp 2.500.000.000',
                'cabang_terlayan' => 15,
                'mode' => 'owner_distributor'
            ];
            return view('livewire.dashboards.owner-distributor', $viewData);
        }

        // 6. SALES (DIPERBARUI DENGAN DATA LENGKAP)
        elseif ($user->role === 'sales') {
            $viewData = [
                'mode' => 'sales',
                'cabang' => $user->cabang->nama_cabang ?? 'PStore Pusat',
                'penjualan_hari_ini' => rand(2, 8),
                'omset_hari_ini' => rand(5000000, 25000000),
                'target_bulan' => 100, // Target Unit
                'capaian_bulan' => rand(45, 95), // Unit Terjual Bulan Ini
                'insentif_estimasi' => rand(2500000, 8000000),
                'recent_sales' => [
                    ['customer' => 'Budi Santoso', 'unit' => 'iPhone 15 Pro 256GB', 'harga' => 'Rp 21.000.000', 'status' => 'Lunas', 'time' => '10:30'],
                    ['customer' => 'Siti Aminah', 'unit' => 'Samsung S24 Ultra', 'harga' => 'Rp 19.500.000', 'status' => 'Proses', 'time' => '11:45'],
                    ['customer' => 'Rudi Hartono', 'unit' => 'Xiaomi 14', 'harga' => 'Rp 12.000.000', 'status' => 'Lunas', 'time' => '13:15'],
                    ['customer' => 'Dewi Persik', 'unit' => 'iPhone 11 128GB', 'harga' => 'Rp 6.500.000', 'status' => 'Lunas', 'time' => '14:00'],
                    ['customer' => 'Aldi Taher', 'unit' => 'Infinix GT 10', 'harga' => 'Rp 3.200.000', 'status' => 'Booking', 'time' => '14:30'],
                ]
            ];
            return view('livewire.dashboards.sales', $viewData);
        }

        // 7. FALLBACK
        return view('livewire.dashboards.general-fallback', [
            'role_name' => str_replace('_', ' ', strtoupper($user->role)),
            'mode' => 'general'
        ]);
    }
}