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
    public function render()
    {
        $user = Auth::user();
        $viewData = []; // Wadah data yang akan dikirim ke view

        // ==========================================================
        // 1. LOGIKA UNTUK SUPERADMIN
        // ==========================================================
        if ($user->role === 'superadmin') {
            $viewData = [
                'total_user' => User::count(),
                'total_cabang' => Cabang::count(),
                'total_distributor' => Distributor::count(),
                'active_users' => User::where('is_active', 1)->count(),
            ];
            return view('livewire.dashboards.superadmin', $viewData);
        }

        // ==========================================================
        // 2. LOGIKA UNTUK INVENTORY STAFF (CABANG DUA ARAH)
        // ==========================================================
        elseif ($user->role === 'inventory_staff') {
            
            // KASUS A: STAFF YANG DITEMPATKAN DI DISTRIBUTOR
            if ($user->distributor_id) {
                $viewData = [
                    'lokasi' => $user->distributor->nama_distributor,
                    'barang_masuk' => 150, // Ganti Query Real
                    'barang_keluar' => 80, // Ganti Query Real
                    'perlu_packing' => 12,
                ];
                return view('livewire.dashboards.inventory-distributor', $viewData);
            } 
            
            // KASUS B: STAFF YANG DITEMPATKAN DI GUDANG FISIK
            elseif ($user->gudang_id) {
                $viewData = [
                    'lokasi' => $user->gudang->nama_gudang,
                    'total_sku' => 4500,
                    'stock_low' => 25,
                    'jadwal_opname' => 'Hari Ini, 14:00',
                ];
                return view('livewire.dashboards.inventory-gudang', $viewData);
            }
        }

        // ==========================================================
        // 3. LOGIKA UNTUK OWNER DISTRIBUTOR (ROLE: DISTRIBUTOR)
        // ==========================================================
        elseif ($user->role === 'distributor') {
            $viewData = [
                'nama_distributor' => $user->distributor->nama_distributor ?? 'Unit Distributor',
                'omset_bulan_ini' => 'Rp 2.500.000.000',
                'cabang_terlayan' => 15,
            ];
            return view('livewire.dashboards.owner-distributor', $viewData);
        }

        // ==========================================================
        // 4. LOGIKA UNTUK SALES / CASHIER
        // ==========================================================
        elseif ($user->role === 'sales') {
            $viewData = [
                'cabang' => $user->cabang->nama_cabang ?? 'PStore Pusat',
                'penjualan_hari_ini' => 12,
                'target_bulan' => 85, // Persen
            ];
            return view('livewire.dashboards.sales', $viewData);
        }

        // ==========================================================
        // 5. LOGIKA UNTUK ROLE LAINNYA (FALLBACK)
        // ==========================================================
        // Admin Produk, Analis, Security, Leader, Toko Offline, Toko Online
        return view('livewire.dashboards.general-fallback', [
            'role_name' => str_replace('_', ' ', strtoupper($user->role))
        ]);
    }
}