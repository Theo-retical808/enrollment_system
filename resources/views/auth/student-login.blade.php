@extends('layouts.auth')

@section('content')
<style>
    body {
        height: 100vh;
        overflow: hidden;
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        padding: 2.25rem 2.5rem;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .logo-section {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .logo-section img {
        height: 55px;
        width: auto;
        margin-bottom: 0.75rem;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.05));
    }

    .system-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #2563eb; /* UdD Blue */
        margin-bottom: 0.5rem;
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .welcome-subtitle {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 0.75rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.9rem 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
        box-sizing: border-box;
    }

    .form-control:focus {
        background: white;
        border-color: #2563eb; /* UdD Blue */
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .btn-login {
        width: 100%;
        background: #2563eb; /* UdD Blue */
        color: white;
        padding: 0.9rem;
        border-radius: 12px;
        border: none;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 0.5rem;
    }

    .btn-login:hover {
        background: #1d4ed8;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
    }

    .test-credentials {
        margin-top: 1rem;
        width: 100%;
        max-width: 440px;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e2e8f0;
    }

    .credential-item code {
        background: white;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        font-size: 0.8rem;
        color: #2563eb;
        font-weight: 600;
    }

    .footer-link {
        color: #2563eb; /* UdD Blue */
        text-decoration: none;
        font-weight: 700;
        transition: color 0.2s;
    }

    .footer-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="logo-section" style="margin-bottom: 1.25rem;">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="UDD Logo" style="height: 70px; margin-bottom: 0.75rem;">
            <h1 class="system-title" style="font-size: 0.75rem; margin-bottom: 0.25rem;">Enrollment System</h1>
            <h2 class="welcome-title" style="font-size: 1.6rem; margin-bottom: 0.25rem;">Welcome back</h2>
            <p class="welcome-subtitle" style="font-size: 0.9rem; margin-bottom: 0;">Please enter your details to sign in</p>
        </div>

        @if ($errors->any())
            <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.85rem; border: 1px solid #fecaca;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('student.login') }}">
            @csrf
            
            <div class="form-group">
                <label for="student_id" class="form-label">Student ID</label>
                <input 
                    id="student_id" 
                    type="text" 
                    class="form-control" 
                    name="student_id" 
                    value="{{ old('student_id') }}" 
                    required 
                    placeholder="e.g., 2024-001"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    class="form-control" 
                    name="password" 
                    required
                    placeholder="••••••••"
                >
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #64748b; cursor: pointer;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn-login">
                Sign in
            </button>
        </form>

        <div style="text-align: center; margin-top: 1rem; font-size: 0.85rem;">
            <p style="color: #64748b;">
                Are you a professor? 
                <a href="{{ route('professor.login') }}" class="footer-link">Sign in here</a>
            </p>
        </div>
    </div>

    <div class="test-credentials">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            <div style="color: #1e3a8a; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; white-space: nowrap;">
                Test Access
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: flex-end;">
                <div class="credential-item" style="font-size: 0.75rem;">
                    <span style="color: #64748b; font-weight: 600;">Student:</span> <code>2024-001</code> <code>password</code>
                </div>
                <div class="credential-item" style="font-size: 0.75rem;">
                    <span style="color: #64748b; font-weight: 600;">Irreg:</span> <code>2024-003</code> <code>password</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection