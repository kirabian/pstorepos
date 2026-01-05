    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>403 | Akses Ditolak - PSTORE</title>
        
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --pstore-black: #000000;
                --pstore-gray: #6c757d;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #f8f9fa;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
                overflow: hidden;
            }

            .error-container {
                text-align: center;
                max-width: 500px;
                padding: 40px;
                background: white;
                border-radius: 40px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            }

            .brand-logo {
                height: 50px;
                margin-bottom: 30px;
            }

            .error-code {
                font-size: 100px;
                font-weight: 800;
                line-height: 1;
                margin-bottom: 10px;
                background: linear-gradient(135deg, #000 0%, #444 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                letter-spacing: -5px;
            }

            .error-title {
                font-weight: 800;
                font-size: 1.5rem;
                color: var(--pstore-black);
                margin-bottom: 15px;
                letter-spacing: -0.5px;
            }

            .error-message {
                color: var(--pstore-gray);
                font-size: 0.95rem;
                margin-bottom: 35px;
                line-height: 1.6;
            }

            .btn-pstore {
                background-color: var(--pstore-black);
                color: white;
                padding: 15px 35px;
                border-radius: 18px;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                border: none;
            }

            .btn-pstore:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.15);
                color: white;
            }

            .illustration-icon {
                font-size: 60px;
                color: #ff4757;
                margin-bottom: 20px;
                opacity: 0.8;
            }
        </style>
    </head>
    <body>

        <div class="error-container animate__animated animate__zoomIn">
            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" class="brand-logo">

            <div class="illustration-icon">
                <i class="fas fa-shield-halt"></i>
            </div>

            <div class="error-code">403</div>
            
            <h2 class="error-title">Akses Terbatas</h2>
            
            <p class="error-message">
                Mohon maaf, Anda tidak memiliki otoritas untuk mengakses halaman ini. 
                Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>

            <a href="{{ url('/') }}" class="btn-pstore">
                <i class="fas fa-arrow-left me-2"></i> KEMBALI KE DASHBOARD
            </a>
        </div>

    </body>
    </html>