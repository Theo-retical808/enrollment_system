<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Enrollment System') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #eff6ff, #f8fafc);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            overflow: hidden;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 40px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .logo-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .system-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            letter-spacing: -0.01em;
        }

        .welcome-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .welcome-subtitle {
            font-size: 15px;
            color: #6b7280;
            font-weight: 400;
        }

        .auth-form {
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s;
            font-family: inherit;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-hint {
            display: block;
            font-size: 13px;
            color: #9ca3af;
            margin-top: 6px;
        }

        .form-group-checkbox {
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #2563eb;
        }

        .btn {
            font-family: inherit;
            font-size: 15px;
            font-weight: 500;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-block {
            width: 100%;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-icon {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alert-content {
            flex: 1;
            font-size: 14px;
            line-height: 1.5;
        }

        .auth-footer {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid #f3f4f6;
        }

        .footer-text {
            font-size: 14px;
            color: #6b7280;
        }

        .footer-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .footer-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .test-credentials {
            margin-top: 24px;
            padding: 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .test-credentials-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
        }

        .test-credentials-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .credential-item {
            font-size: 13px;
        }

        .credential-label {
            display: block;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .credential-values {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .credential-values code {
            background: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            font-size: 13px;
            color: #2563eb;
            border: 1px solid #e5e7eb;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 32px 24px;
            }

            .welcome-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
