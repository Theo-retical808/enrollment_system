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
        background: var(--auth-card-bg);
        backdrop-filter: blur(12px);
        padding: 2.25rem 2.5rem;
        border-radius: 24px;
        box-shadow: var(--auth-card-shadow);
        border: 1px solid var(--auth-card-border);
        width: 100%;
        max-width: 440px;
    }

    .logo-section {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .logo-section img {
        height: 70px;
        width: auto;
        margin-bottom: 0.75rem;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.05));
    }

    .system-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #2563eb;
        margin-bottom: 0.5rem;
    }

    .welcome-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--auth-text-primary);
        margin-bottom: 0.25rem;
    }

    .welcome-subtitle {
        color: var(--auth-text-secondary);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--auth-text-primary);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.9rem 1rem;
        border-radius: 12px;
        border: 1px solid var(--auth-input-border);
        background: var(--auth-input-bg);
        color: var(--auth-text-primary);
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.2s;
        outline: none;
        box-sizing: border-box;
    }

    .form-control:focus {
        background: var(--auth-input-bg);
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .form-control::placeholder {
        color: var(--auth-text-muted);
    }

    .btn-login {
        width: 100%;
        background: #2563eb;
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

    .role-info {
        margin-top: 1.25rem;
        padding: 0.75rem 1rem;
        background: var(--auth-cred-bg);
        border: 1px solid var(--auth-cred-border);
        border-radius: 12px;
        font-size: 0.8rem;
        color: var(--auth-text-secondary);
    }

    .role-info p {
        margin: 0.25rem 0;
    }

    .role-info strong {
        color: var(--auth-text-primary);
    }

    .role-info code {
        background: var(--auth-cred-code-bg);
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid var(--auth-cred-code-border);
        color: var(--auth-cred-code-color);
    }

    .test-credentials {
        margin-top: 1rem;
        width: 100%;
        max-width: 440px;
        background: var(--auth-cred-bg);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        padding: 0.75rem 1.25rem;
        border: 1px solid var(--auth-cred-border);
    }

    .test-credentials-title {
        color: var(--auth-text-primary);
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .credential-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.25rem 0;
        font-size: 0.75rem;
    }

    .credential-row span {
        color: var(--auth-text-secondary);
        font-weight: 600;
    }

    .credential-row code {
        background: var(--auth-cred-code-bg);
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid var(--auth-cred-code-border);
        font-size: 0.75rem;
        color: var(--auth-cred-code-color);
        font-weight: 600;
    }

    .remember-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: var(--auth-text-secondary);
        cursor: pointer;
    }
</style>

<div class="auth-container">
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

        <div class="role-info">
            <p><strong>Auto-detection:</strong> Your role is determined by your ID format.</p>
            <p>Students: <code style="font-size: 0.75rem;">2024-001</code> &nbsp; Professors: <code style="font-size: 0.75rem;">PROF001</code> &nbsp; Admins: <code style="font-size: 0.75rem;">ADMIN001</code></p>
        </div>
    </div>

    <div class="test-credentials">
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
@endsection
