<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Stok;
use App\Models\Cabang;
use App\Models\Distributor;
use App\Models\Gudang;
use App\Models\Penjualan;

#[Layout('layouts.master')]
#[Title('Dashboard Sistem')]
class Dashboard extends Component
{
    public $sim_target_growth = 10;
    public $sim_efficiency = 0;
    
    public function render()
    {
        $user = Auth::user();
        $viewData = []; 

        // 1. SUPERADMIN
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

        // 2. ANALIS
        elseif ($user->role === 'analis') {
            $cabangs = Cabang::all();
            $performanceData = $cabangs->map(function($cabang) {
                $baseOmset = rand(150000000, 500000000); 
                $margin = rand(15, 35); 
                $profit = $baseOmset * ($margin / 100);
                return ['nama_cabang' => $cabang->nama_cabang, 'lokasi' => $cabang->lokasi, 'omset' => $baseOmset, 'profit' => $profit, 'margin' => $margin, 'transaksi_count' => rand(500, 1500)];
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

        // 3. LEADER
        elseif ($user->role === 'leader') {
            $omsetHariIni = Penjualan::where('cabang_id', $user->cabang_id)->whereDate('created_at', today())->sum('harga_jual_real');
            $omsetBulanIni = Penjualan::where('cabang_id', $user->cabang_id)->whereMonth('created_at', now()->month)->sum('harga_jual_real');
            $transaksiHariIni = Penjualan::where('cabang_id', $user->cabang_id)->whereDate('created_at', today())->count();
            $topSales = Penjualan::where('cabang_id', $user->cabang_id)->whereMonth('created_at', now()->month)->selectRaw('user_id, sum(harga_jual_real) as total_omset')->groupBy('user_id')->orderByDesc('total_omset')->with('user')->first();

            $viewData = [
                'mode' => 'leader',
                'nama_cabang' => $user->cabang->nama_cabang ?? '-',
                'lokasi' => $user->cabang->lokasi ?? '-',
                'omset_hari_ini' => $omsetHariIni,
                'omset_bulan_ini' => $omsetBulanIni,
                'transaksi_hari_ini' => $transaksiHariIni,
                'top_sales' => $topSales ? $topSales->user->nama_lengkap : '-',
                'staff_list' => User::where('cabang_id', $user->cabang_id)->where('role', '!=', 'leader')->get(),
            ];
            return view('livewire.dashboards.leader', $viewData);
        }

        // 4. INVENTORY STAFF (Gudang/Distributor)
        elseif ($user->role === 'inventory_staff') {
            if ($user->distributor_id) {
                $viewData = ['mode' => 'distributor', 'location_name' => $user->distributor->nama_distributor, 'stats' => [['label' => 'Masuk', 'value' => 150, 'trend' => '+12%', 'color' => 'primary', 'icon' => 'fa-box-open'], ['label' => 'Keluar', 'value' => 80, 'trend' => '-5%', 'color' => 'success', 'icon' => 'fa-truck-loading']]];
                return view('livewire.dashboards.inventory-distributor', $viewData); 
            } elseif ($user->gudang_id) {
                $viewData = ['mode' => 'gudang', 'location_name' => $user->gudang->nama_gudang, 'stats' => [['label' => 'Total SKU', 'value' => '4,500', 'trend' => 'Stabil', 'color' => 'info', 'icon' => 'fa-tags'], ['label' => 'Stock Low', 'value' => 25, 'trend' => 'Alert', 'color' => 'warning', 'icon' => 'fa-exclamation-triangle']]];
                return view('livewire.dashboards.inventory-gudang', $viewData);
            }
        }

        // 5. DISTRIBUTOR OWNER
        elseif ($user->role === 'distributor') {
            $viewData = ['nama_distributor' => $user->distributor->nama_distributor ?? 'Unit Distributor', 'omset_bulan_ini' => 'Rp 2.500.000.000', 'cabang_terlayan' => 15, 'mode' => 'owner_distributor'];
            return view('livewire.dashboards.owner-distributor', $viewData);
        }

        // 6. SALES
        elseif ($user->role === 'sales') {
            $now = now();
            $penjualanHariIni = Penjualan::where('user_id', $user->id)->whereDate('created_at', $now->today())->where('status_audit', '!=', 'Rejected')->count();
            $omsetHariIni = Penjualan::where('user_id', $user->id)->whereDate('created_at', $now->today())->where('status_audit', '!=', 'Rejected')->sum('harga_jual_real');
            $capaianBulan = Penjualan::where('user_id', $user->id)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('status_audit', '!=', 'Rejected')->count();
            
            $leaderboard = Penjualan::query()->select('user_id', DB::raw('SUM(harga_jual_real) as total_omset'))->where('cabang_id', $user->cabang_id)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('status_audit', '!=', 'Rejected')->groupBy('user_id')->orderByDesc('total_omset')->get();
            $my_rank = 0; $position = 1; foreach($leaderboard as $entry) { if($entry->user_id == $user->id) { $my_rank = $position; break; } $position++; }

            $recentSales = Penjualan::where('user_id', $user->id)->latest()->take(5)->get()->map(function($sale) { return ['customer' => $sale->nama_customer, 'unit' => $sale->nama_produk, 'harga' => 'Rp ' . number_format($sale->harga_jual_real, 0, ',', '.'), 'status' => $sale->status_audit == 'Approved' ? 'Lunas' : 'Proses', 'time' => $sale->created_at->format('H:i')]; });

            $viewData = ['mode' => 'sales', 'cabang' => $user->cabang->nama_cabang ?? 'PStore Pusat', 'penjualan_hari_ini' => $penjualanHariIni, 'omset_hari_ini' => $omsetHariIni, 'target_bulan' => 100, 'capaian_bulan' => $capaianBulan, 'insentif_estimasi' => 0, 'recent_sales' => $recentSales, 'my_rank' => $my_rank, 'total_sales_people' => 0];
            return view('livewire.dashboards.sales', $viewData);
        }

        // 7. TOKO OFFLINE
        elseif ($user->role === 'toko_offline') {
            $cabangId = $user->cabang_id;
            $trxToday = Penjualan::where('cabang_id', $cabangId)->whereDate('created_at', today())->count();
            $omsetToday = Penjualan::where('cabang_id', $cabangId)->whereDate('created_at', today())->sum('harga_jual_real');
            $stokReady = Stok::where('cabang_id', $cabangId)->where('jumlah', '>', 0)->count();
            $lastTrx = Penjualan::with('user')->where('cabang_id', $cabangId)->latest()->take(5)->get()->map(function($sale) { return ['invoice' => '#TRX-' . $sale->id, 'kasir' => $sale->user->nama_lengkap ?? '-', 'customer' => $sale->nama_customer, 'total' => number_format($sale->harga_jual_real, 0, ',', '.'), 'time' => $sale->created_at->format('H:i'), 'method' => 'CASH/QRIS']; });

            $viewData = ['mode' => 'toko_offline', 'cabang_name' => $user->cabang->nama_cabang ?? 'Unknown Store', 'trx_today' => $trxToday, 'omset_today' => $omsetToday, 'stok_ready' => $stokReady, 'last_transactions' => $lastTrx];
            return view('livewire.dashboards.toko-offline', $viewData);
        }

        // 8. TOKO ONLINE
        elseif ($user->role === 'toko_online') {
            $cabangId = $user->cabang_id;
            $pendingOrders = Penjualan::where('cabang_id', $cabangId)->where('status_audit', 'Pending')->count();
            $shippedToday = Penjualan::where('cabang_id', $cabangId)->where('status_audit', 'Approved')->whereDate('updated_at', today())->count();
            $omsetMonth = Penjualan::where('cabang_id', $cabangId)->whereMonth('created_at', now()->month)->sum('harga_jual_real');
            $recentOrders = Penjualan::where('cabang_id', $cabangId)->latest()->take(5)->get()->map(function($sale) { return ['order_id' => 'ORD-' . $sale->id, 'customer' => $sale->nama_customer, 'platform' => 'WhatsApp', 'status' => $sale->status_audit == 'Approved' ? 'Dikirim' : 'Perlu Proses', 'courier' => 'J&T Express', 'time' => $sale->created_at->diffForHumans()]; });

            $viewData = ['mode' => 'toko_online', 'store_name' => $user->cabang->nama_cabang ?? 'PStore Online', 'pending_orders' => $pendingOrders, 'shipped_today' => $shippedToday, 'omset_month' => $omsetMonth, 'recent_orders' => $recentOrders, 'chat_response_rate' => 98];
            return view('livewire.dashboards.toko-online', $viewData);
        }

        return view('livewire.dashboards.general-fallback', ['role_name' => str_replace('_', ' ', strtoupper($user->role)), 'mode' => 'general']);
    }
}