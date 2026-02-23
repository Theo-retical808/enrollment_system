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
            --primary-bg: #f8fafc;
            --accent-blue: #eff6ff; /* UdD Soft Blue */
            --accent-blue-text: #2563eb; /* UdD Primary Blue */
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--primary-bg);
            color: #1e293b;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid #e2e8f0;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
        }
        
        /* Updated Logo Section */
        .logo {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }
        
        /* Updated Active State to UdD Blue */
        .nav-link.active {
            background: var(--accent-blue);
            color: var(--accent-blue-text);
        }
        
        .nav-link:hover:not(.active) {
            background: #f1f5f9;
            color: #0f172a;
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
            background: rgba(248, 250, 252, 0.9);
            backdrop-filter: blur(8px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid #f1f5f9; /* Added subtle border for cleaner header separation */
        }
        
        .content-body { padding: 2rem; }

        /* Modern Card Styles */
        .card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px -1px rgba(0, 0, 0, 0.03);
        }
        
        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
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
    </style>
    @yield('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="Universidad de Dagupan Logo" style="height: 40px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));">
            <span>Student Portal</span>
        </div>
        
        <nav style="display:flex; flex-direction:column; height: 100%;">
            <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="#" class="nav-link">Courses</a>
            <a href="{{ route('student.schedule') }}" class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}">Schedule</a>
            <a href="#" class="nav-link">Finances</a>
            
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
            <h1 style="font-size: 1.25rem; font-weight: 800; color: #0f172a;">@yield('title')</h1>
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-weight: 700; font-size: 0.9rem; color: #475569;">{{ Auth::guard('student')->user()->full_name ?? 'Student' }}</span>
                <div style="width:38px; height:38px; background:#e2e8f0; border-radius:50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);"></div>
            </div>
        </header>

        <main class="content-body">
            @yield('content')
        </main>
    </div>
</body>
</html>