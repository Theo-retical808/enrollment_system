@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-shell">
    <div class="auth-card">
        <div class="logo-section">
            <img src="{{ asset('images/udd_logo.PNG') }}" alt="UDD Logo">
            <h1 class="system-title">Enrollment System</h1>
            <h2 class="welcome-title">Welcome back</h2>
            <p class="welcome-subtitle">Sign in with your ID — we'll direct you automatically</p>
        </div>

        @if ($errors->any())
            <div style="background: var(--auth-alert-bg); color: var(--auth-alert-color); padding: 1rem; border-radius: 12px; margin-bottom: 1.25rem; font-size: 0.85rem; border: 1px solid var(--auth-alert-border);">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div style="background: #064e3b; color: #a7f3d0; padding: 1rem; border-radius: 12px; margin-bottom: 1.25rem; font-size: 0.85rem; border: 1px solid #065f46;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            
            <div class="form-group">
                <label for="user_id" class="form-label">User ID</label>
                <input 
                    id="user_id" 
                    type="text" 
                    class="form-control" 
                    name="user_id" 
                    value="{{ old('user_id') }}" 
                    required 
                    autofocus
                    placeholder="e.g., 2024-001, PROF001, ADMIN001"
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
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn-login">
                Sign in
            </button>
        </form>


    </div>

    <div class="test-credentials" style="display: none;">
        <div class="test-credentials-title">Test Credentials</div>
        <div class="credential-row">
            <span>Student:</span>
            <div><code>2024-001</code> <code>password</code></div>
        </div>
        <div class="credential-row">
            <span>Irregular:</span>
            <div><code>2024-003</code> <code>password</code></div>
        </div>
        <div class="credential-row">
            <span>Professor:</span>
            <div><code>PROF001</code> <code>password</code></div>
        </div>
        <div class="credential-row">
            <span>Admin:</span>
            <div><code>ADMIN001</code> <code>password</code></div>
        </div>
    </div>
    </div>
</div>
@endsection
