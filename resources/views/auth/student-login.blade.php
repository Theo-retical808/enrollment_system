@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo-container">
            <div class="auth-logo-glow"></div>
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="UdD Logo" class="auth-logo">
        </div>
        <h2 class="font-extrabold text-main" style="font-size: 1.85rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">Student Portal</h2>
        <p class="text-muted font-bold" style="font-size: 0.95rem;">Authentication required to proceed</p>
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

    <form method="POST" action="{{ route('student.login') }}">
        @csrf
        
        <div class="form-group">
            <label for="student_id" class="form-label">Student Identification</label>
            <div class="input-wrapper">
                <i data-lucide="user" class="input-icon"></i>
                <input 
                    id="student_id" 
                    type="text" 
                    class="form-control" 
                    name="student_id" 
                    value="{{ old('student_id') }}" 
                    required 
                    placeholder="2024-XXXX"
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
            Authenticate
            <i data-lucide="arrow-right" style="width: 20px;"></i>
        </button>
    </form>

    <div style="text-align: center; margin-top: 2.5rem; font-size: 0.9rem;">
        <p class="text-muted font-bold">
            Accessing as Faculty? 
            <a href="{{ route('professor.login') }}" class="auth-footer-link">Professor Login</a>
        </p>
    </div>

    <div class="test-credentials">
        <div class="flex items-center gap-2 mb-4" style="color: var(--text-main); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
            <i data-lucide="key-round" style="width: 14px; color: var(--udd-blue);"></i>
            Test Environments
        </div>
        <div class="flex flex-column gap-3">
            <div class="flex justify-between items-center">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">Common:</span>
                <div class="credential-item"><code>2024-001</code> <code>password</code></div>
            </div>
            <div class="flex justify-between items-center">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">Alternate:</span>
                <div class="credential-item"><code>2024-003</code> <code>password</code></div>
            </div>
        </div>
    </div>
</div>
@endsection