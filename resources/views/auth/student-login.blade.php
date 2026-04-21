@extends('layouts.auth')

@section('title', 'Student Login — Universidad de Dagupan')

@section('left_heading')
    Student<br/>
    <span class="accent">Enrollment</span><br/>
    Access
@endsection

@section('left_description')
    Student primary gateway for UDD. Access a simplified, high-performance enrollment experience tailored for the modern academic journey.
@endsection

@section('content')
<div class="auth-header" style="margin-bottom: 32px;">
    <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2em; color: var(--blue); margin-bottom: 12px;">Official Student Access</p>
    <h2 style="font-size: 26px; font-weight: 800; color: var(--navy); letter-spacing: -0.02em; margin-bottom: 8px;">Student Login</h2>
    <p style="font-size: 14px; color: var(--gray-500); line-height: 1.5;">Please enter your credentials to proceed to the enrollment system.</p>
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

<form method="POST" action="{{ route('student.login') }}">
    @csrf
    
    <div class="form-group">
        <label for="student_id" class="form-label">Student ID Number</label>
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
            Remember this device
        </label>
    </div>

    <button type="submit" class="btn-auth">
        Sign in to Enrollment System
        <i data-lucide="arrow-right" style="width: 18px;"></i>
    </button>
</form>

<div style="text-align: center; margin-top: 32px; font-size: 14px;">
    <p style="color: var(--gray-500); font-weight: 500;">
        Accessing as Faculty? 
        <a href="{{ route('professor.login') }}" class="auth-footer-link">Professor Login</a>
    </p>
</div>

<div class="test-credentials">
    <div class="flex items-center gap-2 mb-3" style="color: var(--navy); font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
        <i data-lucide="key-round" style="width: 14px; color: var(--blue);"></i>
        Demo Accounts
    </div>
    <div class="flex flex-column gap-3">
        <div class="flex justify-between items-center">
            <span style="font-size: 13px; font-weight: 600; color: var(--gray-500);">Primary:</span>
            <div class="credential-item"><code>2024-001</code> <code>password</code></div>
        </div>
        <div class="flex justify-between items-center">
            <span style="font-size: 13px; font-weight: 600; color: var(--gray-500);">Secondary:</span>
            <div class="credential-item"><code>2024-003</code> <code>password</code></div>
        </div>
    </div>
</div>
@endsection