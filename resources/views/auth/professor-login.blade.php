@extends('layouts.auth')

@section('content')
<style>
    /* Styling isolated to match the UdD Blue theme */
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: radial-gradient(circle at top right, #eff6ff, #f8fafc);
        color: #1e293b;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
    }
    .auth-container {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 2rem;
    }
    .auth-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.5);
        width: 100%;
        max-width: 440px;
        padding: 3.5rem 3rem;
    }
    .auth-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .auth-logo {
        height: 90px;
        width: auto;
        margin-bottom: 1.5rem;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.05));
    }
    .system-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.5px;
    }
    .welcome-subtitle {
        font-size: 1rem;
        color: #64748b;
        margin: 0;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .form-control {
        width: 100%;
        padding: 1rem;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 1rem;
        color: #0f172a;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        background: white;
    }
    .form-group-checkbox {
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
    }
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #64748b;
        cursor: pointer;
    }
    .btn-primary {
        width: 100%;
        background: #2563eb;
        color: white;
        padding: 1.1rem;
        border-radius: 14px;
        border: none;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
    }
    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #ef4444;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        font-size: 0.9rem;
    }
    .alert-icon {
        flex-shrink: 0;
        margin-top: 2px;
    }
    .auth-footer {
        margin-top: 2rem;
        text-align: center;
    }
    .footer-text {
        color: #64748b;
        font-size: 0.95rem;
        margin: 0;
    }
    .footer-link {
        color: #2563eb;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.2s;
    }
    .footer-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="Universidad de Dagupan Logo" class="auth-logo">
            
            <h1 class="system-title">Professor Portal</h1>
            <p class="welcome-subtitle">Sign in to manage your classes</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <svg class="alert-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="alert-content">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('professor.login') }}" class="auth-form">
            @csrf
            
            <div class="form-group">
                <label for="professor_id" class="form-label">Professor ID</label>
                <input 
                    id="professor_id" 
                    type="text" 
                    class="form-control @error('professor_id') is-invalid @enderror" 
                    name="professor_id" 
                    value="{{ old('professor_id') }}" 
                    required 
                    autofocus
                    placeholder="Enter your ID (e.g., P-12345)"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    name="password" 
                    required
                    placeholder="••••••••"
                >
            </div>

            <div class="form-group-checkbox">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Access Portal
            </button>
        </form>

        <div class="auth-footer">
            <p class="footer-text">
                Are you a student? 
                <a href="{{ route('student.login') }}" class="footer-link">Sign in here</a>
            </p>
        </div>
    </div>
</div>
@endsection