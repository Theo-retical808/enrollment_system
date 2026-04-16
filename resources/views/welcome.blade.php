<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Universidad de Dagupan - Enrollment System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/theme.js'])
    
    <style>
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            min-height: 100vh;
            background: radial-gradient(circle at top right, var(--status-info-bg), var(--bg-primary));
            position: relative;
            overflow: hidden;
        }

        .hero-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 10;
        }

        .main-logo {
            height: 140px; 
            width: auto;
            margin-bottom: 2.5rem;
            filter: drop-shadow(0 15px 30px rgba(0,0,0,0.1));
        }

        .hero-title {
            font-size: 4rem; 
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
            margin-bottom: 1rem;
            letter-spacing: -2px;
        }

        .hero-subtitle {
            font-size: 1.25rem; 
            color: var(--udd-blue); 
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-indent: 3px; /* Fixes centering offset from letter-spacing */
            margin-bottom: 2rem;
        }

        .hero-description {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto 3.5rem auto;
            line-height: 1.7;
            font-weight: 500;
        }

        .btn-large {
            padding: 1.25rem 3rem;
            font-size: 1.1rem;
            border-radius: var(--radius-xl);
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }

        .btn-portal {
            background: var(--bg-card);
            color: var(--text-main);
            border: 2px solid var(--border-main);
            min-width: 240px;
            justify-content: center;
        }

        .btn-portal:hover {
            background: var(--udd-blue) !important;
            color: white !important;
            border-color: var(--udd-blue) !important;
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .blob {
            position: absolute;
            z-index: 1;
            filter: blur(100px);
            opacity: 0.2;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.75rem; }
            .hero-subtitle { font-size: 1rem; }
            .btn-group { flex-direction: column; width: 100%; gap: 1rem; }
            .btn-large { width: 100%; }
        }
    </style>
</head>
<body>

    <section class="hero">
        <div class="blob" style="width: 600px; height: 600px; background: var(--udd-blue); top: -15%; right: -10%;"></div>
        <div class="blob" style="width: 500px; height: 500px; background: var(--status-info-text); bottom: -15%; left: -10%;"></div>

        <div class="hero-container">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="Universidad de Dagupan Logo" class="main-logo">
            
            <h2 class="hero-title">Universidad de Dagupan</h2>
            <p class="hero-subtitle">Official Enrollment System</p>

            <p class="hero-description">
                Welcome to the digital academic hub of Universidad de Dagupan. 
                Experience a streamlined enrollment process tailored for excellence.
            </p>
            
            <div style="display: flex; justify-content: center; align-items: center; gap: 1.5rem; width: 100%;">
                <a href="{{ route('student.login') }}" class="btn btn-portal btn-large">
                    <i data-lucide="graduation-cap" style="width: 24px; height: 24px;"></i>
                    Student Portal
                </a>
                <a href="{{ route('professor.login') }}" class="btn btn-portal btn-large">
                    <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                    Professor Access
                </a>
            </div>
        </div>
    </section>

    <footer style="padding: 3rem; text-align: center; color: var(--text-muted); font-size: 0.95rem; font-weight: 700;">
        <div class="flex items-center justify-center gap-2 mb-2">
            <i data-lucide="building-2" style="width: 16px;"></i>
            <span>&copy; 2026 Universidad de Dagupan</span>
        </div>
        <p style="font-size: 0.8rem; opacity: 0.6;">All Rights Reserved. Secure Academic Environment.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>