<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - Enrollment System</title>
    <link rel="icon" href="{{ asset('images/udd_logo.PNG') }}" type="image/png">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/theme.css', 'resources/css/layout.css'])
    @yield('styles')
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
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
            <div class="theme-btn" onclick="toggleTheme()" title="Toggle appearance">
                <i data-lucide="moon" class="icon-moon"></i>
                <i data-lucide="sun" class="icon-sun"></i>
            </div>
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
                
                <div style="flex-grow: 1;"></div>
                
                <form method="POST" action="{{ route('student.logout') }}">
                    @csrf
                    <button type="submit" class="nav-link btn-logout" style="width:100%; border:none; background:none; cursor:pointer; text-align: left;">
                        <i data-lucide="log-out" style="width: 24px; height: 24px; color: inherit;"></i>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-wrapper">
            @if(session('success'))
                <div class="flash-message flash-success">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flash-message flash-error">
                    <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
