@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-section">
                <div class="logo-icon">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="48" height="48" rx="12" fill="#7c3aed"/>
                        <path d="M24 16C20.69 16 18 18.69 18 22C18 25.31 20.69 28 24 28C27.31 28 30 25.31 30 22C30 18.69 27.31 16 24 16ZM24 26C21.79 26 20 24.21 20 22C20 19.79 21.79 18 24 18C26.21 18 28 19.79 28 22C28 24.21 26.21 26 24 26ZM32 32V30C32 27.79 27.97 26 24 26C20.03 26 16 27.79 16 30V32H32Z" fill="white"/>
                    </svg>
                </div>
                <h1 class="system-title">Professor Review System</h1>
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
                    placeholder="Enter your professor ID"
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
                Are you a student? 
                <a href="{{ route('student.login') }}" class="footer-link">Sign in here</a>
            </p>
        </div>
    </div>
</div>
@endsection