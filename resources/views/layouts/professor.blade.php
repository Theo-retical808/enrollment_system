<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Professor Dashboard') - {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-white: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --hover-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            --accent-blue: #2563eb;
            --accent-blue-hover: #1d4ed8;
        }
        
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-white: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-tertiary: #64748b;
            --border-color: #334155;
            --border-light: #293548;
            --hover-bg: #334155;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            --accent-blue: #3b82f6;
            --accent-blue-hover: #2563eb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Updated font family */
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Header */
        .header {
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Replaced old purple gradient box CSS with an image class */
        .header-logo-img {
            height: 40px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
        }

        .header-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary);
            transition: color 0.3s ease;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            transition: color 0.3s ease;
        }

        .user-role {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            transition: color 0.3s ease;
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
            background: var(--accent-blue);
        }
        
        [data-theme="dark"] .theme-toggle-slider {
            transform: translateX(30px);
        }

        .btn-logout {
            padding: 8px 16px;
            background: var(--border-light);
            color: #ef4444;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.02em;
            transition: color 0.3s ease;
        }

        .page-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        /* Cards */
        .card {
            background: var(--bg-white);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .card-header {
            padding: 24px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color 0.3s ease;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: color 0.3s ease;
        }

        .card-body {
            padding: 24px;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fff7ed;
            color: #ea580c;
        }

        .badge-info {
            background: #eff6ff;
            color: #2563eb;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: var(--hover-bg);
            transition: background-color 0.3s ease;
        }

        .table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
            transition: color 0.3s ease, border-color 0.3s ease;
        }

        .table td {
            padding: 16px;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-light);
            transition: color 0.3s ease, border-color 0.3s ease;
        }

        .table tbody tr:hover {
            background: var(--hover-bg);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            font-family: inherit;
        }

        /* Changed from Purple to UdD Blue */
        .btn-primary {
            background: var(--accent-blue);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-blue-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: var(--border-light);
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        /* Alert */
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 64px 24px;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: #cbd5e1;
        }

        .empty-state-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }

        .empty-state-text {
            font-size: 14px;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--text-tertiary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: color 0.3s ease;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
            transition: color 0.3s ease;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        /* Utility Classes */
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mb-8 { margin-bottom: 32px; }
        .mt-4 { margin-top: 16px; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-4 { gap: 16px; }
        .text-sm { font-size: 14px; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .text-gray-600 { color: var(--text-secondary); transition: color 0.3s ease; }
        .text-gray-900 { color: var(--text-primary); transition: color 0.3s ease; }

        @media (max-width: 768px) {
            .header-container { padding: 0 16px; }
            .main-container { padding: 24px 16px; }
            .page-title { font-size: 24px; }
            .user-info { display: none; }
        }
    </style>
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
            const iconElement = document.getElementById('theme-icon');
            if (iconElement) {
                iconElement.textContent = savedTheme === 'dark' ? '🌙' : '☀️';
            }
        });
    </script>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="header-brand">
                <img src="{{ asset('images/udd_logo.PNG') }}" alt="Universidad de Dagupan Logo" class="header-logo-img">
                <div>
                    <div class="header-title">Professor Portal</div>
                </div>
            </div>
            
            <div class="header-user">
                <div class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                    <div class="theme-toggle-slider">
                        <span id="theme-icon">☀️</span>
                    </div>
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::guard('professor')->user()->full_name }}</div>
                    <div class="user-role">{{ Auth::guard('professor')->user()->school->name ?? 'Professor' }}</div>
                </div>
                <form method="POST" action="{{ route('professor.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="main-container">
        @yield('content')
    </main>
</body>
</html>