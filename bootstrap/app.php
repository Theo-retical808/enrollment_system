<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'payment.required' => \App\Http\Middleware\CheckPaymentStatus::class,
            'student.auth' => \App\Http\Middleware\StudentAuth::class,
            'professor.auth' => \App\Http\Middleware\ProfessorAuth::class,
            'rate.limit.enrollment' => \App\Http\Middleware\RateLimitEnrollment::class,
            'rate.limit.login' => \App\Http\Middleware\RateLimitLogin::class,
            'sanitize.input' => \App\Http\Middleware\SanitizeInput::class,
        ]);

        // Apply input sanitization globally to web routes
        $middleware->web(append: [
            \App\Http\Middleware\SanitizeInput::class,
        ]);

        // Configure trusted proxies for production environments
        $middleware->trustProxies(at: '*');

        // Enable CSRF protection (enabled by default in Laravel)
        // Verify CSRF token on all POST, PUT, PATCH, DELETE requests
        $middleware->validateCsrfTokens(except: [
            // Add any routes that need to be excluded from CSRF protection
            // (e.g., webhook endpoints)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle database connection errors gracefully
        $exceptions->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            \Log::error('Database query error', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'DATABASE_ERROR',
                    'message' => 'A database error occurred. Please try again.',
                    'debug' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'A system error occurred. Please try again.')
                ->withInput();
        });

        // Handle model not found errors
        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'NOT_FOUND',
                    'message' => 'The requested resource was not found.',
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'The requested resource was not found.');
        });

        // Handle authentication errors
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'UNAUTHENTICATED',
                    'message' => 'Authentication required.',
                ], 401);
            }

            return redirect()->route('student.login')
                ->with('error', 'Please log in to continue.');
        });

        // Handle authorization errors
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'UNAUTHORIZED',
                    'message' => 'You are not authorized to perform this action.',
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You are not authorized to perform this action.');
        });

        // Handle validation errors with enhanced messaging
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Please correct the errors below.')
                ->withInput();
        });

        // Handle network/timeout errors
        $exceptions->renderable(function (\Illuminate\Http\Client\ConnectionException $e, $request) {
            \Log::error('Network connection error', [
                'message' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'NETWORK_ERROR',
                    'message' => 'Network connection error. Please check your connection and try again.',
                ], 503);
            }

            return redirect()->back()
                ->with('error', 'Network connection error. Please try again.')
                ->withInput();
        });
    })->create();
