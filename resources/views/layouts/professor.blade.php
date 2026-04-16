<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Professor Dashboard') - UdD Enrollment</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/theme.js'])

    <style>
        .header {
            background: var(--bg-card);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 1.5rem;
            min-height: calc(100vh - 80px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        /* Theme Toggle Styling */
        .theme-toggle {
            width: 56px;
            height: 28px;
            background: var(--border-main);
            border-radius: 20px;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            padding: 0 4px;
        }

        .theme-toggle-dot {
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow-sm);
        }

        [data-theme="dark"] .theme-toggle-dot {
            transform: translateX(26px);
            background: var(--udd-blue);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="header-brand">
                <img src="{{ asset('images/udd_logo.PNG') }}" alt="Logo" style="height: 48px; width: auto;">
                <div class="header-title">Professor Portal</div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <div class="theme-toggle-dot">
                        <span id="theme-icon">☀️</span>
                    </div>
                </div>

                <div class="flex items-center gap-2" style="background: var(--border-light); padding: 6px 16px 6px 6px; border-radius: 40px;">
                    <div style="width:36px; height:36px; background: var(--udd-blue-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                        <i data-lucide="shield-check" style="width:20px; height:20px;"></i>
                    </div>
                    <div style="line-height: 1.1;">
                        <span class="font-bold" style="font-size: 0.85rem; display: block;">{{ Auth::guard('professor')->user()->full_name }}</span>
                        <span class="text-muted" style="font-size: 0.7rem;">Professor</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('professor.logout') }}">
                    @csrf
                    <button type="submit" class="btn" style="background: var(--status-danger-bg); color: var(--status-danger-text); padding: 0.6rem 1.2rem; font-size: 0.85rem;">
                        <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="main-container">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>