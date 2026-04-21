<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'UdD Enrollment'))</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/theme.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
          --navy:     #0c1660;
          --navy-mid: #152080;
          --navy-lit: #1c2ea0;
          --blue:     #1a56db;
          --blue-lit: #3b82f6;
          --white:    #ffffff;
          --gray-50:  #f8f9fc;
          --gray-100: #eef0f6;
          --gray-300: #c4cade;
          --gray-500: #7480a0;
          --gray-700: #3d4a6b;
          --text:     #111827;
        }

        html, body {
          height: 100%;
          font-family: 'Figtree', sans-serif;
          background: var(--gray-50);
          color: var(--text);
          overflow: hidden;
        }

        .layout {
          display: flex;
          min-height: 100vh;
        }

        /* ── LEFT PANEL ── */
        .left {
          width: 550px;
          flex-shrink: 0;
          background: var(--navy);
          position: relative;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          padding: 64px 60px;
          overflow: hidden;
        }

        .left::before {
          content: '';
          position: absolute;
          bottom: -140px; right: -140px;
          width: 400px; height: 400px;
          border-radius: 50%;
          background: radial-gradient(circle, rgba(26,86,219,.3) 0%, transparent 70%);
          pointer-events: none;
        }

        .left::after {
          content: '';
          position: absolute;
          top: -80px; left: -80px;
          width: 260px; height: 260px;
          border-radius: 50%;
          background: radial-gradient(circle, rgba(28,46,160,.5) 0%, transparent 70%);
          pointer-events: none;
        }

        .left-body {
          position: relative;
          z-index: 1;
          animation: fadeLeft .55s ease both;
        }

        .seal-wrap {
          display: flex;
          align-items: center;
          gap: 13px;
          margin-bottom: 56px;
        }

        .seal-circle {
          width: 72px; height: 72px;
          display: flex; align-items: center; justify-content: center;
          flex-shrink: 0;
          background: rgba(255, 255, 255, 0.05);
          border: 1.5px solid rgba(255, 255, 255, 0.15);
          border-radius: 50%;
          padding: 12px;
          backdrop-filter: blur(10px);
          -webkit-backdrop-filter: blur(10px);
        }

        .seal-logo {
          width: 100%;
          height: 100%;
          object-fit: contain;
          filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.1));
        }

        .seal-name {
          font-size: 13px;
          font-weight: 700;
          color: var(--white);
          line-height: 1.25;
        }

        .seal-sub {
          font-size: 10px;
          font-weight: 400;
          letter-spacing: .14em;
          text-transform: uppercase;
          color: rgba(255,255,255,.4);
          margin-top: 3px;
        }

        .left-label {
          font-size: 10.5px;
          font-weight: 600;
          letter-spacing: .2em;
          text-transform: uppercase;
          color: rgba(255,255,255,.4);
          margin-bottom: 14px;
        }

        .left-heading {
          font-size: 48px;
          font-weight: 800;
          color: var(--white);
          line-height: 1.1;
          letter-spacing: -.02em;
          margin-bottom: 24px;
        }

        .left-heading .accent { color: #60a5fa; }

        .left-desc {
          font-size: 15px;
          font-weight: 400;
          color: rgba(255,255,255,.5);
          line-height: 1.7;
          max-width: 380px;
        }

        .iso-badge {
          position: relative;
          z-index: 1;
          display: inline-flex;
          align-items: center;
          gap: 9px;
          margin-top: 44px;
          padding: 10px 16px;
          border: 1px solid rgba(255,255,255,.1);
          border-radius: 8px;
          background: rgba(255,255,255,.04);
        }

        .iso-dot {
          width: 7px; height: 7px;
          border-radius: 50%;
          background: #34d399;
          box-shadow: 0 0 7px #34d399;
          flex-shrink: 0;
        }

        .iso-text {
          font-size: 11.5px;
          font-weight: 600;
          color: rgba(255,255,255,.6);
          letter-spacing: .03em;
        }

        .left-footer {
          position: relative;
          z-index: 1;
          font-size: 11px;
          color: rgba(255,255,255,.22);
          letter-spacing: .05em;
          animation: fadeLeft .55s ease .1s both;
        }

        /* ── RIGHT PANEL ── */
        .right {
          flex: 1;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 48px 64px;
          background: var(--white);
          animation: fadeRight .55s ease both;
          overflow-y: auto;
        }

        .right-inner {
          width: 100%;
          max-width: 400px;
        }

        /* Forms */
        .form-group { margin-bottom: 1.5rem; }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--gray-500);
            margin-bottom: 8px;
        }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--gray-300);
            pointer-events: none;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px 14px 46px;
            border-radius: 12px;
            border: 1.5px solid var(--gray-100);
            background: var(--gray-50);
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 500;
            color: var(--text);
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(26, 86, 219, 0.1);
        }
        .form-control:focus + .input-icon { color: var(--blue); }

        .btn-auth {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            color: var(--white);
            background: var(--navy);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 24px;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            background: var(--navy-lit);
            box-shadow: 0 8px 24px rgba(12, 22, 96, 0.2);
        }

        .auth-footer-link {
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer-link:hover { text-decoration: underline; }

        .test-credentials {
            margin-top: 32px;
            padding: 20px;
            background: var(--gray-50);
            border: 1.5px solid var(--gray-100);
            border-radius: 16px;
        }

        .credential-item code {
            background: var(--white);
            border: 1px solid var(--gray-100);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            color: var(--blue);
            font-weight: 600;
        }

        /* ── Animations ── */
        @keyframes fadeLeft {
          from { opacity: 0; transform: translateX(-18px); }
          to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeRight {
          from { opacity: 0; transform: translateX(18px); }
          to   { opacity: 1; transform: translateX(0); }
        }

        /* ── Mobile ── */
        @media (max-width: 850px) {
          .layout { flex-direction: column; }
          .left { width: 100%; padding: 40px 30px; }
          .right { padding: 40px 30px; }
          html, body { overflow: auto; }
        }
    </style>
</head>
<body>

<div class="layout">
    <!-- LEFT PANEL -->
    <aside class="left">
        <div class="left-body">
            <div class="seal-wrap">
                <div class="seal-circle">
                    <img src="{{ asset('images/udd_logo.PNG') }}" alt="UDD Logo" class="seal-logo">
                </div>
                <div>
                    <div class="seal-name">Universidad de Dagupan</div>
                    <div class="seal-sub">Formerly Colegio de Dagupan</div>
                </div>
            </div>

            <p class="left-label">Enrollment System</p>
            <h1 class="left-heading">
                @yield('left_heading', 'Access Enrollment System')
            </h1>
            <p class="left-desc">
                @yield('left_description', 'Empowering the UDD community with a modernized, efficient, and secure digital enrollment experience.')
            </p>

            <div class="iso-badge">
                <div class="iso-dot"></div>
                <div class="iso-text">ISO 21001:2018 Certified</div>
            </div>
        </div>

        <div class="left-footer">
            © {{ date('Y') }} Universidad de Dagupan &nbsp;·&nbsp; All rights reserved
        </div>
    </aside>

    <!-- RIGHT PANEL -->
    <main class="right">
        <div class="right-inner">
            @yield('content')
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
</body>
</html>
