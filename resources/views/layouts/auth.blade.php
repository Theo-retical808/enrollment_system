<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'UdD Enrollment') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/theme.js'])

    <style>
        :root {
            --auth-bg-start: #f8fafc;
            --auth-bg-end: #e2e8f0;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        [data-theme="dark"] {
            --auth-bg-start: #0f172a;
            --auth-bg-end: #020617;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(51, 65, 85, 0.5);
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--auth-bg-end);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* Oceanic Blur Background Components */
        .bg-blur-blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--udd-blue) 0%, transparent 70%);
            filter: blur(80px);
            opacity: 0.15;
            z-index: -1;
            animation: float 20s infinite alternate;
        }

        .blob-1 { top: -200px; right: -100px; background: radial-gradient(circle, #3b82f6 0%, transparent 70%); }
        .blob-2 { bottom: -200px; left: -100px; background: radial-gradient(circle, #8b5cf6 0%, transparent 70%); }

        [data-theme="dark"] .bg-blur-blob { opacity: 0.25; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 440px;
            padding: 3.5rem 2.5rem;
            position: relative;
            z-index: 10;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .auth-logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .auth-logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120%;
            height: 120%;
            background: var(--udd-blue);
            filter: blur(25px);
            opacity: 0.2;
            border-radius: 50%;
        }

        .auth-logo {
            height: 80px;
            width: auto;
            position: relative;
            z-index: 2;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--text-muted);
            margin-bottom: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--text-muted);
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1.25rem 0.9rem 3rem;
            font-size: 0.95rem;
            font-weight: 600;
            border: 2px solid transparent;
            background: rgba(var(--bg-primary-rgb), 0.5); /* Assuming we adds rgb version in app.css or use static */
            background: var(--bg-primary);
            border-radius: 16px;
            color: var(--text-main);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            font-family: inherit;
        }

        .form-control:focus {
            background: var(--bg-card);
            border-color: var(--udd-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .form-control:focus + .input-icon {
            color: var(--udd-blue);
        }

        .btn-auth {
            width: 100%;
            padding: 1.1rem;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-student {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .btn-professor {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.3);
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        .test-credentials {
            margin-top: 2.5rem;
            padding: 1.5rem;
            background: rgba(var(--bg-primary-rgb), 0.3);
            background: var(--bg-primary);
            border-radius: 20px;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .test-credentials:hover {
            opacity: 1;
        }

        .credential-item code {
            background: var(--bg-card);
            padding: 4px 10px;
            border-radius: 8px;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--udd-blue);
        }

        .auth-footer-link {
            color: var(--udd-blue);
            text-decoration: none;
            font-weight: 700;
            position: relative;
            transition: all 0.2s;
        }

        .auth-footer-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--udd-blue);
            transform: scaleX(0);
            transition: transform 0.2s;
            transform-origin: right;
        }

        .auth-footer-link:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }
    </style>
</head>
<body>
    <div class="bg-blur-blob blob-1"></div>
    <div class="bg-blur-blob blob-2"></div>

    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
