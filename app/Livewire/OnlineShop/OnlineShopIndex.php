<?php

namespace App\Livewire\OnlineShop;

use App\Models\OnlineShop;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.master')]
class OnlineShopIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $shopId;
    public $isEdit = false;

    // Form Fields
    #[Rule('required|min:3')]
    public $nama_toko;

    #[Rule('required')]
    public $platform; // Shopee, Tokopedia, TikTok, dll

    #[Rule('nullable|url')]
    public $url_toko;

    #[Rule('nullable|string')]
    public $deskripsi;

    public $is_active = true;

    // List Platform untuk Dropdown
    public $platforms = [
        'Shopee', 'Tokopedia', 'TikTok Shop', 'Lazada', 'Blibli', 'Instagram Shop', 'Website', 'Lainnya'
    ];

    public function resetInputFields()
    {
        $this->nama_toko = '';
        $this->platform = '';
        $this->url_toko = '';
        $this->deskripsi = '';
        $this->is_active = true;
        $this->shopId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        OnlineShop::updateOrCreate(['id' => $this->shopId], [
            'nama_toko' => $this->nama_toko,
            'platform' => $this->platform,
            'url_toko' => $this->url_toko,
            'deskripsi' => $this->deskripsi,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('close-modal');
        
        $this->dispatch('swal', [
            'title' => $this->shopId ? 'Berhasil Diperbarui!' : 'Berhasil Ditambahkan!',
            'text' => 'Data toko online telah disimpan.',
            'icon' => 'success'
        ]);
        
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $shop = OnlineShop::findOrFail($id);
        $this->shopId = $id;
        $this->nama_toko = $shop->nama_toko;
        $this->platform = $shop->platform;
        $this->url_toko = $shop->url_toko;
        $this->deskripsi = $shop->deskripsi;
        $this->is_active = (bool) $shop->is_active;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        OnlineShop::find($id)->delete();
        $this->dispatch('swal', ['title' => 'Terhapus!', 'text' => 'Data berhasil dihapus.', 'icon' => 'success']);
    }

    public function toggleStatus($id)
    {
        $shop = OnlineShop::find($id);
        $shop->is_active = !$shop->is_active;
        $shop->save();
    }

    public function render()
    {
        $shops = OnlineShop::query()
            ->where('nama_toko', 'like', '%' . $this->search . '%')
            ->orWhere('platform', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.online-shop.online-shop-index', [
            'shops' => $shops
        ]);
    }
}