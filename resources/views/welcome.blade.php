<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Universidad de Dagupan - Enrollment System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            background: radial-gradient(circle at top right, #eff6ff, #f8fafc);
            position: relative;
            overflow: hidden;
        }

        .hero-container {
            max-width: 950px;
            text-align: center;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .main-logo {
            height: 150px; 
            width: auto;
            margin-bottom: 2rem;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.08));
        }

        .hero-title {
            font-size: 3.8rem; 
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            margin-bottom: 0.75rem;
            letter-spacing: -1.5px;
        }

        .hero-subtitle {
            font-size: 1.15rem; 
            color: #2563eb; 
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 1.5rem;
        }

        .feature-description {
            font-size: 1.1rem;
            color: #64748b;
            max-width: 600px;
            margin-bottom: 3.5rem;
            line-height: 1.7;
        }

        .button-group {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
        }

        .btn {
            padding: 1.1rem 2.8rem;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-student {
            background: #2563eb; 
            color: white;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
        }

        .btn-student:hover {
            background: #1d4ed8;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
        }

        .btn-professor {
            background: white;
            color: #0f172a;
            border: 2px solid #e2e8f0;
        }

        .btn-professor:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-5px);
        }

        .blob {
            position: absolute;
            z-index: 1;
            filter: blur(100px);
            opacity: 0.3;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero-subtitle { font-size: 1rem; }
            .button-group { flex-direction: column; width: 100%; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

    <section class="hero">
        <div class="blob" style="width: 600px; height: 600px; background: #bfdbfe; top: -15%; right: -10%;"></div>
        <div class="blob" style="width: 500px; height: 500px; background: #dbeafe; bottom: -15%; left: -10%;"></div>

        <div class="hero-container">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="Universidad de Dagupan Logo" class="main-logo">
            
            <h2 class="hero-title">Universidad de Dagupan</h2>
            
            <p class="hero-subtitle">
                The official Enrollment System for Universidad de Dagupan
            </p>

            <p class="feature-description">
                Access your courses, manage schedules, and track your academic progress all in one platform.
            </p>
            
            <div class="button-group">
                <a href="{{ route('student.login') }}" class="btn btn-student">
                    Login as Student
                </a>
                <a href="{{ route('professor.login') }}" class="btn btn-professor">
                    Professor Portal
                </a>
            </div>
        </div>
    </section>

    <footer style="padding: 2.5rem; text-align: center; color: #94a3b8; font-size: 0.95rem; background: transparent;">
        &copy; 2026 Universidad de Dagupan. All Rights Reserved.
    </footer>
</body>
</html>