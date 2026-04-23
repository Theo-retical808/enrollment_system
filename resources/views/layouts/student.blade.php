<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - Enrollment System</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 64px;
            --primary-bg: #f9fafb; /* LMS subtle gray background */
            --bg-white: #ffffff;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #9ca3af;
            --border-color: #e5e7eb;
            --border-light: #f3f4f6;
            --hover-bg: #f3f4f6;
            --active-bg: #eff6ff; /* LMS subtle blue active bg */
            --active-text: #2563eb; /* LMS blue active text */
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --lms-blue: #2f3b94;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-bg);
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top Navigation */
        .top-nav {
            height: var(--topbar-height);
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }

        .top-nav .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .top-nav .user-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--border-color);
            object-fit: cover;
        }

        /* App Layout Container */
        .app-body {
            display: flex;
            margin-top: var(--topbar-height);
            flex: 1;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-white);
            border-right: 1px solid var(--border-color);
            padding: 1.5rem 1rem;
            position: fixed;
            top: var(--topbar-height);
            bottom: 0;
            overflow-y: auto;
            z-index: 100; /* Ensure sidebar is always clickable */
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px; /* More gap like LMS */
            padding: 1rem 1.25rem; /* Taller links */
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem; /* Slightly larger like LMS */
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            position: relative;
            z-index: 101;
            pointer-events: auto;
        }
        
        .nav-link i {
            color: #6b7280;
            width: 24px; /* Larger icons like LMS */
            height: 24px;
            transition: color 0.2s ease;
        }

        .nav-link.active {
            background: #f3f4f6; /* Gray background like LMS */
            color: #111827; /* Dark navy text */
            font-weight: 700; /* Bold active like LMS */
        }

        .nav-link.active i {
            color: #111827;
        }
        
        .nav-link:hover:not(.active) {
            background: #f9fafb;
            color: #111827;
        }

        .nav-link:hover:not(.active) i {
            color: #111827;
        }

        .sidebar-bottom-info {
            padding: 1.5rem 1rem;
            border-top: 1px solid #f3f4f6;
            margin-top: 1rem;
            font-size: 0.95rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 1.25rem 1.5rem; /* Tightened like LMS */
            background: var(--primary-bg);
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Modern Card Styles */
        .card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
        }

        /* Stat cards are styled in the dashboard views */
        
        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            font-size: 0.9rem;
        }
        
        .btn-logout { 
            color: #ef4444; 
            margin-top: auto; 
        }
        
        .btn-logout:hover {
            background: #fef2f2 !important;
            color: #dc2626 !important;
        }
        
        /* Minimalist Theme Button */
        .theme-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #64748b;
            border: 1px solid #f1f5f9;
            background: white;
        }
        
        .theme-btn:hover {
            background: #f8fafc;
            color: #1e293b;
            border-color: #e2e8f0;
        }
        
        .theme-btn i {
            width: 20px;
            height: 20px;
        }

        .icon-moon { display: flex; }
        .icon-sun { display: none; }
        
        [data-theme="dark"] .theme-btn {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }

        [data-theme="dark"] .theme-btn:hover {
            background: #334155;
            color: white;
        }

        [data-theme="dark"] .icon-moon { display: none; }
        [data-theme="dark"] .icon-sun { display: flex; }
    </style>
    @yield('styles')
    <script>
        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</head>
<body>
    <!-- Top Navigation -->
    <header class="top-nav">
        <div class="logo">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="UDD Logo" style="height: 32px; width: auto;">
            <span>Enrollment System</span>
        </div>
        <div class="user-actions">
            <!-- Theme Toggle Integration (Optional) -->
            <div class="theme-btn" onclick="toggleTheme()" title="Toggle appearance">
                <i data-lucide="moon" class="icon-moon"></i>
                <i data-lucide="sun" class="icon-sun"></i>
            </div>
            
            <!-- User Avatar Placeholder -->
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('student')->user()->full_name ?? 'Student') }}&background=2f3b94&color=fff" alt="User Avatar" class="avatar">
        </div>
    </header>

    <div class="app-body">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav style="display:flex; flex-direction:column; height: 100%;">
                <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" style="width: 20px; height: 20px;"></i>
                    Dashboard
                </a>
                <a href="{{ route('student.courses') }}" class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                    <i data-lucide="book-open" style="width: 20px; height: 20px;"></i>
                    My Courses
                </a>
                <a href="{{ route('student.schedule') }}" class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}">
                    <i data-lucide="calendar" style="width: 20px; height: 20px;"></i>
                    Schedule
                </a>
                <a href="{{ route('student.finances') }}" class="nav-link {{ request()->routeIs('student.finances') ? 'active' : '' }}">
                    <i data-lucide="credit-card" style="width: 20px; height: 20px;"></i>
                    Finances
                </a>
                
                <!-- Spacer -->
                <div style="flex-grow: 1;"></div>
                
                <form method="POST" action="{{ route('student.logout') }}">
                    @csrf
                    <button type="submit" class="nav-link btn-logout" style="width:100%; border:none; background:none; cursor:pointer; text-align: left; color: #dc2626;">
                        <i data-lucide="log-out" style="width: 24px; height: 24px; color: inherit;"></i>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-wrapper">
            <!-- Flash Messages -->
            @if(session('success'))
                <div style="margin: 1rem 2rem; padding: 1rem 1.5rem; background: #dcfce7; color: #166534; border-radius: 12px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 12px; font-weight: 600; font-size: 0.95rem;">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="margin: 1rem 2rem; padding: 1rem 1.5rem; background: #fef2f2; color: #991b1b; border-radius: 12px; border: 1px solid #fee2e2; display: flex; align-items: center; gap: 12px; font-weight: 600; font-size: 0.95rem;">
                    <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>