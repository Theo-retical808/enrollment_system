<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ config('app.name', 'Enrollment System') }}</title>
    <link rel="icon" href="{{ asset('images/udd_logo.PNG') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/theme.css', 'resources/css/layout.css', 'resources/css/admin.css'])
</head>
<body class="admin-page">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
    </script>
    <nav class="admin-nav">
        <div class="nav-brand">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="UDD Logo">
            <span>Admin Panel</span>
            <span class="nav-badge">Admin</span>
        </div>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.accounts') }}" class="{{ request()->routeIs('admin.accounts') ? 'active' : '' }}">Accounts</a>
            <a href="{{ route('admin.payments') }}" class="{{ request()->routeIs('admin.payments') ? 'active' : '' }}">Payments</a>
            <a href="{{ route('admin.enrollments') }}" class="{{ request()->routeIs('admin.enrollments') ? 'active' : '' }}">Enrollments</a>
        </div>
        <div class="nav-user">
            <button onclick="toggleTheme()" class="theme-toggle" title="Toggle theme">🌓</button>
            <span>{{ Auth::guard('admin')->user()->full_name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="admin-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>
