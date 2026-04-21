<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            line-height: 1.6;
        }
        
        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: #2563eb;
            text-decoration: none;
        }
        
        .navbar-nav {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        
        .nav-link {
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .nav-link:hover {
            color: #2563eb;
        }
        
        .nav-link.active {
            color: #2563eb;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="/" class="navbar-brand">🎓 Enrollment System</a>
            <ul class="navbar-nav">
                <li><a href="{{ route('courses.index') }}" class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">Courses</a></li>
                <li><a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">Finances</a></li>
                <li><a href="{{ route('student.login') }}" class="nav-link">Student Login</a></li>
                <li><a href="{{ route('professor.login') }}" class="nav-link">Faculty Login</a></li>
            </ul>
        </div>
    </nav>

    <main style="min-height: calc(100vh - 80px); padding: 2rem 0;">
        @yield('content')
    </main>
</body>
</html>
