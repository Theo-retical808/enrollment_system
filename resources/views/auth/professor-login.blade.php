@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo-container">
            <div class="auth-logo-glow"></div>
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="UdD Logo" class="auth-logo">
        </div>
        <h2 class="font-extrabold text-main" style="font-size: 1.85rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">Professor Portal</h2>
        <p class="text-muted font-bold" style="font-size: 0.95rem;">Faculty Authentication Required</p>
    </div>

    @if ($errors->any())
        <div style="background: var(--status-danger-bg); color: var(--status-danger-text); padding: 1.1rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid rgba(var(--status-danger-text-rgb), 0.2); font-size: 0.85rem; font-weight: 600;">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" style="width: 16px;"></i>
                    {{ $error }}
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('professor.login') }}">
        @csrf
        
        <div class="form-group">
            <label for="professor_id" class="form-label">Professor ID / Email</label>
            <div class="input-wrapper">
                <i data-lucide="mail" class="input-icon"></i>
                <input 
                    id="professor_id" 
                    type="text" 
                    class="form-control" 
                    name="professor_id" 
                    value="{{ old('professor_id') }}" 
                    required 
                    placeholder="prof@udd.edu.ph"
                    autocomplete="username"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Access Password</label>
            <div class="input-wrapper">
                <i data-lucide="lock" class="input-icon"></i>
                <input 
                    id="password" 
                    type="password" 
                    class="form-control" 
                    name="password" 
                    required
                    placeholder="••••••••"
                    autocomplete="current-password"
                >
            </div>
        </div>

        <div class="flex justify-between items-center mb-8">
            <label class="flex items-center gap-2 text-muted font-bold" style="font-size: 0.85rem; cursor: pointer;">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--udd-blue); cursor: pointer;">
                Stay authenticated
            </label>
        </div>

        <button type="submit" class="btn-auth btn-student">
            Access Portal
            <i data-lucide="shield-check" style="width: 20px;"></i>
        </button>
    </form>

    <div style="text-align: center; margin-top: 2.5rem; font-size: 0.9rem;">
        <p class="text-muted font-bold">
            Entering as a Student? 
            <a href="{{ route('student.login') }}" class="auth-footer-link">Student Login</a>
        </p>
    </div>

    <div class="test-credentials">
        <div class="flex items-center gap-2 mb-4" style="color: var(--text-main); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
            <i data-lucide="shield-check" style="width: 14px; color: var(--udd-blue);"></i>
            Faculty Access
        </div>
        <div class="flex flex-column gap-3">
            <div class="flex justify-between items-center">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">Standard:</span>
                <div class="credential-item"><code>prof001@example.com</code> <code>password</code></div>
            </div>
        </div>
    </div>
</div>
@endsection