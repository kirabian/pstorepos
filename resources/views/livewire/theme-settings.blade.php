<div class="p-3">
    <h6 class="fw-bold mb-3 sidebar-text text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--ps-accent);">Appearance</h6>
    
    <div class="bg-white bg-opacity-10 rounded-3 p-2 d-flex justify-content-between align-items-center mb-4 border border-secondary border-opacity-25">
        <button wire:click="setMode('light')" class="btn btn-sm flex-grow-1 {{ $theme_mode === 'light' ? 'bg-white text-dark shadow-sm' : 'text-secondary' }}" title="Light Mode">
            <i class="fas fa-sun"></i>
        </button>
        <button wire:click="setMode('dark')" class="btn btn-sm flex-grow-1 {{ $theme_mode === 'dark' ? 'bg-white text-dark shadow-sm' : 'text-secondary' }}" title="Dark Mode">
            <i class="fas fa-moon"></i>
        </button>
        <button wire:click="setMode('system')" class="btn btn-sm flex-grow-1 {{ $theme_mode === 'system' ? 'bg-white text-dark shadow-sm' : 'text-secondary' }}" title="System Default">
            <i class="fas fa-desktop"></i>
        </button>
    </div>

    <h6 class="fw-bold mb-3 sidebar-text text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--ps-accent);">Accent Color</h6>

    <div class="d-flex flex-wrap gap-2 justify-content-start">
        @foreach($colors as $key => $hex)
            <button wire:click="setColor('{{ $key }}')" 
                class="btn rounded-circle p-0 position-relative d-flex align-items-center justify-content-center transition-all"
                style="width: 32px; height: 32px; background-color: {{ $hex }}; border: 2px solid {{ $theme_color === $key ? '#fff' : 'transparent' }}; box-shadow: {{ $theme_color === $key ? '0 0 0 2px ' . $hex : 'none' }};"
                aria-label="{{ ucfirst($key) }}">
                
                @if($theme_color === $key)
                    <i class="fas fa-check text-white" style="font-size: 0.8rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"></i>
                @endif
            </button>
        @endforeach
    </div>
</div>