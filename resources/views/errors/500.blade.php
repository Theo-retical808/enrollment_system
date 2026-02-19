@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-9xl font-bold text-red-600">500</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Server Error
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Something went wrong on our end. We're working to fix it.
            </p>
        </div>
        
        <div class="mt-8 space-y-4">
            <a href="{{ route('student.dashboard') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Go to Dashboard
            </a>
            
            <div>
                <button onclick="location.reload()" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Try refreshing the page
                </button>
            </div>
        </div>
        
        @if(config('app.debug') && isset($exception))
        <div class="mt-8 p-4 bg-red-50 rounded-lg text-left">
            <h3 class="text-sm font-medium text-red-800 mb-2">Debug Information:</h3>
            <pre class="text-xs text-red-700 overflow-auto">{{ $exception->getMessage() }}</pre>
        </div>
        @endif
    </div>
</div>
@endsection
