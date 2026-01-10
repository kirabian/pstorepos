<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ThemeSettings extends Component
{
    public $theme_mode;
    public $theme_color;

    public $colors = [
        'teal'   => '#00ADB5',
        'purple' => '#8B5CF6',
        'blue'   => '#3B82F6',
        'green'  => '#10B981',
        'yellow' => '#F59E0B',
        'red'    => '#EF4444',
        'pink'   => '#EC4899',
        'orange' => '#F97316',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->theme_mode = $user->theme_mode ?? 'system';
        $this->theme_color = $user->theme_color ?? 'teal';
    }

    public function setMode($mode)
    {
        $this->theme_mode = $mode;
        $this->save();
    }

    public function setColor($color)
    {
        $this->theme_color = $color;
        $this->save();
    }

    public function save()
    {
        $user = Auth::user();
        $user->update([
            'theme_mode' => $this->theme_mode,
            'theme_color' => $this->theme_color
        ]);

        // Refresh halaman agar CSS variable di-load ulang dari backend
        $this->dispatch('themeChanged');
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.theme-settings');
    }
}