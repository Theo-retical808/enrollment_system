@extends('layouts.app')

@section('title', 'Service Unavailable')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-9xl font-bold text-orange-600">503</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Service Unavailable
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                The enrollment system is temporarily unavailable. Please try again in a few moments.
            </p>
        </div>
        
        <div class="mt-8 space-y-4">
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Try Again
            </button>
            
            <div>
                <p class="text-xs text-gray-500">
                    If the problem persists, please contact technical support.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
