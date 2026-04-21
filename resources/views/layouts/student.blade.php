<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Access') - Enrollment System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/theme.js'])

    <style>
        .sidebar {
            width: 280px;
            background: var(--bg-sidebar);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .main-wrapper {
            margin-left: 280px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-nav {
            padding: 1.25rem 2rem;
            background: var(--bg-card);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.85rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-link i {
            width: 20px;
            height: 20px;
        }

        .nav-link:hover {
            background: var(--border-light);
            color: var(--text-main);
        }

        .nav-link.active {
            background: var(--udd-blue-light);
            color: var(--udd-blue);
        }

        .content-body {
            padding: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Custom Theme Toggle Styling */
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
    @yield('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="flex items-center gap-4 mb-8" style="margin-bottom: 3rem;">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="Logo" style="height: 48px; width: auto;">
            <div>
                <h1 class="font-extrabold" style="font-size: 1.1rem; line-height: 1.1;">Enrollment System</h1>
                <p class="text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Enrollment</p>
            </div>
        </div>
        
        <nav style="flex: 1;">
            <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                Dashboard
            </a>
            <a href="{{ route('student.courses') }}" class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                <i data-lucide="book-open"></i>
                My Courses
            </a>
            <a href="{{ route('student.schedule') }}" class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                Schedule
            </a>
            <a href="{{ route('student.finances') }}" class="nav-link {{ request()->routeIs('student.finances') ? 'active' : '' }}">
                <i data-lucide="credit-card"></i>
                Finances
            </a>
        </nav>

        <form method="POST" action="{{ route('student.logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="width:100%; border:none; background:none; cursor:pointer; color: var(--status-danger-text);">
                <i data-lucide="log-out"></i>
                Logout
            </button>
        </form>
    </aside>

    <div class="main-wrapper">
        <header class="top-nav">
            <h2 class="font-extrabold text-main" style="font-size: 1.25rem;">@yield('title')</h2>
            
            <div class="flex items-center gap-4">
                <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <div class="theme-toggle-dot">
                        <span id="theme-icon">☀️</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-2" style="background: var(--border-light); padding: 4px 12px 4px 4px; border-radius: 30px;">
                    <div style="width:32px; height:32px; background: var(--udd-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                        <i data-lucide="user" style="width:18px; height:18px;"></i>
                    </div>
                    <span class="font-bold" style="font-size: 0.85rem;">{{ Auth::guard('student')->user()->full_name ?? 'Student' }}</span>
                </div>
            </div>
        </header>

        <main class="content-body">
            @yield('content')
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>