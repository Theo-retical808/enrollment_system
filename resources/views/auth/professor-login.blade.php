@extends('layouts.auth')

@section('title', 'Professor Access — Universidad de Dagupan')

@section('left_heading')
    Faculty & Staff<br/>
    <span class="accent">Management</span><br/>
    Access
@endsection

@section('left_description')
    Secure access for UDD faculty members. Manage advising, student records, and academic responsibilities with our streamlined administrative tools.
@endsection

@section('content')
<div class="auth-header" style="margin-bottom: 32px;">
    <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2em; color: var(--blue); margin-bottom: 12px;">Official Faculty Access</p>
    <h2 style="font-size: 26px; font-weight: 800; color: var(--navy); letter-spacing: -0.02em; margin-bottom: 8px;">Professor Login</h2>
    <p style="font-size: 14px; color: var(--gray-500); line-height: 1.5;">Authenticate with your faculty credentials to access enrollment management tools.</p>
</div>

@if ($errors->any())
    <div style="background: #fef2f2; color: #991b1b; padding: 16px; border-radius: 12px; margin-bottom: 24px; border: 1px solid rgba(153, 27, 27, 0.1); font-size: 13px; font-weight: 600;">
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
        <label for="professor_id" class="form-label">Email or Faculty ID</label>
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
        <label for="password" class="form-label">Password</label>
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

    <div class="flex justify-between items-center mb-6">
        <label class="flex items-center gap-3" style="font-size: 13.5px; font-weight: 500; color: var(--gray-700); cursor: pointer;">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--blue); cursor: pointer; border-radius: 4px;">
            Remember this session
        </label>
    </div>

    <button type="submit" class="btn-auth">
        Access Management System
        <i data-lucide="shield-check" style="width: 18px;"></i>
    </button>
</form>

<div style="text-align: center; margin-top: 32px; font-size: 14px;">
    <p style="color: var(--gray-500); font-weight: 500;">
        Student looking to enroll? 
        <a href="{{ route('student.login') }}" class="auth-footer-link">Student Login</a>
    </p>
</div>

<div class="test-credentials">
    <div class="flex items-center gap-2 mb-3" style="color: var(--navy); font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
        <i data-lucide="shield-check" style="width: 14px; color: var(--blue);"></i>
        Faculty Credentials
    </div>
    <div class="flex flex-column gap-3">
        <div class="flex justify-between items-center">
            <span style="font-size: 13px; font-weight: 600; color: var(--gray-500);">Account:</span>
            <div class="credential-item"><code>prof001@example.com</code> <code>password</code></div>
        </div>
    </div>
</div>
@endsection