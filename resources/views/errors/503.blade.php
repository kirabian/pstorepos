<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance | PSTORE</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --pstore-black: #000000; --pstore-gray: #6c757d; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8f9fa; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
        }
        .maintenance-container { 
            text-align: center; 
            max-width: 600px; 
            padding: 50px; 
            background: white; 
            border-radius: 40px; 
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.15); 
        }
        .brand-logo { height: 55px; margin-bottom: 35px; }
        .gear-icon { 
            font-size: 80px; 
            color: var(--pstore-black); 
            margin-bottom: 25px; 
        }
        .maintenance-title { 
            font-weight: 800; 
            font-size: 1.8rem; 
            color: var(--pstore-black); 
            margin-bottom: 15px; 
            letter-spacing: -0.5px; 
        }
        .maintenance-message { 
            color: var(--pstore-gray); 
            font-size: 1rem; 
            line-height: 1.6; 
            margin-bottom: 30px; 
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(0,0,0,0.05);
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--pstore-black);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="maintenance-container animate__animated animate__fadeInUp">
        <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" class="brand-logo">
        
        <div class="gear-icon">
            <i class="fas fa-tools fa-spin"></i>
        </div>

        <div class="status-badge">Maintenance Mode</div>
        
        <h2 class="maintenance-title">Kami Akan Segera Kembali</h2>
        
        <p class="maintenance-message">
            Saat ini sistem <strong>PSTORE Inventory</strong> sedang menjalani pembaruan rutin untuk meningkatkan performa dan keamanan. Mohon tunggu beberapa saat lagi.
        </p>

        <div class="text-muted small fw-bold text-uppercase opacity-50 tracking-widest">
            &copy; 2026 PSTORE Group IT Team
        </div>
    </div>

</body>
</html>