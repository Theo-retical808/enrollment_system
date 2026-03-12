<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - Enrollment System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            /* The perfect cream background! */
            --primary-bg: #fdfbf5; 
            --accent-blue: #eff6ff;
            --accent-blue-text: #2563eb;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-white: #ffffff;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --hover-bg: #f1f5f9;
            --card-shadow: 0 4px 15px -1px rgba(0, 0, 0, 0.03);
        }
        
        [data-theme="dark"] {
            /* True neutral grays - no more blue paint! */
            --primary-bg: #121212; 
            --accent-blue: #1e3a8a;
            --accent-blue-text: #60a5fa;
            --text-primary: #f5f5f5; 
            --text-secondary: #a3a3a3; 
            --bg-white: #1e1e1e; 
            --border-color: #333333; 
            --border-light: #262626; 
            --hover-bg: #262626; 
            --card-shadow: 0 4px 15px -1px rgba(0, 0, 0, 0.3);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--primary-bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-white);
            border-right: 1px solid var(--border-color);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Updated Logo Section */
        .logo {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: color 0.3s ease;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        /* Updated Active State to UdD Blue */
        .nav-link.active {
            background: var(--accent-blue);
            color: var(--accent-blue-text);
        }
        
        .nav-link:hover:not(.active) {
            background: var(--hover-bg);
            color: var(--text-primary);
        }

        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .top-nav {
            padding: 1.5rem 2rem;
            background: var(--bg-white);
            backdrop-filter: blur(8px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid var(--border-light);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        .content-body { padding: 2rem; }

        /* Modern Card Styles */
        .card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-light);
            box-shadow: var(--card-shadow);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
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
        
        /* Theme Toggle Switch */
        .theme-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            background: var(--border-color);
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            padding: 3px;
        }
        
        .theme-toggle-slider {
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        [data-theme="dark"] .theme-toggle {
            background: var(--accent-blue-text);
        }
        
        [data-theme="dark"] .theme-toggle-slider {
            transform: translateX(30px);
        }
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
            
            // Update icon
            document.getElementById('theme-icon').textContent = newTheme === 'dark' ? '🌙' : '☀️';
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.getElementById('theme-icon').textContent = savedTheme === 'dark' ? '🌙' : '☀️';
        });
    </script>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="Universidad de Dagupan Logo" style="height: 40px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));">
            <span>Student Portal</span>
        </div>
        
        <nav style="display:flex; flex-direction:column; height: 100%;">
            <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('student.courses') }}" class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">My Courses</a>
            <a href="{{ route('student.schedule') }}" class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}">Schedule</a>
            <a href="{{ route('student.finances') }}" class="nav-link {{ request()->routeIs('student.finances') ? 'active' : '' }}">Finances</a>
            
            <form method="POST" action="{{ route('student.logout') }}" style="margin-top: auto;">
                @csrf
                <button type="submit" class="nav-link btn-logout" style="width:100%; border:none; background:none; cursor:pointer; text-align: left;">
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="top-nav">
            <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); transition: color 0.3s ease;">@yield('title')</h1>
            <div style="display:flex; align-items:center; gap:16px;">
                <div class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                    <div class="theme-toggle-slider">
                        <span id="theme-icon">☀️</span>
                    </div>
                </div>
                <span style="font-weight: 700; font-size: 0.9rem; color: var(--text-secondary); transition: color 0.3s ease;">{{ Auth::guard('student')->user()->full_name ?? 'Student' }}</span>
                <div style="width:38px; height:38px; background:var(--border-color); border-radius:50%; border: 2px solid var(--bg-white); box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;"></div>
            </div>
        </header>

        <main class="content-body">
            @yield('content')
        </main>
    </div>
</body>
</html>