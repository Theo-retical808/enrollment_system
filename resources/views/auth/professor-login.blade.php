@extends('layouts.app')

@section('nav-links')
    <a href="{{ route('student.login') }}" class="nav-link">Student Login</a>
    <a href="{{ url('/') }}" class="nav-link">Home</a>
@endsection

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h2 style="color: #4f46e5; margin-bottom: 0.5rem;">Professor Login</h2>
            <p style="color: #6b7280;">Enter your credentials to access the review system</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('professor.login') }}">
            @csrf
            
            <div class="form-group">
                <label for="professor_id" class="form-label">Professor ID</label>
                <input 
                    id="professor_id" 
                    type="text" 
                    class="form-input @error('professor_id') error @enderror" 
                    name="professor_id" 
                    value="{{ old('professor_id') }}" 
                    required 
                    autofocus
                    placeholder="Enter your professor ID"
                >
                @error('professor_id')
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
                Are you a student? 
                <a href="{{ route('student.login') }}" style="color: #4f46e5; text-decoration: none;">Login here</a>
            </p>
        </div>
    </div>
</div>
@endsection