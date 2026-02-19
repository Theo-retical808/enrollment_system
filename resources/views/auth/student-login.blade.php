@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-section">
                <div class="logo-icon">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="48" height="48" rx="12" fill="#2563eb"/>
                        <path d="M24 14L16 20V32H20V26H28V32H32V20L24 14Z" fill="white"/>
                    </svg>
                </div>
                <h1 class="system-title">Student Enrollment System</h1>
            </div>
            <h2 class="welcome-title">Welcome back</h2>
            <p class="welcome-subtitle">Please enter your details to sign in</p>
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

        @if (session('error'))
            <div class="alert alert-danger">
                <svg class="alert-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('student.login') }}" class="auth-form">
            @csrf
            
            <div class="form-group">
                <label for="student_id" class="form-label">Student ID</label>
                <input 
                    id="student_id" 
                    type="text" 
                    class="form-control @error('student_id') is-invalid @enderror" 
                    name="student_id" 
                    value="{{ old('student_id') }}" 
                    required 
                    autofocus
                    placeholder="2024-001"
                >
                <small class="form-hint">Format: YYYY-XXX (e.g., 2024-001)</small>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    name="password" 
                    required
                    placeholder="Enter your password"
                >
            </div>

            <div class="form-group-checkbox">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Sign in
            </button>
        </form>

        <div class="auth-footer">
            <p class="footer-text">
                Are you a professor? 
                <a href="{{ route('professor.login') }}" class="footer-link">Sign in here</a>
            </p>
        </div>

        <!-- Test Credentials Card -->
        <div class="test-credentials">
            <div class="test-credentials-header">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                </svg>
                <span>Test Credentials</span>
            </div>
            <div class="test-credentials-body">
                <div class="credential-item">
                    <span class="credential-label">Regular Student:</span>
                    <div class="credential-values">
                        <code>2024-001</code>
                        <code>password</code>
                    </div>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Irregular Student:</span>
                    <div class="credential-values">
                        <code>2024-003</code>
                        <code>password</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection