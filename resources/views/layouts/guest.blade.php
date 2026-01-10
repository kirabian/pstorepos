<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login System (Debug Mode)</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        /* Import Palette Variables Locally or match them manually if app layout is not extended */
        :root {
            --ps-dark: #222831;
            --ps-secondary: #393E46;
            --ps-accent: #00ADB5;
            --ps-light: #EEEEEE;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--ps-light); /* Palette Light Grey */
            margin: 0;
            overflow: hidden;
            color: var(--ps-dark);
        }
        
        /* Optional: Add accents to specific elements if used in slot */
        .text-accent { color: var(--ps-accent); }
    </style>

    {{-- HANYA LOAD CSS, JANGAN LOAD JS APP.JS DULU --}}
    @vite(['resources/css/app.css'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body>

    {{ $slot }}

    {{-- Livewire Scripts (Wajib di bawah) --}}
    @livewireScripts

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>