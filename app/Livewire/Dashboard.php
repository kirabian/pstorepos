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
use App\Models\Penjualan; // <--- PASTIKAN MODEL INI DI-IMPORT

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
                // Simulasi data analis (tetap dummy karena analis biasanya main prediksi)
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
            $cabang = Cabang::find($user->cabang_id);
            $namaCabang = $cabang ? $cabang->nama_cabang : 'Cabang Tidak Diketahui';

            // Ambil Data Realtime Penjualan Cabang Ini
            $omsetHariIni = Penjualan::where('cabang_id', $user->cabang_id)->whereDate('created_at', today())->sum('harga_jual_real');
            $omsetBulanIni = Penjualan::where('cabang_id', $user->cabang_id)->whereMonth('created_at', now()->month)->sum('harga_jual_real');
            $transaksiHariIni = Penjualan::where('cabang_id', $user->cabang_id)->whereDate('created_at', today())->count();
            
            // Cari Top Sales
            $topSales = Penjualan::where('cabang_id', $user->cabang_id)
                ->whereMonth('created_at', now()->month)
                ->selectRaw('user_id, sum(harga_jual_real) as total_omset')
                ->groupBy('user_id')
                ->orderByDesc('total_omset')
                ->with('user')
                ->first();

            $viewData = [
                'mode' => 'leader',
                'nama_cabang' => $namaCabang,
                'lokasi' => $cabang->lokasi ?? '-',
                'omset_hari_ini' => $omsetHariIni,
                'omset_bulan_ini' => $omsetBulanIni,
                'transaksi_hari_ini' => $transaksiHariIni,
                'top_sales' => $topSales ? $topSales->user->nama_lengkap : '-',
                'staff_list' => User::where('cabang_id', $user->cabang_id)->where('role', '!=', 'leader')->get(),
            ];

            return view('livewire.dashboards.leader', $viewData);
        }

        // 4. LOGIKA INVENTORY STAFF
        elseif ($user->role === 'inventory_staff') {
            if ($user->distributor_id) {
                // ... (Logic Distributor Inventory tetap sama/dummy dulu jika belum ada tabel distribusi)
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

        // 6. SALES (FIXED: MENGGUNAKAN DATA REAL DATABASE)
        elseif ($user->role === 'sales') {
            
            // Hitung Penjualan Hari Ini (Milik Sales Ini Saja)
            $penjualanHariIni = Penjualan::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count();

            // Hitung Omset Hari Ini
            $omsetHariIni = Penjualan::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->sum('harga_jual_real');

            // Hitung Capaian Bulan Ini
            $capaianBulan = Penjualan::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // Target Bulanan (Bisa dibuat dinamis nanti, sementara hardcode 100)
            $targetBulan = 100; 

            // Hitung Estimasi Insentif (Misal 1% dari omset bulanan)
            $totalOmsetBulan = Penjualan::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('harga_jual_real');
            
            $insentifEstimasi = $totalOmsetBulan * 0.01; // 1 Persen

            // Ambil 5 Transaksi Terakhir
            $recentSalesRaw = Penjualan::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            // Format Data untuk View
            $recentSales = $recentSalesRaw->map(function($sale) {
                // Tentukan Status berdasarkan Audit
                $statusLabel = 'Proses'; // Default Pending
                if ($sale->status_audit == 'Approved') $statusLabel = 'Lunas';
                if ($sale->status_audit == 'Rejected') $statusLabel = 'Ditolak';

                return [
                    'customer' => $sale->nama_customer,
                    'unit' => $sale->nama_produk, // Mengambil dari kolom nama_produk di tabel penjualans
                    'harga' => 'Rp ' . number_format($sale->harga_jual_real, 0, ',', '.'),
                    'status' => $statusLabel,
                    'time' => $sale->created_at->format('H:i'),
                ];
            });

            $viewData = [
                'mode' => 'sales',
                'cabang' => $user->cabang->nama_cabang ?? 'PStore Pusat',
                'penjualan_hari_ini' => $penjualanHariIni,
                'omset_hari_ini' => $omsetHariIni,
                'target_bulan' => $targetBulan, 
                'capaian_bulan' => $capaianBulan, 
                'insentif_estimasi' => $insentifEstimasi,
                'recent_sales' => $recentSales // Data Real
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