@extends('layouts.app')

@section('nav-links')
    <a href="{{ route('professor.login') }}" class="nav-link">Professor Login</a>
    <a href="{{ url('/') }}" class="nav-link">Home</a>
@endsection

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h2 style="color: #4f46e5; margin-bottom: 0.5rem;">Student Login</h2>
            <p style="color: #6b7280;">Enter your credentials to access the enrollment system</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
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
                    class="form-input @error('student_id') error @enderror" 
                    name="student_id" 
                    value="{{ old('student_id') }}" 
                    required 
                    autofocus
                    placeholder="Enter your student ID"
                >
                @error('student_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    class="form-input @error('password') error @enderror" 
                    name="password" 
                    required
                    placeholder="Enter your password"
                >
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Login
            </button>
        </form>

        <div class="text-center mt-4">
            <p style="color: #6b7280;">
                Are you a professor? 
                <a href="{{ route('professor.login') }}" style="color: #4f46e5; text-decoration: none;">Login here</a>
            </p>
        </div>
    </div>
</div>
@endsection